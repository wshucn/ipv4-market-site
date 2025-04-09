<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();
$item_count				= $order->get_item_count() - $order->get_item_count_refunded();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="sr-only uk-text-center alt woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<div class='woocommerce-table woocommerce-table--order-details shop_table order_details'>

		<!-- Order Details Header/Tabs -->
		<div class='uk-grid uk-margin-remove-bottom uk-flex-between uk-flex-bottom' uk-grid>

			<!-- Order Details Data -->
			<div>
				<ul class='uk-grid uk-flex-middle' uk-grid>
					<li class='uk-text-large uk-margin-remove woocommerce-order-overview__date date'>
						<span hidden><?php esc_html_e( 'Date:', 'woocommerce' ); ?></span>
						<span><time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></span>
					</li>
					<?php if( is_user_logged_in() && is_account_page() ): ?>
					<li class='woocommerce-order-overview__status status'>
						<span hidden><?php esc_html_e( 'Status:', 'woocommerce' ); ?></span>
						<span class='uk-badge uk-text-nowrap'><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
					</li>
					<?php endif; ?>
					<li class='uk-text-large uk-text-normal woocommerce-order-overview__total total'>
						<span><?php esc_html_e( 'Total:', 'woocommerce' ); ?></span>
						<span uk-tooltip='title: <?= wp_kses_post( sprintf( '%s&nbsp;%s', $item_count, _n('item', 'items', esc_html( $item_count ), 'text_domain') ) ); ?>; pos: right; delay: 500'><?= $order->get_formatted_order_total() ?></span>
					</li>
				</ul>
				<ul class='uk-margin-small-top uk-grid uk-grid-small uk-grid-row-collapse uk-text-small' uk-grid>
					<li class="woocommerce-order-overview__order order">
						<span><?php esc_html_e( 'Order number:', 'woocommerce' ); ?></span>
						<span class='uk-text-bolder'><?php echo esc_html($order->get_order_number()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</li>

					<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<span><?php esc_html_e( 'Email:', 'woocommerce' ); ?></span>
						<span class='uk-text-bolder'><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</li>
					<?php endif; ?>

					<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<span><?php esc_html_e( 'Payment method:', 'woocommerce' ); ?></span>
						<span class='uk-text-bolder'><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
					</li>
					<?php endif; ?>
				</ul>
			</div>
			<!-- / Order Details Data -->

			<!-- Order Details Tabs -->
			<div>
				<ul class='uk-subnav uk-subnav-pill' uk-switcher='connect: #switcher-<?= $order->get_ID() ?>; animation: uk-animation-fade'>
					<li class='uk-active'><a href="#" class='has-icon'><ion-icon name='bag-check'></ion-icon><?php _e('Items purchased', 'woocommerce'); ?></a></li>
					<li><a href="#" class='has-icon'><ion-icon name='person-circle'></ion-icon><?php _e('Customer Details', 'woocommerce'); ?></a></li>
				</ul>
			</div>
			<!-- / Order Details Tabs -->
		</div>
		<!-- / Order Details Header/Tabs -->

		<hr class='uk-margin-small-top' style='border-style: dashed'>


		<ul id='switcher-<?= $order->get_ID() ?>' class='uk-switcher'>

			<li>

			<?php do_action( 'woocommerce_order_details_before_order_table_items', $order ); ?>

				<div class='uk-grid uk-grid-small uk-child-width-1-1' uk-grid>

					<?php
					$order_details_items = array();
					// Loop over the items, store in array to enable initial sorting
					foreach ( $order_items as $item_id => $item ) {

						$product = $item->get_product();
						$product_id = $product->get_ID();
						$sort = $item_id;

						ob_start();
						try {
							wc_get_template(
								'order/order-details-item.php',
								array(
									'order'              => $order,
									'item_id'            => $item_id,
									'item'               => $item,
									'show_purchase_note' => $show_purchase_note,
									'purchase_note'      => $product ? $product->get_purchase_note() : '',
									'product'            => $product,
								)
							);

						$order_details_items[$sort] = ob_get_contents();

						} finally {
							ob_end_clean();
						}
					}

					// Sort the items by key ($sort) ascending
					// ksort($order_details_items);

					// Output the items
					foreach( $order_details_items as $order_detail_item ) {
						echo wp_kses_post( $order_detail_item );
					}

					?>

				</div>

			<?php do_action( 'woocommerce_order_details_after_order_table_items', $order ); ?>

			</li>
			<li>

				<?php
				if ( $show_customer_details ) {
					wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
				}
				?>

			</li>

		</ul>

	</div>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

