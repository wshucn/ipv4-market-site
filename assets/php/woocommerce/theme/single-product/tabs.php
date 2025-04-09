<?php
/**
 * WooCommerce Single Product Tabs
 *
 * @package woocommerce
 */

// Change the heading for the Additional Information tab in the single product page.
// This is not the tab text itself.
add_filter(
	'woocommerce_product_additional_information_heading',
	fn ( $text ) => __( 'Specifications', 'woocommerce' )
);

// Name/slug of the Custom Field group for product attributes tab.
add_filter(
	'product_attributes_field_name',
	fn( $field_name ) => 'additional_information',
	10,
	1
);

// Set the titles and content of product tabs.
add_filter( 'woocommerce_product_tabs', 'mp_woocommerce_product_tabs', 10, 1 );
function mp_woocommerce_product_tabs( $tabs ) {

	// Don't show Reviews tab if there are no reviews.
	// $comments = get_comments( [ 'id' => get_the_id() ] );
	// if(empty($comments)) unset($tabs['reviews']);

	// Rename the Tabs titles.
	$titles = array(
		'description'            => __( 'Product Info', 'woocommerce' ),
		'reviews'                => __( 'Ratings', 'woocommerce' ),
		'additional_information' => __( 'Specifications', 'woocommerce' ),
	);
	foreach ( $titles as $tab => $title ) {
		if ( ! empty( $tabs[ $tab ] ) ) {
			$tabs[ $tab ]['title'] = $title;
		}
	}

	// Disable Additional Information if there is nothing in the 'additional_information' Custom Field.
	// We do this because we've hidden the weight/dimensions table that normally appears in that tab.
	$product_attributes_field_name = apply_filters( 'product_attributes_field_name', 'product_attributes' );
	$product_attributes_field      = get_field( $product_attributes_field_name );
	if ( empty( $product_attributes_field ) ) {
		unset( $tabs['additional_information'] );
	}

	return $tabs;
}


// For number ACF fields, prepend or append the unit (specified by 'prepend' or 'append').
add_filter(
	'acf/format_value/type=number',
	function( $value, $post_id, $field ) {
		if ( empty( $value ) ) {
			return $value;
		}
		if ( ! empty( $field['append'] ) ) {
			$value .= $field['append'];
		}
		if ( ! empty( $field['prepend'] ) ) {
			$value = $field['prepend'] . $value;
		}
		return $value;
	},
	10,
	3
);

// For boolean ACF fields, convert True and False to Yes and No for display.
add_filter(
	'acf/format_value/type=true_false',
	function( $value, $post_id, $field ) {
		// This function is only for the single product page, not in the loop.
		if ( ! is_product() ) {
			return $value;
		}

		// Only target the additional_information_* fields (product additional info tab)
		if ( false !== strpos( $field['name'], 'additional_information' ) ) {
			$value = (int) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			return ( 1 === $value ) ? __( 'Yes', 'proper' ) : __( 'No', 'proper' );
		}
		return $value;
	},
	20,
	3
);


// Format Product Attributes table rows on the single product page.
$product_attributes_field_name = apply_filters( 'product_attributes_field_name', 'product_attributes' );
add_filter(
	"acf/format_value/name={$product_attributes_field_name}",
	function( $value, $post_id, $field ) use ( $product_attributes_field_name ) {
		if ( ! \is_array( $value ) ) {
			return $value;
		}
		// This function is only for the single product page, not in the loop.
		if ( ! is_product() ) {
			return $value;
		}

		$table_class = apply_filters( 'mp_wc_product_attributes_table_class', 'uk-table' );

		ob_start();
		?>
	<table class='<?php echo $table_class; ?>'>
		<?php
		$notes = '';
		foreach ( $value as $field_name => $field_value ) :
			if ( empty( $field_value ) ) {
				continue;
			}
			$field_label = get_field_object( "{$product_attributes_field_name}_{$field_name}" )['label'];

			// Get footnotes to attach to each field label.
			$field_note = apply_filters( "product_attributes_field_note/{$field_name}", null); // phpcs:ignore
			$field_label = product_attributes_field_note( $field_label, $field_note, $field_name, $post_id );
			?>
			<tr class="woocommerce-product-attributes-item woocommerce-product-attributes-item--<?php echo esc_attr( $field_name ); ?>">
				<th class="woocommerce-product-attributes-item__label"><?php echo wp_kses_post( $field_label ); ?></th>
				<td class="woocommerce-product-attributes-item__value"><?php echo esc_html( $field_value ); ?></td>
		</tr>
			<?php
			if ( $field_note ) {
				$notes .= buildAttributes( array( 'id' => $field_name ), 'li', $field_note );
			}
			endforeach;
		?>
</table>
		<?php
		$html = ob_get_clean();

		if ( ! empty( $notes ) ) :
			ob_start();
			?>
	<footer class='footnotes uk-text-small'>
		<ol class='uk-list uk-list-decimal'>
				<?php echo wp_kses_post( $notes ); ?>
		</ol>
	</footer>
			<?php
			$html .= ob_get_clean();
		endif;
		return $html;
	},
	10,
	3
);

// Format the footnote numbers for attribute row headers.
function product_attributes_field_note( $label, $note = null, $field_name, $post_id ) {

	if ( \is_null( $note ) ) {
		return $label;
	}

	// Wrap label with a footnote link if there is a note.
	$a_attrs = array(
		'aria-describedby' => 'footnote-label',
		'href'             => "#{$field_name}",
		'class'            => 'uk-link-reset',
	);
	$label   = buildAttributes( $a_attrs, 'a', $label );
	$label   = apply_filters( 'product_attributes_field_note_html', $label, $field_name, $post_id );

	return $label;
}
