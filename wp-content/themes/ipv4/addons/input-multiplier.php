<?php
/**
 * The Template for displaying quantity/input multiplier field.
 *
 * @version 3.0.27
 */

$field_name    = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key     = 'addon-' . sanitize_title( $field_name );
$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ] ) ? wc_clean( $_POST[ $addon_key ] ) : '';
$adjust_price  = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price         = ! empty( $addon['price'] ) ? $addon['price'] : '';
$price_raw     = apply_filters( 'woocommerce_product_addons_price_raw', '1' == $adjust_price && $price ? $price : '', $addon );
$price_type    = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$min           = ! empty( $addon['min'] ) ? $addon['min'] : '0';
$max           = ! empty( $addon['max'] ) ? $addon['max'] : '';
$restrictions  = ! empty( $addon['restrictions'] ) ? $addon['restrictions'] : '';
$price_display = apply_filters( 'woocommerce_product_addons_price',
	'1' == $adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '',
	$addon,
	0,
	'input_multiplier'
);

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}
?>

<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo sanitize_title( $field_name ); ?>">
	<button type='button' class='minus uk-button uk-button-link uk-margin-small-right' uk-icon='minus-circle'></button>
	<input type='number' class="uk-input uk-form-width-xsmall uk-text-center input-text wc-pao-addon-field wc-pao-addon-input-multiplier" data-raw-price="<?php echo esc_attr( $price_raw ); ?>" data-price="<?php echo esc_attr( $price_display ); ?>" name="<?php echo $addon_key ?>" id="<?php echo $addon_key ?>" data-price-type="<?php echo esc_attr( $price_type ); ?>" value="<?php esc_attr_e( $min ) ?>" <?php if ( 1 === $restrictions || $min >= '0' ) echo 'min="' . esc_attr( $min ) .'"'; ?> <?php if ( ! empty( $max ) && 1 === $restrictions ) echo 'max="' . $max .'"'; ?>
	<?php if ( WC_Product_Addons_Helper::is_addon_required( $addon ) ) { echo 'required'; } ?>
	/>
	<button type='button' class='plus uk-button uk-button-link uk-margin-small-left' uk-icon='plus-circle'></button>
</p>
<?php
add_action( 'wp_footer', 'mp_addon_multiplier_plus_minus' );
function mp_addon_multiplier_plus_minus() {
	// Only run this on the single product page
	if ( ! is_product() ) return; ?>
		<script type="text/javascript">
		jQuery(document).ready(function($){   
			$('.wc-pao-addon-wrap').on( 'click', 'button.plus, button.minus', function() {
				// Get current quantity values
				let mul = $( this ).closest('.wc-pao-addon-container').find( '.wc-pao-addon-input-multiplier' );
				let val   = parseFloat(mul.val());
				let max = parseFloat(mul.attr( 'max' ));
				let min = parseFloat(mul.attr( 'min' ));
				let step = parseFloat(mul.attr( 'step' )) || 1;
				// Change the value if plus or minus
				if ( $( this ).is( '.plus' ) ) {
					if ( max && ( max <= val ) ) {
						mul.val( max );
					} else {
						mul.val( val + step );
					}
				} else {
					if ( min && ( min >= val ) ) {
						mul.val( min );
					} else if ( val > 1 ) {
						mul.val( val - step );
					}
				}
			});
		});
		</script>
	<?php
}