<?php

/**
 * Contains main architure class
 * 
 * @package YA_Content_Architecture
 */
if ( !class_exists( 'YA_Content_Architecture' ) ) {

	/**
	 * Builds the content architecture for WordPress plugins
	 * 
	 * @author Saurabh Shukla <saurabh@yapapaya.com>
	 */
	class YA_Content_Architecture {

		/**
		 * Architecture configuration
		 * 
		 * @var array 
		 */
		private $architecture = array();

		/**
		 * Custom table names
		 * 
		 * @var array
		 */
		private $tables = array();

		/**
		 * Custom meta data table names
		 * 
		 * @var array 
		 */
		private $meta_tables = array();

		/**
		 * Table preix
		 * 
		 * @var string
		 */
		private $prefix = '';

		/**
		 * Database architecture version
		 *  
		 * @var string
		 */
		private $version = '';

		/**
		 * Path to directory where schema information is stored
		 * 
		 * @var string
		 */
		private $schema_path = '';

		/**
		 * Constructor
		 * 
		 * @param string $prefix table prefix
		 * @param type $schema_path Path to schema directory
		 * @param string $db_version The version number
		 * @return type
		 */
		public function __construct( $prefix, $schema_path, $db_version = '0.0.1' ) {

			if ( empty( $prefix ) || empty( $schema_path ) ) {
				return;
			}

			$this->prefix = $prefix;

			$this->version = $db_version;

			$this->schema_path = trailingslashit( $schema_path );

			$this->architecture = include_once $this->schema_path . 'config.php';

			$this->initialise_table_names();
		}

		/**
		 * Initialises custom table names
		 * 
		 * @global object $wpdb
		 */
		private function initialise_table_names() {

			global $wpdb;

			foreach ( $this->architecture['custom'] as $name => $params ) {

				$this->tables[$name] = $wpdb->prefix . $this->prefix . $this->prettify( $name );

				if ( empty( $params ) ) {
					continue;
				}

				if ( !isset( $params['has_meta'] ) ) {
					continue;
				}

				if ( $params['has_meta'] === true ) {
					$this->meta_tables[$name] = $wpdb->prefix . $this->prefix . $this->prettify( $name ) . '_meta';
				}
			}
		}

		/*
		 * ================================
		 * Installation methods
		 * ================================
		 * Use on plugin installation/ activation
		 */

		/**
		 * Creates custom tables
		 * 
		 * @return array result strings from dbDelta()
		 */
		public function install() {

			$update_result_tables = $this->install_tables();

			$update_result_meta_tables = $this->install_meta_tables();

			$update_results = $update_result_tables + $update_result_meta_tables;

			$this->update_db_version();

			return $update_results;
		}

		/**
		 * Installs custom content tables
		 * 
		 * @global object $wpdb
		 * @return array
		 */
		private function install_tables() {

			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$for_update = array();

			foreach ( $this->tables as $file_name => $table_name ) {

				$sql = include_once $this->schema_path . 'custom/' . $file_name . '.php';

				$sql = sprintf( $sql, $table_name );

				$sql .= $charset_collate . ';';

				$for_update[] = dbDelta( $sql );
			}

			return $for_update;
		}

		/**
		 * Installs custom meta tables for custom tables
		 * 
		 * @global object $wpdb
		 * @return array
		 */
		private function install_meta_tables() {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$for_update = array();

			foreach ( $this->meta_tables as $index => $table_name ) {

				$sql = include_once $this->schema_path . 'custom-meta/custom-meta.php';
				
				$key = $this->prettify( $index );

				$sql = sprintf( $sql, $table_name, $key, $key );

				$sql .= $charset_collate . ';';

				$for_update[] = dbDelta( $sql );
			}

			return $for_update;
		}

		/**
		 * Updates database version in options table
		 */
		private function update_db_version() {

			add_option( $this->prefix . '_db_version', $this->version );
		}

		/**
		 * Delete all tables
		 */
		public function uninstall() {
			$tables = $this->tables + $this->meta_tables;

			foreach ( $tables as $index => $table_name ) {
				$sql = "DROP TABLE IF EXISTS $table_name";
			}
		}

		/*
		 * ================================
		 * Initialisation methods
		 * ================================
		 */

		/**
		 * Initialises all content on WP init
		 */
		public function init() {

			// intialise cpts and taxonomies
			add_action( 'init', array( $this, 'init_wp_types' ) );

			// initialise meta tables
			add_action( 'init', array( $this, 'hook_custom_meta' ), 0 );
			add_action( 'switch_blog', array( $this, 'hook_custom_meta' ), 0 );
		}

		/**
		 * Initialises cpts & taxonomies
		 */
		public function init_wp_types() {
			$this->register_wp_types( 'post_type' );
			$this->register_wp_types( 'taxonomy' );
		}

		/**
		 * Registers cpts & taxonomies
		 * 
		 * @param string $type post_type or taxonomies
		 */
		public function register_wp_types( $type ) {

			foreach ( $this->architecture[$type] as $ind_type ) {

				// include the schema
				$arguments = include_once $this->schema_path . $type . '/' . $ind_type . '.php';

				// register post_type or taxonomy
				${'register_' . $type}( $ind_type, $arguments );
			}
		}

		/**
		 * Initialises custom meta tables for Metadata API
		 * 
		 * @global object $wpdb
		 */
		public function hook_meta_tables() {

			global $wpdb;

			foreach ( $this->meta_tables as $table ) {
				$wpdb->eventmeta = $wpdb->prefix . $table;

				$wpdb->tables[] = $table;
			}
		}

		/*
		 * ================================
		 * Helper methods
		 * ================================
		 */

		/**
		 * Replaces hyphens with underscores in a string for use in table names
		 * 
		 * @param string $string_with_hyphens
		 * @return string
		 */
		private function prettify( $string_with_hyphens ) {

			$string_with__s = str_replace( '-', '_', $string_with_hyphens );

			return $string_with__s;
		}

	}

}
