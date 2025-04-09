<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ordering_options = array(
	// 'Label'         => array( 'data_slug', 'asc text (optional)', 'desc text (optional)' ),
	'Default' => array( 'order' ),
	'Newest'  => array( 'date' ),
	'Price'   => array( 'price' ),
);
$active           = 'order asc';

$totalproducts = wc_get_loop_prop( 'total' ) ? wc_get_loop_prop( 'total' ) : $wp_query->post_count;

?>
<?php if ( 1 < $totalproducts && ! empty( $ordering_options ) ) : ?>
	<?php // if ( is_product_category() ) : ?>
<!-- Loop Ordering -->
<div uk-sticky="show-on-up: true; animation: uk-animation-slide-top">
	<div class="uk-margin-top uk-margin-bottom uk-width-expand@s uk-text-right">
		<div class='uk-text-nowrap uk-grid uk-grid-large uk-flex-right@s' uk-grid>
			<?php foreach ( $ordering_options as $label => $ordering_option ) : ?>
			<div>
				<span><?php _e( $label, 'text_domain' ); ?></span>
				<?php
				// <span><a><ion-icon></ion-icon></a></span>

				if ( ! is_array( $ordering_option ) ) {
					$ordering_option = array();
				}
				$data_slug         = empty( $ordering_option[0] ) ? preg_replace( '/[^a-zA-Z0-9]/', '_', strtolower( $label ) ) : $ordering_option[0];
				$sort_text['asc']  = empty( $ordering_option[1] ) ? __( sprintf( 'lowest %s', strtolower( $label ) ), 'text_domain' ) : $ordering_option[1];
				$sort_text['desc'] = empty( $ordering_option[2] ) ? __( sprintf( 'highest %s', strtolower( $label ) ), 'text_domain' ) : $ordering_option[2];
				foreach ( array( 'asc', 'desc' ) as $sort_direction ) {
					$sort_button_wrapper_attrs = array(
						'class'             => array( 'uk-inline' ),
						'uk-filter-control' => array( "sort: data_{$data_slug}", "order: {$sort_direction}" ),
					);
					// set active class
					if ( $active === "{$data_slug} {$sort_direction}" ) {
						$sort_button_wrapper_attrs['class'][] = 'uk-active';
					}

					// build the elements
					$sort_button_attrs              = array(
						'title' => __( 'Sort: ', 'text_domain' ) . $sort_text[ $sort_direction ],
						'class' => array( 'uk-margin-small-left uk-icon-link' ),
					);
					$sort_button_icon_attrs['name'] = ( 'asc' === $sort_direction ) ? 'caret-up' : 'caret-down';

					// <ion-icon>
					$sort_button_icon = buildAttributes( $sort_button_icon_attrs, 'ion-icon', true );
					// <a>
					$sort_button = buildAttributes( $sort_button_attrs, 'a', $sort_button_icon );
					// <span>
					echo buildAttributes( $sort_button_wrapper_attrs, 'span', $sort_button );
				}
				?>
			</div>
			<?php endforeach; ?>
		</div>
		<hr class='uk-margin-small'>
	</div>
</div>
<!-- END: Loop Ordering -->
	<?php // endif; ?>
	<?php
endif;
