<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<hr role='separator'>

<section>
	<form id='apply-coupon-form' method='post' data-validate-required='[name="coupon_code"]'>
		<div class='uk-grid uk-grid-small uk-flex-middle' uk-grid>
			<div class='form-row uk-width-expand uk-position-relative'>
				<label class='uk-form-label' for='coupon_code'><?php esc_html_e( 'Gift card or coupon code', 'woocommerce' ); ?></label>
				<span class='woocommerce-input-wrapper uk-display-block uk-form-controls'>
					<input type='text' data-lpignore='true' name='coupon_code' class='uk-input input-text' value='' placeholder='<?php esc_attr_e( 'Gift card or discount code', 'woocommerce' ); ?>' />
				</span>
			</div>
			<div class='form-row uk-width-auto uk-position-relative uk-flex'>
				<button disabled aria-disabled='true' type='submit' class='has-icon uk-button uk-button-secondary uk-button-responsive button' name='apply_coupon' value='<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>'>
					<span class='uk-visible@s'><?php esc_attr_e( 'Apply', 'woocommerce' ); ?></span>
					<?php echo get_icon( 'arrow-forward', array( 'class' => 'uk-hidden@s uk-margin-remove' ), 'large' ); ?>
				</button>
			</div>
		</div>
	</form>
</section>
