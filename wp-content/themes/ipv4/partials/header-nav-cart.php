<?php
$cart_count = WC()->cart->get_cart_contents_count();           // when you want total number of items
// $cart_count = count(WC()->cart->get_cart());                 // when you want total number of different products
$cart_count_text = ( !empty($cart_count) ) ? sprintf( _n( '(%d item)', '(%d items)', $cart_count ), $cart_count ) : '';

?>
<a uk-tooltip='<?= $cart_count_text ?>' class='uk-visible@s cart menu-item-cart has-icon uk-text-nowrap' data-count='<?= $cart_count ?>' href="<?= wc_get_cart_url() ?>" title="<?php _e('View your shopping cart', 'text_domain') ?>">
    <ion-icon class='uk-icon' name='cart'></ion-icon>
    <?php if(!empty($cart_count)) : ?>
        <span class='cart-total'><?= WC()->cart->get_cart_total(); ?></span>
        <span class='cart-items uk-visible@m'><?= $cart_count_text ?></span>
    <?php else: ?>
        <span class='uk-visible@m'><?php _e('Cart', 'woocommerce'); ?></span>
    <?php endif; ?>
</a>
