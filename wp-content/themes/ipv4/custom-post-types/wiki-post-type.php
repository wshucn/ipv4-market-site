<?php
/**
 * Register Wiki Custom Post Type
 */
function register_wiki_post_type() {
	$labels = array(
		'name'                 => _x( 'Wiki', 'Post Type General Name', 'text_domain' ),
		'singular_name'        => _x( 'Wiki', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'            => __( 'Wiki', 'text_domain' ),
		'name_admin_bar'       => __( 'Wiki', 'text_domain' ),
		'archives'             => __( 'Wiki Archives', 'text_domain' ),
		'attributes'           => __( 'Wiki Attributes', 'text_domain' ),
		'parent_item_colon'    => __( 'Parent Wiki:', 'text_domain' ),
		'all_items'            => __( 'All Wiki Posts', 'text_domain' ),
		'add_new_item'         => __( 'Add New Wiki Post', 'text_domain' ),
		'add_new'              => __( 'Add New', 'text_domain' ),
		'new_item'             => __( 'New Wiki Post', 'text_domain' ),
		'edit_item'            => __( 'Edit Wiki Post', 'text_domain' ),
		'update_item'          => __( 'Update Wiki Post', 'text_domain' ),
		'view_item'            => __( 'View Wiki Post', 'text_domain' ),
		'view_items'           => __( 'View Wiki Posts', 'text_domain' ),
		'search_items'         => __( 'Search Wiki Post', 'text_domain' ),
	);

	$args = array(
		'label'               => __( 'Wiki', 'text_domain' ),
		'description'         => __( 'Wiki posts', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		'taxonomies'          => array( 'wiki_category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-book-alt',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'show_in_rest'        => true, // Enable Gutenberg editor
	);

	register_post_type( 'wiki', $args );

	// Register Wiki Category Taxonomy
	$tax_labels = array(
		'name'              => _x( 'Wiki Categories', 'taxonomy general name', 'text_domain' ),
		'singular_name'     => _x( 'Wiki Category', 'taxonomy singular name', 'text_domain' ),
		'search_items'      => __( 'Search Wiki Categories', 'text_domain' ),
		'all_items'         => __( 'All Wiki Categories', 'text_domain' ),
		'parent_item'       => __( 'Parent Wiki Category', 'text_domain' ),
		'parent_item_colon' => __( 'Parent Wiki Category:', 'text_domain' ),
		'edit_item'         => __( 'Edit Wiki Category', 'text_domain' ),
		'update_item'       => __( 'Update Wiki Category', 'text_domain' ),
		'add_new_item'      => __( 'Add New Wiki Category', 'text_domain' ),
		'new_item_name'     => __( 'New Wiki Category Name', 'text_domain' ),
		'menu_name'         => __( 'Wiki Categories', 'text_domain' ),
	);

	$tax_args = array(
		'hierarchical'      => true,
		'labels'            => $tax_labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'wiki-category' ),
		'show_in_rest'      => true, // Enable Gutenberg editor
	);

	register_taxonomy( 'wiki_category', array( 'wiki' ), $tax_args );
}
add_action( 'init', 'register_wiki_post_type' );

/**
 * Flush rewrite rules on activation
 */
function wiki_rewrite_flush() {
	register_wiki_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wiki_rewrite_flush' );
