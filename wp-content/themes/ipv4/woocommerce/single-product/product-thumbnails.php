<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$attachment_ids = _proper_get_product_image_ids( $product );
$columns        = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );

$nav_attrs = apply_filters(
	'woocommerce_single_product_image_gallery_navigation_attrs',
	array(
		'class' => array(
			// 'uk-dotnav',
			'uk-thumbnav uk-grid-small uk-child-width-1-' . absint( $columns ),
			'uk-slidenav',
			'uk-margin-top',
		),
		'uk-grid',
	)
);

if ( ! empty( $attachment_ids ) ) {
	$html = '';

	// Get the HTML markup (wc_get_gallery_image_html), filtered through 'woocommerce_single_product_image_thumbnail_html', for each image.
	foreach ( $attachment_ids as $attachment_id ) {
		$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id ), $attachment_id );
	}

	// Change the wrappers to <li> tags.
	foreach ( $attachment_ids as $i => $attachment_id ) {
		$html = mp_html_attrs( $html, "//*[contains(@class, 'woocommerce-product-gallery__image')][" . ( $i + 1 ) . ']', array( 'uk-slideshow-item' => $i ), true, 'li' );
	}

	// Alter the image links for slider items.
	$html = mp_html_attrs( $html, '//li[@uk-slideshow-item]/a', array( 'href' => '#' ), true );
	?>
	<ul <?php echo buildAttributes( $nav_attrs ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?>>
		<?php echo wp_kses_post( $html ); ?>
	</ul>
	<?php
}
