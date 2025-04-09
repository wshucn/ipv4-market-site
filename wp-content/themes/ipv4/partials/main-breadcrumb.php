<?php
if ( ! is_front_page() && ! is_cart() && ! is_checkout() ) : ?>
<!-- Breadcrumb -->
<div class='breadcrumbs uk-flex-last uk-margin-medium-top uk-margin-small-bottom' typeof="BreadcrumbList" vocab="https://schema.org/">
	<?php
	if ( function_exists( 'bcn_display' ) ) {
		// Breadcrumb NavXT breadcrumbs.
		$crumbs = bcn_display_list( true );
		echo mp_wrap_element( $crumbs, '//html/body', 'ul', 'uk-breadcrumb' );
	} elseif ( function_exists( 'yoast_breadcrumb' ) ) {
		// Yoast SEO breadcrumbs. Style in functions-vendor.php.
		yoast_breadcrumb();
	} elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		rank_math_the_breadcrumbs();
	}
	?>
</div>
<!-- END: Breadcrumb -->
	<?php
endif;
