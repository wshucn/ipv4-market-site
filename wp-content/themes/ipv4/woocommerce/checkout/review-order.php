<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class='shop_table woocommerce-checkout-review-order-table'>
	<section role='table'>
		<div role='rowgroup'>
			<div role='row' class='screen-reader-text'>
				<span role='columnheader' aria-sort='none' class='product-thumbnail'><?php esc_html_e( 'Product Image', 'woocommerce' ); ?></span>
				<span role='columnheader' aria-sort='none' class='product-name'><?php esc_html_e( 'Product', 'woocommerce' ); ?></span>
				<span role='columnheader' aria-sort='none' class='product-quantity'><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></span>
				<span role='columnheader' aria-sort='none' class='product-total'><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
			</div>
		</div>
		<?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>
		<div role='rowgroup'>
		<?php
		// List of Order Products.
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			// Item Data, including Product Add-ons variation data.
			$cart_item_data = wc_get_formatted_cart_item_data( $cart_item );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<div role='row' style='line-height: normal' class='<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'uk-flex cart_item', $cart_item, $cart_item_key ) ); ?>'>
					<span role='cell' class='product-thumbnail uk-inline'>
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_checkout_cart_item_thumbnail',
							$_product->get_image( 'thumbnail', array( 'class' => 'uk-position-center' ) ),
							$cart_item,
							$cart_item_key
						);
						?>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <span aria-hidden="true" class="product-quantity uk-badge uk-badge-small uk-badge-form uk-position-top-right">' . $cart_item['quantity'] . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span role='rowheader' class='uk-flex-1 product-name uk-text-bolder'>
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span role='cell' class='product-quantity screen-reader-text'>
						<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', sprintf( '<span aria-hidden="true">&times;</span>%s', $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span role='cell' class='product-total uk-text-bolder uk-text-right'>
						<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				</div>
				<?php
			}
		}
		?>
		</div>
	<?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>
	</section>

	<?php woocommerce_checkout_coupon_form(); ?>

	<hr role='separator'>

	<div role='table'>
		<div role='rowgroup'>
			<!-- SUBTOTAL -->
			<div role='row' class='cart-subtotal'>
				<span role='rowheader' class='uk-width-expand'><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
				<span role='cell' class='uk-text-right uk-text-bolder'><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>
			<!-- /SUBTOTAL -->
			<!-- COUPONS -->
			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div role='row' class='cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>'>
				<span role='rowheader' class='uk-width-expand'><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span role='cell' class='uk-text-right uk-text-bolder'><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
			<?php endforeach; ?>
			<!-- /COUPONS -->
			<!-- SHIPPING -->

				<?php mp_wc_cart_totals_shipping_html(); ?>

			<!-- /SHIPPING -->
			<!-- FEES -->
			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div role='row' class='fee'>
				<span role='rowheader' class='uk-width-expand'><?php echo esc_html( $fee->name ); ?></span>
				<span role='cell' class='uk-text-right uk-text-bolder'><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
			<?php endforeach; ?>
			<!-- /FEES -->
			<!-- TAXES -->
			<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
				<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
					<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
						<div role='row' class='tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>'>
							<span role='rowheader' class='uk-width-expand'><?php echo esc_html( $tax->label ); ?></span>
							<span role='cell' class='uk-text-right uk-text-bolder'><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div role='row' class='tax-total'>
						<span role='rowheader' class='uk-width-expand'><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
						<span role='cell' class='uk-text-right uk-text-bolder'><?php wc_cart_totals_taxes_total_html(); ?></span>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<!-- /TAXES -->
		</div>

		<hr role='separator'>

		<div role='rowgroup'>
			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
			<div role='row' class='order-total'>
				<span role='rowheader' class='uk-width-expand' style='font-size: 125%'><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
				<span role='cell' class='uk-text-right uk-text-bolder'><?php wc_cart_totals_order_total_html(); ?></span>
			</div>
			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
		</div>
	</table>
</div>
