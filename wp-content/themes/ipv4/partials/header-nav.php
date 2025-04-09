<!-- Sticky Wrapper -->
<div uk-sticky="sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky">

		<!-- Navigation Bar -->
			<nav class='uk-navbar-container' role='navigation' aria-label='Primary Navigation' uk-navbar>
				<!-- Logo -->
				<div class='nav-overlay uk-navbar-left' style="padding-top: 24px; padding-bottom: 24px; padding-left: 30px;">
					<?php
					// do not add margin or padding to the logo, as it will cause CLS. add it to one of the parents
					// logo height and width should match the SVG viewBox attribute height and width! (this happens automatically if you leave them out)
					$logo_src   = 'images/logo.svg'; // relative to /assets/
					$logo_attrs = array(
						'height' => '56',
						'width'  => '130',
						'alt'    => get_bloginfo( 'name' ),
						'src'    => get_asset_url( $logo_src ),
						'class'  => 'uk-preserve uk-preserve-width uk-padding-remove-horizontal',
						'style'  => 'max-width: 100%',
						'uk-svg',
					);

					// Set logo height if necessary to avoid CLS
					if ( empty( $logo_attrs['height'] ) || 'auto' === $logo_attrs['height'] ) {
						$logo_src_path   = get_asset_path( $logo_src );
						$logo_dimensions = svgViewBox( $logo_src_path );
						if ( $logo_dimensions ) {
							$logo_attrs['width'] ?? $logo_dimensions['width'];
							$logo_height          = round( $logo_dimensions['height'] / ( $logo_dimensions['width'] / $logo_attrs['width'] ) );
							$logo_attrs['height'] = $logo_height;
						}
					}

					$home_link_attrs = array(
						'class'      => 'uk-logo',
						'href'       => esc_url( home_url( '/' ) ),
						'title'      => get_bloginfo( 'name' ),
						'aria-label' => 'Home',
						'rel'        => 'home',
					);
					?>

					<?php echo buildAttributes( $home_link_attrs, 'a', buildAttributes( $logo_attrs, 'img' ) ); ?>
				</div>
				<!-- END: Logo -->


				<!-- Main Menu -->
				<div class='nav-overlay uk-navbar-right uk-flex-nowrap uk-flex-stretch' style=" padding-right: 30px;">
					<?php
					// do not add left margin to uk-navbar-right, as it needs auto margin to float right!
					// https://getuikit.com/docs/navbar

					// Use 'items_wrap' to change <ul> attributes, since filter hooks may rely on having a <ul> in the passed HTML.
					if ( has_nav_menu( 'primary' ) ) {
						$menu = wp_nav_menu(
							array(
								'menu_class'     => 'uk-navbar-nav uk-flex-middle uk-visible@l uk-text-small',
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								'container'      => false,
								'theme_location' => 'primary',   // must be registered with register_nav_menu()
								'walker'         => new Walker_UIkit(),
								'echo'           => false,
							)
						);
						echo wp_kses_post( $menu );
					}
					?>
					<div class='uk-flex uk-flex-nowrap'>
						<!-- Hamburger -->
						<a uk-toggle='target: #offcanvas-overlay' class='uk-hidden@l uk-navbar-toggle uk-preserve-width'
							href='#' aria-label='Toggle menu'>
							<span uk-navbar-toggle-icon></span> <span class='uk-margin-small-left uk-visible@s'><?php _e( 'Menu', 'text_domain' ); ?></span>
						</a>
						<!-- END: Hamburger -->
					</div>
				</div>
				<!-- END: Main Menu -->

			</nav>

		<!-- END: Navigation Bar -->


	<?php
	$page_id = get_the_ID();
	if ( have_rows( 'page_header_nav', $page_id ) ) :
		?>
		<div class="page-nav uk-background-white uk-padding-small uk-padding-remove-horizontal">
			<div class="uk-container uk-container-large">
				<div class="uk-width-auto uk-flex-right" uk-grid>
					<?php
					while ( have_rows( 'page_header_nav', $page_id ) ) :
						the_row();
						$link        = get_sub_field( 'nav_link' );
						$link_url    = $link['url'];
						$link_title  = $link['title'];
						$link_target = $link['target'] ? $link['target'] : '_self';
						?>
						<a href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>" class="uk-text-bold"><?php echo esc_html( $link_title ); ?></a>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	<?php elseif ( ( ! is_front_page() && is_home() ) || is_single() || get_field( 'show_resource_menu', $page_id ) ) : ?>
		<div class="resource-nav uk-background-white uk-padding-small uk-padding-remove-horizontal uk-box-shadow-medium uk-visible@m">
			<div class="uk-container uk-container-large">
				<div class="uk-width-auto uk-flex-center" uk-grid>
					<?php
					if ( has_nav_menu( 'resources' ) ) {
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
					}
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>
<!-- END: Sticky Wrapper -->
