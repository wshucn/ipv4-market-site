<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

$needs_shipping_address = WC()->cart->needs_shipping_address();

// Remove Payment from Order Review.
remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

?>
<form name="checkout" method="post" class="uk-margin-bottom checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	<div class='uk-grid uk-grid-divider uk-grid-divider-vertical' uk-grid>
		<div class='uk-width-3-5@m uk-flex uk-flex-column uk-flex-gap'>
			<?php
			// If checkout registration is disabled and not logged in, the user cannot checkout.
			if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
				echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
				return;
			}
			?>

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div id='customer_details'>

				<?php do_action( 'woocommerce_checkout_billing' ); ?>

				<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				<?php endif; ?>

				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

			<div class='woocommerce-checkout-shipping'>
				<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

					<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

					<?php wc_cart_totals_shipping_html(); ?>

					<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

				<?php endif; ?>
			</div>
			<?php woocommerce_checkout_payment(); ?>
			<?php wc_get_template( 'checkout/policies.php' ); ?>

		</div>

		<aside class='woocommerce_checkout_order_review uk-width-2-5@m uk-flex-first uk-flex-last@m'>
			<div class='full-width-small'>

				<div class='padding-small'>
					<div class='uk-hidden@m uk-flex uk-flex-between'>
						<button id='order_review_toggle' class='uk-button uk-button-link has-icon' uk-toggle='target: .order_review_toggle; animation: uk-animation-fade' type='button'>
							<ion-icon class='uk-icon' name='cart'></ion-icon>
							<span class='uk-text-truncate'><?php esc_html_e( 'Show order summary', 'woocommerce' ); ?></span>
							<ion-icon class='uk-icon toggle-icon' name='chevron-down'></ion-icon>
						</button>
						<div class='woocommerce-checkout-order-summary-total order-total woocommerce-price-hide-currency'>
							<span role='heading' class='screen-reader-text'><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
							<span><?php wc_cart_totals_order_total_html(); ?></span>
						</div>
					</div>
				</div>

				<div hidden id='woocommerce_checkout_order_review' class='padding-small order_review_toggle'>
					<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

					<h3 class='screen-reader-text alt' id='order_review_heading'><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

					<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

					<div id='order_review' class='woocommerce-checkout-review-order'>
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>
			</div>
		</aside>
	</div>

</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
