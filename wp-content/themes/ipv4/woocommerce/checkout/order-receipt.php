<?php
/**
 * Checkout Order Receipt Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/order-receipt.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class='uk-margin-bottom order_details'>
	<ul class='uk-grid' uk-grid>
		<li class='uk-h4 uk-margin-remove date'>
			<span hidden><?php esc_html_e( 'Date:', 'woocommerce' ); ?></span>
			<span><time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></span>
		</li>
		<li class='uk-h4 uk-text-normal total'>
			<span hidden><?php esc_html_e( 'Total:', 'woocommerce' ); ?></span>
			<span uk-tooltip='title: <?= wp_kses_post( sprintf( '%s&nbsp;%s', $item_count, _n('item', 'items', esc_html( $item_count ), 'text_domain') ) ); ?>; pos: right; delay: 500'><?= $order->get_formatted_order_total() ?></span>
		</li>
	</ul>
	<ul class='uk-margin-small-top uk-grid uk-grid-small uk-grid-divider uk-text-meta' uk-grid>
		<li class="order">
			<span><?php esc_html_e( 'Order number:', 'woocommerce' ); ?></span>
			<span class='uk-text-bolder'><?php echo esc_html($order->get_order_number()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</li>

		<?php if ( $order->get_payment_method_title() ) : ?>
		<li class="method">
			<span><?php esc_html_e( 'Payment method:', 'woocommerce' ); ?></span>
			<span class='uk-text-bolder'><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
		</li>
		<?php endif; ?>
	</ul>
</div>

<?php do_action( 'woocommerce_receipt_' . $order->get_payment_method(), $order->get_id() ); ?>
