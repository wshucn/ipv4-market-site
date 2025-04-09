	</div><!-- END: Grid -->
	</main>

	<?php if ( is_active_sidebar( 'before-footer' ) ) : ?>
	<aside class='before-footer'>
		<?php dynamic_sidebar( 'before-footer' ); ?>
	</aside>
	<?php endif; ?>

	<?php if ( class_exists( 'GFCommon' ) ) : ?>
	<div class='uk-section uk-section-small uk-section-primary'>
		<?php
		echo do_shortcode( '[gravityform id="1" title="false" description="false" ajax="false"]' );
		?>
	</div>
	<?php endif; ?>

	<footer class='uk-light uk-padding uk-background-secondary uk-background-cover'
		data-src='<?php echo get_asset_url( 'images/bg-footer-mtn.webp' ); ?>' uk-img role='contentinfo'>
		<div class='uk-container uk-container-expand uk-padding-large uk-padding-remove-horizontal'>
			<?php get_template_part( 'partials/footer', 'logo', 'uk-display-block uk-margin-bottom' ); ?>
			<div class='uk-grid uk-flex-between uk-child-width-1-1 uk-child-width-auto@s' uk-grid>
				<div>
					<?php
					$address = the_contact_address( '', 0, array( 'class' => 'single-line' ), false, false );
					ob_start();
					?>
					<div class='single-line'>
						<span><?php the_contact_phone( '', 0 ); ?></span>
						<span><?php the_contact_email( '', 0 ); ?></span>
					</div>
					<?php
					$phone_email = ob_get_clean();
					// insert Phone and Email within the <address> element
					$address = mp_insert_element( $address, $phone_email, 'address', 'lastChild' );
					echo $address;
					?>
				</div>
				<div>
					<?php get_template_part( 'partials/footer', 'menu', 'uk-margin-small-top uk-text-small uk-grid-row-collapse uk-grid-small uk-grid-divider' ); ?>
					<?php get_template_part( 'partials/content', 'social' ); ?>
				</div>
			</div>
			<hr style='border-color: var(--primary)'>
			<div class='uk-grid uk-grid-small uk-flex-middle uk-flex-center uk-flex-between@s uk-text-small' uk-grid>
				<div>
					<?php get_template_part( 'partials/copyright', '', 'uk-margin-remove uk-link-text' ); ?>
					<?php get_template_part( 'partials/copyright', 'menu', 'uk-grid-row-collapse uk-grid-xsmall uk-grid-divider' ); ?>
				</div>
				<?php get_template_part( 'partials/copyright', 'backlink', 'uk-margin-remove uk-link-text' ); ?>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>
	</body>

	</html>
