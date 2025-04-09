<?php
/**
 * Add payment method form form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-add-payment-method.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.3.0
 */

defined( 'ABSPATH' ) || exit;

$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

if ( $available_gateways ) : ?>
	<form id="add_payment_method" method="post">
		<div id="payment" class="woocommerce-Payment">
			<div class="uk-grid uk-margin uk-child-width-1-1 woocommerce-PaymentMethods payment_methods methods" uk-grid>
				<?php
				// Chosen Method.
				if ( count( $available_gateways ) ) {
					current( $available_gateways )->set_current();
				}

				foreach ( $available_gateways as $gateway ) {
					?>
					<div>
						<input hidden class='uk-radio input-radio' id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
						<label class='uk-display-block' for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" style='user-select: none'>
							<div class="uk-card uk-card-default wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
								<div class='uk-light uk-position-absolute uk-position-top-center'>
									<span hidden class='checked uk-icon-button' uk-icon="icon: check" style='color: #fff; border: 0px solid #fff; transform: translateY(-33%)'></span>
								</div>
								<div class='uk-card-body uk-padding uk-padding-remove-bottom'>
									<h4 class='alt'>
										<?php echo $gateway->get_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?> <?php echo $gateway->get_icon(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
									</h4>
									<?php
									if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
										<div class="uk-text-small uk-margin-top woocommerce-PaymentBox woocommerce-PaymentBox--<?= esc_attr( $gateway->id ) . ' payment_box payment_method_' . esc_attr( $gateway->id ) ?>" style="display: none;">
											<?php
											// make forms grids
											$html = '';
											ob_start();
											try {
												$gateway->payment_fields();
												$html = ob_get_contents();
											} finally {
												ob_end_clean();
											}
											$payment_method_form_attrs = [
												'class'		=> 'uk-grid uk-grid-small uk-child-width-1-1',
												'uk-grid'	=> NULL,
											];
											$html = mp_html_attrs($html, '//div[contains(@class, "payment-method-form")]', $payment_method_form_attrs, TRUE);
											$html = mp_html_class($html, '//div[contains(@id, "expiry_field")]', 'uk-width-1-2@s', TRUE);
											$html = mp_html_class($html, '//div[contains(@id, "csc_field")]', 'uk-width-1-2@s', TRUE);
											echo $html;
											?>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</label>
					</div>
					<?php
				}
				?>
			</div>

			<?php do_action( 'woocommerce_add_payment_method_form_bottom' ); ?>

			<div class="form-row">
				<?php wp_nonce_field( 'woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce' ); ?>
				<button type="submit" class="uk-button uk-button-default woocommerce-Button woocommerce-Button--alt button alt" id="place_order" value="<?php esc_attr_e( 'Add payment method', 'woocommerce' ); ?>"><?php esc_html_e( 'Add payment method', 'woocommerce' ); ?></button>
				<input type="hidden" name="woocommerce_add_payment_method" id="woocommerce_add_payment_method" value="1" />
			</div>
		</div>
	</form>
<?php else : ?>
	<div uk-alert class="woocommerce-notice woocommerce-notice--info woocommerce-info"><?php esc_html_e( 'New payment methods can only be added during checkout. Please contact us if you require assistance.', 'woocommerce' ); ?></div>
<?php endif; ?>
