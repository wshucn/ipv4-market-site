<?php
/**
 * WooCommerce Product Variations
 * Client-specific
 *
 * @package woocommerce
 */

// The swatch img, $attr param for wp_get_attachment_image.
add_filter(
	'mp_wc_variation_swatch_img_attr',
	fn( $attr) => array_merge(
		$attr,
		array(
			'class' => 'uk-flex-none',
		)
	)
);

// The swatch li attributes.
add_filter(
	'mp_wc_variation_swatch_li_attr',
	fn( $attr) => array_merge(
		$attr,
		array(
			'class' => array( 'uk-flex-none' ), // all the same width
			// 'style'			=> ['flex-basis: 150px'], // maximum width
		)
	)
);

// The swatch a attributes.
add_filter(
	'mp_wc_variation_swatch_a_attr',
	fn( $attr) => array_merge(
		$attr,
		array(
			'class' => array( 'uk-text-center uk-link-reset' ),
			// 'uk-tooltip'    => "title: {$option_name}; pos: bottom",
		)
	)
);
