<?php
define( 'MP_THEME_ASSETS_FOLDER', 'assets' );

// Initialize Carbon Fields.
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'after_setup_theme', 'crb_load' );
function crb_load() {
	require_once 'vendor/autoload.php';
	\Carbon_Fields\Carbon_Fields::boot();

	// Carbon Fields Gutenberg Blocks.
	include_asset( 'php/blocks/slider.php' );
	// include_asset( 'php/blocks/container.php' );
}

/**
 * get_asset_url
 *
 * Returns the assets url to a specified file or folder
 *
 * @param  string $asset
 * @return string
 */
function get_asset_url( string $filename = '' ): string {
	return apply_filters( 'mp_theme_assets_url', get_theme_file_uri( join( '/', array( MP_THEME_ASSETS_FOLDER, ltrim( $filename, '/' ) ) ) ) );
}

/**
 * get_asset_path
 *
 * Returns the assets path to a specified file or folder
 *
 * @param  string $asset
 * @return string
 */
function get_asset_path( string $filename = '' ): string {
	return apply_filters( 'mp_theme_assets_path', get_theme_file_path( join( '/', array( MP_THEME_ASSETS_FOLDER, ltrim( $filename, '/' ) ) ) ) );
}

/**
 * include_asset
 *
 * @param  mixed $filename
 * @return void
 */
function include_asset( string $filename = '' ): void {
	include_once get_asset_path( $filename );
}

$gutenberg_enabled_for = array(
	'page',
	'post',
	'product',
);

/*
For my and your sanity, functions.php is split into several 'component' files.
 * Include or disable them here.
 */
$php_assets = array(
	'helpers',                  // various utility functions
	'uikit-navwalker',          // builds UIkit-friendly navigation menus
	'shortcodes',               // our theme shortcodes
	'_setup',                   // site-specific data
	'functions-images',         // images stuff
	'functions-blocks',         // adjusts how specific Gutenberg blocks are rendered
	'functions-vendor',         // configuration and manipulation of specific add-ons in /assets/vendor
	'site-variables',            // site-specific ACF data
);

// Gravity Forms functions alter page content and extend Gravity Forms on the admin side.
if ( class_exists( 'GFCommon' ) ) {
	$php_assets[] = 'functions-gravity';
	$php_assets[] = 'functions-gravity-client';
}

// WooCommerce functions alter WooCommerce settings and change the way WooCommerce renders elements.
if ( class_exists( 'woocommerce' ) ) {
	$php_assets[] = 'woocommerce/functions-woocommerce';
}

foreach ( $php_assets as $component ) {
	include_asset( "php/{$component}.php" );
}


// Enable Gutenberg for post types listed in $gutenberg_enabled_for
// add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) use($gutenberg_enabled_for){
// return in_array($post_type, $gutenberg_enabled_for) ? true : $use_block_editor;
// }, 10, 2);


// Disable automatic plugin updates
add_filter( 'auto_update_plugin', '__return_false' );

// Custom CSS in WordPress Admin side
function admin_style() {
	wp_enqueue_style( 'admin-styles', get_theme_file_uri( 'assets/css/admin.min.css' ) );
}


/**
 *  functions.php
 *  General site-related functions that may not need to change for every site.
 *  Contents:
 *  - SVG site icons (change $icon_sizes when new devices require more or fewer favicon sizes)
 *  -
 *  (TODO)
 */

// SVG site icons
// https://css-tricks.com/svg-favicons-and-all-the-fun-things-we-can-do-with-them/
$mask_icon = get_asset_url( 'images/logo-icon-black.svg' );    // Safari
$svg_icon  = get_asset_url( 'images/logo-icon.svg' );           // general

// Emoji for development environment site icon
$dev_icon       = '🐳';
$dev_admin_icon = '⚙️';

// These are the site icon sizes we need. Array keys are the rel attribute.
$icon_sizes = array(
	// 'apple-touch-icon' => array( 120, 152, 167 ),
	'icon' => array( 57, 76, 96, 128, 228 ),
	// 'shortcut icon'    => array( 196 ),
	// 'windows'          => array( 144 ),
);


/**
 * Schema Image Sizes
 * You can select/crop these image sizes in the Theme Customizer.
 */

// slug => [ label, description, height, width ]
$schema_image_sizes = array(
	'2x3'      => array( 'Google and Pinterest', '1200×1800px uncropped', 1200, 1800 ),
	'1x1'      => array( 'Article 1:1 (Google)', '1200×1200px cropped', 1200, 1200 ),
	'4x3'      => array( 'Article 4:3 (Google)', '1200×900px cropped', 1200, 900 ),
	'16x9'     => array( 'Article 16:9 (Google)', '1200×675px cropped', 1200, 675 ),
	'facebook' => array( 'Thumbnail (Facebook, etc.)', '1200×630px cropped', 1200, 630 ),
);


// Fav Icons
add_action( 'init', 'mp_default_site_icon' );
/**
 * Set default icons for Development and Staging/Live
 *
 * @return void
 */
function mp_default_site_icon() {
	global $dev_icon, $dev_admin_icon, $svg_icon;
	if ( is_development() ) {
		if ( is_admin() && ! empty( $dev_admin_icon ) ) {
			$icon = $dev_admin_icon;
		} elseif ( ! empty( $dev_icon ) ) {
			$icon = $dev_icon;
		}
		if ( ! empty( $icon ) ) {
			add_filter( 'get_site_icon_url', '__return_true' );
			add_filter(
				'site_icon_meta_tags',
				function ( $meta_tags ) use ( $icon ) {
					return array( mp_emoticon_link( $icon ) );
				},
				20,
				1
			);
		}
	} elseif ( ! has_site_icon() && ! empty( $svg_icon ) ) {
		add_filter(
			'get_site_icon_url',
			function ( $url, $size, $blog_id ) use ( $svg_icon ) {
				return $svg_icon;
			},
			10,
			3
		);
	} else {
		// Add more icon sizes, variations.
		// add_filter( 'site_icon_meta_tags', 'mp_site_icons', 20, 1 );
	}
}


/*
	Section: Register Shortcodes
	Purpose: Tell WordPress that out shortcode functions are shortcodes.
			Shortcode functions are in /assets/php/functions-shortcodes.php.

*/
function mp_register_shortcodes() {
	add_shortcode( 'dynamic_content', 'dynamic_content_shortcode' );
	add_shortcode( 'dynamic_content_by_term', 'dynamic_content_by_term_shortcode' );
	add_shortcode( 'feature', 'feature_shortcode' );
	add_shortcode( 'url', 'url_shortcode' );
	add_shortcode( 'site_name', 'site_name_shortcode' );
	add_shortcode( 'tel', 'tel_shortcode' );
	add_shortcode( 'fax', 'fax_shortcode' );
	add_shortcode( 'email', 'email_shortcode' );
	add_shortcode( 'physical_address', 'physical_address_shortcode' );
	add_shortcode( 'icon', 'icon_shortcode' );
	add_shortcode( 'x', 'x_shortcode' );
	add_shortcode( 'privacy_policy', 'privacy_policy_shortcode' );
	add_shortcode( 'returns_policy', 'returns_policy_shortcode' );
}

/*
	Section: Media Proper Theme Setup
	Purpose: This is the main function for setting everything up and it should
			always stay at the top of this file.

	Last updated: 3 January 2021

*/

function mp_theme_setup() {
	/* update CSS within admin */
	add_action( 'admin_enqueue_scripts', 'admin_style' );

	/* removing WordPress head cruft */
	add_action( 'init', 'mp_head_cleanup' );

	/* register shortcodes */
	add_action( 'init', 'mp_register_shortcodes' );

	/* preload fonts */
	global $preload_fonts;
	if ( ! empty( $preload_fonts ) ) {
		mp_preload_fonts( $preload_fonts );
	}

	/* preload assets */
	global $preload_assets;
	if ( ! empty( $preload_assets ) ) {
		foreach ( $preload_assets as $preload_asset ) {
			mp_preload( $preload_asset );
		}
	}

	/* a better title */
	add_filter( 'wp_title', 'mp_better_title', 10, 3 );

	/* remove versions from rss */
	add_filter( 'the_generator', '__return_empty_string' );

	add_action( 'wp_enqueue_scripts', 'mp_scripts_and_styles', 900 );

	// Remove WordPress global styles.
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
	// Remove Duotone SVG junk that WordPress puts in the <body> tag.
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

	// Use an image placeholder on staging.
	// add_filter('the_content', 'mp_fake_uploads', 15);

	if ( function_exists( 'mp_add_theme_support' ) ) {
		mp_add_theme_support();
	}
	if ( function_exists( 'mp_custom_image_sizes' ) ) {
		mp_custom_image_sizes();
	}
	if ( function_exists( 'mp_custom_post_types' ) ) {
		mp_custom_post_types();
	}
	if ( function_exists( 'mp_custom_taxonomies' ) ) {
		mp_custom_taxonomies();
	}
	if ( function_exists( 'mp_nav_menus' ) ) {
		mp_nav_menus();
	}

	/* remove p tags */
	add_filter( 'the_content', 'mp_filter_ptags_on_images' );
	// remove_filter('the_content', 'wpautop');
	remove_filter( 'the_excerpt', 'wpautop' );

	/* disable TinyMCE auto corrections */
	add_filter( 'tiny_mce_before_init', 'override_mce_options' );
	function override_mce_options( $initArray ) {
		$opts                                 = '*[*]';
		$initArray['valid_elements']          = $opts;
		$initArray['extended_valid_elements'] = $opts;
		return $initArray;
	}

	/* add slug to body class */
	add_filter( 'body_class', 'mp_add_category_to_single' );
	add_filter( 'body_class', 'mp_add_slug_to_single' );

	/* remove yoast auto-generated schema */
	add_filter( 'wpseo_json_ld_output', '__return_empty_array', 10, 1 );
	add_filter( 'disable_wpseo_json_ld_search', '__return_true' );

	/* register the theme sidebars */
	add_action( 'widgets_init', 'mp_register_sidebars' );

	remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'mp_theme_setup' );



/*
	Section: Scripts and Styles
	Purpose: Queue JavaScript & CSS theme dependencies

	Last updated: 8 February 2021

 */
function mp_scripts_and_styles( $script_styles_setup ) {
	if ( ! is_admin() ) {

		// Remove WooCommerce block CSS
		wp_deregister_style( 'wc-blocks-style' );
		wp_register_style( 'wc-blocks-style', false );

		// Remove WordPress block CSS
		wp_deregister_style( 'wp-block-library' );
		wp_register_style( 'wp-block-library', false );

		// Disable loading Noto-Sans Google Font on frontend
		wp_deregister_style( 'wp-editor-font' );
		wp_register_style( 'wp-editor-font', false );

		// Disable Media Cloud video block style, as this adds all kinds of block editor styles to the frontend.
		wp_deregister_style( 'mux_video_block_style' );
		wp_register_style( 'mux_video_block_style', false );

		// Remove dashicons in frontend for unauthenticated users
		// wp_deregister_style( 'dashicons' );
		// wp_register_style( 'dashicons', false );

		// Vendor packages -- for specific pages (register and enqueue by hook)

		// Vendor packages -- for all pages
		// Disable Gravity Forms Invisible reCAPTCHA frontend styles, since all it does
		// is hide the reCAPTCHA badge. We can do this easily in our SCSS.
		add_action( 'gform_enqueue_scripts', 'gform_dequeue_script_list', PHP_INT_MAX );
		function gform_dequeue_script_list() {
			global $wp_styles;
			if ( isset( $wp_styles->registered['gfGoogleCaptchaStylesFrontend'] ) ) {
				unset( $wp_styles->registered['gfGoogleCaptchaStylesFrontend'] );
			}
		}

		// Outdated Browser Rework
		// https://github.com/mikemaccana/outdated-browser-rework
		// wp_enqueue_script(
		// 'outdated-browser-rework',
		// get_asset_url( 'vendor' ) . '/outdated-browser-rework/dist/outdated-browser-rework.min.js',
		// array(),
		// '',
		// false
		// );
		// // Outdated Browser Rework: our custom settings
		// wp_enqueue_script(
		// 'outdated-browser-rework-custom',
		// get_asset_url( 'js' ) . '/custom/outdated-browser-rework-custom.js',
		// array( 'outdated-browser-rework' ),
		// '',
		// false
		// );

		// UIkit: All components + core. ~135kb. You get a slight performance boost in Lighthouse.
		wp_enqueue_script(
			'uikit',
			get_asset_url( 'vendor' ) . '/uikit/dist/js/uikit.min.js',
			array(),
			'3.7.4',
			true
		);

		// UIkit: Core, includes common components. ~88kb. Actual savings may be negligible.
		// wp_enqueue_script(
		// 'uikit',            get_asset_url('vendor') . '/uikit/dist/js/uikit-core.min.js',
		// array(), '3.7.4', true );

		// UIkit: Additional components, only for use with uikit-core.min.js.
		// Hand-pick those you need! (Optimize SCSS by component, too, if you wish.)

		// NOTE: The following only registers the scripts, so that you can
		// either enqueue them only for specific pages, or for all pages.

		// Scripts automatically enqueued by shortcodes:
		// - uk-slider             slider_shortcode()

		// wp_register_script(
		// 'uk-countdown',         get_asset_url('vendor') . "/uikit/dist/js/components/countdown.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-filter',            get_asset_url('vendor') . "/uikit/dist/js/components/filter.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-lightbox-panel',    get_asset_url('vendor') . "/uikit/dist/js/components/lightbox-panel.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-lightbox',          get_asset_url('vendor') . "/uikit/dist/js/components/lightbox.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-notification',      get_asset_url('vendor') . "/uikit/dist/js/components/notification.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-parallax',          get_asset_url('vendor') . "/uikit/dist/js/components/parallax.min.js",
		// array('uikit'), '3.7.4', false );
		// wp_register_script(
		// 'uk-slider-parallax',   get_asset_url('vendor') . "/uikit/dist/js/components/slider-parallax.min.js",
		// array('uikit'), '3.7.4', false );
		// wp_register_script(
		// 'uk-slider',            get_asset_url('vendor') . "/uikit/dist/js/components/slider.min.js",
		// array('uikit'), '3.7.4', false );
		// wp_register_script(
		// 'uk-slideshow-parallax',get_asset_url('vendor') . "/uikit/dist/js/components/slideshow-parallax.min.js",
		// array('uikit'), '3.7.4', false );
		// wp_register_script(
		// 'uk-slideshow',         get_asset_url('vendor') . "/uikit/dist/js/components/slideshow.min.js",
		// array('uikit'), '3.7.4', false );
		// wp_register_script(
		// 'uk-sortable',          get_asset_url('vendor') . "/uikit/dist/js/components/sortable.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-tooltip',           get_asset_url('vendor') . "/uikit/dist/js/components/tooltip.min.js",
		// array('uikit'), '3.7.4' );
		// wp_register_script(
		// 'uk-upload',            get_asset_url('vendor') . "/uikit/dist/js/components/upload.min.js",
		// array('uikit'), '3.7.4' );

		// wp_enqueue_script( 'uk-countdown' );
		// wp_enqueue_script( 'uk-filter' );
		// wp_enqueue_script( 'uk-lightbox-panel' );
		// wp_enqueue_script( 'uk-lightbox' );
		// wp_enqueue_script( 'uk-notification' );
		// wp_enqueue_script( 'uk-parallax' );
		// wp_enqueue_script( 'uk-slider-parallax' );
		// wp_enqueue_script( 'uk-slider' );
		// wp_enqueue_script( 'uk-slideshow-parallax' );
		// wp_enqueue_script( 'uk-slideshow' );
		// wp_enqueue_script( 'uk-sortable' );
		// wp_enqueue_script( 'uk-tooltip' );
		// wp_enqueue_script( 'uk-upload' );

		global $scripts_styles_setup;
		$icon_packs = $scripts_styles_setup['icon_packs'];
		/*
		https://getuikit.com/docs/icon
		* UIkit Icons: Ever-growing library of SVG icons. Integrates with UIkit styles.
		*/
		if ( in_array( 'uikit', $icon_packs ) ) {
			wp_enqueue_script(
				'uikit-icons',
				get_asset_url( 'vendor' ) . '/uikit/dist/js/uikit-icons.min.js',
				array( 'uikit' ),
				'3.7.4',
				true
			);
		}

		/*
		https://ionicons.com/usage/
		* Ionicons: Beautiful SVG icons. Automatic lazy loading.
		*/
		if ( in_array( 'ionicons', $icon_packs ) ) {
			wp_enqueue_script(
				'ionicons-module',
				get_asset_url( 'vendor' ) . '/ionicons/dist/ionicons/ionicons.esm.js',
				array(),
				'5.5.3',
				true
			);

			wp_enqueue_script(
				'ionicons',
				get_asset_url( 'vendor' ) . '/ionicons/dist/ionicons/ionicons.js',
				array(),
				'5.5.3',
				true
			);
		}

		/*
		https://fontawesome.com/how-to-use/on-the-web/setup/hosting-font-awesome-yourself
		* FontAwesome: Overused SVG icons. You need the core (fontawesome.min.js) plus
		* any specific style(s) for the site.
		*/
		if ( in_array( 'fontawesome', $icon_packs ) ) {
			// wp_enqueue_script(
			// 'fontawesome-free', get_asset_url('vendor') . '/@fortawesome/fontawesome-free/js/fontawesome.min.js',
			// array(), '5.15.4', true );
			// wp_enqueue_script(
			// 'fontawesome-free', get_asset_url('vendor') . '/@fortawesome/fontawesome-free/js/brands.min.js',
			// array(), '5.15.4', true );
			// wp_enqueue_script(
			// 'fontawesome-free', get_asset_url('vendor') . '/@fortawesome/fontawesome-free/js/solid.min.js',
			// array(), '5.15.4', true );
			// wp_enqueue_script(
			// 'fontawesome-free', get_asset_url('vendor') . '/@fortawesome/fontawesome-free/js/regular.min.js',
			// array(), '5.15.4', true );
			wp_enqueue_style(
				'fontawesome-stylesheet',
				get_asset_url( 'vendor' ) . '/@fortawesome/fontawesome-free/css/all.min.css',
				array(),
				'5.15.4',
				'all'
			);
		}

		// Theme CSS & JavaScript (site-wide)
		wp_enqueue_script(
			'mp-custom-js',
			get_asset_url( 'js' ) . '/custom.min.js',
			array( 'jquery' ),
			null,
			true
		);
		wp_enqueue_style(
			'mp-css',
			get_asset_url( 'css' ) . '/style.min.css',
			array(),
			null,
			'all'
		);

		wp_register_script( 'counter', get_template_directory_uri() . '/assets/vendor/jquery-number-counter/jquery.animateNumbers.min.js', array( 'jquery' ), '', true );

		// // Scripts and styles to enqueue only for the front page
		if ( is_front_page() ) {
			// wp_enqueue_style( 'mp-front-css' );
			// Remove Gutenberg Block Library CSS from loading on the frontend (front page only)
			// wp_dequeue_style( 'wp-block-library' );
			// wp_dequeue_style( 'wp-block-library-theme' );
			// wp_dequeue_style( 'fontawesome-stylesheet' );
		}

		// Scripts and styles to enqueue only for the 'single.php' post type
		if ( is_page_template( 'single.php' ) ) {
			// wp_enqueue_style( 'mp-single-css' );
		}

		// Dequeue the SearchWP Live Search styles.
		// https://searchwp.com/extensions/live-search/
		wp_dequeue_style( 'searchwp-live-search' );

		// Dequeue any styles that have dynamic slugs
		global $wp_styles;
		foreach ( $wp_styles->queue as $style ) :
			if ( str_contains( $style, 'sv-wc-payment-gateway-payment-form' ) ) {
				wp_dequeue_style( $style );
			}
		endforeach;
	}
}


add_action( 'enqueue_block_editor_assets', 'mp_block_editor_assets_enqueue' );
function mp_block_editor_assets_enqueue() {
	wp_enqueue_script(
		'mp-block-variations',
		get_asset_url( 'js' ) . '/block-variations.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ),
		filemtime( get_asset_path( 'js/block-variations.js' ) )
	);
}

add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_script(
			'mp-gutenberg-filters',
			get_asset_url( 'js/gutenberg-filters.js' ),
			array( 'wp-edit-post' ),
			filemtime( get_asset_path( 'js/gutenberg-filters.js' ) )
		);
	}
);



/**
 * Outputs <link rel='preload' ...> in <head>
 *
 * @param array $preload... keys are the 'type', values are the 'href'
 */
function mp_preload( ...$assets ) {
	if ( empty( $assets ) || is_admin() ) {
		return;
	}

	foreach ( $assets as $asset ) {
		foreach ( $asset as $type => $url ) {

			// printf ("Preload %s: %s (%s)", $type, $url, wp_check_filetype($url)['type']);

			$as    = strtok( $type, '/' );
			$attrs = array(
				'rel'  => 'preload',
				'type' => $type,
				'as'   => $as,
			);
			if ( $as === 'font' ) {
				$attrs['crossorigin'] = '';
			}

			// Does this have media queries?
			if ( ! is_array( $url ) ) {
				$url = array( $url );
			}

			foreach ( $url as $query => $url ) {
				$attrs['href'] = esc_url( $url );
				if ( ! is_int( $query ) ) {
					$attrs['media'] = sprintf( '%s', $query );
				}

				add_action(
					'wp_head',
					function () use ( $attrs ) {
						echo buildAttributes( $attrs, 'link' );
					}
				);
			}
		}
	}
}


// For handles that are *-module, add type=module to <script> tag
// Also, add preload attribute to those we want to preload.
add_filter( 'script_loader_tag', 'mp_script_loader_tag', 10, 3 );
function mp_script_loader_tag( $tag, $handle, $src ) {
	if ( is_admin()
	|| false === strpos( $src, '.js' )
	|| 'jquery-core' === $handle
	) {
		return $tag;
	}

	global $scripts_styles_setup;
	extract( $scripts_styles_setup );

	$ext         = pathinfo( parse_url( $src, PHP_URL_PATH ), PATHINFO_EXTENSION );
	$is_external = isExternalURL( $src );

	$is_module       = str_contains( $handle, '-module' ) || str_contains( $src, '.esm.' ) || $ext === 'mjs';
	$is_wp           = str_contains( $src, 'wp-includes/js' );
	$is_ionicons     = str_contains( $handle, 'ionicons' );
	$to_preload      = in_array( $handle, $preload );
	$can_be_deferred = ( ! in_array( $handle, $defer_not, true ) && ! str_contains_any( $handle, array( 'gateway', 'credit_card', 'collect' ) ) ) && ! $is_module;

	$dom     = mp_load_html( $tag );
	$scripts = $dom->getElementsByTagName( 'script' );
	foreach ( $scripts as $script ) {
		$is_deferred  = $script->hasAttribute( 'defer' );
		$is_preloaded = ( $script->hasAttribute( 'rel' ) && $script->getAttribute( 'rel' ) === 'preload' );

		// Modules, set type=module
		if ( $is_module ) {
			$script->setAttribute( 'type', 'module' );
		}

		// Preload, set rel=preload
		if ( ! $is_preloaded && $to_preload ) {
			$script->setAttribute( 'rel', 'preload' );
		}

		// Ionicons, which is not module, set nomodule
		if ( $is_ionicons && ! $is_module ) {
			$script->setAttribute( 'nomodule', null );
		}

		// Non-WordPress scripts, set defer or async (for external)
		if ( $can_be_deferred && ( ! $is_wp && ! $is_deferred ) ) {
			$defer = $is_external ? 'async' : 'defer';
			$script->setAttribute( $defer, null );
		}

		// Do not allow user-defined preloading when logged into WordPress Admin
		if ( is_user_logged_in() ) {
			$script->removeAttribute( 'rel' );
		}
	}

	$tag = mp_save_html( $dom );
	return $tag;
}


/**
 * Preloads fonts in <head>
 *
 * @param array $fonts key = font folder name (such as 'garamond'), value = weights, i.e., array(400, 700, 900)
 */
function mp_preload_fonts( $fonts ) {
	if ( ! is_array( $fonts ) ) {
		return;
	}
	foreach ( $fonts as $font => $weights ) {
		$font = str_replace( ' ', '-', strtolower( $font ) );
		foreach ( $weights as $weight ) {
			preg_match( '/(?P<weight>\d{3})-*(?P<subset>\w+)*/', $weight, $weight_subset );
			$subset = empty( $weight_subset['subset'] ) ? 'english' : $weight_subset['subset'];
			$weight = empty( $weight_subset['weight'] ) ? '400' : $weight_subset['weight'];
			mp_preload( array( 'font/woff2' => get_asset_url( "fonts/{$font}/{$weight}/{$font}-{$weight}-{$subset}.woff2" ) ) );
		}
	}
}

/**
 * Preload image(s) in the WordPress Media Library in <head>
 *
 * @param array $ids The image IDs.
 */
function mp_preload_images( ...$images ) {
	$images = to_array( $images );
	foreach ( $images as $img ) {
		// If a URL, try to get post ID.
		if ( ! is_numeric( $img ) ) {
			$img = attachment_url_to_postid( $img );
		}
		if ( $img ) {
			// Add MIME type so browser can decide if it supports this file.
			$mime = wp_get_image_mime( wp_get_original_image_path( $img ) );
			$type = $mime ? $mime : 'image/jpeg';
			$src  = wp_get_attachment_image_url( $img, 'fullsize' );

			$attrs = array(
				'rel'  => 'preload',
				'type' => $type,
				'as'   => 'image',
				'href' => $src,
			);

			add_action(
				'wp_head',
				function () use ( $attrs ) {
					echo buildAttributes( $attrs, 'link' );
				}
			);
		}
	}
}

/**
 * Return true if we're in the development environment.
 *
 * @return boolean
 */
function is_development() {
	$parse_url = wp_parse_url( get_site_url() );
	return ( strpos( $parse_url['host'], '.docker' ) !== false );
}


function mp_site_icons( $meta_tags ) {
	global $primary_theme_color, $icon_sizes, $mask_icon;

	// Generate tags for more icon sizes.
	foreach ( $icon_sizes as $rel => $sizes ) {
		foreach ( $sizes as $size ) {
			$icon = get_site_icon_url( $size );
			if ( $icon ) {
				switch ( $rel ) {
					case 'icon':
						$meta_tags[] = sprintf( '<link rel="icon" href="%s" sizes="%sx%s" />', esc_url( $icon ), $size, $size );
						break;
					case 'apple-touch-icon':
						$meta_tags[] = sprintf( '<link rel="apple-touch-icon" href="%s" sizes="%sx%s" />', esc_url( $icon ), $size, $size );
						break;
					case 'windows':
						$meta_tags[] = sprintf( '<meta name="msapplication-TileImage" content="%s" />', esc_url( $icon ) );
						break;
				}
			}
		}
	}

	// Add Windows stuff
	$meta_tags[] = sprintf( "<meta name='application-name' content='%s'>", get_bloginfo( 'name' ) );
	$meta_tags[] = sprintf( "<meta name='msapplication-TileColor' content='%s'>", $primary_theme_color );

	// Mask icon for Safari pinned tabs
	if ( ! empty( $mask_icon ) && @mime_content_type( $mask_icon ) ) {
		$link = mp_emoticon_link(
			$mask_icon,
			array(
				'rel'   => 'mask-icon',
				'color' => $primary_theme_color,
			)
		);
		if ( ! empty( $link ) ) {
			$meta_tags[] = $link;
		}
	}

	$meta_tags = apply_filters( 'site_icon_meta_tags', $meta_tags );
	$meta_tags = array_filter( $meta_tags );

	return $meta_tags;
}

/**
 * Define site icon sizes
 *
 * @param $meta_tags
 *
 * @return array
 */
function mp_site_icon_sizes( $sizes ) {
	global $icon_sizes;
	if ( function_exists( 'flatten' ) ) {
		array_push( $sizes, flatten( $icon_sizes ) );
	}
	return $sizes;
}
add_filter( 'site_icon_image_sizes', 'mp_site_icon_sizes' );


/*
Section: Editor Color Palette
Purpose: Register theme colors for WordPress editor

Last updated: 11 December 2021

*/
function ea_setup() {
	// Disable Custom Colors
	// add_theme_support( 'disable-custom-colors' );

	// Padding Controls for Blocks like columns, etc. Probably leave this off.
	// add_theme_support( 'custom-spacing' );

	// Editor Color Palette
	$palette = array();
	foreach ( array( 'primary', 'secondary', 'emphasis', 'muted' ) as $color ) {
		foreach ( array( 'lighter', 'light', '', 'dark', 'darker' ) as $tone ) {
			$slug      = strtolower( trim_join( '-', $color, $tone ) );
			$name      = ucwords( str_replace( '-', ' ', $slug ) );
			$palette[] = array(
				'name'  => esc_attr__( ucwords( $name ), 'text_domain' ),
				'slug'  => $slug,
				'color' => "var(--{$slug})",
			);
		}
	}
	$palette[] = array(
		'name'  => esc_attr__( 'Default', 'text_domain' ),
		'slug'  => 'default',
		'color' => '#6E6E6E',
	);
	$palette[] = array(
		'name'  => esc_attr__( 'White', 'text_domain' ),
		'slug'  => 'white',
		'color' => '#ffffff',
	);
	$palette[] = array(
		'name'  => esc_attr__( 'Black', 'text_domain' ),
		'slug'  => 'black',
		'color' => '#000000',
	);
	add_theme_support( 'editor-color-palette', $palette );

	// Editor Font Sizes
	$base       = 15;
	$font_sizes = array(
		'small'   => round( $base * ( 5 / 6 ) ),
		'medium'  => round( $base * 1.35 ),
		'large'   => round( $base * 1.5 ),
		'xlarge'  => round( $base * 1.9 ),
		'2xlarge' => round( $base * 2.625 ),
	);
	foreach ( $font_sizes as $font_size => $px ) {
		$editor_font_sizes[] = array(
			'name' => esc_attr__( $font_size, 'text_domain' ),
			'size' => $px,
			'slug' => $font_size,
		);
		add_theme_support( 'editor-font-sizes', $editor_font_sizes );
	}
}
add_action( 'after_setup_theme', 'ea_setup' );


/*
	Section: Custom Post Types
	Purpose: Support custom post types by 'require_once'

	Author: Media Proper
	Last updated: 9 December 2020

*/
function mp_custom_post_types() {
	include_once 'custom-post-types/team-post-type.php';
	include_once 'custom-post-types/testimonial-post-type.php';
	include_once 'custom-post-types/casestudy-post-type.php';
	include_once 'custom-post-types/press-post-type.php';
	include_once 'custom-post-types/wiki-post-type.php';
}

/*
	Section: Custom Taxonomies
	Purpose: Create custom taxonomies

	Author: Media Proper
	Last updated: 4 May 2021

*/
function mp_custom_taxonomies() { }


/*
	Section: Add Theme Support
	Purpose: Register theme support for certain features

	Last updated: 9 December 2020

*/
function mp_add_theme_support() {
	/* post formats */
	add_theme_support(
		'post-formats',
		array(
			'aside',
			'quote',
		)
	);

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'gallery' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );

	// add_post_type_support( 'product', 'excerpt' );

	// Responsive video embeds
	add_theme_support( 'responsive-embeds' );

	// Allow editor style.
	add_theme_support( 'editor-styles' );
	add_editor_style( '/assets/css/editor-style.min.css' );

	/* WooCommerce */
	if ( class_exists( 'woocommerce' ) ) {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width'         => 80,
				'gallery_thumbnail_image_width' => 400,
				'single_image_width'            => 972,
			)
		);
		// add_theme_support( 'wc-product-gallery-zoom' );
		// add_theme_support( 'wc-product-gallery-slider' );
		// add_theme_support( 'wc-product-gallery-lightbox' );
	}

	// Enable Yoast breadcrumbs
	if ( class_exists( 'WPSEO_Options' ) ) {
		add_theme_support( 'yoast-seo-breadcrumbs' );
	}
	if ( class_exists( 'RankMath' ) ) {
		add_theme_support( 'rank-math-breadcrumbs' );
	}
}

/*
	Section: Navigation Menus
	Purpose: Register navigation menu locations

	Last updated: 9 December 2020

*/
function mp_nav_menus() {
	register_nav_menus(
		array(
			'top'       => __( 'Top Navigation', 'text_domain' ),
			'primary'   => __( 'Primary Navigation', 'text_domain' ),
			'mobile'    => __( 'Mobile (Off-Canvas) Navigation', 'text_domain' ),
			'resources' => __( 'Resources Navigation', 'text_domain' ),
			'footer'    => __( 'Footer Navigation', 'text_domain' ),
			'footer2'   => __( 'Footer Service Navigation', 'text_domain' ),
		)
	);
}

/*
	Section: Define Sidebars
	Purpose: Build the definitions for theme sidebars

	Last updated: 9 December 2020

*/
function mp_register_sidebars() {
	register_sidebar(
		array(
			'id'            => 'blog-sidebar',
			'name'          => __( 'Resource Page Sidebar', 'mpbase' ),
			'description'   => __( 'Resource sidebar area', 'mpbase' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widgettitle uk-h3">',
			'after_title'   => '</div>',
		)
	);
	register_sidebar(
		array(
			'id'            => 'single-resource',
			'name'          => __( 'Single Resource Sidebar', 'mpbase' ),
			'description'   => __( 'Single resource sidebar area', 'mpbase' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widgettitle uk-h3">',
			'after_title'   => '</div>',
		)
	);

	register_sidebar(
		array(
			'id'            => 'footer-widget',
			'name'          => __( 'Footer Widget', 'mpbase' ),
			'description'   => __( 'Footer widget area', 'mpbase' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widgettitle uk-h3">',
			'after_title'   => '</div>',
		)
	);
	register_sidebar(
		array(
			'id'            => 'footer-menu',
			'name'          => __( 'Footer Menu', 'mpbase' ),
			'description'   => __( 'Footer menu area', 'mpbase' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widgettitle uk-h3">',
			'after_title'   => '</div>',
		)
	);
	register_sidebar(
		array(
			'id'            => 'copyright',
			'name'          => __( 'Copyright', 'mpbase' ),
			'description'   => __( 'Copyright area widget', 'mpbase' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widgettitle uk-h3">',
			'after_title'   => '</div>',
		)
	);
}

/*
	Section: <head> cleanup
	Purpose: Remove WordPress cruft from the <head> tag

	Author: Media Proper
	Last updated: 9 December 2020

*/
function mp_head_cleanup() {
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// Windows Live Writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	// Feed links
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'body_class', 'feed_links', 2 );
}


/*
	Section: Better Title
	Purpose: Generate better page titles

	Author: Media Proper
	Last updated: 9 December 2020

*/
function mp_better_title( $title, $sep, $seplocation ) {
	global $page, $paged;

	// Don't affect in feeds.
	if ( is_feed() ) {
		return $title;
	}

	// Add the blog's name
	if ( 'right' === $seplocation ) {
		$title .= get_bloginfo( 'name' );
	} else {
		$title = get_bloginfo( 'name' ) . $title;
	}

	// Add the blog description for the front page.
	$site_description = get_bloginfo( 'description', 'display' );

	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " {$sep} {$site_description}";
	}

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
		$title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );
	}

	return $title;
}

/*
	Section: Filter <p> tags on images
	Purpose: <p><img></p> -> <img>

	Author: Media Proper
	Last updated: 9 December 2020

*/
function mp_filter_ptags_on_images( $content ) {
	return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}


/*
	Section: Page and post slugs classnames
	Purpose: Add page template and post slugs to <body>

	Author: Media Proper
	Last updated: 9 December 2020

*/
function mp_add_category_to_single( $classes ) {
	global $wp_query;
	$page = '';
	if ( is_front_page() ) {
		$page = 'home';
	} elseif ( is_page() ) {
		$page = $wp_query->query_vars['pagename'];
	}

	$classes[] = $page;
	return $classes;
}

function mp_add_slug_to_single( $classes ) {
	global $post;
	if ( isset( $post ) ) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
}



/*
	Section: Disable Emojis
	Purpose: Drop support for emojis

	Author: Media Proper
	Last updated: 9 December 2020

*/
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter(
		'tiny_mce_plugins',
		function ( $plugins ) {
			return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
		},
		10,
		1
	);
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param  array  $plugins
 * @return array  Difference betwen the two arrays
 */
// function disable_emojis_tinymce($plugins) {
// return is_array($plugins) ? array_diff($plugins, array( 'wpemoji' )) : [];
// }


function mp_customize_register( $wp_customize ) {
	if ( class_exists( 'WP_Customize_Cropped_Image_Control' ) ) {
		$wp_customize->add_section(
			'mp_schema',
			array(
				'title'       => __( 'SEO Schema', 'mediaproper' ),
				'priority'    => 30,
				'description' => __( '<p>Google forever wants more image sizes to decorate search results: "For best results, provide multiple high-resolution images with the following aspect ratios: 16×9, 4×3, and 1×1." And there are a few more.</p><p><strong>🖼 You will be able to crop the image after you select it.</strong></p>', 'mediaproper' ),
			)
		);

		global $schema_image_sizes;
		foreach ( $schema_image_sizes as $slug => $data ) {
			$setting             = "schema_image_{$slug}";
			$control             = "schema_image_{$slug}_control";
			$control_label       = __( $data[0], 'mediaproper' );
			$control_description = __( $data[1], 'mediaproper' );

			$wp_customize->add_setting(
				$setting,
				array()
			);
			$wp_customize->add_control(
				new WP_Customize_Cropped_Image_Control(
					$wp_customize,
					$control,
					array(
						'label'         => $control_label,
						'description'   => $control_description,
						'priority'      => 20,
						'section'       => 'mp_schema',
						'mime_type'     => 'image',
						'settings'      => $setting,
						'width'         => $data[2],
						'height'        => $data[3],
						'button_labels' => array(// All These labels are optional
							'select' => __( 'Select Image', 'mediaproper' ),
							'remove' => __( 'Remove Image', 'mediaproper' ),
							'change' => __( 'Change Image', 'mediaproper' ),
						),
					)
				)
			);
		}
	}
}
add_action( 'customize_register', 'mp_customize_register' );



/**
 * Convert emoticon or <svg> string to inline data: string.
 *
 * @param string $icon
 * @return void
 */
function mp_emoticon_data( string $icon ) {
	if ( strpos( $icon, '<svg ' ) ) {
		$base64 = base64_encode( $icon );
	} elseif ( has_emoji( $icon ) ) {
		$base64 = base64_encode( sprintf( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><text x="0" y="14">%s</text></svg>', $icon ) );
	}
	if ( ! empty( $base64 ) ) {
		return "data:image/svg+xml;base64,{$base64}";
	}
}

/**
 * mp_emoticon_link
 *
 * @param   string $icon   Emoticon, <svg></svg> block, or filename
 * @return  string  <link> tag that inlines a block of <svg> xml, or wraps an emoticon, for use as a site icon.
 */
function mp_emoticon_link( string $icon, $attrs = array() ) {
	if ( strpos( $icon, '<svg ' ) || has_emoji( $icon ) ) {
		$data = mp_emoticon_data( $icon );
	} elseif ( @mime_content_type( $icon ) ) {
		$attrs['href'] = esc_url( $icon );
		$attrs['type'] = mime_content_type( $icon );
	}

	if ( empty( $attrs['rel'] ) ) {
		$attrs['rel'] = 'icon';
	}
	// if(!empty($base64) && empty($attrs['type'])) $attrs['type'] = 'image/svg+xml';
	if ( empty( $attrs['href'] ) ) {
		$attrs['href'] = $data;
	}

	return empty( $attrs['href'] ) ? '' : buildAttributes( $attrs, 'link' );
}


/**
 * WordPress site login screen (/wp-admin)
 */
add_action(
	'login_enqueue_scripts',
	function () { ?>
<style type='text/css'>
	#login h1 a,
	.login h1 a {
		background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.svg');
		height: 80px;
		width: 280px;
		background-size: contain;
		background-position: center center;
		background-repeat: no-repeat;
		padding-bottom: 10px;
	}
</style>
		<?php
	}
);

// Text appears above the login form at /wp-admin.
add_filter(
	'login_message',
	function () {
		_e( 'Please enter your administrator login and password.', 'text_domain' );
	}
);
// Header image on /wp-admin login page links to wordpress.org by default. Change that here.
add_filter(
	'login_headerurl',
	function ( $login_header_url ) {
		return esc_url( home_url( '/' ) );
	},
	10,
	1
);

// Allow more types of HTML tags in notices, etc.
// uk-toggle WILL NOT WORK in WooCommerce messages unless you enable that attribute here.
// Use this to add other allowed UIkit attributes
add_filter(
	'wp_kses_allowed_html',
	function ( $allowed_html ) {
		foreach ( $allowed_html as &$tag ) {
			$tag += array(
				'uk-alert'  => true,
				'uk-toggle' => true,
				'uk-margin' => true,
				'uk-grid'   => true,
				'uk-modal'  => true,
				'uk-close'  => true,
				'uk-icon'   => true,
				'uk-leader' => true,
				'hidden'    => true,
			);
		}
		$proper_allowed_html = array(
			'img'      => array(
				'uk-cover' => true,
			),
			'li'       => array(
				'uk-slideshow-item' => true,
				'uk-slider-item'    => true,
			),
			'a'        => array(
				'uk-slidenav-next'     => true,
				'uk-slidenav-previous' => true,
			),
			'span'     => array(
				'data-*' => true,
			),
			'ion-icon' => array(
				'name'       => true,
				'class'      => true,
				'role'       => true,
				'aria-label' => true,
			),
			'svg'      => true,
			'g'        => true,
			'path'     => true,
		);

		return array_merge_recursive( $allowed_html, $proper_allowed_html );
	},
	10,
	1
);

// disable automatic full screen mode in backend
if ( is_admin() ) {
	function jba_disable_editor_fullscreen_by_default() {
		$script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
		wp_add_inline_script( 'wp-blocks', $script );
	}
	add_action( 'enqueue_block_editor_assets', 'jba_disable_editor_fullscreen_by_default' );
}

// acf block register
add_action( 'acf/init', 'mp_acf_blocks_init' );
function mp_acf_blocks_init() {
	if ( function_exists( 'acf_register_block_type' ) ) {
		acf_register_block_type(
			array(
				'name'              => 'stats',
				'title'             => __( 'Stats' ),
				'description'       => __( 'A block showing stats.' ),
				'render_template'   => 'partials/blocks/stats.php',
				'category'          => 'formatting',
			)
		);
	}
		acf_register_block_type(
			array(
				'name'              => 'new-stats',
				'title'             => __( 'new-stats' ),
				'description'       => __( 'A custom new-stats block.' ),
				'render_template'   => 'partials/blocks/new-stats.php',
				'category'          => 'tools',
				'icon'              => 'list-view',
				'keywords'          => array( 'new-stats' ),
				'mode'              => 'edit',
			)
		);
	acf_register_block_type(
		array(
			'name'              => 'accordion',
			'title'             => __( 'Accordion' ),
			'description'       => __( 'A custom accordion block.' ),
			'render_template'   => 'partials/blocks/accordion.php',
			'category'          => 'tools',
			'icon'              => 'list-view',
			'keywords'          => array( 'accordion' ),
			'mode'              => 'edit',
		)
	);
	acf_register_block_type(
		array(
			'name'              => 'blog',
			'title'             => __( 'Blog' ),
			'description'       => __( 'A custom blog block.' ),
			'render_template'   => 'partials/blocks/blog.php',
			'category'          => 'tools',
			'icon'              => 'list-view',
			'keywords'          => array( 'blog' ),
			'mode'              => 'edit',
		)
	);
}

if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title'  => 'Theme Options',
			'menu_title'  => 'Theme Options',
			'menu_slug'   => 'theme-options',
			'capability'  => 'edit_posts',
			'redirect'    => false,
		)
	);
}

function mp_wp_remove_global_css() {
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	wp_dequeue_style( 'global-styles' );
}
add_action( 'init', 'mp_wp_remove_global_css' );

remove_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );
remove_filter( 'render_block', 'gutenberg_render_layout_support_flag', 10, 2 );

/*
	Section: Page Navigation
	Purpose: Generate pagination for results of a posts query

	Author: Media Proper
	Last updated: 9 December 2020

*/
function mp_page_navi( $postquery ) {
	// global $wp_query;
	$wp_query   = $postquery;
	$post_count = $wp_query->found_posts;
	if ( $post_count % get_option( 'posts_per_page' ) === 0 ) {
		$total_pages = ( $post_count / get_option( 'posts_per_page' ) );
	} else {
		$total_pages = $post_count / get_option( 'posts_per_page' ) + 1;
	}
	$bignum = 999999999;
	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}
	echo '<nav class="pagination">';
	$return = paginate_links(
		array(
			'base'      => str_replace( $bignum, '%#%', esc_url( get_pagenum_link( $bignum ) ) ),
			'format'    => '',
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'type'      => 'list',
			'end_size'  => 3,
			'mid_size'  => 3,
			'total'     => $total_pages,
		)
	);
	echo str_replace( "<ul class='page-numbers'>", '<ul class="uk-pagination uk-flex-right@m">', $return );
	echo '</nav>';
}

function mp_search_filter( $query ) {
	if ( $query->is_search ) {
		$query->set( 'posts_per_page', '999' );
	}
}
add_filter( 'pre_get_posts', 'mp_search_filter' );
