<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $product;

$min_value = apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product);
$max_value = apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product);

?>
<div
	class="uk-flex uk-flex-wrap uk-flex-center uk-flex-left@s uk-flex-gap-small woocommerce-variation-add-to-cart variations_button">
	<?php do_action('woocommerce_before_add_to_cart_button'); ?>

	<div class='uk-flex-none' <?php if ($max_value && $min_value === $max_value) {
    echo ' hidden';
}?>>
		<?php
    do_action('woocommerce_before_add_to_cart_quantity');

    woocommerce_quantity_input(
        array(
            'min_value'   => $min_value,
            'max_value'   => $max_value,
            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
        )
    );

    do_action('woocommerce_after_add_to_cart_quantity');
    ?>
	</div>

	<button type="submit"
		class="uk-width-auto@s uk-button uk-button-secondary uk-text-nowrap single_add_to_cart_button"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>

	<?php do_action('woocommerce_after_add_to_cart_button'); ?>

	<input type="hidden" name="add-to-cart"
		value="<?php echo absint($product->get_id()); ?>" />
	<input type="hidden" name="product_id"
		value="<?php echo absint($product->get_id()); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>