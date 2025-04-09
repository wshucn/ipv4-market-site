<?php
/**
 * Order Item Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-item.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
	return;
}

$use_buttons_for_product_resources = true;
$product_resources_button_style    = 'uk-button-link';

?>


<?php
$product_id   = $item->get_product_id();
$product_name = $item->get_name();
$quantity     = $item->get_quantity();
$subtotal     = $item->get_subtotal();
$total        = $item->get_total();

$is_visible        = $product && $product->is_visible();
$product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
$sizes             = mp_sizes_attribute( 'medium', 2, 'uk-width-small' );
$thumbnail         = apply_filters(
	'woocommerce_order_item_thumbnail',
	$product->get_image( 'thumbnail', array( 'class' => 'uk-width-small uk-width-1-1@s' ) ),
	$item
);
// $thumbnail         = wp_get_attachment_image(
// get_post_thumbnail_id( $product_id ),
// 'full',
// false,
// array(
// 'sizes' => $sizes,
// 'class' => 'uk-width-small uk-width-1-1@s',
// )
// );

ob_start();
wc_display_item_meta( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
$item_data = ob_get_clean();

// Alter the HTML of $item_data
$item_data = mp_html_attrs_by_class(
	$item_data,
	'wc-item-meta-label',
	array(
		'class'     => 'uk-width-expand uk-leader',
		'uk-leader' => '',
	),
	false,
	'div'
);
$item_data = mp_html_attrs( $item_data, '//p', array(), true, 'div' );
$item_data = mp_html_attrs(
	$item_data,
	'//li',
	array(
		'class'   => 'uk-grid-column-small uk-flex-bottom uk-grid-row-collapse uk-grid',
		'uk-grid' => '',
	),
	true,
	'div'
);
$item_data = mp_html_attrs( $item_data, '//ul', array( 'class' => 'variation uk-text-small uk-margin-bottom' ), true, 'div' );

// pre($item_data);

// Data used for filtering/sorting
$mp_data = getACFs( $product_id, false, 'product_tag' );

// For each field, attach data- attributes to the product <li>.
$product_attrs = array();
foreach ( $mp_data as $key => $data ) {

	$attr                   = 'data-' . preg_replace( '/[^A-Za-z0-9\-]/', '-', $key );
	$product_attrs[ $attr ] = ' ' . join( ' ', array_column( $data, 'slug' ) ) . ' ';

}
?>

<?php
// <div>
echo buildAttributes( $product_attrs, 'div' );
	$cart_item_attrs          = array( 'uk-grid' );
	$cart_item_attrs['class'] = array(
		'uk-position-relative',
		'uk-card',
		'uk-card-default',
		'uk-grid',
		'uk-grid-collapse',
		'uk-text-left',
		esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ),
	);

	// <div>
	echo buildAttributes( $cart_item_attrs, 'div' )
	?>

		<?php if ( ! empty( $thumbnail ) ) : ?>
		<!-- Product Image -->
		<div class='uk-card-media-left uk-visible@s uk-width-small@s uk-padding-small uk-text-center'>
			<?php echo $thumbnail; ?>
		</div>
		<!-- / Product Image -->
		<?php endif; ?>

		<!-- Product Body -->
		<div class='uk-width-expand uk-card-body uk-padding-small'>
			<div class='uk-height-1-1 uk-flex uk-flex-column uk-flex-between'>
				<!-- Product Name/Data -->
				<div>
					<div class='uk-flex uk-flex-middle uk-flex-between'>
						<?php
						echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<h4 class="alt"><a class="uk-link-text" href="%s">%s</a></h4>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible ) );
						?>
					</div>

					<!-- Product Variation -->
					<div>
						<?php
							do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

							echo $item_data;

							do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
						?>
					</div>
					<!-- / Product Variation -->
				</div>
				<!-- / Product Name/Data -->

				<?php
				// Output links for downloads attached to this product
				$downloads = get_field( 'download', $product_id );
				if ( ! empty( $downloads ) ) {
					$downloads_object = get_field_object( 'download', $product_id );
					$downloads_title  = __( $downloads_object['label'], 'text_domain' );
					$downloads_items  = array();

					if ( ! empty( $use_buttons_for_product_resources ) && $use_buttons_for_product_resources === true ) {

						$downloads_item_wrapper_tag   = 'div';
						$downloads_item_wrapper_attrs = array( 'class' => 'uk-grid uk-margin' );
					} else {

						$downloads_item_wrapper_tag   = 'ul';
						$downloads_item_wrapper_attrs = array( 'class' => 'none uk-padding-remove uk-margin' );
					}

					$downloads_item_tag = $downloads_item_wrapper_tag === 'ul' ? 'li' : 'div';

					foreach ( $downloads as $download ) :
						if ( empty( $download['file'] ) && empty( $download['url'] ) ) {
							continue;
						}

						$a_attrs = array(
							'class' => array( 'has-icon' ),
						);
						if ( ! empty( $use_buttons_for_product_resources ) && $use_buttons_for_product_resources === true ) {
							$a_attrs['class'][] = 'uk-button';
							$a_attrs['class'][] = $product_resources_button_style;
						}


						$url_key = ! empty( $download['file'] ) ? 'file' : 'url';
						switch ( $url_key ) {
							case 'file':
								$action    = __( 'Download', 'text_domain' );
								$icon_name = 'document-outline';
								break;

							case 'url':
								$action            = __( 'Browse to', 'text_domain' );
								$a_attrs['rel']    = 'noopener';
								$a_attrs['target'] = '_blank';
								$icon_name         = 'open-outline';
								break;
						}

						$label = __( sanitize_text_field( $download['label'] ), 'text_domain' );

						$a_attrs['href']       = esc_url( $download[ $url_key ] );
						$a_attrs['aria-label'] = $action . ' ' . $label;

						$icon = buildAttributes( array( 'name' => $icon_name ), 'ion-icon', true );
						$span = buildAttributes( array(), 'span', $label );
						$a    = buildAttributes( $a_attrs, 'a', $icon . $span );

						// Use <li> for text links, <div> for button links.
						$downloads_items[] = buildAttributes( array(), $downloads_item_tag, $a );

					endforeach;

					if ( ! empty( $downloads_items ) ) :
						?>
					<div class='uk-text-meta'>
						<?php echo buildAttributes( $downloads_item_wrapper_attrs, $downloads_item_wrapper_tag, $downloads_items ); ?>
					</div>
						<?php
					endif;
				}
				?>

				<!-- Product Footer -->
				<div class='product-subtotal'>
					<hr class='uk-margin-small-bottom'>
					<div class='uk-flex uk-flex-between uk-text-bolder'>
						<!-- Product Quantity -->
						<div>
							<?php
								$qty          = $item->get_quantity();
								$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

							if ( $refunded_qty ) {
								$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
							} else {
								$qty_display = esc_html( $qty );
							}

								echo apply_filters( 'woocommerce_order_item_quantity_html', ' <span class="product-quantity">' . sprintf( '%s&nbsp;%s', $qty_display, _n( 'item', 'items', esc_html( $qty - ( $refunded_qty * -1 ) ), 'text_domain' ) ) . '</span>', $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>
						<!-- / Product Quantity -->
						<!-- Product Subtotal -->
						<div title='<?php esc_html_e( 'Subtotal', 'woocommerce' ); ?>'>
							<span class='uk-margin-small-right'><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
							<?php echo $order->get_formatted_line_subtotal( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<!-- / Product Subtotal -->
					</div>
				</div>
				<!-- / Product Footer -->
			</div>
		</div>
		<!-- / Product Body -->
	</div>
</div>

<?php if ( $show_purchase_note && $purchase_note ) : ?>

	<?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php endif; ?>
