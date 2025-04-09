<?php
/**
 * WooCommerce Content-related
 *
 * @package woocommerce
 */

// We'll manually add the wrapper.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );

// We'll position the breadcrumb where we want.
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Disable page titles on WooCommerce pages.
add_filter( 'woocommerce_show_page_title', '__return_false' );



// If a page ID is specified in custom field 'the_content', get that page's contents and echo it
// through 'the_content' filter. Used mainly to attach Gutenberg content to object types that won't
// use the Gutenberg editor, such as categories and products.
function mp_prepend_page_content() {
	$queried_object = get_queried_object();
	if ( is_product_category() ) {
		$id = sprintf( '%s_%s', $queried_object->taxonomy, $queried_object->term_id );
	} elseif ( is_product() ) {
		$id = $queried_object->ID;
	}
	if ( ! empty( $id ) ) {
		$the_content = get_field( 'the_content', $id );
		if ( $the_content ) {
			echo apply_filters( 'the_content', get_the_content( null, false, $the_content ) );
		}
	}
}




// General WooCommerce JS. Adds classes, attributes, etc.
add_action( 'woocommerce_before_main_content', 'mp_wc_script_enqueue' );
function mp_wc_script_enqueue() {
	add_action( 'wp_footer', 'mp_wc_script' );
}
function mp_wc_script() {
	?>
	<script type='text/javascript'>
		jQuery(window).on('load', function(){
			// add aria-required to required woocommerce fields
			jQuery('.woocommerce .validate-required [type="text"]').attr('aria-required','true');
			jQuery('.woocommerce-loop-product__title').attr('tabindex', 0);

		});

	</script>
	<?php
}
