<?php
/**
 * WooCommerce My Account
 * Client-specific
 *
 * @package woocommerce
 */

/**
 * Edit My Account menu items
 *
 * @link https://rudrastyh.com/woocommerce/my-account-menu.html
 */
add_filter( 'woocommerce_account_menu_items', 'mp_woocommerce_account_menu_items' );
function mp_woocommerce_account_menu_items( $menu_links ) {
	$menu_links = array(
		// 'dashboard'          => __( 'Dashboard', 'woocommerce' ),
		'orders'          => __( 'My Orders', 'woocommerce' ),
		'edit-account'    => __( 'Account Settings', 'woocommerce' ),
		// 'downloads'          => __( 'Download MP4s', 'woocommerce' ),
		'edit-address'    => __( 'Addresses', 'woocommerce' ),
		'payment-methods' => __( 'Payment Methods', 'woocommerce' ),
		// 'customer-logout'    => __( 'Logout', 'woocommerce' ),
	);

	return $menu_links;
}

