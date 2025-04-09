<?php
/**
 * Testimonials
 *
 * @package      CoreFunctionality
 * @author       Bill Erickson
 * @since        1.0.0
 * @license      GPL-2.0+
**/

class MP_Testimonials {

	/**
	 * Initialize all the things
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		// Actions
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_filter( 'render_block', array( $this, 'extend_quote_block' ), 99, 2);
		// add_filter( 'wp_insert_post_data', array( $this, 'set_testimonial_title' ), 99, 2 );
	}

	/**
	 * Register the custom post type
	 *
	 * @since 1.2.0
	 */
	function register_cpt() {

		$labels = array(
			'name'               => 'Testimonials',
			'singular_name'      => 'Testimonial',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Testimonial',
			'edit_item'          => 'Edit Testimonial',
			'new_item'           => 'New Testimonial',
			'view_item'          => 'View Testimonial',
			'search_items'       => 'Search Testimonials',
			'not_found'          => 'No Testimonials found',
			'not_found_in_trash' => 'No Testimonials found in Trash',
			'parent_item_colon'  => 'Parent Testimonial:',
			'menu_name'          => 'Testimonials',
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
			'rewrite'             => array( 'slug' => 'testimonial', 'with_front' => false ),
			'menu_icon'           => 'dashicons-heart',
			'template'            => array( array( 'core/quote' ) ),
			'template_lock'       => 'all',
		);

		register_post_type( 'testimonial', $args );

	}


	/**
	 * Set testimonial title
	 *
	 */
	// function set_testimonial_title( $data, $postarr ) {
	// 	if( 'testimonial' == $data['post_type'] ) {
	// 		$title = $this->get_citation( $data['post_content'] );
	// 		if( empty( $title ) )
	// 			$title = 'Testimonial ' . $postarr['ID'];
	// 		$data['post_title'] = $title;
	// 	}

	// 	return $data;
	// }

	/**
	 * Get Citation
	 *
	 */
	// function get_citation( $content ) {
	// 	$matches = array();
	// 	$regex = '#<cite>(.*?)</cite>#';
	// 	preg_match_all( $regex, $content, $matches );
	// 	if( !empty( $matches ) && !empty( $matches[0] ) && !empty( $matches[0][0] ) )
	// 		return strip_tags( $matches[0][0] );
	// }

	function extend_quote_block($block_content, $block){
		if ( $block['blockName'] === 'core/quote' ){
			// $block_content = str_replace('<cite>', '<cite class="uk-text-bold">', $block_content);
			$block_content = preg_replace('#<cite>(.*?)</cite>#', '<footer><cite>\1</cite></footer>', $block_content);
		}
		return $block_content;
	}
}
new MP_Testimonials();
