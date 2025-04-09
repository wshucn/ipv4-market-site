<?php
/**
 * My Account page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class='uk-grid' uk-grid>
	<div class='uk-flex-first@m uk-width-medium@m uk-margin-medium-bottom'>
		<aside uk-sticky='media: @s; bottom: true; offset: 20px;'>
<?php
/**
 * My Account navigation.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_navigation' ); ?>
		</aside>
	</div><!-- /grid column -->
	<div class='uk-flex-last@m uk-width-expand@m'>
		<main class='main' role='main'>
			<section class='uk-section uk-section-small'>
<?php
	/**
	 * My Account content.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_content' );
?>
			</section>
		</main>
	</div>
</div><!-- /grid -->