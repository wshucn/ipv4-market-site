<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.6.0
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class='woocommerce-customer-details'>

	<?php if ( $show_shipping ) : ?>
	<div class='uk-grid woocommerce-columns--addresses addresses' uk-grid>
	<?php endif; ?>

	<div class='uk-width-1-2@m woocommerce-column--billing-address'><div class='uk-card uk-card-small uk-card-default'>
		<div class='uk-card-body'>
			<h4 class='alt'><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h4>
			<address class='uk-margin-remove'>
				<?php echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
				<?php if ( $order->get_billing_phone() || $order->get_billing_email() ) : ?>

					<hr>

					<div class='uk-flex uk-flex-column uk-flex-gap-small'>
					<?php if ( $order->get_billing_phone() ) : ?>
						<div title='<?php _e('Billing phone number', 'text_domain') ?>' class='has-icon woocommerce-customer-details--phone'>
							<ion-icon name='call-outline'></ion-icon>
							<?php echo esc_html( $order->get_billing_phone() ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $order->get_billing_email() ) : ?>
						<div title='<?php _e('Billing email', 'text_domain') ?>' class='has-icon woocommerce-customer-details--email'>
							<ion-icon name='at-outline'></ion-icon>
							<?php echo esc_html( $order->get_billing_email() ); ?>
						</div>
					<?php endif; ?>
					</div>
				<?php endif; ?>
			</address>
		</div>
	</div></div>

	<?php if ( $show_shipping ) : ?>
	<div class='uk-width-1-2@m woocommerce-column--shipping-address'><div class='uk-card uk-card-small uk-card-default'>
		<div class='uk-card-body'>
			<h4 class='uk-text-normal'><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h4>
			<address class='uk-margin-remove'>
			<?php echo wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
			<div class='uk-flex uk-flex-column uk-flex-gap-small'>
			<?php if ( $order->get_shipping_phone() ) : ?>
				<div title='<?php _e('Shipping phone number', 'text_domain') ?>' class='has-icon woocommerce-customer-details--phone'>
					<ion-icon role='presentation' name='call-outline'></ion-icon>
					<?php echo esc_html( $order->get_shipping_phone() ); ?>
				</div>
			<?php endif; ?>
			</div>
			</address>
		</div>
	</div></div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
