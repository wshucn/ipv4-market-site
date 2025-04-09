<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<h1 class='uk-h2 alt'><?php _e( 'Your shopping cart', 'woocommerce' ); //phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction ?></h1>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<!-- Cart Table/Grid -->
	<table class='shop_table cart woocommerce-cart-form__contents uk-table uk-table-responsive uk-table-justify uk-table-divider uk-table-middle'>
		<thead>
			<tr>
				<th class='product-thumbnail'><span class='screen-reader-text'><?php esc_html_e( 'Product Image', 'woocommerce' ); ?></span></th>
				<th class='product-name'><span class='screen-reader-text'><?php esc_html_e( 'Product', 'woocommerce' ); ?></span></th>
				<th class='product-price'><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th class='product-quantity uk-text-center'><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
				<th class='product-subtotal uk-text-right'><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody id='cart_contents'>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>
			<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) : ?>

				<?php
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
				$product_subtotal  = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );


				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :

					$product_row_quantity = ( $_product->is_sold_individually() ) ? 1 : $cart_item['quantity'];
					$product_row_price    = wc_format_decimal( $_product->get_price() );
					$product_row_subtotal = wc_format_decimal( $product_row_price * $product_row_quantity, 2, false );
					?>
			<tr
				class='uk-text-center uk-text-left@m uk-visible-toggle woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>'
				data-price='<?php echo esc_attr( $product_row_price ); ?>'
				data-subtotal='<?php echo esc_attr( $product_row_subtotal ); ?>'
				data-id='<?php echo esc_attr( $product_id ); ?>'
				data-name='<?php echo esc_attr( $product_name ); ?>'>

				<td class='product-image' data-title='<?php esc_attr_e( 'Product Image', 'woocommerce' ); ?>'>
					<?php
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_thumbnail',
						$_product->get_image( 'thumbnail', array( 'class' => 'uk-position-center' ) ),
						$cart_item,
						$cart_item_key
					);
					?>
				</td>
				<td class='product-name uk-text-bolder' data-title='<?php esc_attr_e( 'Product', 'woocommerce' ); ?>'>
					<?php
					if ( ! $product_permalink ) {
						echo wp_kses_post( $product_name );
					} else {
						$product_link = buildAttributes(
							array(
								'href'  => esc_url( $product_permalink ),
								'class' => 'uk-link-reset',
							),
							'a',
							$product_name
						);
						echo wp_kses_post( $product_link );
					}

					do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

					// Meta data.
					echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

					// Backorder notification.
					if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
						echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
					}
					?>

					<p>
					<?php
					$remove_item_attrs = array(
						'href'             => esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
						'aria-label'       => esc_html__( 'Remove this product', 'woocommerce' ),
						'data-product_id'  => esc_attr( $product_id ),
						'data-product_sku' => esc_attr( $_product->get_sku() ),
						'class'            => 'remove',
					);
					echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'woocommerce_cart_item_remove_link',
						buildAttributes( $remove_item_attrs, 'a', __( 'Remove', 'woocommerce' ) ),
						$cart_item_key
					);
					?>
					</p>

				</td>

				<td class='product-price' data-title='<?php esc_attr_e( 'Price', 'woocommerce' ); ?>'>
					<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok. ?>
				</td>
				<td class='product-quantity uk-text-center' data-title='<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>'>
					<?php
					if ( $_product->is_sold_individually() ) {
						$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
					} else {
						$product_quantity = woocommerce_quantity_input(
							array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $_product->get_max_purchase_quantity(),
								'min_value'    => '0',
								'product_name' => $product_name,
							),
							$_product,
							false
						);
					}

					echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
					?>
				</td>
				<td class='product-total uk-text-bolder uk-text-right@m' data-title='<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>'>
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>

			</tr>
				<?php endif; ?>
			<?php endforeach; ?>

		<?php do_action( 'woocommerce_cart_contents' ); ?>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>

	</table>
	<!-- / Cart Table/Grid -->

	<?php do_action( 'woocommerce_after_cart_table' ); ?>

	<div class='uk-grid uk-grid-divider' uk-grid>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

		<div class='uk-width-3-5@m'>

			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

		</div>

		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

			<?php woocommerce_shipping_calculator(); ?>

		<?php endif; ?>

		<div class='uk-width-expand@m'>

		<?php woocommerce_cart_totals(); ?>

		</div>

	</div>


	<div class='uk-margin-medium-top'>

		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

		<div class='cart-collaterals uk-margin-medium-bottom'>
			<?php
				/**
				 * Cart collaterals hook.
				 *
				 * @hooked woocommerce_cross_sell_display
				 * @hooked woocommerce_cart_totals - 10
				 */
				remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
				do_action( 'woocommerce_cart_collaterals' );
			?>
		</div>

	</div>


</form>

<?php
do_action( 'woocommerce_after_cart' );
