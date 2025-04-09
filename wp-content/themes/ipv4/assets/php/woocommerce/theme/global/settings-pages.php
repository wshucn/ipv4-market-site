<?php
/**
 * WooCommerce Admin Settings
 *
 * @package woocommerce
 */

// Add a Shipping & Returns special Woocommerce page.
add_filter( 'woocommerce_settings_pages', 'mp_woocommerce_settings_pages', 10, 1 );
function mp_woocommerce_settings_pages( $settings ) {
	// Get the index of the end of the 'Advanced > Page setup' section. Default: 5
	$end_advanced_page_options = array_search(
		array(
			'id'   => 'advanced_page_options',
			'type' => 'sectionend',
		),
		$settings
	);
	$mp_woocommerce_pages      = array(
		array(
			'title'    => __( 'Shipping and Returns policy', 'woocommerce' ),
			/* Translators: %s Page contents. */
			'desc'     => __( 'You can define a "Shipping and Returns" page for use in themes.', 'woocommerce' ),
			'id'       => 'woocommerce_shipping_returns_page_id',
			'type'     => 'single_select_page_with_search',
			'default'  => '',
			'class'    => 'wc-page-search',
			'css'      => 'min-width:300px;',
			'args'     => array(
				'exclude' =>
					array(
						wc_get_page_id( 'cart' ),
						wc_get_page_id( 'checkout' ),
						wc_get_page_id( 'myaccount' ),
					),
			),
			'desc_tip' => true,
			'autoload' => false,
		),
	);

	array_splice( $settings, $end_advanced_page_options, 0, $mp_woocommerce_pages );
	return $settings;
}

