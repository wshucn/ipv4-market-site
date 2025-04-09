<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php

// Buttons to link to My Account and back to the shop

ob_start();
?>
<div class='uk-grid uk-flex-center' uk-margin uk-grid>
	<?php if ( is_user_logged_in() ) : ?>
		<div>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="uk-button uk-button-default button"><?php esc_html_e( 'View your account', 'woocommerce' ); ?></a>
		</div>
	<?php endif; ?>
	<div>
		<a class="uk-button uk-button-default woocommerce-Button button" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Return to Catalog', 'text_domain' ); ?></a>
	</div>
</div>
<?php $success_actions = ob_get_clean(); ?>


<div class="woocommerce-order">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<h1 class='uk-h2 alt uk-text-center'><?php _e('We\'re sorry', 'woocommerce'); ?></h1>
			<p class="uk-text-center woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed">
				<?php esc_html_e( 'Unfortunately, your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?>
			</p>

			<div class="uk-grid uk-flex-center woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions" uk-margin uk-grid>
				<div><a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="uk-button uk-button-default button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a></div>
				<?php if ( is_user_logged_in() ) : ?>
					<div><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="uk-button uk-button-default button"><?php esc_html_e( 'View your account', 'woocommerce' ); ?></a></div>
				<?php endif; ?>
			</div>

		<?php else : ?>
			<h1 class='uk-h2 alt uk-text-center'><?php _e('Thank you', 'woocommerce'); ?></h1>
			<p class="uk-text-center woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php echo $success_actions; ?>

		<?php endif; ?>

		<hr class='uk-divider-icon uk-margin-large'>

		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<h1 class='uk-h2 alt uk-text-center'><?php _e('Thank you', 'woocommerce'); ?></h1>
		<p class="uk-text-center woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		<?php echo $success_actions; ?>

	<?php endif; ?>

</div>
