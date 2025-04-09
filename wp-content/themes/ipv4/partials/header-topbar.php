<div class='topbar uk-light uk-background-secondary-dark uk-text-uppercase' style='line-height: inherit;'>
	<div class='uk-container uk-container-expand'>
		<div class='uk-flex'>
			<div class='uk-visible@m'>
				<p class='uk-margin-remove'><?php echo get_bloginfo( 'description' ); ?></p>
			</div>
			<div class='uk-width-1-1 uk-width-auto@s uk-margin-auto-left@s uk-flex uk-flex-gap'>
				<ul class='icon-primary uk-text-uppercase uk-link-text uk-grid uk-grid-row-collapse uk-flex-between' uk-grid>
					<?php get_template_part( 'partials/topbar', 'menu' ); ?>
					<?php
					$contact_links   = array();
					$contact_links[] = the_contact_phone( 'call', 0, array(), false );
					if ( is_object( WC()->cart ) ) {
						ob_start();
						get_template_part( 'partials/header', 'nav-cart' );
						$contact_links[] = ob_get_clean();
					}
					echo trim_join( '', list_items( $contact_links ) );
					?>
				</ul>
				<div class='uk-text-nowrap uk-flex uk-flex-middle uk-flex-gap-small'>
					<img src='<?php echo get_asset_url( 'images/flag_usa.svg' ); ?>' alt='Flag of the United States of America' style='height: 1em'>
					<p class='uk-text-bold uk-margin-remove uk-visible@s'><?php _e( '100% American Made', 'text_domain' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div><!-- end .topbar -->
