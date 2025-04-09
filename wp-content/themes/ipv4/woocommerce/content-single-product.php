<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<article id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<?php
	// if ( !$product->get_meta('_product_addons') )
	// woocommerce_template_single_price();
	?>
	<div class='uk-background-white uk-padding-large uk-padding-remove-horizontal uk-padding-remove-bottom'>
		<?php get_template_part( 'partials/main', 'container' ); ?>
		<div class='uk-grid uk-grid-large uk-margin-medium-bottom' uk-grid>
			<div class='uk-width-2-5@m'>
				<?php
					/**
					 * Hook: woocommerce_before_single_product_summary.
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
					do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div>

			<?php
				// Output the tabs: description and reviews
				// woocommerce_output_product_data_tabs();
			?>
			<div class="uk-width-expand summary entry-summary">
				<?php
					/**
					 * Hook: woocommerce_single_product_summary.
					 *
					 * @hooked woocommerce_template_single_title - 5
					 * @hooked woocommerce_template_single_rating - 10
					 * @hooked woocommerce_template_single_price - 10
					 * @hooked woocommerce_template_single_excerpt - 20
					 * @hooked woocommerce_template_single_add_to_cart - 30
					 * @hooked woocommerce_template_single_meta - 40
					 * @hooked woocommerce_template_single_sharing - 50
					 * @hooked WC_Structured_Data::generate_product_data() - 60
					 */
					// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
					// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

					add_action( 'woocommerce_single_product_summary', 'mp_wc_template_single_category', 4 );
					add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 5 );
					add_action( 'woocommerce_single_product_summary', 'mp_wc_template_single_sku', 8 );
					add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 9 );

					// Product Add-Ons output here
					add_action( 'woocommerce_single_product_summary', 'mp_prepend_page_content', 1 );
					do_action( 'woocommerce_single_product_summary' );
				?>

				<!-- <hr class='uk-divider-icon uk-divider-muted-dark uk-margin-top uk-margin-bottom uk-margin-auto'> -->

				<?php
					/**
					 * Hook: woocommerce_after_single_product_summary.
					 *
					 * @hooked woocommerce_output_product_data_tabs - 10
					 * @hooked woocommerce_upsell_display - 15
					 * @hooked woocommerce_output_related_products - 20
					 */
					// remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
					remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
					do_action( 'woocommerce_after_single_product_summary' );
				?>

			</div>

		</div>
	</div><!-- / container -->
	<?php
	ob_start();
	woocommerce_output_related_products();
	$woocommerce_output_related_products = ob_get_clean();

	if ( ! empty( $woocommerce_output_related_products ) ) :
		?>
	</div>

	<div class='uk-background-muted-lighter'>
		<?php echo $woocommerce_output_related_products; ?>

		<?php
	endif;
	?>
		<?php
		get_template_part( 'partials/main', 'container' );
		get_template_part( 'partials/main', 'breadcrumb' );
		echo '</div>';
		?>
	</div>

</article>

<?php
do_action( 'woocommerce_after_single_product' );
