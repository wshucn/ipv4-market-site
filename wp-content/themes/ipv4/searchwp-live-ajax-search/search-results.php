<?php
/**
 * Search results are contained within a div.searchwp-live-search-results
 * which you can style accordingly as you would any other element on your site
 *
 * Some base styles are output in wp_footer that do nothing but position the
 * results container and apply a default transition, you can disable that by
 * adding the following to your theme's functions.php:
 *
 * add_filter( 'searchwp_live_search_base_styles', '__return_false' );
 *
 * There is a separate stylesheet that is also enqueued that applies the default
 * results theme (the visual styles) but you can disable that too by adding
 * the following to your theme's functions.php:
 *
 * wp_dequeue_style( 'searchwp-live-search' );
 *
 * You can use ~/searchwp-live-search/assets/styles/style.css as a guide to customize
 */
?>

<?php if ( have_posts() ) :
$excerpt_length = (int) _x( '55', 'excerpt_length' );
$excerpt_length = (int) apply_filters( 'excerpt_length', $excerpt_length );
$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );	
?>
<div class='uk-overlay uk-light' style='background: rgba(0,0,0,0.8)'>

	<div class='uk-padding'>

		<a class="uk-link-text uk-navbar-toggle uk-flex-right" style='transform: translateY(-50%)' uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#">Hide search results</a>

		<div class='uk-grid uk-grid-small uk-child-width-1-2@m uk-margin-large-bottom' uk-margin uk-grid='masonry: true'>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php //$post_type = get_post_type_object( get_post_type() ); ?>
			<div class="searchwp-live-search-result" role="option" id="" aria-selected="false">
				<a class='uk-link-reset' href="<?php echo esc_url( get_permalink() ); ?>">
					<div class="uk-card uk-card-hover uk-card-small uk-card-default uk-grid uk-grid-collapse uk-margin uk-border-rounded" uk-grid>
						<?php if(has_post_thumbnail()): ?>
						<div class="uk-card-media-left uk-width-small uk-padding-small uk-visible@s">
							<?php the_post_thumbnail('theme-small', [ 'sizes' => '150px' ]); ?>
						</div>
						<?php endif; ?>
						<div class='uk-width-1-1 uk-width-expand@s'>
							<div class='uk-card-body'>
								<h4 class="uk-card-title alt"><?php the_title(); ?></h4>
								<p style='line-height: 1.5'>
								<?php
									// When the manual excerpts metabox does not work, we need to roll our own.
									$raw_excerpt = get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true);
									if(empty($raw_excerpt)) $raw_excerpt = get_the_excerpt();
									$excerpt = $raw_excerpt;
									$excerpt = strip_shortcodes( $excerpt );
									$excerpt = excerpt_remove_blocks( $excerpt );
									$excerpt = wp_strip_all_tags($excerpt);
									$excerpt = wp_trim_words($excerpt, $excerpt_length, $excerpt_more);
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
	</div>
</div>
<?php else : ?>
	<div class='uk-background-white uk-padding-small uk-border-rounded'>
		<div class="uk-text-center" role="option">
			<?php esc_html_e( 'Your search didn\'t return any results.', 'searchwp-live-ajax-search' ); ?>
		</div>
	</div>
<?php endif; ?>
