<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.9.0
 */

if (! defined('ABSPATH')) {
    exit;
}

if ($related_products) :
    wc_set_loop_prop('is_slider', false);
    $is_slider = wc_get_loop_prop('is_slider', false);

    $section_attrs = [
        'class'			=> [ 'related products uk-padding uk-padding-remove-horizontal' ],
    ];
    $container_attrs = [];

    if ($is_slider) {
        $section_attrs['uk-slider'][] = 'finite: true; sets: true';
        $container_attrs['class'][] = 'uk-slider-container';
    }
?>
<!-- <hr class='uk-divider-icon uk-divider-muted-dark uk-margin-large-top uk-margin-large-bottom uk-width-medium uk-margin-auto'> -->

<?= buildAttributes($section_attrs, 'section'); ?>
<?php get_template_part('partials/main', 'container'); ?>
<div class="uk-position-relative">

	<?php
            $heading = apply_filters('woocommerce_product_related_products_heading', __('Related Products', 'woocommerce'));

            if ($heading) :
                ?>
	<h2 class='uk-text-center'><?php echo esc_html($heading); ?>
	</h2>
	<?php endif; ?>

	<?= buildAttributes($container_attrs, 'div'); ?>

	<?php woocommerce_product_loop_start(); ?>

	<?php foreach ($related_products as $related_product) : ?>

	<?php
                        $post_object = get_post($related_product->get_id());

                        setup_postdata($GLOBALS['post'] =& $post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

                        wc_get_template_part('content', 'product');
                        ?>

	<?php endforeach; ?>

	<?php woocommerce_product_loop_end(); ?>

</div>

<?php if ($is_slider): ?>
<div class="uk-hidden@s">
	<a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
	<a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next uk-slider-item="next"></a>
</div>

<div class="uk-visible@s">
	<a class="uk-slidenav-large uk-position-center-left-out uk-position-small" href="#" uk-slidenav-previous
		uk-slider-item="previous"></a>
	<a class="uk-slidenav-large uk-position-center-right-out uk-position-small" href="#" uk-slidenav-next
		uk-slider-item="next"></a>
</div>
<?php endif; ?>

</div>

<?php if ($is_slider): ?>
<ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-medium"></ul>
<?php endif; ?>
</div><!-- / container -->
</section>
<?php
endif;

wp_reset_postdata();
