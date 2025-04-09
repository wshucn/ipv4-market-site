<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h3 class='screen-reader-text'><?php esc_html_e( 'Cart totals', 'woocommerce' ); ?></h3>

	<?php if ( wc_coupons_enabled() ) : ?>
		<!-- <hr role='separator'> -->
		<section class='coupon' id='apply-coupon-form' data-validate-required='[name="coupon_code"]'>
			<div class='uk-grid uk-grid-small uk-flex-middle' uk-grid>
				<div class='form-row uk-width-expand uk-position-relative'>
					<label class='uk-form-label' for='coupon_code'><?php esc_html_e( 'Gift card or coupon code', 'woocommerce' ); ?></label>
					<span class='woocommerce-input-wrapper uk-display-block uk-form-controls'>
						<input type='text' data-lpignore='true' name='coupon_code' class='uk-input input-text' value='' placeholder='<?php esc_attr_e( 'Gift card or discount code', 'woocommerce' ); ?>' />
					</span>
				</div>
				<div class='form-row uk-width-auto uk-position-relative uk-flex'>
					<button disabled aria-disabled='true' type='submit' class='has-icon uk-button uk-button-secondary uk-button-responsive button' name='apply_coupon' value='<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>'>
						<span class='uk-visible@s'><?php esc_attr_e( 'Apply', 'woocommerce' ); ?></span>
						<?php echo get_icon( 'arrow-forward', array( 'class' => 'uk-hidden@s uk-margin-remove' ), 'large' ); ?>
					</button>
				</div>
			</div>
			<?php do_action( 'woocommerce_cart_coupon' ); ?>
		</section>


	<?php endif; ?>

	<hr role='separator'>

	<div role='table' class='shop_table shop_table_responsive'>

		<div role='rowgroup'>
			<!-- SUBTOTAL -->
			<div role='row' class='cart-subtotal'>
				<span role='rowheader' class='uk-width-expand uk-flex uk-flex-middle'>
					<?php esc_html_e( 'Subtotal', 'woocommerce' ); ?>
					<button uk-tooltip='<?php esc_html_e( 'Update cart', 'woocommerce' ); ?>' type='submit' style='border: 0' class='uk-button-text has-icon button' name='update_cart' value='<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>'>
						<ion-icon name='refresh' class='uk-icon'></ion-icon>
						<span class='screen-reader-text'><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></span>
					</button>
				</span>
				<span role='cell' class='uk-text-right uk-text-bolder' data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></span>
			</div>
			<!-- /SUBTOTAL -->

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div role='row' class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span role='rowheader' class='uk-width-expand'><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span role='cell' class='uk-text-right uk-text-bolder' data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php // BEGIN EDIT SHIPPING TOTALS/CALCULATOR ?>
		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php mp_wc_cart_totals_shipping_html(); ?>

		<?php endif; ?>
		<?php // END EDIT SHIPPING TOTALS/CALCULATOR ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div role='row' class="fee">
				<span role='rowheader' class='uk-width-expand'><?php echo esc_html( $fee->name ); ?></span>
				<span role='cell' class='uk-text-right uk-text-bolder' data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				/* translators: %s location. */
				$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					?>
					<div role='row' class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span role='rowheader' class='uk-width-expand'><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span role='cell' class='uk-text-right uk-text-bolder' data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
					<?php
				}
			} else {
				?>
				<div role='row' class="tax-total">
					<span role='rowheader' class='uk-width-expand'><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span role='cell' class='uk-text-right uk-text-bolder' data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
				<?php
			}
		}
		?>
		</div>

		<hr role='separator'>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<div role='rowgroup'>
			<div role='row' class="order-total">
				<span role='rowheader' class='uk-width-expand' style='font-size: 125%'>
					<?php esc_html_e( 'Total', 'woocommerce' ); ?>
				</span>
				<span role='cell' class='uk-text-right uk-text-bolder' data-title='<?php esc_attr_e( 'Total', 'woocommerce' ); ?>'><?php wc_cart_totals_order_total_html(); ?></span>
			</div>
		</div>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</div>

	<?php do_action( 'woocommerce_cart_actions' ); ?>

	<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
