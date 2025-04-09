<?php
function slider_post() {
	$labels = array(
		'name'                  => _x( 'Slides', 'Slides', 'text_domain' ),
		'singular_name'         => _x( 'Slide', 'Slide', 'text_domain' ),
		'menu_name'             => __( 'Slider', 'text_domain' ),
		'name_admin_bar'        => __( 'Slider', 'text_domain' ),
		'archives'              => __( 'Slider Archives', 'text_domain' ),
		'attributes'            => __( 'Slider Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Slider Member:', 'text_domain' ),
		'all_items'             => __( 'All Slides', 'text_domain' ),
		'add_new_item'          => __( 'Add New Slide', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Slide', 'text_domain' ),
		'edit_item'             => __( 'Edit Slide', 'text_domain' ),
		'update_item'           => __( 'Update Slide', 'text_domain' ),
		'view_item'             => __( 'View Slide', 'text_domain' ),
		'view_items'            => __( 'View Slides', 'text_domain' ),
		'search_items'          => __( 'Search Slide', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		// 'featured_image'        => __( 'Featured Image', 'text_domain' ),
		// 'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		// 'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		// 'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into slide', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this slide', 'text_domain' ),
		'items_list'            => __( 'Slides list', 'text_domain' ),
		'items_list_navigation' => __( 'Slides list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter slides list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Slide', 'text_domain' ),
		'description'           => __( 'Slide Item', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'revisions', 'blocks' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_rest'          => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon' 			=> 'dashicons-slides',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);

	register_post_type( 'slider', $args );
}

add_action( 'init', 'slider_post', 0 );
?>
