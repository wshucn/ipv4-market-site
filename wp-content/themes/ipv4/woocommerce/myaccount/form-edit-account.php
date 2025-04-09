<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

<?php
$endpoint       = WC()->query->get_current_endpoint();
$endpoint_title = WC()->query->get_endpoint_title( $endpoint );

// https://rudrastyh.com/woocommerce/woocommerce_form_field.html
// Construct $field arrays, so each field is built with woocommerce_form_field(),
// and remains consistent with the checkout and address fields.

$fields = array(
	'account_first_name'   => array(
		'type'         => 'text',
		'label'        => __( 'First name', 'woocommerce' ),
		'placeholder'  => _x( 'First name', 'placeholder', 'woocommerce' ),
		'required'     => true,
		'value'        => esc_attr( $user->first_name ),
		'autocomplete' => 'given-name',
		'class'        => array( 'form-row-first' ),
	),
	'account_last_name'    => array(
		'type'         => 'text',
		'label'        => __( 'Last name', 'woocommerce' ),
		'placeholder'  => _x( 'Last name', 'placeholder', 'woocommerce' ),
		'required'     => true,
		'value'        => esc_attr( $user->last_name ),
		'autocomplete' => 'family-name',
		'class'        => array( 'form-row-last' ),
	),
	'account_display_name' => array(
		'type'        => 'text',
		'label'       => __( 'Display name', 'woocommerce' ),
		'placeholder' => _x( 'Display name', 'placeholder', 'woocommerce' ),
		'required'    => true,
		'value'       => esc_attr( $user->display_name ),
		'description' => 'This will be how your name will be displayed in the account section.',
		'class'       => array( 'form-row-wide' ),
	),
	'account_email'        => array(
		'type'        => 'email',
		'label'       => __( 'Email address', 'woocommerce' ),
		'placeholder' => _x( 'Email address', 'placeholder', 'woocommerce' ),
		'required'    => true,
		'value'       => esc_attr( $user->user_email ),
		'validate'    => 'email',
		'class'       => array( 'form-row-wide' ),
	),
);

$change_password_fields = array(
	'password_current' => array(
		'type'        => 'password',
		'label'       => __( 'Current password', 'woocommerce' ),
		'placeholder' => _x( 'Current password', 'placeholder', 'woocommerce' ),
		'class'       => array( 'form-row-wide' ),
	),
	'password_1'       => array(
		'type'        => 'password',
		'label'       => __( 'New password', 'woocommerce' ),
		'placeholder' => _x( 'New password', 'placeholder', 'woocommerce' ),
		'class'       => array( 'form-row-wide' ),
	),
	'password_2'       => array(
		'type'        => 'password',
		'label'       => __( 'Confirm new password', 'woocommerce' ),
		'placeholder' => _x( 'Confirm new password', 'placeholder', 'woocommerce' ),
		'class'       => array( 'form-row-wide' ),
	),
);


?>

	<h3><?php esc_html_e( $endpoint_title ); ?></h3>

	<div class='uk-grid uk-grid-small' uk-grid uk-margin>
	<?php foreach ( $fields as $key => $field ) : ?>
		<?php woocommerce_form_field( $key, $field, $field['value'] ); ?>
	<?php endforeach; ?>
	</div>
	<hr class='uk-margin-top'>
	<fieldset class='uk-fieldset'>
		<legend class='uk-legend'><?php esc_html_e( 'Change account password', 'woocommerce' ); ?></legend>
		<div class='uk-grid uk-grid-small' uk-grid uk-margin>
		<?php foreach ( $change_password_fields as $key => $field ) : ?>
			<?php woocommerce_form_field( $key, $field, null ); ?>
		<?php endforeach; ?>
		</div>
	</fieldset>
<!--
		<div class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide uk-position-relative uk-margin'>
			<label class='uk-form-label' for="password_current"><?php esc_html_e( 'Current password', 'woocommerce' ); ?></label>
			<div class='uk-form-controls'>
				<div class='uk-inline uk-width-1-1'>
					<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
					<input data-lpignore="true" type="password" class="woocommerce-Input woocommerce-Input--password input-text uk-input" name="password_current" id="password_current" autocomplete="off" />
				</div>
			</div>
		</div>
		<div class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide uk-position-relative uk-margin'>
			<label class='uk-form-label' for="password_1"><?php esc_html_e( 'New password', 'woocommerce' ); ?></label>
			<div class='uk-form-controls'>
				<div class='uk-inline uk-width-1-1'>
					<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
					<input data-lpignore="true" type="password" class="woocommerce-Input woocommerce-Input--password input-text uk-input" name="password_1" id="password_1" autocomplete="off" />
				</div>
			</div>
		</div>
		<div class='woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide uk-position-relative uk-margin'>
			<label class='uk-form-label' for="password_2"><?php esc_html_e( 'Confirm new password', 'woocommerce' ); ?></label>
			<div class='uk-form-controls'>
				<div class='uk-inline uk-width-1-1'>
					<span class='uk-form-icon uk-form-icon-flip' uk-icon='icon: lock'></span>
					<input data-lpignore="true" type="password" class="woocommerce-Input woocommerce-Input--password input-text uk-input" name="password_2" id="password_2" autocomplete="off" />
				</div>
			</div>
		</div> -->

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button uk-button uk-button-default" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
