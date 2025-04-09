<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

// Get IDs for featured, gallery, and varition images, filtered for duplicates.
$attachment_ids = _proper_get_product_image_ids( $product );

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		if ( ! empty( $attachment_ids ) ) {

			$html = '';

			// Get the HTML markup (wc_get_gallery_image_html), filtered through 'woocommerce_single_product_image_thumbnail_html', for each image.
			foreach ( $attachment_ids as $attachment_id ) {
				$thumbnail_html = wc_get_gallery_image_html( $attachment_id );
				if ( $thumbnail_html ) {
					$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', $thumbnail_html, $attachment_id );
				}
			}

			// Change the wrappers to <li> tags.
			$html = mp_html_attrs( $html, '.woocommerce-product-gallery__image', array(), true, 'li' );

			// Get the parameters for the slideshow element.
			$slider_params = apply_filters( 'proper_gallery_image_html_slider_params', array( 'finite: true' ), $attachment_ids );
			?>
			<div <?php echo buildAttributes( array( 'uk-slideshow' => $slider_params ) ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped ?>>
				<ul class='uk-slideshow-items'>
				<?php echo wp_kses_post( $html ); ?>
				</ul>
				<?php
				// Thumbnail navigation needs to be within the slider element.
				do_action( 'woocommerce_product_thumbnails' );
				?>
			</div>
			<?php
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			do_action( 'woocommerce_product_thumbnails' );
		}

		?>
	</figure>
</div>
