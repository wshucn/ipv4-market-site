<?php
/*
  Section: Images
  Purpose: Alter how WordPress images work and render.

  Author: Media Proper
  Last updated: 14 June 2021

*/


// Disable maximum srcset image width.
add_filter( 'max_srcset_image_width', '__return_false' );


// Disable WP Gallery stylesheet.
add_filter( 'gallery_style', '__return_empty_string' );


/**
 * mp_custom_image_sizes
 *
 * Define additional custom image sizes
 * Our function can specify size-specific imgix query parameters
 */
// These are defined in UIkit:
// $breakpoint-small:      640px;
// $breakpoint-medium:     960px;
// $breakpoint-large:      1200px;
// $breakpoint-xlarge:     1600px;
// $width-small-width:                              150px;
// $width-medium-width:                             300px;
// $width-large-width:                              450px;
// $width-xlarge-width:                             600px;
// $width-2xlarge-width:                            750px;
$mp_custom_sizes = array(
	// 'default-100vw'         => 304,
	'small-100vw'  => 610,
	'medium-100vw' => 890,
	'large-100vw'  => 1130,
	'larger-100vw' => 1530,
	'theme-small'  => 150,
	// 'theme-medium'          => 300,
	'theme-large'  => 450,
	// 'theme-larger'          => 600,
	// 'theme-largest'         => 750,
);

// add_action( 'after_setup_theme', 'mp_custom_image_sizes' );
function mp_custom_image_sizes() {
	// mp_add_image_size( string $name, int $width, int $height, bool|array $crop = false, $query = array() )
	global $mp_custom_sizes;
	if ( is_array( $mp_custom_sizes ) ) {
		foreach ( $mp_custom_sizes as $name => $w ) {
			mp_add_image_size( $name, $w );
		}
	}

	// Pretty much all our normal image sizes are not cropped, apart from thumbnail.
	// Define some larger cropped thumbnail sizes with the same aspect ratio, for screens with higher pixel densities.
	mp_add_image_size( 'thumbnail-1x', get_option( 'thumbnail_size_w' ), get_option( 'thumbnail_size_h' ), true, array( 'crop' => 'edges' ) );
	mp_add_image_size( 'thumbnail-2x', get_option( 'thumbnail_size_w' ) * 2, get_option( 'thumbnail_size_h' ) * 2, true, array( 'crop' => 'edges' ) );
	mp_add_image_size( 'thumbnail-3x', get_option( 'thumbnail_size_w' ) * 3, get_option( 'thumbnail_size_h' ) * 3, true, array( 'crop' => 'edges' ) );
}

function mp_add_tags_to_attachments() {
	 register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
add_action( 'init', 'mp_add_tags_to_attachments' );




// add_filter( 'wp_calculate_image_srcset', 'mp_calculate_image_srcset', 10, 5 );
function mp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
	pp( $sources );
	return $sources;
}

/**
 * Add image size, and image processor parameters. Just like the normal way to add sizes except
 * you can tie the image processing query to it. One use case could be for headshots, if you
 * want to have the processor automatically crop/pad with facial recognition. Another is if you
 * would like an image size (heros?) to be overlaid on-the-fly with a color or gradient.
 *
 * @param           $name           slug for size
 * @param           $width
 * @param           $height
 * @param           $crop           true/false, or, for instance, array('left', 'top')
 * @param   array                                                             $query_args     array('param1' => 'value1', 'param2' => 'value2', etc.) to send to image processor (such as imgix)
 */
function mp_add_image_size( $name, $width = 0, $height = 0, $crop = false, $query_args = array() ) {
	global $_wp_additional_image_sizes;
	$_wp_additional_image_sizes[ $name ] = array(
		'width'  => absint( $width ),
		'height' => absint( $height ),
		'crop'   => $crop,
	);
	if ( ! empty( $query_args ) ) {
		$_wp_additional_image_sizes[ $name ]['query'] = $query_args;
	}
}

/**
 * mp_get_attachment_image_attributes_imgix
 *
 * With mp_add_image_size(), applies size-specific imgix parameters to image URLs
 */
add_filter( 'wp_get_attachment_image_attributes', 'mp_get_attachment_image_attributes_imgix', 10, 3 );
function mp_get_attachment_image_attributes_imgix( $attrs, $attachment, $size ) {
	if ( is_admin() ) {
		return $attrs;
	}
	$wp_additional_image_sizes = wp_get_additional_image_sizes();
	@$imgix_attrs              = $wp_additional_image_sizes[ $size ]['query'];
	if ( ! empty( $imgix_attrs ) ) {
		foreach ( $attrs as &$attr ) {
			$attr = mp_imgix_attrs( $attr, $imgix_attrs );
		}
	}
	return $attrs;
}

/**
 * mp_get_attachment_image_attributes_imgix_crop
 *
 * Apply size-specific crop to all srcset urls.
 */
// add_filter( 'wp_get_attachment_image_attributes', 'mp_get_attachment_image_attributes_imgix_crop', 20, 3 );
function mp_get_attachment_image_attributes_imgix_crop( $attrs, $attachment, $size ) {
	if ( is_admin() ) {
		return $attrs;
	}
	if ( 'full' === $size ) {
		return $attrs;
	}

	$query_attrs = mp_split_query( $attrs['src'] );
	$query_attrs = array_diff_key( $query_attrs, array_flip( array( 'w', 'h', 'wpsize' ) ) );
	if ( ! empty( $query_attrs ) ) {
		foreach ( $attrs as &$attr ) {
			$attr = mp_imgix_attrs( $attr, $query_attrs, true );
		}
	}
	return $attrs;
}


/**
 * Fix an issue where SVGs don't display properly in the Media Library
 */
function fix_svg_thumb_display() {
	echo buildAttributes(
		array(
			'type'  => 'text/css',
			'media' => 'screen',
		),
		'style',
		'.image-icon.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { width: 60px !important;  height: auto !important; }'
	);
}
add_action( 'admin_head', 'fix_svg_thumb_display' );

/**
 * Change src and srcset to data-src and data-srcset, add UIkit 'uk-img' attribute
 * Modified from: jer0dh
 *
 * @link https://jhtechservices.com/changing-your-image-markup-in-wordpress/
 * @param $attributes
 *
 * @return mixed
 */
function mp_change_attachment_image_markup( $attrs, $attachment, $size ) {
	if ( is_admin() ) {
		return $attrs;
	}

	if ( empty( $attrs['width'] ) || empty( $attrs['height'] ) ) {
		$src = wp_get_attachment_image_src( $attachment->ID, $size );
		if ( ! empty( $src[1] ) ) {
			$attrs['width'] = $src[1];
		}
		if ( ! empty( $src[2] ) ) {
			$attrs['height'] = $src[2];
		}
	}

	if ( $attachment->post_mime_type === 'image/svg+xml' && filesize( get_attached_file( $attachment->ID ) ) > 100000 ) {
		// inline svgs < 100k -- maybe disable this if you have large svgs and it's causing problems
		$attrs['uk-svg'] = null;
		$attrs['class'] .= ' uk-preserve';
	} else {
		// lazy-load non-svgs
		// $attrs['uk-img'] = NULL;
		// foreach([ 'src', 'srcset', 'sizes' ] as $attr){
		// if (isset($attrs[$attr])) {
		// $attrs['data-' . $attr] = $attrs[$attr];
		// $attrs[$attr] = '';
		// }
		// }
		if ( empty( $attrs['srcset'] ) ) {
			$attrs['srcset'] = wp_get_attachment_image_srcset( $attachment->ID, $size );
		}
		if ( empty( $attrs['sizes'] ) ) {
			$attrs['sizes'] = wp_get_attachment_image_sizes( $attachment->ID, $size );
		}
	}
	return $attrs;
}
// add_filter( 'wp_get_attachment_image_attributes', 'mp_change_attachment_image_markup', 10, 3 );




function get_intermediate_widths() {
	// Calculate Image Sizes by type and breakpoint
	$our_sizes = wp_get_registered_image_subsizes();

	// Get the WooCommerce sizes
	if ( function_exists( 'wc_get_image_size' ) ) {
		$woocommerce_sizes = array(
			'woocommerce_thumbnail',
			'woocommerce_single',
			'woocommerce_gallery_thumbnail',
		);
		foreach ( $woocommerce_sizes as $woocommerce_size ) {
			$our_sizes[ $woocommerce_size ] = wc_get_image_size( $woocommerce_size );
		}
	}
	$widths = array_combine( array_keys( $our_sizes ), array_column( $our_sizes, 'width' ) );

	return $widths ? $widths : false;
}


// breakpoint min-widths
// $breakpoint-small:      640px;       // Mobile (landscape)
// $breakpoint-medium:     960px;       // Tablet (portrait)
// $breakpoint-large:      1200px;      // Desktops & Tablets (landscape)
// $breakpoint-xlarge:     1600px;      // Large Desktops

function mp_declare_custom_image_responsive_sizes( $sizes, $size ) {
	$width = $size[0];

	$widths = get_intermediate_widths();

	if ( $widths['woocommerce_thumbnail'] <= $width ) {
		$sizes = '(min-width: 640px) calc(100vw - 330px), (min-width: 960px) calc( (100vw - 350px ) / 3), (min-width: 1200px) 300px, calc(100vw - 30px)';
	} else {
		$sizes = '(min-width: 1200px) calc(100vw - 80px), calc(100vw - 60px)';
	}

	return $sizes;
}
// add_filter('wp_calculate_image_sizes', 'mp_declare_custom_image_responsive_sizes', 10 , 2);



/**
 * mp_attachment_image_art_direction
 *
 * Re-writes <img> elements as <picture><source></source><img></picture> in order for attachment images to
 * use art direction, via a custom field where mobile crop area and/or an entirely different image is set.
 *
 * @param   string $html     An HTML string. Can contain many <img> tags.
 * @return  string           The HTML string with <img> tags replaced by <picture><source></source><img></picture> blocks
 */

// add_filter('wp_get_attachment_image', 'mp_attachment_image_art_direction', 50, 5);
function mp_attachment_image_art_direction( $html, $attachment_id, $size, $icon, $attr ) {
	if ( is_admin() ) {
		return $html;
	}

	$dom = new DOMDocument();
	@$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NODEFDTD );
	$xpath = new DOMXPath( $dom );

	// Target ALL images. Probably not the best idea.
	// @$imgs = $dom->getElementsByTagName('img');

	// Only target images that are 1) not children of <picture>
	@$imgs = $xpath->query( '//*[not(self::picture)]/img' );

	// Or, only target images that are 1) not children of <picture> and 2) have a srcset.
	// @$imgs = $xpath->query('//*[not(self::picture)]/img[attribute::*[contains(local-name(), "srcset")]]');

	if ( ! empty( $imgs ) ) {
		foreach ( $imgs as $i => $img ) {
			// pre($attachment_id);

			$sources['mobile'] = get_field( 'mobile_image', $attachment_id );
			$sources['tablet'] = get_field( 'tablet_image', $attachment_id );

			if ( ! empty( $sources ) ) {
				$picture = $dom->createElement( 'picture' );
				foreach ( $sources as $breakpoint => $data ) {
					// pre($data);
					if ( empty( $data['id'] ) ) {
						continue;
					}

					switch ( $breakpoint ) {
						case 'mobile':
							$px = 640;
							break;
						case 'tablet':
							$px = 960;
							break;
					}

					// Create a new <source> block for this resolution
					$source = $dom->createElement( 'source' );
					$source->setAttribute( 'media', "(max-width: {$px}px)" );

					// Allow the <source> tag to be targetted by attachment ID by some future filter -- mainly to fine-tune the sizes attribute
					$source->setAttribute( 'data-id', $attachment_id );

					// $srcset = wp_get_attachment_image_srcset($data['id'], $size);
					$image_meta = wp_get_attachment_metadata( $attachment_id );
					$srcset     = wp_calculate_image_srcset( $data['sizes'], $data['url'], $image_meta, $data['id'] );
					// $sizes = empty($attr['sizes']) ? wp_get_attachment_image_sizes($data['id'], $size) : $attr['sizes'];
					$sizes = empty( $attr['sizes'] ) ? $data['sizes'] : $attr['sizes'];

					if ( ! empty( $srcset ) ) {
						$source->setAttribute( 'srcset', $srcset );
						if ( ! empty( $sizes ) ) {
							$source->setAttribute( 'sizes', $sizes );
						}
						$picture->appendChild( $source );
					}
				}

				// Now a fallback for larger screens
				$source = $dom->createElement( 'source' );
				$srcset = $img->hasAttribute( 'srcset' ) ? $img->getAttribute( 'srcset' ) : wp_get_attachment_image_srcset( $attachment_id, $size );
				$sizes  = empty( $attr['sizes'] ) ? wp_get_attachment_image_sizes( $attachment_id, $size ) : $attr['sizes'];

				if ( ! empty( $srcset ) ) {
					$source->setAttribute( 'srcset', $srcset );
					if ( ! empty( $sizes ) ) {
						$source->setAttribute( 'sizes', $sizes );
					}
				}
				// Allow the <source> tag to be targetted by attachment ID by some future filter -- mainly to fine-tune the sizes attribute
				$source->setAttribute( 'data-id', $attachment_id );
				if ( ! empty( $srcset ) ) {
					$picture->appendChild( $source );
				}

				// Replace the <img> with the <picture> containing the <source> blocks
				if ( $img->parentNode instanceof DOMElement ) {
					$img->parentNode->replaceChild( $picture, $img );
				} else {
					$dom->replaceChild( $picture, $img );
				}

				$picture->appendChild( $img );
				$img->removeAttribute( 'srcset' );
				$img->setAttribute( 'srcset', null );
				$img->removeAttribute( 'sizes' );
				$img->removeAttribute( 'width' );
				$img->removeAttribute( 'height' );
				// $img->removeAttribute('class');

				// Allow the <img> tag to be targetted by attachment ID by some future filter.
				// Disable this if the Media Cloud plugin keeps replacing the srcset, since it targets data-id attribute and wp-image-## class
				$img->setAttribute( 'data-id', $attachment_id );
			}
		}
	}

	// Save the DOM
	if ( ! empty( $imgs ) ) {
		$html = $dom->saveHTML();
		// Strip <html> and <body> tags, which are automatically added when doing loadHTML
		$html = preg_replace( '~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html );
	}
	return $html;
}



add_filter( 'the_content', 'mp_images_loading', 1 );
function mp_images_loading( $content ) {
	if ( function_exists( 'mp_html_attrs' ) ) {
		$content = mp_html_attrs( $content, '//img[not(attribute::*[contains(local-name(), "loading")])]', array( 'loading' => 'lazy' ), false );
	}
	return $content;
}


/**
 * mp_attachment_image_fitmax
 *
 * Don't allow images to be upscaled in imgix, by adding the fit=max parameter
 */
// add_filter('wp_get_attachment_image', 'mp_attachment_image_fitmax', 90, 5);
function mp_attachment_image_fitmax( $html, $attachment_id, $size, $icon, $attr ) {
	if ( is_admin() ) {
		return $html;
	}
	$html = mp_imgix_attrs( $html, array( 'fit' => 'max' ), false );
	return $html;
}


/**
 * Don't let imgix change GIFs
 */
add_filter( 'wp_get_attachment_image', 'mp_get_attachment_image_imgix_gifs', 90, 5 );
function mp_get_attachment_image_imgix_gifs( $html, $attachment_id, $size, $icon, $attr ) {
	if ( is_admin() ) {
		return $html;
	}
	if ( false !== strpos( $attr['src'], '.gif' ) ) {
		$html = mp_imgix_attrs(
			$html,
			array(
				'auto' => null,
			),
			true
		);
	}
	return $html;
}

add_action(
	'wp_enqueue_media',
	function() {
		wp_enqueue_script( 'media-library-taxonomy-filter', get_stylesheet_directory_uri() . '/assets/js/custom-media-filter.js', array( 'media-editor', 'media-views' ) );
		// Load 'terms' into a JavaScript variable that custom-media-filter.js has access to
		wp_localize_script(
			'media-library-taxonomy-filter',
			'MediaLibraryTaxonomyFilterData',
			array(
				'terms' => get_terms( 'post_tag', array( 'hide_empty' => false ) ),
			)
		);
		// Overrides code styling to accommodate for a third dropdown filter
		add_action(
			'admin_footer',
			function() {
				?>
		<style>
		.media-modal-content .media-frame select.attachment-filters {
			max-width: -webkit-calc(33% - 12px);
			max-width: calc(33% - 12px);
		}
		</style>
				<?php
			}
		);
	}
);

// Set 'product' tag for product images.
add_filter( 'wp_generate_attachment_metadata', 'mp_generate_attachment_metadata_tag', 10, 3 );
function mp_generate_attachment_metadata_tag( $metadata, $attachment_id, $context ) {
	if ( 'create' !== $context || has_tag( 'product', $attachment_id ) ) {
		return $metadata;
	}
	// wp_set_post_tags( $attachment_id, 'test', true );

	$attached_file = get_attached_file( $attachment_id );
	if ( strpos( $attached_file, 'prod-images' ) !== false ) {
		wp_set_post_tags( $attachment_id, 'product', true );
	}

	return $metadata;
}

// add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments', 10, 1 );
// function show_current_user_attachments( $query = array() ) {
// ob_start();
// var_dump( $query );
// error_log( ob_get_clean() );
// return $query;
// }


// Redirect media file attachments to their actual files rather than attachment pages.
function mp_redirect_attachment_page() {
	if ( is_attachment() ) {
		$url = wp_get_attachment_url( get_queried_object_id() );
		wp_redirect( $url, 301 );
		exit();
	}
	return;
}
// add_action( 'template_redirect', 'mp_redirect_attachment_page' );

// Imgix seems to break PDF thumbnails. Use a PDF icon instead.
add_filter( 'wp_get_attachment_image_attributes', 'mp_get_attachment_image_attributes', 10, 3 );
function mp_get_attachment_image_attributes( $attr, $attachment, $size ) {
	if ( is_admin() ) {
		if ( 'application/pdf' === get_post_mime_type( $attachment ) ) {
			$attr['src']    = get_asset_url( 'images/pdf.svg' );
			$attr['width']  = 48;
			$attr['height'] = 48;
			$attr['style']  = 'border: 0';
		}
	}
	return $attr;
}

// Add direct link to PDFs in the list view.
add_filter( 'media_row_actions', 'mp_media_row_actions_direct_link', 10, 3 );
function mp_media_row_actions_direct_link( $actions, $post, $detached ) {
	if ( 'application/pdf' === get_post_mime_type( $post ) ) {
		$actions['file_url'] = '<a href="' . wp_get_attachment_url( $post->ID ) . '" download="' . wp_get_attachment_url( $post->ID ) . '" target="_blank">Direct Link</a>';
	}
	return $actions;
}

// Disable PDF preview thumbnails.
function mp_disable_pdf_previews() {
	$fallbacksizes = array();
	return $fallbacksizes;
}
add_filter( 'fallback_intermediate_image_sizes', 'mp_disable_pdf_previews' );
