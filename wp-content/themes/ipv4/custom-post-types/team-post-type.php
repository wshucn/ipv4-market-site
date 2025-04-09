<?php
/**
 * Team
 *
 * @package      CoreFunctionality
 * @author       Bill Erickson
 * @since        1.0.0
 * @license      GPL-2.0+
**/

class MP_Team {

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
			'name'               => 'Team Members',
			'singular_name'      => 'Team Member',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Team Member',
			'edit_item'          => 'Edit Team Member',
			'new_item'           => 'New Team Member',
			'view_item'          => 'View Team Member',
			'search_items'       => 'Search Team Members',
			'not_found'          => 'No Team Members found',
			'not_found_in_trash' => 'No Team Members found in Trash',
			'parent_item_colon'  => 'Parent Team Member:',
			'menu_name'          => 'Team Members',
			'featured_image'        => 'Image',
			'set_featured_image'    => 'Set image',
			'remove_featured_image' => 'Remove image',
			'use_featured_image'    => 'Use as image',
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array( 'slug' => 'team', 'with_front' => false ),
			'menu_icon'           => 'dashicons-groups'
		);

		register_post_type( 'team', $args );

	}
}
new MP_Team();
