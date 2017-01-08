#YA Content Architecture for WordPress

Helps manage a plugin's content architecture including:

 1. Custom Post Types
 1. Custom Taxonomies
 1. Custom Tables for Custom Content
 1. Metadata API support for Custom Content. See: [Meta for Custom Objects in WordPress](http://hookrefineandtinker.com/2015/02/meta-for-custom-objects-in-wordpress/)

## Usage

### Creating schemas

 1. See `/sample-schema` directory for examples.
 1. **Do not change** the directory structure.
 1. **Do not edit** `/custom-meta/custom-meta.php`.
 1. Add one schema file each (using the sample) for every custom post type in `/post_type/`.
 1. Add one schema file each (using the sample) for every custom taxonomy in `/taxonomy/`.
 1. Add one schema file each (using the sample) for every custom table in `/custom/`.
 1. Create a config file called `config.php` inside the root of the schema directory.

### Config File

Use the included sample to return an array of all the content types. 


```php
<?php
return $architecture = array(
	'post_type' => array(
		'books',
	),
	'taxonomy' => array(
		'authors',
	),
	'custom' => array(
		// this will add support for Metadata API
		'libraries' => array(
			'has_meta' => true,
		),
		'lending' => array(),
	),
);
```
**Note**

 * Under the `custom` index of this array, include each custom table as `'table_name' => array()`.
 * Tables that create custom content objects that need Metadata API support will need a `has_meta` key in the config.

### Initialising Architecture

Include the class `class-ya-content-architecture.php` in your plugin somewhere.

**Parameters**

 1. `$prefix` to be used for the custom tables.
 2. `$schema_path` is the path to your schema directory.
 3. Optionally, pass a `$db_version` to save in the options table.

**Instantiate**

```php
$architecture = new YA_Content_Architecture( '_my_prefix', plugin_dir_path().'/schema/', '0.1.0');

register_activation_hook( __FILE__, array ( $architecture, 'install' ) );

$architecture->init();
```
*Methods*

 * An `install()` method that'll install/update the custom tables. Can be used in the activation hook.
 * An `init()` method that registers the post types, taxonomies and support for Metadata API. Just call it, it will automatically hook the appropriate methods to WordPress's `init` action.

