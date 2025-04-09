<?php
/**
 * Shipping Calculator
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/shipping-calculator.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

?>

<?php

// printf( '<a uk-toggle="target: #shipping-calculator-modal" href="#" class="shipping-calculator-button">%s</a>', esc_html( ! empty( $button_text ) ? $button_text : __( 'Calculate shipping', 'woocommerce' ) ) );

?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="woocommerce-shipping-calculator" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<section class="_shipping-calculator-form" data-validate-required='.uk-input, .uk-select'>

		<div class='uk-grid uk-grid-small' uk-grid>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_country', true ) ) : ?>
			<div class="form-row form-row-wide uk-width-1-2@s" id="calc_shipping_country_field">
				<label for="calc_shipping_country" class="uk-form-label uk-text-nowrap" id="calc_shipping_country-label"><?php esc_html_e( 'Country / Region', 'woocommerce' ); ?></label>
				<span class='woocommerce-input-wrapper uk-display-block uk-form-controls'>
					<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state country_select uk-select" rel="calc_shipping_state" data-lpignore='true' aria-required='true' autocomplete='country'>
						<option value="default"><?php esc_html_e( 'Select a country / region&hellip;', 'woocommerce' ); ?></option>
						<?php
						foreach ( WC()->countries->get_shipping_countries() as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
				</span>
			</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_state', true ) ) : ?>
			<div class="form-row uk-width-1-2@s" id="calc_shipping_state_field">
				<label for="calc_shipping_state" class="uk-form-label uk-text-nowrap" id="calc_shipping_state-label"><?php esc_attr_e( 'State / County', 'woocommerce' ); ?></label>
				<?php
				$current_cc = WC()->customer->get_shipping_country();
				$current_r  = WC()->customer->get_shipping_state();
				$states     = WC()->countries->get_states( $current_cc );

				if ( is_array( $states ) && empty( $states ) ) {
					?>
					<input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" />
					<?php
				} elseif ( is_array( $states ) ) {
					?>
						<select name="calc_shipping_state" class="uk-select state_select" id="calc_shipping_state" data-placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>">
							<option value=""><?php esc_html_e( 'Select an option&hellip;', 'woocommerce' ); ?></option>
							<?php
							foreach ( $states as $ckey => $cvalue ) {
								echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
							}
							?>
						</select>
					</span>
					<?php
				} else {
					?>
					<span class='woocommerce-input-wrapper uk-display-block uk-form-controls'>
						<input type="text" class="uk-input input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" />
					</span>
					<?php
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', true ) ) : ?>
			<div class="form-row uk-width-expand@s" id="calc_shipping_city_field">
				<label for="calc_shipping_city" class="uk-form-label uk-text-nowrap" id="calc_shipping_city-label">Town / City</label>
				<span class='woocommerce-input-wrapper uk-display-block uk-form-controls'>
					<input type="text" class="uk-input input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php esc_attr_e( 'Town / City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" data-lpignore="true" aria-required="true" autocomplete="address-level2" aria-labelledby="calc_shipping_city-label" />
				</span>
			</div>
		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>
			<div class="form-row uk-width-expand@s" id="calc_shipping_postcode_field">
				<label for="calc_shipping_postcode" class="uk-form-label uk-text-nowrap" id="calc_shipping_postcode-label"><?php esc_attr_e( 'Postcode / ZIP', 'woocommerce' ); ?></label>
				<span class='woocommerce-input-wrapper uk-display-block uk-form-controls'>
					<input type="text" class="uk-input input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php esc_attr_e( 'Postcode / ZIP', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode"  data-lpignore="true" aria-required="true" autocomplete="postal-code" aria-labelledby="calc_shipping_postcode-label" />
				</span>
			</div>
		<?php endif; ?>

		<div class="form-row uk-width-expand@s">
			<button disabled aria-disabled='true' type='submit' class='uk-width-1-1 uk-button uk-button-default button' name='calc_shipping' value='1'>
				<?php esc_attr_e( 'Get Rates', 'woocommerce' ); ?>
			</button>
		</div>
		<?php wp_nonce_field( 'woocommerce-shipping-calculator', 'woocommerce-shipping-calculator-nonce' ); ?>
	</div>

	</section>

</form>

<hr role='separator'>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>
