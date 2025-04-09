<?php
get_header();

// global $query_string;
// wp_parse_str( $query_string, $search_query );
// $search = new WP_Query( $search_query );
global $wp_query;
$total_results = $wp_query->found_posts;
?>
<!-- Content -->
<div class='content uk-width-expand uk-margin-medium-top'>
	<div class='uk-section'>
		<div class='uk-text-center uk-margin-medium-bottom'>
			<h2 class='uk-h1 uk-margin-remove alt'><?php printf( __( 'You searched for `%s`', 'text_domain' ), explode( '=', $query_string )[1] ); ?></h2>
			<p class='uk-text-meta uk-text-large'><?php printf( __( '%s results', 'text_domain' ), $total_results ); ?></p>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class='uk-grid uk-grid-small uk-container uk-container-large uk-child-width-1-2@m uk-margin-auto' uk-margin uk-grid>
			<?php
			$excerpt_length = (int) _x( '55', 'excerpt_length' );
			$excerpt_length = (int) apply_filters( 'excerpt_length', $excerpt_length );
			$excerpt_more   = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			while ( have_posts() ) :
				the_post();
				?>
				<div class="searchwp-live-search-result" role="option" id="" aria-selected="false">
					<a class='uk-link-muted' href="<?php echo esc_url( get_permalink() ); ?>">
						<div class="uk-card uk-card-hover uk-card-small uk-card-default uk-grid uk-grid-collapse uk-margin uk-border-rounded" uk-grid>
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="uk-card-media-left uk-width-small uk-padding-small uk-visible@s">
									<?php the_post_thumbnail( 'theme-small', array( 'sizes' => '150px' ) ); ?>
								</div>
							<?php endif; ?>
							<div class='uk-width-1-1 uk-width-expand@s'>
								<div class='uk-card-body'>
									<h4 class="uk-card-title alt"><?php the_title(); ?></h4>
									<p style='line-height: 1.5'>
										<?php
										// When the manual excerpts metabox does not work, we need to roll our own.
										$raw_excerpt = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc', true );
										if ( empty( $raw_excerpt ) ) {
											$raw_excerpt = get_the_excerpt();
										}
										$excerpt = $raw_excerpt;
										$excerpt = strip_shortcodes( $excerpt );
										$excerpt = excerpt_remove_blocks( $excerpt );
										$excerpt = wp_strip_all_tags( $excerpt );
										$excerpt = wp_trim_words( $excerpt, $excerpt_length, $excerpt_more );
										echo apply_filters( 'wp_trim_excerpt', $excerpt, $raw_excerpt );
										?>
									</p>
								</div>
							</div>
						</div>
					</a>
				</div>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
<!-- END: Content -->

<?php get_footer(); ?>
