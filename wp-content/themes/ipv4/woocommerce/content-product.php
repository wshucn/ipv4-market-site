<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$li_attrs = array(
	'class' => wc_get_product_class( 'uk-text-center', $product ),
	//
	// 'data_flow_rate' => "0.00",
);

global $woocommerce_loop;

if ( 'related' !== $woocommerce_loop['name'] ) {
	// add data-* attributes to the <li> element for each piece of additional information
	// these attributes can be used with uk-filter for JS sorting & filtering

	$product_attributes_field_name = apply_filters( 'product_attributes_field_name', 'product_attributes' );
	$product_attributes_field      = get_field( $product_attributes_field_name );

	if ( is_array( $product_attributes_field ) && ! empty( array_filter( $product_attributes_field ) ) ) {
		foreach ( array_filter( $product_attributes_field ) as $data_key => $data_value ) {
			if ( empty( $data_value ) || is_array( $data_value ) ) {
				$data_value = 0;
			}
			$data_value                                  = is_numeric( $data_value ) ? number_format( $data_value, 2 ) : $data_value;
			$li_attrs[ 'data_' . esc_html( $data_key ) ] = esc_html( $data_value );
		}
	}

	// data-* attributes for sorting. Sorting will fail unless ALL products have these attributes set!
	// Each attribute here needs to have a default fallback!!

	// Allow sorting by price.
	$li_attrs['data_price'] = ( $product->get_price() ) ? $product->get_price() : '0.00';

	// Allow sorting by date.
	$li_attrs['data_date'] = ( get_the_date( 'Y-m-d H:i:s', $product->get_id() ) ) ? get_the_date( 'Y-m-d H:i:s', $product->get_id() ) : gmdate( 'Y-m-d H:i:s' );

	// Allow sorting by menu order.
	$li_attrs['data_order'] = ( $product->get_menu_order() ) ? $product->get_menu_order() : 10;
}

// product element class. use a small card when in the related products loop
$card_class[] = 'uk-flex uk-flex-column uk-card uk-card-default uk-card-small uk-padding-small';


// Cross-Sells
if ( is_cart() ) {
	$card_class[] = 'uk-card-small uk-padding-small';
}


// <li>
echo buildAttributes( $li_attrs, 'li' );
?>
<div class='<?php echo buildClass( $card_class ); ?>'>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	// do_action( 'woocommerce_before_shop_loop_item' );
	// woocommerce_template_loop_product_link_open():
	$link                      = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
	$link_attrs                = array(
		'href'  => esc_url( $link ),
		'class' => array( 'uk-link-reset woocommerce-LoopProduct-link woocommerce-loop-product__link' ),
	);
		$link_attrs['class'][] = 'uk-flex-1';
	// <a>
	echo buildAttributes( $link_attrs, 'a' );
	?>

	<div class='uk-card-media-top uk-overflow-hidden uk-flex uk-flex-center uk-flex-middle'>
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
		do_action( 'woocommerce_before_shop_loop_item_title' );

		$loop_columns = esc_attr( wc_get_loop_prop( 'columns' ) );

		$sizes = mp_sizes_attribute( 'medium', $loop_columns );
		echo $product->get_image(
			'full',
			array(
				'sizes' => $sizes,
				'class' => '',
			)
		);
		?>

	</div>
	<div class='uk-card-body'>
		<?php
		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		// do_action( 'woocommerce_shop_loop_item_title' );
		echo '<div class="uk-h4 alt woocommerce-loop-product__title">' . get_the_title() . '</div>';

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		// if (is_product() && $woocommerce_loop['name'] == 'related') {
		// add_action('woocommerce_after_shop_loop_item_title', 'mp_loop_product_excerpt', 9);
		// }

		do_action( 'woocommerce_after_shop_loop_item_title' );
		?>
	</div>
	</a>
	<div class='uk-card-footer'>
	<div class='uk-margin-small-bottom'>
	<?php
	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_close - 5
	 * @hooked woocommerce_template_loop_add_to_cart - 10
	 */

	woocommerce_template_loop_price();
	?>
	</div>
	<?php
	$add_to_cart_args = array(
		'class' => 'uk-width-1-1 uk-width-auto@s uk-button uk-button-secondary uk-text-nowrap',
	);
	if ( is_product_category() || is_shop() ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	do_action( 'woocommerce_after_shop_loop_item', $add_to_cart_args );
	?>
	</div>
</div>
</li>
