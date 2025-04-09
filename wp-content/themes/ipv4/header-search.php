<?php get_template_part( 'partials/head', 'meta' ); ?>
<header role='banner' class='site-header'>
<?php
	get_template_part( 'partials/header', 'topbar' );
	get_template_part( 'partials/header', 'nav' );
	get_template_part( 'partials/header', 'nav-offcanvas' );
	// get_template_part( 'partials/header', 'widget' );
?>
</header>

<main role='main' class='uk-background-muted-lighter'>
<?php
get_template_part( 'partials/main', 'container' );
?>
	<!-- Grid -->
	<div>