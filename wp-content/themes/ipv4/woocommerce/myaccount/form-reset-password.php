<?php
/**
 * Lost password reset form.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<section class='uk-section'>
<div class='uk-container uk-container-small'>
<form method="post" class="uk-form-horizontal woocommerce-ResetPassword lost_reset_password">

	<h3>Reset Password</h3>
	<p><?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'Enter a new password below.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

	<div class="woocommerce-form-row form-row uk-margin">
		<label class='uk-form-label' for="password_1"><?php esc_html_e( 'New password', 'woocommerce' ); ?>&nbsp;<abbr class="required" title="required" hidden>*</abbr></label>
		<div class='uk-form-controls'>
			<div class='uk-inline uk-width-1-1'>
				<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
				<input aria-required data-lpignore="true" type="password" class="uk-input" name="password_1" id="password_1" autocomplete="new-password" />
			</div>
		</div>
	</div>
	<div class="woocommerce-form-row form-row uk-margin">
		<label class='uk-form-label' for="password_2"><?php esc_html_e( 'Re-enter new password', 'woocommerce' ); ?>&nbsp;<abbr class="required" title="required" hidden>*</abbr></label>
		<div class='uk-form-controls'>
			<div class='uk-inline uk-width-1-1'>
				<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
				<input aria-required data-lpignore="true" type="password" class="uk-input" name="password_2" id="password_2" autocomplete="new-password" />
			</div>
		</div>
	</div>

	<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
	<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

	<?php do_action( 'woocommerce_resetpassword_form' ); ?>

	<div class="woocommerce-form-row form-row uk-margin">
		<input type="hidden" name="wc_reset_password" value="true" />
		<button type="submit" class="woocommerce-Button uk-button uk-button-default" value="<?php esc_attr_e( 'Reset Password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset Password', 'woocommerce' ); ?></button>
	</div>

	<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

</form>
</div>
</section>
<?php
do_action( 'woocommerce_after_reset_password_form' );

