<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$proceed_to_checkout_attrs = array(
	'class' => array(
		'checkout-button button alt wc-forward',
		'uk-button uk-button-secondary uk-button-large',
		'uk-flex-center',
		'has-icon',
		'uk-width-1-1',
		'uk-width-auto@m',
		'uk-margin-top',
	),
	'href'  => esc_url( wc_get_checkout_url() ),
);

?>

<a <?php echo buildAttributes( $proceed_to_checkout_attrs ); ?>>
	<ion-icon name="lock-closed" aria-hidden='true' role='img'></ion-icon>
	<?php esc_html_e( 'Checkout', 'woocommerce' ); ?>
</a>
