<?php
get_header();
?>
<div class="resource-nav uk-background-white uk-padding-small uk-padding-remove-horizontal uk-box-shadow-medium uk-visible@m">
	<div class="uk-container uk-container-large">
		<div class="uk-width-auto uk-flex-right" uk-grid>
			<?php if (has_nav_menu('resources')) {
				$menu = wp_nav_menu(
					array(
						'menu_class'     => 'uk-navbar-nav uk-flex-middle uk-flex-wrap',
						'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'container'      => false,
						'theme_location' => 'resources',
						'walker'         => new Walker_UIkit(),
						'echo'           => false,
					)
				);
				echo $menu;
			} ?>
		</div>
	</div>
</div>
<!-- Content -->
<div class='content uk-container uk-container-large uk-margin-medium-top'>
	<div class="uk-padding uk-padding-remove-vertical" uk-grid>
		<div class="uk-width-2-3@m">
			<?php if (get_field('case_study_archive_title', 'options')) : ?>
				<h1 class="uk-text-primary"><?php the_field('case_study_archive_title', 'options'); ?></h1>
			<?php endif; ?>

			<?php if (get_field('case_study_archive_intro', 'options')) : ?>
				<div class="page-intro" style="line-height:1.5;">
					<?php the_field('case_study_archive_intro', 'options'); ?>
				</div>
			<?php endif; ?>

			<?php
			$paged = get_query_var('paged') ? get_query_var('paged') : 1;
			$args = array(
				'post_type' => 'case-studies',
				'posts_per_page' =>  get_option('posts_per_page'),
				'paged' => $paged
			);
			$my_posts = new WP_Query($args);
			if ($my_posts->have_posts()) :
				while ($my_posts->have_posts()) : $my_posts->the_post(); ?>
					<div class="news-post uk-background-muted uk-padding-small uk-margin-bottom">
						<div uk-grid>
							<div class="uk-width-1-3@m">
								<div>
									<?php
									$image = get_post_thumbnail_id(get_the_ID());
									if (get_post_thumbnail_id(get_the_ID())) {
										$image = (has_post_thumbnail(get_the_ID()) ? wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail') : '');
										$image = $image[0];
									} else {
										$image = get_field('default_case_study_thumbnail', 'options');
									} ?>

									<a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>">
										<img data-src="<?php echo $image; ?>" uk-img>
									</a>
								</div>
							</div>

							<div class="content uk-width-2-3@m">
								<?php if (get_field('directory_title')) : ?>
									<h2 class="uk-h3 uk-margin-remove"><a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>" class="uk-text-primary"><?php the_field('directory_title'); ?></a></h2>
								<?php else : ?>
									<h2 class="uk-h3 uk-margin-remove"><a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>" class="uk-text-primary"><?php the_title(); ?></a></h2>
								<?php endif; ?>
								<p class="date uk-margin-remove"><strong><?php echo get_the_date(); ?></strong></p>

								<p style="font-size:16px;"><?php the_excerpt(); ?> <a href="<?php the_permalink(); ?>" aria-label="<?php the_title(); ?>"><strong>Read more</strong></a></p>
							</div>
						</div>
					</div>
				<?php endwhile; ?>

				<div class="sect-pagination uk-margin-large-top">
					<?php mp_page_navi($my_posts); ?>
				</div>

			<?php endif;
			wp_reset_postdata(); ?>
		</div>

		<div class="sidebar uk-margin-medium-bottom uk-width-1-3@m uk-flex-first@m">
			<?php dynamic_sidebar('blog-sidebar'); ?>
		</div>
	</div>
</div>
<!-- END: Content -->
<?php
get_footer();
