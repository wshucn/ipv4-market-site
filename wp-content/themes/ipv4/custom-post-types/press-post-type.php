<?php
/**
 * Team
 *
 * @package      CoreFunctionality
 * @author       Bill Erickson
 * @since        1.0.0
 * @license      GPL-2.0+
**/

class MP_Press {

	/**
	 * Initialize all the things
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		// Actions
		add_action( 'init', array( $this, 'register_cpt' ) );
		// add_filter( 'render_block', array( $this, 'extend_quote_block' ), 99, 2);
		// add_filter( 'wp_insert_post_data', array( $this, 'set_testimonial_title' ), 99, 2 );
	}

	/**
	 * Register the custom post type
	 *
	 * @since 1.2.0
	 */
	function register_cpt() {

		$labels = array(
			'name'               => 'Press Releases',
			'singular_name'      => 'Press Release',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Press Release',
			'edit_item'          => 'Edit Press Release',
			'new_item'           => 'New Press Release',
			'view_item'          => 'View Press Release',
			'search_items'       => 'Search Press Releases',
			'not_found'          => 'No Press Releases found',
			'not_found_in_trash' => 'No Press Releases found in Trash',
			'parent_item_colon'  => 'Parent Press Release:',
			'menu_name'          => 'Press Releases',
			'featured_image'        => 'Image',
			'set_featured_image'    => 'Set image',
			'remove_featured_image' => 'Remove image',
			'use_featured_image'    => 'Use as image',
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'blocks', 'excerpt', 'page-attributes' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array( 'slug' => 'press-releases', 'with_front' => false ),
			'menu_icon'           => 'dashicons-media-text'
		);

		register_post_type( 'press', $args );

	}
}
new MP_Press();
