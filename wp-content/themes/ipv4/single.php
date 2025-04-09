<?php
get_header();
?>
<!-- Content -->
<div class='content uk-container uk-container-large uk-margin-medium-top'>
	<div class="uk-padding uk-padding-remove-vertical" uk-grid>
		<div class="uk-width-2-3@m">
			<div class="post-wrap">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<h1 class="uk-text-secondary uk-margin-remove"><?php the_title(); ?></h1>
					<?php the_content(); ?>
				<?php endwhile; ?>

				<div class="sect-pagination uk-margin-large-top">
					<?php mp_page_navi( $wp_query ); ?>
				</div>
			</div>
		</div>

		<div class="sidebar uk-width-1-3@m uk-flex-first@m">
			<?php dynamic_sidebar( 'single-resource' ); ?>
		</div>
	</div>

	<div class="next-prev uk-margin-bottom">
		<hr class="uk-divider uk-divider-muted uk-width-1-1">

		<div class="uk-flex uk-flex-between">
			<?php
			/**
			 * Infinite next and previous post looping in WordPress
			 */
			if ( get_adjacent_post( false, '', true ) ) {
				?>
			<div class="nav-previous">
				<?php
				previous_post_link( '%link', '&larr; Previous Post' );
				?>
			</span></div>
				<?php
			} else {
				$first = new WP_Query( 'posts_per_page=1&order=DESC&post_type=post' );
				$first->the_post();
				echo '<div class="nav-previous"><a href="' . get_permalink() . '" class="alpha">&larr; Previous Post</a></span></div>';
				wp_reset_query();
			}
			if ( get_adjacent_post( false, '', false ) ) {
				?>
			<div class="nav-next">
				<?php
				next_post_link( '%link', 'Next Post &rarr;' );
				?>
			</span></div>
				<?php
			} else {
				$last = new WP_Query( 'posts_per_page=1&order=ASC&post_type=post' );
				$last->the_post();
				echo '<div class="nav-next"><a href="' . get_permalink() . '" class="alpha">Next Post &rarr;</a></span></div>';
				wp_reset_query();
			}
			?>
		</div>
	</div>
	<?php wp_reset_postdata(); ?>
</div>
<!-- END: Content -->
<?php

get_footer();
