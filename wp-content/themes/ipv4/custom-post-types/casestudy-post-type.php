<?php
/**
 * Team
 *
 * @package      CoreFunctionality
 * @author       Bill Erickson
 * @since        1.0.0
 * @license      GPL-2.0+
**/

class MP_CaseStudies {

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
			'name'               => 'Case Studies',
			'singular_name'      => 'Case Study',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Case Study',
			'edit_item'          => 'Edit Case Study',
			'new_item'           => 'New Case Study',
			'view_item'          => 'View Case Study',
			'search_items'       => 'Search Case Studies',
			'not_found'          => 'No Case Studies found',
			'not_found_in_trash' => 'No Case Studies found in Trash',
			'parent_item_colon'  => 'Parent Case Study:',
			'menu_name'          => 'Case Studies',
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
			'rewrite'             => array( 'slug' => 'case-studies', 'with_front' => false ),
			'menu_icon'           => 'dashicons-admin-site'
		);

		register_post_type( 'case-studies', $args );

	}
}
new MP_CaseStudies();
