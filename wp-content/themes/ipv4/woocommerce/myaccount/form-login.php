<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

<section class='uk-section'>

<div class="uk-grid uk-child-width-1-2@m" uk-grid>

	<div><div id="customer_login" class='uk-card uk-card-default'>

<?php endif; ?>

		<div class='uk-card-header uk-padding-remove-bottom'>
			<h2 class='uk-card-title'><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
		</div>

		<div class='uk-card-body'>
		<form class="uk-form-stacked woocommerce-form woocommerce-form-login login uk-grid uk-grid-small uk-child-width-1-1" method="post" uk-grid>

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

		<div class="uk-card-footer uk-padding-remove-top">
			<?= icon_link('icon: question', esc_html( 'Lost your password?', 'woocommerce' ), esc_url( wp_lostpassword_url() ) ) ?>
		</div>

<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

	</div></div>

	<div><div class="uk-card">
		<div class='uk-card-header uk-padding-remove-bottom'>
			<h2 class='uk-card-title'><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
		</div>
		<div class='uk-card-body'>
		<form method="post" class="woocommerce-form woocommerce-form-register register uk-grid uk-grid-small uk-child-width-1-1" <?php do_action( 'woocommerce_register_form_tag' ); ?> uk-grid>

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<div class="woocommerce-form-row form-row">
					<label hidden for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<div class='uk-inline uk-width-1-1'>
						<span class='uk-form-icon' uk-icon='icon: user'></span>
						<input data-lpignore="true" type="text" class="uk-input" placeholder="User name" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</div>
				</div>

			<?php endif; ?>

			<div class="woocommerce-form-row form-row">
				<label hidden for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<div class='uk-inline uk-width-1-1'>
					<ion-icon class='uk-form-icon' name="at-outline"></ion-icon>
					<input data-lpignore="true" type="email" class="uk-input" placeholder="Email address" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</div>
			</div>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<div class="woocommerce-form-row form-row">
					<label hidden for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<div class='uk-inline uk-width-1-1'>
						<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
						<input data-lpignore="true" type="password" class="uk-input" placeholder="Password" name="password" id="reg_password" autocomplete="new-password" />
					</div>
				</div>

			<?php else : ?>

				<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<div class="uk-margin">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="uk-button uk-button-default uk-width-small" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
			</div>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>
		</div>

	</div></div>

</div>
</section>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
