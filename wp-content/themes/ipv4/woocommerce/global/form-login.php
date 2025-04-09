<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_user_logged_in() ) {
	return;
}

?>

<div id="login" uk-modal>
<div class='uk-modal-dialog'>
	<button class="uk-modal-close-default" type="button" uk-close></button>
	<div class="uk-modal-header">
		<h2 class='uk-modal-title'><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
	</div>
	<div class='uk-modal-body'>
	<?php echo ( $message ) ? wpautop( wptexturize( $message ) ) : ''; // @codingStandardsIgnoreLine ?>
	<form class="uk-form-stacked woocommerce-form woocommerce-form-login login uk-grid uk-grid-small uk-child-width-1-1" method="post" <?php echo ( $hidden ) ? 'style="display:none;"' : ''; ?> uk-grid>

		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<div class='woocommerce-form-row form-row'>
			<label hidden class='uk-label' for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<div class='uk-inline uk-width-1-1'>
				<span class='uk-form-icon' uk-icon='icon: user'></span>
				<input data-lpignore="true" type="text" class="uk-input" placeholder="User name" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>"><?php // @codingStandardsIgnoreLine ?>
			</div>
		</div>
		<div class='woocommerce-form-row form-row'>
			<label hidden class='uk-label' for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
			<div class='uk-inline uk-width-1-1'>
				<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
				<input data-lpignore="true" class="uk-input" type="password" placeholder="Password" name="password" id="password" autocomplete="current-password">
			</div>
		</div>

		<?php do_action( 'woocommerce_login_form' ); ?>

		<div class="woocommerce-form-row form-row">
			<div class="uk-grid" uk-grid>
				<div class='uk-width-expand uk-flex uk-flex-middle'>
					<label><input class="uk-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span style='margin-left: 3px'><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span></label>
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				</div>
				<div class='uk-width-small uk-flex uk-flex-middle'>
					<button type="submit" class="uk-width-1-1 uk-button uk-button-primary" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
				</div>
			</div>
		</div>
		<?php do_action( 'woocommerce_login_form_end' ); ?>

	</form>
	</div>

	<div class="uk-modal-footer uk-padding-remove-top">
		<?= icon_link('icon: question', esc_html( 'Lost your password?', 'woocommerce' ), esc_url( wp_lostpassword_url() ) ) ?>
	</div>
</div>
</div>