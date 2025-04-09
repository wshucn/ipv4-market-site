<?php
get_header();
?>
<!-- Content -->
<div class='content uk-width-expand uk-margin-medium-top'>
<?php
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$posts = new WP_Query(array('posts_per_page' => -1, 'post_type' => 'post', 'paged' => $paged));
while($posts->have_posts()) : $posts->the_post();
?>

	<article class="uk-card uk-card-default">
		<div class='uk-card-body'>
			<h2><?php the_title(); ?></h2>
			<?php the_content(); ?>
		</div>
	</article>

<?php
endwhile;
wp_reset_postdata();
?>

</div>
<!-- END: Content -->
<?php

get_footer();
