<?php

/**
 * Blog Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */
?>
<div>
	<?php
	// Check rows exist.
	if ( have_rows( 'blog' ) ) :

		// Loop through rows.
		while ( have_rows( 'blog' ) ) :
			the_row();

			// Load sub field value.
			$id      = get_sub_field( 'blog_post' );
			$title   = get_the_title( $id ); // USE THE ID
			$title   = strip_tags( $title );
			$link    = get_permalink( $id );
			$excerpt = get_the_excerpt( $id );
			$date    = get_the_date( 'M d, Y', $id );

			$image = get_post_thumbnail_id( $id );
			if ( get_post_thumbnail_id( $id ) ) {
				$image = ( has_post_thumbnail( $id ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' ) : '' );
				$image = $image[0];
			} else {
				$image = get_field( 'default_resource_thumbnail', 'options' );
			}
			?>

			<div class="news-post uk-background-muted uk-padding-small uk-margin-bottom">
				<div uk-grid>
					<div class="uk-width-1-3@m">
						<div>
							<a href="<?php echo $link; ?>" aria-label="<?php echo $title; ?>">
								<img data-src="<?php echo $image; ?>" uk-img>
							</a>
						</div>
					</div>

					<div class="content uk-width-2-3@m">
						<h2 class="uk-h3 uk-margin-remove"><a href="<?php echo $link; ?>" aria-label="<?php echo $title; ?>" class="uk-text-primary"><?php echo $title; ?></a></h2>
						<p style="font-size:16px;"><?php echo $excerpt; ?> <a href="<?php echo $link; ?>" aria-label="<?php echo $title; ?>"><strong>Read more</strong></a></p>
					</div>
				</div>
			</div>
			<?php
	endwhile;
endif;
	?>
</div>
