<?php
/*
    Template Name: Front Page
*/
get_header();

if (have_posts()) :
	while (have_posts()) :
		the_post();
		$image = (has_post_thumbnail(get_the_ID()) ? wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'single-post-thumbnail') : '');
?>
		<!-- Content -->
		<div class="body-content">
			<div class="sect-hero uk-position-relative uk-background-cover uk-background-bottom" <?php if ($image) : ?>style="  background-image: url('<?php echo $image[0]; ?>');" <?php endif; ?>>
				<?php if (get_field('hero_title')) : ?>
					<div class="hero-text uk-padding uk-padding-remove-vertical uk-text-center uk-position-center">
						<div>
							<hr class="uk-divider uk-divider-secondary uk-margin-auto">
							<h1 class="uk-light"><?php the_field('hero_title'); ?></h1>

							<?php if (get_field('hero_subtitle')) : ?>
								<p class="uk-light uk-text-medium uk-width-4-5@m uk-margin-auto"><?php the_field('hero_subtitle'); ?></p>
							<?php endif; ?>

							<?php if (have_rows('hero_links')) : ?>
								<div class="wp-block-buttons uk-flex uk-flex-center">
									<?php while (have_rows('hero_links')) : the_row();
										$link = get_sub_field('hero_link');
										$link_url = $link['url'];
										$link_title = $link['title'];
										$link_target = $link['target'] ? $link['target'] : '_self'; ?>
										<a class="uk-button uk-button-front" href="<?php echo esc_url($link_url); ?>" target="<?php echo esc_attr($link_target); ?>"><?php echo esc_html($link_title); ?></a>
									<?php endwhile; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if (get_field('hero_badge')) : ?>
					<div class="hero-badge">
						<?php echo wp_get_attachment_image(get_field('hero_badge'), '200x200'); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php the_content(); ?>
		</div>
		<div class="uk-position-fixed uk-position-bottom uk-background-white" style="z-index: 2;">
			<?php echo do_shortcode('[prior-sales]'); ?>
		</div>

		<!-- END: Content -->
<?php
	endwhile;
endif;
wp_reset_postdata();

get_footer();
