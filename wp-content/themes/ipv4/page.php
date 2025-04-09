<?php
get_header();
?>

<!-- Content -->
<div class='content uk-width-expand uk-background-<?php the_field( 'page_background_color' ); ?>'>
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
				// $content = apply_filters( 'the_content', get_the_content() );
				// echo $content;
				the_content();
		endwhile;
	endif;
	?>
</div>
<!-- END: Content -->
<?php

// get_sidebar();

get_footer();
