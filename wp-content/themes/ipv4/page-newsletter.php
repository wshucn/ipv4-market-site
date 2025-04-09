<?php
/*
	Template Name: Newsletter Page
*/
get_header();
?>

<!-- Content -->
<div class='content uk-width-expand'>
	<div class="uk-container">

		<!-- Newsletter Introduction -->
		<div class="uk-margin-medium-top">
			<?php if ( get_field( 'newsletter_title', 'options' ) ) : ?>
				<h1 class="uk-text-primary uk-heading-medium"><?php the_field( 'newsletter_title', 'options' ); ?></h1>
			<?php endif; ?>

			<?php if ( get_field( 'newsletter_intro', 'options' ) ) : ?>
				<div class="page-intro uk-margin-medium-bottom">
					<?php the_field( 'newsletter_intro', 'options' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Gravity Form -->
		<div class="uk-margin-medium-bottom">
			<?php
			if ( class_exists( 'GFAPI' ) ) {
				// Replace '1' with your actual Gravity Form ID
				gravity_form( 19, false, false, false, '', true, 1 );
			}
			?>
		</div>

		<?php
		// Check if there are newsletters in the repeater
		if ( have_rows( 'newsletter' ) ) :
			?>
			<div class="uk-grid uk-grid-match uk-child-width-1-3@m uk-grid-medium uk-margin-medium-top uk-margin-medium-bottom" uk-grid>
				<?php
				while ( have_rows( 'newsletter' ) ) :
					the_row();
					// Get sub field values
					$image = get_sub_field( 'newsletter_image' );
					$date  = get_sub_field( 'newsletter_date' );
					$pdf   = get_sub_field( 'newsletter_pdf' );

					if ( $pdf ) :
						?>
						<div class="newsletter-card">
							<a href="<?php echo esc_url( $pdf ); ?>" target="_blank" class="uk-link-reset">
								<div class="uk-card uk-card-default uk-card-hover" style="border-radius: 8px; border: 1px solid #dcdcdc;">
									<?php if ( $image ) : ?>
										<div class="uk-card-media-top uk-text-center uk-padding-small uk-padding-remove-bottom">
											<img style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; border: 1px solid #dcdcdc;" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
										</div>
									<?php endif; ?>

									<?php if ( $date ) : ?>
										<div class="uk-card-body uk-text-left uk-padding-small">
											<h3 class="uk-margin-remove"><?php echo $date; ?></h3>
										</div>
									<?php endif; ?>
								</div>
							</a>
						</div>
						<?php
					endif;
				endwhile;
				?>
			</div>
			<?php
		endif;
		?>
	</div>
</div>
<!-- END: Content -->

<?php
// get_sidebar();
get_footer();
