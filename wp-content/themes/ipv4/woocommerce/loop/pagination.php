<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/pagination.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$total   = isset( $total ) ? $total : wc_get_loop_prop( 'total_pages' );
$current = isset( $current ) ? $current : wc_get_loop_prop( 'current_page' );
$base    = isset( $base ) ? $base : esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
$format  = isset( $format ) ? $format : '';

if ( $total <= 1 ) {
	return;
}
?>
<nav class='woocommerce-pagination uk-flex uk-flex-center uk-h3 alt'>
	<?php
	$paginate_links = paginate_links(
		apply_filters(
			'woocommerce_pagination_args',
			array( // WPCS: XSS ok.
				'base'      => $base,
				'format'    => $format,
				'add_args'  => false,
				'current'   => max( 1, $current ),
				'total'     => $total,
				'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
				'next_text' => is_rtl() ? '&larr;' : '&rarr;',
				'type'      => 'array',
				'end_size'  => 3,
				'mid_size'  => 3,
			)
		)
	);
	if ( is_array( $paginate_links ) ):
	?>
		<ul class='uk-pagination'>
		<?php 
		foreach ($paginate_links as $paginate_link):
			$paginate_link_class = str_contains($paginate_link, 'current') ? 'uk-active' : '';
			// <li>
				echo empty($paginate_link_class) ? '<li>' : buildAttributes([ 'class' => $paginate_link_class ], 'li');
				$paginate_link = mp_html_class_by_class($paginate_link, 'page-numbers', '!page-numbers', TRUE);
				echo wp_kses_post($paginate_link);
				?>
			</li>
		<?php
		endforeach;
		?>  
		</ul>
	<?php
	endif;
	?>
</nav>
