<?php get_template_part( 'partials/head', 'meta' ); ?>
<header role='banner' class='site-header'>
	<?php
	get_template_part( 'partials/header', 'topbar' );
	get_template_part( 'partials/header', 'nav' );
	get_template_part( 'partials/header', 'nav-offcanvas' );
	// get_template_part( 'partials/header', 'widget' );
	?>
</header>
<?php
$main_attrs       = array( 'role' => 'main' );
$background_color = 'muted-lighter';
if ( ! empty( $background_color ) && 'default' !== $background_color ) {
	$main_attrs['class'][] = "uk-background-{$background_color}";
}
?>
<main <?php echo buildAttributes( $main_attrs ); ?>>
<?php
if ( ! is_product() ) {
	get_template_part( 'partials/main', 'title' );
}
?>
<!-- Grid -->
<div class='uk-grid' uk-grid>
