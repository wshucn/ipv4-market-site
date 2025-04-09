<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
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

/*
 * @hooked wc_empty_cart_message - 10
 */

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>

<div class='uk-text-center uk-margin-medium-top uk-margin-medium-bottom'>
	<h3 class='cart-empty alt'><?php do_action( 'woocommerce_cart_is_empty' ); ?></h3>
	<div class='uk-grid uk-grid-small uk-flex-center' uk-grid>
		<div class="return-to-shop">
			<a class="uk-button uk-button-default button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
				<?php
					/**
					 * Filter "Return To Shop" text.
					 *
					 * @since 4.6.0
					 * @param string $default_text Default text.
					 */
					echo esc_html( apply_filters( 'woocommerce_return_to_shop_text', __( 'Return to shop', 'woocommerce' ) ) );
				?>
			</a>
		</div>
		<?php if ( ! is_user_logged_in() ) : ?>
		<div class='woocommerce-form-login-toggle'>
			<a href="#login" class='uk-button uk-button-default' uk-toggle><?php esc_html_e( 'Login', 'woocommerce' ); ?></a>
			<?php
			woocommerce_login_form(
				array(
					'message'  => esc_html__( 'If you have shopped with us before, please enter your details below.', 'woocommerce' ),
					'redirect' => wc_get_cart_url(),
					'hidden'   => false,
				)
			);
			?>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>
