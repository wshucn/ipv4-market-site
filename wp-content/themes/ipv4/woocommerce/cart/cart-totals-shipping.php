<?php
/**
 * Shipping Totals Display
 *
 * Not strictly a WooCommerce template. We're displaying only the shipping line item and total,
 * but no shipping methods. (We display the shipping methods table in another part of the page.)
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination    = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
$has_calculated_shipping  = ! empty( $has_calculated_shipping );
$show_shipping_calculator = ! empty( $show_shipping_calculator );
$calculator_text          = '';
$package_label            = apply_filters( 'woocommerce_shipping_package_label', ( $available_methods ) ? $available_methods[ $chosen_method ]->label : '', $index, $package );
?>
<div role='row' class='woocommerce-shipping-totals shipping'>
	<span role='rowheader' class='uk-width-expand'>
		<?php
		if ( ! empty( $package_label ) ) {
			echo wp_kses_post( sprintf( '%s: <span class="uk-text-muted">%s</span>', $package_name, $package_label ) );
		} else {
			echo wp_kses_post( $package_name );
		}
		?>
	</span>
	<span role='cell' class='uk-text-right uk-text-bolder' data-title="<?php echo wp_kses_post( $package_name ); ?>">
		<?php
		if ( $available_methods ) :
			// if ( 1 < count( $available_methods ) ) {
			if ( empty( $chosen_method ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_not_chosen_html', __( 'Choose a method', 'woocommerce' ) ) );
			} else {
				echo wp_kses_post( mp_wc_cart_totals_shipping_method_cost( $available_methods[ $chosen_method ] ) );
			}
			// } else {
			// echo wp_kses_post( mp_wc_cart_totals_shipping_method_cost( $available_methods[0] ) );
			// }

		elseif ( ! $has_calculated_shipping || ! $formatted_destination ) :
			if ( is_cart() && 'no' === get_option( 'woocommerce_enable_shipping_calc' ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_not_enabled_on_cart_html', __( 'Calculated at next step', 'woocommerce' ) ) );
			} else {
				echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Waiting for address', 'woocommerce' ) ) );
			}
		elseif ( ! is_cart() && WC()->cart->show_shipping() ) :
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'Cannot ship to your address', 'woocommerce' ) ) );
		else :
			echo wp_kses_post( apply_filters( 'woocommerce_no_shipping_available_html', __( 'Cannot ship to your address', 'woocommerce' ) ) );
		endif;
		?>
	</span>
</div>
