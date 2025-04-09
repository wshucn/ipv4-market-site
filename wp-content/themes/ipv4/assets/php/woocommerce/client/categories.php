<?php
/**
 * WooCommerce Category-related
 * Specific to Client
 *
 * @package woocommerce
 */

// Add 'Product Categories' to the main and mobile menus.
add_filter( 'wp_nav_menu', 'mp_nav_menu_product_cat', 10, 2 );
function mp_nav_menu_product_cat( $nav_menu, $args ) {
	if ( in_array( $args->theme_location, array( 'primary', 'mobile' ) ) ) {
		$product_categories_list = wp_list_categories(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'echo'       => false,
				'title_li'   => '',
			)
		);
		$product_categories_list = str_replace( 'current-cat', 'uk-active', $product_categories_list );
		$product_categories_li   = mp_buildDrop( __( 'Shop', 'text-domain' ), $product_categories_list, $args );
		$product_categories_li   = mp_html_attrs( $product_categories_li, '//html/body/li/a', array( 'href' => get_permalink( wc_get_page_id( 'shop' ) ) ), true );
		$nav_menu                = mp_move_element( $nav_menu, $product_categories_li, '/ul[@id]', 'firstChild' );
	}
	return $nav_menu;
}
