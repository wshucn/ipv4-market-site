<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query;
// Don't allow more columns than the number of products being displayed.
// $loop_columns = min($wp_query->found_posts, $wp_query->query_vars['posts_per_page'], esc_attr( wc_get_loop_prop( 'columns' ) ));
$loop_columns = esc_attr( wc_get_loop_prop( 'columns' ) );
$is_slider    = wc_get_loop_prop( 'is_slider', false );

$ul_attrs            = array();
$ul_attrs['class'][] = "products uk-margin uk-child-width-1-{$loop_columns}@m";
$ul_attrs['class'][] = 'uk-flex-center'; // center when not enough products to fill columns

if ( $is_slider ) {
	$ul_attrs['class'][]         = 'uk-slider-items uk-grid';
	$ul_attrs['uk-height-match'] = 'target: .woocommerce-loop-product__link';
} else {
	$ul_attrs['class'][]         = 'uk-grid';
	$ul_attrs['uk-grid']         = '';
	$ul_attrs['uk-height-match'] = 'target: .uk-card';
}
?>
<!-- Loop -->
<?php
echo buildAttributes( $ul_attrs, 'ul' );
