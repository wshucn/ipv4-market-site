<?php
/**
 * WooCommerce Placeholder Image
 *
 * @package woocommerce
 */

// Disable the default WooCommerce product image placeholder, even if it has been set.
add_filter( 'option_woocommerce_placeholder_image', '__return_false' );


// Use a custom WooCommerce placeholder image.
add_filter( 'woocommerce_placeholder_img_src', 'mp_woocommerce_placeholder_img_src', 10, 1 );
function mp_woocommerce_placeholder_img_src( $src ) {
	$src = get_asset_url( 'images/placeholder.svg' );
	return $src;
}


// Remove 'width' and 'height' attributes from placeholder image if it is an SVG,
// because these muck up the size on the page.
add_filter( 'woocommerce_placeholder_img', 'mp_wc_placeholder_img', 10, 3 );
function mp_wc_placeholder_img( $image_html, $size, $dimensions ) {
	$image = wc_placeholder_img_src( $size );
	if ( ! empty( $image ) ) {
		$extension = pathinfo( wp_parse_url( $image, PHP_URL_PATH ), PATHINFO_EXTENSION );
		if ( 'svg' === $extension ) {
			$image_html = mp_html_attrs(
				$image_html,
				'//img',
				array(
					'style' => 'width: 100%; height: 100%; object-fit: contain; object-position: top',
				),
				array(
					'width'  => false,
					'height' => false,
				)
			);
		}
	}
	return $image_html;
}
