<?php
global $theme_location_has_dropdown, $theme_colors, $additional_colors, $preload_fonts, $preload_assets, $scripts_styles_setup, $site_variables;

$theme_location_has_dropdown = array( 'primary' );

/*
  Section: Colors
  Purpose: Theme colors can be used for certain favicons, and for setting up
		   Gutenberg colors.

  Author: Media Proper
  Last updated: 3 January 2021

  NOTE: Don't forget to change the colors in variables.scss!

*/

/* Colors will be added to the Gutenberg editor color palette */
$theme_colors      = array(
	'primary'   => '#03974f',   // Indicates the primary action.
	'secondary' => '#2b246b',   // Indicates an important action.
	'emphasis'  => '#333',   // Could be an alternate text/heading color.
	'muted'     => '#efefef',   // Usually a light, non-white background color.
	'default'   => '#353535',   // Text color.
);
$additional_colors = array(
	'black'     => '#000',
	'white'     => '#fff',
	'lightgray' => '#dee2e6',
	'gray'      => '#adb5bd',
	'darkgray'  => '#343a40',
);


/*
  Section: Preloads
  Purpose: Set up preloading for fonts, scripts, images, whatevs.

  Author: Media Proper
  Last updated: 8 February 2021

*/

/**
 *  Preload initial fonts. Mainly to reduce layout shift.
 *  It's not usually necessary to preload variants such as Italic, Small-Caps, etc.
 *
 *  Specify fontname => [ 400, 500, 700 ], for example, where fontname is the folder name within /assets/fonts (it will be made lowercase)
 *  The woff2 will be preloaded.
 */
$preload_fonts = array(
	'Proxima Nova' => array( 400, 700, '700-rest' ),
);

/**
 *  Preload other things.
 *
 *  For Media Library images, use mp_preload_images() with the image ID.
 *  For scripts, add the handle to $scripts_styles_setup['preload'].

 *  Protip: Small SVG images can be inlined (add the uk-svg attribute), so don't need to be preloaded.
 */
// You can preload images with optional media queries.
$preload_assets = array();
// $preload_assets[] = array(
// 'image/jpeg' => [ wp_get_attachment_image_src(get_post_thumbnail_id(45), 'fullsize')[0] ]
// );


/**
 *  Preload scripts. Adds rel='preload' to the script tag.
 */
$scripts_styles_setup['preload'] = array();


/**
 *  Do not defer these script handles
 *
 *  All .js are deferred, with these exceptions. jQuery is never deferred.
 *  If you have JavaScript that adds classes and reflows the page,
 *  try adding them here, and preloading them, to reduce Cumulative Layout Shift.
 */
$scripts_styles_setup['defer_not'] = array(
	'gforms_recaptcha_frontend',
	'gforms_recaptcha_recaptcha',
	// 'uikit',
	// 'nmi_gateway_woocommerce_credit_card-frontend',
	// 'xl_nmi_wc_collect-js',
);


// Enable/disable icon packs
$scripts_styles_setup['icon_packs'] = array(
	'uikit',
	'ionicons',
	'fontawesome',
);


/**
 *
 * Site Variables
 *
 * New sites: update 'email', 'phone', 'address_pin'
 */

/** Schema
 *
 * https://hallanalysis.com/json-ld-generator/
 *
 * Paste here a generated schema, doesn't need to be complete. Anything here will override what you set in Yoast.
 * It's useful to use a generator to easily get schema fields for @type, opening hours, etc., but since
 * generators don't always allow for multiple emails, telephones, or addresses, we rely on our own data for
 * those. Or should we just read all our data from a schema JSON?
 *
 * For best results, provide multiple high-resolution images (minimum of 50K pixels when multiplying width
 * and height) with the following aspect ratios: 16x9, 4x3, and 1x1.
 *
 * Be sure to include (if needed):
 *      - @type (defaults to Organization)
 *      - hasMap (url)
 *      - openingHours
 *      - menu (url)
 *      - servesCuisine (type of cuisine)
 *      - acceptsReservations (true/false)
 *      - priceRange (how many $?)
 */

$site_variables['schema'] = '{
    "potentialAction": {
        "@type": "SearchAction",
        "target": "' . get_site_url( null, '/?s={search_term_string}' ) . '",
        "query-input": "required name=search_term_string"
      }
}';

/**
 * Google Analytics: set the ID here.
 */
$site_variables['analytics'] = '';


/**
 * Customize maxmium length for post excerpts ( the_excerpt() ), after which [...] will be displayed
 */
add_filter(
	'excerpt_length',
	function() {
		return 35;
	},
	PHP_INT_MAX
);

date_default_timezone_set( 'America/New_York' );
