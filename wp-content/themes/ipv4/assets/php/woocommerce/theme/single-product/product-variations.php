<?php
/**
 * WooCommerce Product Variations
 *
 * @package woocommerce
 */

// Register a swatch image size that can be cropped. The 'swatch' image size can have its own crop.
add_action( 'after_setup_theme', 'mp_custom_image_size_swatch' );
function mp_custom_image_size_swatch() {
	if ( function_exists( 'wc_get_image_size' ) ) {
		$woocommerce_thumbnail = wc_get_image_size( 'thumbnail' );
		mp_add_image_size( 'swatch', $woocommerce_thumbnail['width'], $woocommerce_thumbnail['height'], $woocommerce_thumbnail['crop'] );
	}
}

// Variation Dropdown -> Swatches
// This works in combination with JS (below) to change the product image when a swatch is clicked.
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'mp_dropdown_variation_attribute_options_html', 10, 2 );
function mp_dropdown_variation_attribute_options_html( $html, $args ) {
	$html = mp_html_attrs( $html, '//select', array( 'class' => 'uk-hidden@s' ), true );

	$wp_additional_image_sizes = wp_get_additional_image_sizes();
	$thumbnail_width           = isset( $wp_additional_image_sizes['swatch'] ) ? $wp_additional_image_sizes['swatch']['width'] : get_option( 'thumbnail_size_w' );
	$variations                = $args['product']->get_available_variations();

	// Needed to preserve order. We can't just use $args['options']
	// $swatches = array_flip(flatten(wp_list_pluck($variations, 'attributes')));
	$swatches    = array();
	$swatch_attr = apply_filters( 'mp_wc_variation_swatch_img_attr', array( 'sizes' => "{$thumbnail_width}px" ) );

	$attribute = $args['attribute'];

	foreach ( $variations as $i => $variation ) {
		$img_id    = $variation['image_id'];
		$thumbnail = wp_get_attachment_image(
			$img_id,
			'swatch',
			false,
			$swatch_attr
		);

		$select_id      = sanitize_title_with_dashes( $attribute );
		$attribute_slug = 'attribute_' . $select_id;

		$option_slug = $variation['attributes'][ $attribute_slug ];
		$option_name = mp_get_inner( $html, "//option[@value='{$option_slug}']", true );
		$li_attrs    = apply_filters(
			'mp_wc_variation_swatch_li_attr',
			array(
				'data-value' => $option_slug,
			)
		);

		if ( $option_slug === $args['selected'] ) {
			$li_attrs['class'][] = 'uk-active';
		}

		$a_attrs = apply_filters(
			'mp_wc_variation_swatch_a_attr',
			array(
				'href'    => '#',
				'onclick' => "event.preventDefault(); jQuery('#{$select_id}').val('{$option_slug}').change(); return false;",
			)
		);

		// wrap thumbnail in cover container.
		$thumbnail = mp_wrap_element( $thumbnail, null, 'div', 'swatch__image uk-flex uk-flex-center uk-flex-middle' );

		$swatch     = buildAttributes( $a_attrs, 'a', $thumbnail . "<div class='swatch__title'>{$option_name}</div>" );
		$swatches[] = buildAttributes( $li_attrs, 'li', $swatch );
	}
	if ( ! empty( $swatches ) ) {
		$thumbnav = buildAttributes( 'swatch__thumbnav uk-thumbnav uk-flex-nowrap uk-visible@s', 'ul', $swatches );
		$html    .= $thumbnav;
	}
	return $html;
}

// JavaScript that handles swatch clicks, updating the attribute dropdown.
add_action( 'woocommerce_after_variations_table', 'mp_after_variations_table_script', 20, 1 );
function mp_after_variations_table_script( $data ) {
	?>
<script type='text/javascript'>
	jQuery(document).ready(function( $ ) {

		var variations = JSON.parse($('.variations_form').attr('data-product_variations'));
		if (variations) {
			var first_attr = Object.keys(variations[0].attributes)[0];

			// fires when the select changes, and on page load
			var variation_change = function(choice) {
				var found = false;
				var slide = 0;
				// loop variations JSON
				if(choice) {
					for (const i in variations) {
						if (found) continue;
						if (variations.hasOwnProperty(i)) {
							// if first attribute has the same value as has been selected
							if (choice === variations[i].attributes[Object.keys(variations[0].attributes)[0]]) {
								// change featured image
								var image_full_src = variations[i].image.full_src;
								// get the src filename
								var image_file = image_full_src.split("/").pop().replace(/[\#\?].*$/, '');
								// find the gallery slider item with that image
								var slide = $('.woocommerce-product-gallery--with-images')
									.find('img[src*="' + image_file + '"]').parents('li').index();
								found = true;
							}
						}
					}
				}
				// Change the gallery slider to the chosen image, if it's there.
				if ( $('.woocommerce-product-gallery--with-images [uk-slideshow]').length ) {
					UIkit.slideshow('.woocommerce-product-gallery--with-images [uk-slideshow]').show(slide);
				}
			}

			// Activate chosen swatch.
			$('.variations_form').on('woocommerce_variation_select_change', function(e) {
				const attribute_selects = $('select[data-attribute_name]', e.target);
				attribute_selects.each(function(i, el){
					const choice = el.value;
					$(el).siblings('ul.swatch__thumbnav').children('li').removeClass('uk-active');
					if(choice) {
						$(el).siblings('ul.swatch__thumbnav').children('li[data-value=' + choice + ']').addClass('uk-active');
					} else {
						// Necessary to change the image when there's no choice.
						variation_change();
					}
				});
			});

			// When all attribute have been selected, a final variation image is shown. Intercept that and use the slider image instead.
			// Change the image when the page initializes, since the selected attribute can be passed in the query string.
			// $( document ).on( 'found_variation.first', function ( e, v ) {
			$('.single_variation_wrap').on('show_variation', function(e, v) {
				const choice = v.attributes[first_attr];
				variation_change(choice);
			});
		}
	});
</script>
	<?php
}

// Change the way variation price is dynamically displayed when a product variation is chosen.
add_filter( 'woocommerce_available_variation', 'mp_available_variation_prepend', 10, 3 );
function mp_available_variation_prepend( $variation_data, $product, $variation ) {
	$price_html_prepend = buildAttributes( array( 'uk-margin-right' ), 'span', __( 'Price as configured: ', 'woocommerce' ) );

	// If the variation price is same as base product price, 'price_html' will be empty
	if ( ! empty( $variation_data['price_html'] ) ) {
		$variation_data['price_html'] = mp_wrap_element( $variation_data['price_html'], null, 'span', array( 'class' => 'uk-text-large uk-text-normal' ) );
		$variation_data['price_html'] = $price_html_prepend . $variation_data['price_html'];
	}

	return $variation_data;
}
