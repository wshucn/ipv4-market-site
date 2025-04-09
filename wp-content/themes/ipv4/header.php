<?php get_template_part( 'partials/head', 'meta' ); ?>
<header role='banner' class='site-header'>

	<?php if ( get_field( 'banner_text', 'options' ) ) : ?>
		<div class="options-banner uk-margin-remove uk-text-center" style="" uk-alert>
			<?php echo the_field( 'banner_text', 'options' ); ?>
			<a class="uk-alert-close" uk-close></a>
		</div>
	<?php endif; ?>
	<?php
	if ( class_exists( 'woocommerce' ) && ! is_checkout() && ! is_cart() ) {
		get_template_part( 'partials/header', 'topbar' );
	}
	get_template_part( 'partials/header', 'nav' );
	get_template_part( 'partials/header', 'nav-offcanvas' );
	// get_template_part( 'partials/header', 'widget' );
	?>

</header>
<?php
	$main_attrs            = array( 'role' => 'main' );
	$main_attrs['style'][] = 'overflow-x: hidden';
	$background_color      = get_field( 'background_color' );
if ( ! empty( $background_color ) && 'default' !== $background_color ) {
	$main_attrs['class'][] = "uk-background-{$background_color}";
}

	// Add a little padding to the content when the page header is hidden.
// if ( get_field( 'page_title_visible' ) === false && empty( get_field( 'page_image' ) ) ) {
// $main_attrs['class'][] = 'uk-padding-remove-bottom';
// }

// <main>
echo buildAttributes( $main_attrs, 'main' );

// get_template_part( 'partials/main', 'title' );
// get_template_part( 'partials/main', 'container' );
// get_template_part( 'partials/main', 'breadcrumb' );
// edit_post_link();
?>

<!-- Grid -->
<div>
