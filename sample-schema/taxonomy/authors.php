<?php

$labels = array(
	'name' => _x( 'Authors', 'taxonomy general name', 'textdomain' ),
	'singular_name' => _x( 'Author', 'taxonomy singular name', 'textdomain' ),
	'search_items' => __( 'Search Authors', 'textdomain' ),
	'popular_items' => __( 'Popular Authors', 'textdomain' ),
	'all_items' => __( 'All Authors', 'textdomain' ),
	'parent_item' => null,
	'parent_item_colon' => null,
	'edit_item' => __( 'Edit Author', 'textdomain' ),
	'update_item' => __( 'Update Author', 'textdomain' ),
	'add_new_item' => __( 'Add New Author', 'textdomain' ),
	'new_item_name' => __( 'New Author Name', 'textdomain' ),
	'separate_items_with_commas' => __( 'Separate authors with commas', 'textdomain' ),
	'add_or_remove_items' => __( 'Add or remove authors', 'textdomain' ),
	'choose_from_most_used' => __( 'Choose from the most used authors', 'textdomain' ),
	'not_found' => __( 'No authors found.', 'textdomain' ),
	'menu_name' => __( 'Authors', 'textdomain' ),
);

return $args = array(
	'hierarchical' => false,
	'labels' => $labels,
	'show_ui' => true,
	'show_admin_column' => true,
	'update_count_callback' => '_update_post_term_count',
	'query_var' => true,
	'rewrite' => array( 'slug' => 'author' ),
);