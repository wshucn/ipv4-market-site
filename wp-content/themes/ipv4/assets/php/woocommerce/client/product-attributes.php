<?php
/**
 * WooCommerce Single Product Page Tabs
 * Client-specific
 *
 * @package woocommerce
 */

// Classes for the product attributes or specifications table on the single product page tab.
add_filter( 'mp_wc_product_attributes_table_class', fn( $class) => $class . ' uk-table-justify uk-table-responsive woocommerce-product-attributes shop_attributes' );

// Alter the footnote numbers for attribute row headers.
// add_filter('product_attributes_field_note_html', 'mp_wc_product_attributes_field_note_html');
function mp_wc_product_attributes_field_note_html( $html, $field_name, $post_id ) {
	// ...
	return $html;
}

// Footnotes for certain keys.
// add_filter(
// 'product_attributes_field_note/frame_material',
// fn() => __( 'Footnote for frame material', 'proper' )
// );

// 'Made in USA' custom field: convert true/false to 'Yes' or 'No' for display.
// add_filter(
// 'acf/format_value/name=usa',
// function( $value, $post_id, $field ) {
// return ( true === $value ) ? __( 'Yes', 'proper' ) : __( 'No', 'proper' );
// },
// 10,
// 3
// );
