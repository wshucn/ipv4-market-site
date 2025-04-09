<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-lost-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.2
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>
<section class='uk-section'>
<div class='uk-container uk-container-xsmall'>
<div class='uk-card uk-card-default'>
	<div class='uk-card-header uk-padding-remove-bottom'>
		<h2 class='uk-card-title'><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></h2>
	</div>
	<div class='uk-card-body'>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password">

			<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Please enter your email address for a password reset link.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

			<div class="woocommerce-form-row form-row uk-margin">
				<label hidden class='uk-form-label' for="user_login"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
				<div class='uk-grid uk-grid-small' uk-grid>
					<div class='uk-width-expand@s'>
						<div class='uk-inline uk-width-1-1'>
							<ion-icon class='uk-form-icon' name="at-outline"></ion-icon>
							<input aria-required='true' class="uk-input woocommerce-validate" data-lpignore="true" type="text" placeholder="Email address" name="user_login" id="user_login" autocomplete="email" />
						</div>
					</div>
					<div>
						<input type="hidden" name="wc_reset_password" value="true" />
						<button type="submit" class="woocommerce-Button uk-button uk-button-primary" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset password', 'woocommerce' ); ?></button>
					</div>
			</div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

		</form>
	</div>
</div>
</div>
</section>
<?php
do_action( 'woocommerce_after_lost_password_form' );
