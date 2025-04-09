		</div><!-- END: Grid -->
	</div><!-- END: Container -->
</main>

<footer class='uk-light uk-padding-small uk-background-primary' role='contentinfo'>
	<div class="uk-container uk-container-large">
		<div class='uk-grid uk-child-width-1-3@s' uk-grid>
			<div>
				<?php
					get_template_part( 'partials/footer', 'logo', 'uk-display-block uk-margin-bottom uk-preserve' );

					$address = the_contact_address( '', 0, array( 'class' => 'single-line' ), false, false );

					$address = mp_insert_element( $address, 'address', 'lastChild' );
					echo $address;

					get_template_part( 'partials/content', 'social' );
				?>
			</div>

			<div>
				<p class="uk-h3 uk-text-secondary uk-text-normal" style="margin-bottom:5px;">menu</p>
				<?php
				ob_start();
				get_template_part( 'partials/footer', 'menu', '' );
				$menu_content = ob_get_clean();
				$menu_content = str_replace(
					'Your Privacy Choices',
					'Your Privacy Choices <img src="' . get_template_directory_uri() . '/assets/images/privacy.png" alt="Privacy Choices Icon" style="width: 2em; height: 1em; vertical-align: middle; margin-left: 5px;">',
					$menu_content
				);
				echo $menu_content;
				?>

			</div>

			<div>
				<p class="uk-h3 uk-text-secondary uk-text-normal" style="margin-bottom:5px;">services</p>
				<?php get_template_part( 'partials/footer', 'menu2', '' ); ?>

				<p class="uk-h3 uk-text-secondary uk-text-normal uk-margin-small-top" style="margin-bottom:5px;">search</p>
				<?php get_search_form(); ?>
			</div>
		</div>

		<?php get_template_part( 'partials/copyright', '', 'uk-text-center uk-margin-small-top uk-link-text' ); ?>
	</div>
</footer>

<?php
if ( get_field( 'hubspot_embed_code', 'options' ) ) :
	the_field( 'hubspot_embed_code', 'options' );
endif;
?>

<?php wp_footer(); ?>
</body>
</html>
