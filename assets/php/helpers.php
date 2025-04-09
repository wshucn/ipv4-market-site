<?php
define( 'GRID_WIDTHS', grid_widths( array( 4, 5, 6 ) ) );

/** PHP 8 PolyFills */
if ( ! function_exists( 'str_contains' ) ) {
	function str_contains( string $haystack, string $needle ): bool {
		return '' === $needle || false !== strpos( $haystack, $needle );
	}
}

if ( ! function_exists( 'str_starts_with' ) ) {
	function str_starts_with( string $haystack, string $needle ): bool {
		return 0 === strncmp( $haystack, $needle, \strlen( $needle ) );
	}
}
if ( ! function_exists( 'str_ends_with' ) ) {
	function str_ends_with( string $haystack, string $needle ): bool {
		return '' === $needle || ( '' !== $haystack && 0 === substr_compare( $haystack, $needle, -\strlen( $needle ) ) );
	}
}

if ( ! function_exists( 'preg_grep_keys' ) ) {
	function preg_grep_keys( $pattern, $input, $flags = 0 ) {
		return array_intersect_key( $input, array_flip( preg_grep( $pattern, array_keys( $input ), $flags ) ) );
	}
}

// Check if your needed plugin functions exist and create safe ones if they are not.
// Avoids PHP critical errors.
// add_action('plugins_loaded', 'mp_safe_functions');
function mp_safe_functions() {
	// If Advanced Custom Fields isn't active, a lot of get_fields calls will fail.
	if ( ! function_exists( 'get_field' ) ) {
		function get_field( $selector = null, $post_id = false, $format_value = true ) {
			return false;
		}
		function get_fields( $post_id = null, $format_value = true ) {
			return false;
		}
		function get_field_object( $selector = null, $post_id = false, $format_value = true, $load_value = true ) {
			return false;
		}
		function get_field_objects( $post_id = false, $format_value = true, $load_value = true ) {
			return false;
		}
	}
}


function mp_transient_expiration( $expiration = 0 ) {
	return is_development() ? 0 : $expiration;
}


/**
 * Attempt to parse the DOM Content into a DOMDocument HTML model.
 *
 * @since 1.0.0
 *
 * @return DOMDocument|false
 */
function mp_load_html( string $html ) {
	if ( empty( $html ) ) {
		return false;
	}

	try {
		$dom = new \DOMDocument();
		// PHP (as of 7.4) does not fully support HTML5, so we need to suppress errors/warnings, which WILL occur.
		libxml_use_internal_errors( true );
		// $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NODEFDTD);
		// DO NOT USE LIBXML_HTML_NOIMPLIED -- it messes up the code; we just need to strip the <html> and <body> tags that are added here
		// LIBXML_NOWARNING: not working due to a bug?
		$dom->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING );
		$errors = libxml_get_errors();
		foreach ( $errors as $error ) {
			/* @var $error LibXMLError */
			if ( $error->level > 2 && function_exists( 'mp_write_log' ) ) {
				mp_write_log( sprintf( 'Warning: DOMDocument::loadHTML(): %s in %s on line %s', $error->message, $error->file, $error->line ) );
			}
		}
		libxml_clear_errors();

		return $dom;
	} catch ( \Exception $e ) {
		return false;
	}
}



/**
 * mp_save_html
 *
 * Given a DOMDocument, returns an HTML string
 */
function mp_save_html( DOMDocument $dom ) {
	try {
		$html = $dom->saveHTML();

		// Strip <html> and <body> tags, which are automatically added when doing loadHTML
		$html = preg_replace( '~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html );
		return trim( $html );
	} catch ( \Exception $e ) {
		return false;
	}
}



/**
 * clearAttributes
 */
function clearAttributes( DOMElement $node ) {
	// Remove all attributes
	$attributes = $node->attributes;
	while ( $attributes->length ) {
		$node->removeAttribute( $attributes->item( 0 )->name );
	}
	return $node;
}

/**
 * mp_multicolumn_menu
 *
 * Converts 2nd-level dropdowns to columns
 */

function mp_multicolumn_menu( $html ) {
	if ( empty( $html ) ) {
		return;
	}

	$dom   = mp_load_html( $html );
	$xpath = new \DOMXPath( $dom );

	@$parents_of_columns = $xpath->query( "//span[contains(@class, 'uk-nav-header')]/following-sibling::div[contains(concat(' ',normalize-space(@class),' '), ' uk-navbar-dropdown ')]/ancestor::div[contains(concat(' ',normalize-space(@class),' '), ' uk-navbar-dropdown ')]" );
	if ( ! empty( $parents_of_columns ) ) {
		// Select .uk-navbar-dropdowns containing headers followed by dropdowns (these are going to be columns)
		foreach ( $parents_of_columns as $parent ) {
			// Get number of headers (columns) within this dropdown
			$headers = $xpath->query( "ul/li/span[contains(@class, 'uk-nav-header')]/following-sibling::div[contains(concat(' ',normalize-space(@class),' '), ' uk-navbar-dropdown ')]", $parent );
			$columns = $headers->length;

			// Remove all attributes
			$parent = clearAttributes( $parent );

			// replace class with "uk-navbar-dropdown uk-navbar-dropdown-width-#", maximum 5
			$parent->setAttribute( 'class', 'uk-navbar-dropdown uk-navbar-dropdown-width-' . min( 5, $columns ) );
			$submenus = $xpath->query( "ul[contains(concat(' ',normalize-space(@class),' '), ' uk-navbar-dropdown-nav ')]", $parent );
			// select child ul.uk-navbar-dropdown-nav
			foreach ( $submenus as $submenu ) {
				// replace class with "uk-navbar-dropdown-grid uk-child-width-1-#", maximum 5
				$submenu->setAttribute( 'class', 'uk-navbar-dropdown-grid uk-child-width-1-' . min( 5, $columns ) );
				// add attribute uk-grid
				$submenu->setAttribute( 'uk-grid', null );
				$submenu_items = $xpath->query( 'li', $submenu );
				// each child li becomes a div, no class
				foreach ( $submenu_items as $submenu_item ) {
					$submenu_dropdowns = $xpath->query( "div[contains(concat(' ',normalize-space(@class),' '), ' uk-navbar-dropdown ')]", $submenu_item );
					foreach ( $submenu_dropdowns as $submenu_dropdown ) {
						// $submenu_dropdown = clearAttributes($submenu_dropdown);
						// Remove the div.uk-navbar-dropdown wrapper (i.e., clone all the children up one level and then remove it)
						foreach ( $submenu_dropdown->childNodes as $submenu_dropdown_child ) {
							$submenu_dropdown->parentNode->insertBefore( $submenu_dropdown_child->cloneNode( true ), $submenu_dropdown );
						}
						$submenu_dropdown->parentNode->removeChild( $submenu_dropdown );
					}
					$submenu_item->removeAttribute( 'class' );
					$submenu_item = renameNode( $submenu_item, 'div' );
				}
				$submenu = renameNode( $submenu, 'div' );
			}
		}
	}

	$html = mp_save_html( $dom );
	return $html;
}

/**
 * buildPostImg
 * Returns the <img> tag for a post image.
 *
 * @param object $query         WP_Query object for the posts.
 * @param string $image_size    Registered image size (medium, large, full, etc.)
 * @param array  $attrs         Additional attributes for the <img> tag, such as 'sizes'
 * @param array  $imgix_attrs   imgix query parameters in associated array form, for applying filters, etc.
 *
 * @return string               The <img> tag.
 */
function buildPostImg( object $query, $image_size = 'full', $attrs = array(), $imgix_attrs = array() ) {
	$id = get_post_thumbnail_id( $query->post->ID );

	if ( ! empty( $id ) ) {
		$img = wp_get_attachment_image( $id, $image_size, false, $attrs );

		// imgix query
		if ( ! empty( $imgix_attrs ) ) {
			$img = mp_imgix_attrs( $img, $imgix_attrs );
		}

		// for sliders, lazy-load all but first slide
		// https://getuikit.com/docs/image#load-previous-and-next
		if ( 'slider' === $query->query_vars['post_type'] ) {
			$attrs['post_number'] = $query->current_post;
			switch ( $query->current_post ) {
				case 0:
					$attrs['uk-img'] = 'target: !.uk-slideshow-items > :last-child, !* +*';
					// Preload first slide image
					// if( !did_action('wp_head') ) mp_preload_images($img);
					break;
				case ( $query->found_posts - 1 ):
					$attrs['uk-img'] = 'target: !* -*, !.uk-slideshow-items > :first-child';
					break;
				default:
					$attrs['uk-img'] = 'target: !* -*, !* +*';
			}
		}
		// return buildAttributes($attrs, 'img');
		return $img;
	}
}


/**
 * mp_sizes_attribute
 *
 * Returns the 'sizes' attribute for an image, given breakpoint name(s) and number of columns.
 * It's not precise, but it should be approximately better than WordPress's default.
 *
 * The function takes into consideration the max_width field for the page, or any
 * uk-container/uk-width class ($block_class).
 */
function mp_sizes_attribute( $sizes_breakpoints = array( 'medium' ), $columns = 1, $block_class = '' ) {
	if ( ! is_array( $sizes_breakpoints ) ) {
		$sizes_breakpoints = to_array( $sizes_breakpoints );
	}

	$block_class = buildClass( $block_class );

	// $container-max-width:                            1070px;
	// $container-xsmall-max-width:                     750px;
	// $container-small-max-width:                      900px;
	// $container-large-max-width:                      1400px;
	// $container-xlarge-max-width:                     1600px;

	// $container-padding-horizontal:                   8px;
	// $container-padding-horizontal-s:                 15px;
	// $container-padding-horizontal-m:                 35px;

	// $breakpoint-small:      640px;
	// $breakpoint-medium:     960px;
	// $breakpoint-large:      1200px;
	// $breakpoint-xlarge:     1600px;

	$breakpoint = array(
		'small'  => '640px',
		'medium' => '960px',
		'large'  => '1200px',
		'xlarge' => '1600px',
	);

	// total padding, so double the initial values
	$container_padding = array(
		'default' => '16px',
		'small'   => '30px',
		'medium'  => '70px',
	);

	$container_max_width = array(
		'xsmall' => 750,
		'small'  => 900,
		'normal' => 1070,
		'large'  => 1400,
		'xlarge' => 1600,
	);

	$widths = array(
		'small'   => '150px',
		'medium'  => '300px',
		'large'   => '450px',
		'xlarge'  => '600px',
		'2xlarge' => '750px',
	);

	// We need the lesser number of $max_width, $block_container_class, and $block_width_class.
	// Or 100vw, if none of those is present. The $max_width will default to 'medium'.
	// Then choose between columns and $block_width_fraction_class.

	$initial_width = '100vw';
	$max_width     = get_field( 'max_width' );
	// Default container width is in partials/main-container.php. Usually uk-container-medium ('normal' in our associative array)
	if ( empty( $max_width ) ) {
		$max_width = 'normal';
	}

	if ( in_array( $max_width, array_keys( $container_max_width ) ) ) {
		switch ( $max_width ) {
			case 'none':
			case 'expand':
				$initial_width = '100vw';
				break;
			default:
				$initial_width = sprintf( '%spx', $container_max_width[ $max_width ] );
				break;
		}
	}

	if ( ! empty( $block_class ) ) {
		preg_match_all( '/(uk-container[^ ]*)/', strtolower( $block_class ), $block_container_class );
		preg_match_all( '/uk-width-([1-9\-]*)/', strtolower( $block_class ), $block_width_fraction_class );
		preg_match_all( '/uk-width-([a-z]*)/', strtolower( $block_class ), $block_width_class );
	}

	// If a specific size, such as uk-width-large, we just need to return that size.
	// if(!empty($block_width_class[1][0]) && !empty($widths[$block_width_class[1][0]])) {
	// if(empty($sizes_breakpoints)) {
	// $sizes = $widths[$block_width_class[1][0]];
	// }
	// return $sizes;
	// }

	// Width for all the breakpoints
	foreach ( $sizes_breakpoints as $b => $sizes_breakpoint ) {
		$padding = $container_padding['default'];
		if ( ! empty( $container_padding[ $sizes_breakpoint ] ) ) {
			$padding = $container_padding[ $sizes_breakpoint ];
		} elseif ( $b > 0 && isset( $container_padding[ $b - 1 ] ) ) {
			$padding = $container_padding[ $b - 1 ];
		}

		// If a specific size, such as uk-width-large, we just need to return that size.
		if ( ! empty( $block_width_class[1][0] ) && ! empty( $widths[ $block_width_class[1][0] ] ) ) {
			$sizes[] = sprintf( '(min-width: %s) %s', $breakpoint[ $sizes_breakpoint ], $widths[ $block_width_class[1][0] ] );
		} else {
			if ( $columns > 6 ) {
				$sizes[] = sprintf( '(min-width: %s) calc(%s * (%s - %s))', $breakpoint[ $sizes_breakpoint ], round( $columns / 100, 3 ), $initial_width, $padding );
			} elseif ( $columns > 1 ) {
				$sizes[] = sprintf( '(min-width: %s) calc(%s * (%s - %s))', $breakpoint[ $sizes_breakpoint ], round( 1 / $columns, 3 ), $initial_width, $padding );
			} elseif ( $columns < 1 && $columns > 0 ) {
				$sizes[] = sprintf( '(min-width: %s) calc(%s * (%s - %s))', $breakpoint[ $sizes_breakpoint ], round( $columns, 3 ), $initial_width, $padding );
			} else {
				$sizes[] = sprintf( '(min-width: %s) calc(%s - %s)', $breakpoint[ $sizes_breakpoint ], $initial_width, $padding );
			}
		}
	}

	// Initial width (mobile)
	$sizes[] = sprintf( 'calc(100vw - %s)', $container_padding['default'] );
	$sizes   = join( ', ', $sizes );

	return $sizes;
}



/**
 * mp_imgix_attrs
 * Add imgix parameters to 'src' or 'srcset' URLs, given an HTML string.
 *
 * @param        $image         HTML string OR attributes array
 * @param array                                         $attrs         Additional attributes for the imgix query strings
 * @param bool                                          $overwrite     Overwrite existing query parameters?
 *
 * @return string               HTML string with altered URLs
 */

function mp_imgix_attrs( $image, array $attrs = array(), $overwrite = true ) {
	if ( empty( $attrs ) ) {
		return $image;
	}

	if ( is_array( $image ) ) {
		foreach ( $image as $attr => $val ) {
			if ( ! is_numeric( $image[ $attr ] ) ) {
				$image[ $attr ] = mp_imgix_attrs( $val );
			}
		}
		return $image;
	}

	// Match all URLs in string
	preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $image, $urls );
	if ( empty( $urls ) ) {
		return $image;
	}

	// get list of imgix domains registered with the Media Cloud plugin
	$imgix_domains = get_option( 'mcloud-imgix-domains' );
	if ( ! empty( $imgix_domains ) ) {
		$imgix_domains = explode( PHP_EOL, $imgix_domains );
	}

	// imgix query
	$replace = array();
	foreach ( $urls[0] as $url ) {
		$parts = parse_url( $url );

		// Check if there's a query and if the URL hostname is a registered imgix domain for this site
		if ( ! empty( $parts['query'] ) && in_array( $parts['host'], $imgix_domains ) ) {

			// 1. Combine the query keys and values into an associative array.
			$query_attrs = mp_split_query( $url );

			// Don't allow images to be scaled beyond their full size.
			if ( ! empty( $query_attrs['fit'] ) && 'scale' === $query_attrs['fit'] ) {
				$query_attrs['fit'] = 'max';
			}

			// 2. Merge in the new query parameters.
			// $query_attrs = ($overwrite) ? array_merge($query_attrs, $attrs) : array_merge($attrs, $query_attrs);
			if ( true === $overwrite ) {
				$query_attrs = $attrs;
			}

			// 3. Build that into a new http query string.
			$query_str                                      = http_build_query( $query_attrs );
			$replace[ '/' . preg_quote( $url, '/' ) . '/' ] = $parts['scheme'] . '://' . $parts['host'] . $parts['path'] . '?' . $query_str;
		}
	}
	if ( ! empty( $replace ) && ! empty( $urls ) ) {
		$image = preg_replace( array_keys( $replace ), array_values( $replace ), $image, -1 );

		// Replace srcset width ('w') values with multiples that reflect the dpr part of the imgix query.
		// if(!empty($attrs['dpr'])) {
		// Multiply the width specifiers (768w, 800w, etc.)
		// preg_match_all('#\s(\d+)(w[\'",]*)#', $image, $widths);
		// pre($widths);
		// $widths_replace = [];
		// foreach($widths[0] as $i_widths => $width) {
		// $widths_replace[$i_widths] = sprintf(" %s%s",
		// ( (int)preg_replace('/[^0-9]/', '', $widths[1][$i_widths] ) * $attrs['dpr'] ),
		// $widths[2][$i_widths]
		// );
		// }
		// $image = preg_replace(
		// array_reverse(preg_filter('/^|$/', '/', $widths[0])),
		// array_reverse($widths_replace),
		// $image, 1
		// );
		// }
	}
	return $image;
}


/**
 *   buildAttributes
 *
 *   Convert an associative array into HTML attributes.
 *   If $tag is given, the whole (opening) tag is returned. Otherwise, just the attribute pairs (string) are returned.
 *   Setting $content to TRUE will force an end tag, if needed.
 *
 * @param    array  $attributes     Attributes for the element
 * @param    string $tag            Tag for the element.
 * @param    mixed  $content        Content that the element will wrap.
 *
 * @return   string  HTML attributes
 */
function buildAttributes( $attributes = array(), string $tag = '', $content = '' ) {
	if ( is_string( $attributes ) ) {
		$attributes = array( 'class' => $attributes );
	}

	$attributes = mp_sanitize_attrs( $attributes );

	// Join the keys and values into attribute="value" attribute pairs
	array_walk(
		$attributes,
		function ( &$v, $k ) {
			$k = esc_attr( $k );
			if ( 'class' === $k ) {
				$v = buildClass( $v );
			}
			// Double-quote the value
			$v = empty( $v ) ? $k : $k . '=' . sprintf( '"%s"', $v );
		}
	);
	$attributes = trim_join( ' ', array_filter( $attributes ) );

	if ( empty( $tag ) ) {
		$return = $attributes;
	} else {
		$tag    = esc_attr( $tag );
		$return = empty( $attributes ) ? "<{$tag}>" : "<{$tag} {$attributes}>";
	}

	// Add the content if necessary.
	if ( ! empty( $content ) ) {
		if ( is_array( $content ) ) {
			$content = join( PHP_EOL, $content );
		}

		// Setting $content to TRUE will force an end tag, if needed.
		if ( $content !== true ) {
			$return .= $content;
		}

		// Terminate the tag if necessary, and only if there is content (or content is boolean TRUE).
		if ( ! in_array( $tag, array( 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr' ) ) ) {
			$return .= "</{$tag}>";
		}
	}

	return $return;
}

/**
 *   buildClass
 *
 *   Shortcut function to join only unique mixed arguments with spaces.
 *   Removes duplicate classes. Removes any classes starting with !:
 *       i.e., !remove_me will remove the class .remove_me
 *
 * @param    mixed $mixed      String or array of any dimension
 *
 * @return   string  space-delimited values
 */
function buildClass( ...$mixed ) {
	// Remove classes if they start with !
	$classes           = to_array( $mixed );
	$classes_to_remove = array_filter(
		$classes,
		function ( $v ) {
			return strpos( $v, '!' ) === 0;
		}
	);

	// Remove the negated classes from the original class list.
	$classes = array_diff( $classes, $classes_to_remove );
	$classes = array_filter(
		$classes,
		function ( $v ) use ( $classes_to_remove ) {
			return ! \in_array( '!' . $v, $classes_to_remove, true );
		}
	);

	// Remove duplicates and return array_flip twice is much faster than array_unique.
	return join( ' ', array_flip( array_flip( $classes ) ) );
}

/**
 *   hasClass
 *
 *   Returns true if an array or space-separated list of classes or combination of them contains a class.
 *   Compares against any number/combination of class arrays & strings, and does not compare against !classnames beginning with bang.
 *
 * @param    string $class
 * @param    mixed  $mixed
 *
 * @return   boolean  true or false
 */
function hasClass( $class, ...$mixed ) {
	$class   = is_array( $class ) ? $class : array( $class );
	$pattern = '/(\s|^)(' . join( '|', $class ) . ')(\s|$)/i';
	if ( preg_match( $pattern, buildClass( $mixed ) ) ) {
		return true;
	}
	return false;
}

// Returns an array reduced to elements starting with a string(s)
function mp_array_starts_with( array $array, ...$strings ) {
	$search  = to_array( $strings );
	$pattern = '/^(' . join( '|', $search ) . ')/i';
	return preg_grep( $pattern, $array );
}

// Returns string or array with !class as class and class as !class
function mp_negate_class( ...$class ) {
	if ( empty( $class ) ) {
		return $class;
	}
	if ( is_array( $class ) ) {
		return preg_replace( array( '/^!/', '/^([^!])/' ), array( '', '!$1' ), flatten( $class ) );
	}
	if ( is_string( $class ) ) {
		return strpos( $class, '!' ) === 0 ? str_replace( '!', '', $class ) : '!' . $class;
	}
	return $class;
}


/**
 * mp_buildDrop
 *
 *   Builds a dropdown menu item given a parent title and HTML or an array of <li> items.
 *   In the 'mobile' wp_nav_menu theme location, it will build a standard sub menu.
 *
 * @param   string   $title      Parent item title
 * @param   mixed    $items      Dropdown items
 * @param   stdClass $args       Arguments passed to wp_nav_menu hook
 * @param   mixed    $attrs      Parameters for the uk-drop attribute (see https://getuikit.com/docs/drop)
 *
 * @return  string  The <li> with the dropdown or sub-menu
 */

function mp_buildDrop( string $title, $items, $args = null, $attrs = '' ) {
	$li_attrs  = array( 'class' => array( 'uk-parent', 'menu-item' ) );
	$a_attrs   = array( 'href' => '#' );
	$div_attrs = array();
	$ul_attrs  = array();

	$theme_location = isset( $args->theme_location ) ? $args->theme_location : '';

	switch ( $theme_location ) {
		case 'primary':
			$ul_attrs['class'][] = 'uk-nav uk-navbar-dropdown-nav';
			// $li_attrs['class'][] = 'menu-item';
			$div_attrs['class'][] = 'uk-navbar-dropdown';
			break;
		case 'mobile':
			$ul_attrs['class'][] = 'uk-nav-sub uk-nav-default';
			break;
		default:
			$div_attrs['class'][] = 'uk-dropdown';
			$ul_attrs['class'][]  = 'uk-dropdown-nav';
	}

	if ( ! empty( $attrs ) ) {
		$div_attrs['uk-drop'] = $attrs;
	}

	// Add classes to nested navs.
	$items = mp_html_class_by_class( $items, 'ul.children', 'uk-nav-sub' );
	$items = mp_html_class( $items, '//ul[contains(@class, "uk-nav-sub")]/parent::li', 'uk-parent', true );

	$ul  = buildAttributes( $ul_attrs, 'ul', trim_join( '', $items ) );
	$div = empty( $div_attrs ) ? $ul : buildAttributes( $div_attrs, 'div', $ul );

	// $title = __( $title, 'text_domain' );

	// Add a dropdown indicator for dropdowns
	if ( str_contains( buildClass( $ul_attrs['class'] ), 'dropdown' ) ) {
		$title             .= buildAttributes(
			array(
				'uk-icon' => 'triangle-down',
				'class'   => 'uk-dropdown-icon',
			),
			'span',
			true
		);
		$a_attrs['class'][] = 'has-icon icon-after';
	}

	$a  = buildAttributes( $a_attrs, 'a', $title );
	$li = buildAttributes( $li_attrs, 'li', $a . $div );

	return $li;
}

/**
 * mp_buildMyAccount
 *
 *    Builds the My Account menu for WooCommerce, for a siderbar or a navbar
 *
 * @param   array $attrs      Attributes for the <ul> element
 */
function mp_buildMyAccount( $attrs = array() ) {
	if ( ! class_exists( 'woocommerce' ) ) {
		return false;
	}

	$ul_attrs = is_array( $attrs ) ? $attrs : array( 'class' => $attrs );

	$li_logout_attrs = array(
		'href'  => wp_logout_url( '/' ),
		'title' => __( 'Sign out', 'woocommerce' ),
	);

	$items = array();
	foreach ( wc_get_account_menu_items() as $endpoint => $label ) {
		$li_attrs            = array();
		$li_attrs['class'][] = wc_get_account_menu_item_classes( $endpoint );

		$a_attrs         = array();
		$item_url        = wc_get_account_endpoint_url( $endpoint );
		$a_attrs['href'] = $item_url;
		if ( isExternalURL( $item_url ) ) {
			$a_attrs['target'] = '_blank';
		}

		$a       = buildAttributes( $a_attrs, 'a', esc_html( $label ) );
		$items[] = buildAttributes( $li_attrs, 'li', $a );
	}

	$items[] = '<li class="uk-nav-divider"></li>';
	$items[] = '<li>' . buildAttributes( $li_logout_attrs, 'a', __( 'Sign out', 'woocommerce' ) ) . '</li>';

	$ul = buildAttributes( $ul_attrs, 'ul', $items );

	// WooCommerce uses the 'is-active' class for the current menu item.
	$ul = mp_html_class_by_class( $ul, 'is-active', 'uk-active', true );
	return $ul;
}

/**
 *   http_strip_query
 *
 *   Strips part or all of a query parameter from a URL.
 *
 * @param    string $url        Complete URL
 *
 * @return   string                  Stripped URL
 */
function http_strip_query( string $url ) {
	$pieces   = parse_url( $url );
	$scheme   = isset( $pieces['scheme'] ) ? $pieces['scheme'] . '://' : '';
	$host     = isset( $pieces['host'] ) ? $pieces['host'] : '';
	$port     = isset( $pieces['port'] ) ? ':' . $pieces['port'] : '';
	$user     = isset( $pieces['user'] ) ? $pieces['user'] : '';
	$pass     = isset( $pieces['pass'] ) ? ':' . $pieces['pass'] : '';
	$pass     = ( $user || $pass ) ? "$pass@" : '';
	$path     = isset( $pieces['path'] ) ? $pieces['path'] : '';
	$query    = isset( $pieces['query'] ) ? '?' . $pieces['query'] : '';
	$fragment = isset( $pieces['fragment'] ) ? '#' . $pieces['fragment'] : '';

	return "$scheme$host$port$path$fragment";
}

function mp_split_query( $url ) {
	// preg_match_all("/(?P<keys>\w+)=(?P<values>[\w@,-\.]+)/", urldecode($url), $query_split);
	// $array = array_combine($query_split['keys'], $query_split['values']);

	parse_str( parse_url( $url, PHP_URL_QUERY ), $array );
	return $array;
}


function get_script_src_by_handle( string $handle ) {
	if ( in_array( $handle, wp_scripts()->queue ) ) {
		return wp_scripts()->registered[ $handle ]->src;
	}
}

/**
 *   validateDate
 *
 *   Returns false if a date string is not in a given format
 *
 * @param    string $date
 * @param    string $format
 *
 * @return   boolean     false if invalid
 */
function validateDate( string $date, string $format = 'Y-m-d H:i:s' ) : bool {
	$d = DateTime::createFromFormat( $format, $date );
	return $d && $d->format( $format ) === $date;
}

/**
 *   trim_join
 *
 *   Join values by delimiter, but only if the value isn't empty.
 *
 * @param    string $d  delimiter
 * @param    mixed  $a  items
 * @return   string  joined items
 */
function trim_join( string $d = ' ', ...$a ) {
	$strings = array_filter( flatten( $a ) );
	return join( $d, $strings );
}


/**
 *   delimiter
 *
 *   Determine the delimiter for a string.
 *
 * @param    string $string
 *
 * @return   string Delimiter
 */
function delimiter( string $string = null, string $fallback = null ) {
	// The first listed delimiter here will be the fallback if none is found.
	$delimiters = array_fill_keys(
		array(
			' ',
			',',
			';',
			"\t",
			'|',
		),
		0
	);

	$fallback = empty( $fallback ) ? array_key_first( $delimiters ) : $fallback;

	// If no string is given, return first of the likely delimiters.
	if ( ! is_string( $string ) ) {
		return $fallback;
	}

	foreach ( $delimiters as $delimiter => &$count ) {
		$count = count( explode( $delimiter, $string ) );
	}

	return array_search( max( $delimiters ), $delimiters );
}

/**
 *   to_array
 *
 *   Make a one-dimensional array from arrays and/or strings.
 *
 * @param    mixed $mixed
 * @return   array   one-dimensional array
 */
function to_array( ...$mixed ) {
	if ( empty( array_filter( $mixed ) ) ) {
		return array();
	}
	$array = flatten( $mixed );
	foreach ( $array as &$string ) {
		$string = explode( delimiter( $string ), $string );
	}
	return flatten( $array );
}

/**
 * Flattens a multidimensional array to a simple array.
 *
 * @param array $array a multidimensional array.
 *
 * @return array a simple array
 */
function flatten( ...$arrays ) {
	$result = array();
	array_walk_recursive(
		$arrays,
		function ( $v ) use ( &$result ) {
			$result[] = trim( $v );
		}
	);
	return array_filter( $result, 'strlen' );
}

/**
 * Check if a string contains any value from an array
 *
 * @param string $str String to check (haystack)
 * @param array  $arr Array of needles
 *
 * @return bool     true or false
 */
function str_contains_any( $str, array $arr ) {
	foreach ( $arr as $a ) {
		if ( stripos( $str, $a ) !== false ) {
			return true;
		}
	}
	return false;
}

function in_array_all( $needles, $haystack ) {
	return empty( array_diff( $needles, $haystack ) );
}

function in_array_any( $needles, $haystack ) {
	return ! empty( array_intersect( $needles, $haystack ) );
}

function array_key_contains( string $key, $array, string $needle ) {
	if ( ! is_array( $array ) || ! isset( $key, $array ) ) {
		return false;
	}
	return strpos( $array[ $key ], $needle ) !== false;
}

function is_associative( array $arr ) {
	if ( array() === $arr ) {
		return false;
	}
	return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
}

/**
 * Return depth of array.
 */
function getArrayDepth( $array ) {
	$depth  = 0;
	$iteIte = new RecursiveIteratorIterator( new RecursiveArrayIterator( $array ) );

	foreach ( $iteIte as $ite ) {
		$d     = $iteIte->getDepth();
		$depth = $d > $depth ? $d : $depth;
	}

	return $depth;
}


/**
 * Join a string with a natural language conjunction at the end.
 * https://gist.github.com/angry-dan/e01b8712d6538510dd9c
 */
function natural_language_join( array $list, string $conjunction = 'and' ) {
	$list = array_map(
		function ( $i ) {
			return trim( $i );
		},
		$list
	);
	$last = array_pop( $list );
	if ( $list ) {
		return implode( ', ', $list ) . ' ' . $conjunction . ' ' . $last;
	}
	return $last;
}

/**
 *   query_params
 *
 *   Builds WP_Query parameters to fetch posts by id, slug, tag id, tag slug, category id, or category name.
 *
 * @param    string $post_type  Post type for the WP_Query
 * @param    array  $a          Shortcode attributes, which must include 'post', 'tag', and/or 'category'.
 *
 * @return   array               Query parameters for WP_Query
 */
function query_params( array $a = array() ) {
	if ( empty( $a ) ) {
		return;
	}

	// Build query filters. Can be IDs or slugs or a combination.
	$query_filters = array_intersect_key(
		$a,
		array_flip( array( 'tag', 'category', 'post', 'author' ) )
	);

	$query_params = array();

	foreach ( array_filter( $query_filters ) as $filter => $value ) {

		// Sanitize
		$value = preg_replace(
			array( '/,+(?=,)/', '/\s/' ),
			'',
			$value
		);

		// Loop over the comma-separated string supplied to the shortcode.
		// post__in                 ⎫
		// tag__in                 ⎬  IDs
		// category__in           ⎭

		// post_name__in            ⎫
		// tag_slug__in            ⎬  slugs
		// category_name (string) ⎭

		foreach ( explode( ',', $value ) as $filter_item ) {
			$filter_param = $filter;
			if ( is_string( $filter_item ) ) {
				$filter_param .= ( $filter === 'tag' ) ? '_slug' : '_name';
			}

			$query_params[ $filter . '__in' ][] = $filter_item;
		}
		if ( array_key_exists( 'category_name__in', $query_params ) ) {
			$query_params['category_name'] = implode( ',', $query_params['category_name__in'] );
			unset( $query_params['category_name__in'] );
		}
	}

	// Preserve the given post order, when querying specific posts.
	foreach ( array(
		'post__in',
		'post_name__in',
	) as $post__in ) {
		if ( array_key_exists( $post__in, $query_params ) ) {
			$query_params['orderby'] = $post__in;
		}
	}

	// Append our common query parameters.
	$accepted = array(
		'post_type',
		'posts_per_page',
		'numberposts',
		'order',
		'orderby',
		'meta_query',
		'meta_key',
		'meta_value',
		'meta_type',
		'meta_compare',
		'taxonomy',
		'hide_empty',
		'tax_query',
	);
	foreach ( $accepted as $param ) {
		// if(array_key_exists($param, $a) && is_string($a[$param]))
		// $query_params[$param] = in_array(strtolower($a[$param]), ['true','false']) ? boolval($a[$param]) : $a[$param];
		if ( array_key_exists( $param, $a ) ) {
			$query_params[ $param ] = $a[ $param ];
		}
	}
	// $query_params += [
	// 'post_type'      => array_key_exists('post_type', $a) ? $a['post_type'] : 'post',
	// ];

	return $query_params;
}


/**
 *   pre
 *
 *   Pretty-print anything. Will handle HTML code.
 *
 * @param    mixed $var
 */
function pre( $var ) {
	// if( is_admin() ) return;

	if ( is_string( $var ) ) {
		$var = isHTML( $var ) ? esc_html( $var ) : $var;
	}
	echo '<pre>';
	var_dump( $var );
	echo '</pre>';
}

function debug_caller_data() {
	$backtrace        = debug_backtrace();
	$caller_functions = array_column( $backtrace, 'function' );
	$offset           = array_search( 'pp', $caller_functions );
	if ( $offset > 0 ) {
		return $backtrace[ $offset + 1 ];
	} else {
		return false;
	}
}

/**
 * Pretty Printing
 *
 * @since 1.0.0
 * @author Chris Bratlien
 * @param mixed  $obj
 * @param string $label
 * @return null
 */
function pp( $obj, $label = '' ) {
	// if( is_admin() ) return;
	if ( is_string( $obj ) && isHTML( $obj ) ) {
		$obj = esc_html( $obj );
	}
	$data      = json_encode( print_r( $obj, true ) );
	$backtrace = debug_caller_data();
	if ( empty( $label ) && $backtrace && isset( $backtrace['function'] ) ) {
		$label = $backtrace['function'];
	} ?>
<style type="text/css">
	#bsdLogger {
		font-family: 'JetBrains Mono', 'VictorMono-Medium', 'Fira Code', Menlo, Monaco, 'Courier New', monospace;
		position: absolute;
		top: 30px;
		right: 0px;
		border-left: 4px solid #bada55;
		padding: 6px;
		background: #272c35;
		z-index: 2000;
		width: 400px;
		height: 800px;
		overflow: scroll;
	}

	#bsdLogger>* {
		color: #fff;
		font-size: 10.5px;
	}

	#bsdLogger>h2 {
		color: #bada55;
		font-size: 14px;
		font-weight: bold;
	}
</style>
<script type="text/javascript">
	var doStuff = function() {
		var obj = <?php echo $data; ?> ;
		var logger = document.getElementById('bsdLogger');
		if (!logger) {
			logger = document.createElement('div');
			logger.id = 'bsdLogger';
			document.body.appendChild(logger);
		}
		////console.log(obj);
		var pre = document.createElement('pre');
		var h2 = document.createElement('h2');
		pre.innerHTML = obj;
		h2.innerHTML = '<?php echo addslashes( $label ); ?>';
		logger.appendChild(h2);
		logger.appendChild(pre);
	};
	window.addEventListener("DOMContentLoaded", doStuff, false);
</script>
	<?php
}



function renameNode( DOMElement $node, string $name = 'div' ) {
	if ( $node->tagName === $name ) {
		return $node;
	}

	$dom       = new DOMDocument();
	$cloneNode = $dom->createElement( $name );

	foreach ( $node->childNodes as $childNode ) {
		$newChild = $dom->importNode( $childNode, true );
		$cloneNode->appendChild( $newChild );
	}
	foreach ( $node->attributes as $attribute ) {
		$cloneNode->setAttribute( $attribute->nodeName, $attribute->nodeValue );
	}
	// input -> ?: use value as text
	if ( ! $node->hasChildNodes() && $node->hasAttribute( 'value' ) ) {
		$cloneNode->appendChild( $dom->createTextNode( $node->getAttribute( 'value' ) ) );
	}

	$dom->appendChild( $cloneNode );
	$newNode = $node->ownerDocument->importNode( $dom->documentElement, true );

	return $node->parentNode->replaceChild( $newNode, $node );
}

function appendHTML( DOMNode $parent, $source ) {
	$tmpDoc = new DOMDocument();
	@$tmpDoc->loadHTML( $source );
	foreach ( $tmpDoc->getElementsByTagName( 'body' )->item( 0 )->childNodes as $node ) {
		$node = $parent->ownerDocument->importNode( $node, true );
		$parent->appendChild( $node );
	}
}


/**
 * o
 *
 * @param   int $i  Integer to make into an ordinal
 * @return  string      String ordinal number
 */
function o( int $i ) {
	return $i . @( ( $j = abs( $i ) % 100 ) > 10 && $j < 14 ? 'th' : ( array( 'th', 'st', 'nd', 'rd' )[ $j % 10 ] ?: 'th' ) );
}

/**
 * Generate a map link
 *
 * @param   array  $address Address lines
 * @param   string $ll Lat and Long coordinates
 * @param   string $q Name of place
 * @return  string  Map URL
 */
function map_url( array $address, string $ll, string $q ) {
	$data  = array(
		'address' => html_entity_decode( implode( ',', $address ), ENT_QUOTES, 'UTF-8' ),
		'll'      => $ll,
		'q'       => html_entity_decode( $q, ENT_QUOTES, 'UTF-8' ),
	);
	$query = http_build_query( $data );
	$http  = 'https://maps.apple.com/?';
	return $http . $query;
}

/**
 * Format a phone number
 *
 * @param   string $phone Phone number with country code
 * @param   string $style Format string
 * @return  string  Formatted phone number
 */
function sanitize_phone( string $phone, string $style = '($2) $3-$4' ) {
	$format = '/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/';

	$alt_format = '/^(\+\s*)?((0{0,2}1{1,3}[^\d]+)?\(?\s*([2-9][0-9]{2})\s*[^\d]?\s*([2-9][0-9]{2})\s*[^\d]?\s*([\d]{4})){1}(\s*([[:alpha:]#][^\d]*\d.*))?$/';

	// Trim & Clean extension
	$phone = trim( $phone );
	$phone = preg_replace( '/\s*(#|x|p|ext(ension)?)\.?:?\s*(\d+)/', ' ext \3', $phone );

	if ( preg_match( $alt_format, $phone, $matches ) ) {
		return '(' . $matches[4] . ') ' . $matches[5] . '-' . $matches[6] . ( ! empty( $matches[8] ) ? ' ' . $matches[8] : '' );
	} elseif ( preg_match( $format, $phone, $matches ) ) {

		// format
		$phone = preg_replace( $format, $style, $phone );

		// Remove likely has a preceding dash
		$phone = ltrim( $phone, '-' );

		// Remove empty area codes
		if ( false !== strpos( trim( $phone ), '()', 0 ) ) {
			$phone = ltrim( trim( $phone ), '()' );
		}

		// Trim and remove double spaces created
		return preg_replace( '/\\s+/', ' ', trim( $phone ) );
	}

	return false;
}

/**
 * Wrap array values in <li> tags
 *
 * @param   array $a Array of elements
 * @return  array Array with values wrapped in <li> tags
 */
function list_items( $a = array(), array $attrs = array() ) {
	if ( empty( $a ) ) {
		return;
	}
	if ( ! is_array( $a ) ) {
		$a = array( $a );
	}

	$a = array_filter( $a );

	return array_map(
		function ( $el ) use ( $attrs ) {
			return buildAttributes( $attrs, 'li', $el );
		},
		$a
	);
}

/**
 * Test if an array is multidimensional
 *
 * @param   array $array
 * @return  bool  true if it is multidimensional
 */
function is_multi( array $array ):bool {
	return is_array( $array[ array_key_first( $array ) ] );
}

/**
 * Find the common prefix of two strings
 *
 * @param   string $s1
 * @param   string $s2
 * @return  string Prefix common to both strings
 */
function getPrefix( string $s1 = '', string $s2 = '' ) {
	$len = min( strlen( $s1 ), strlen( $s2 ) );
	for ($i = 0; $i < $len && $s1[ $i ] == $s2[ $i ]; $i++);
	return substr( $s1, 0, $i );
}

/**
 * Remove prefix cruft from array keys
 *
 * @param   array $array    Array of elements with keys that have a common prefix.
 * @param   mixed $prefix   Manually specify the prefix(es) to remove.
 * @param   mixed $retain   Keys not to alter.
 * @return  array Array with keys shorn of any common prefix.
 */
function deprefixKeys( array $array, $prefix = array(), $retain = array() ): array {
	if ( ! is_array( $array ) ) {
		return $array;
	}

	if ( empty( $prefix ) ) {
		// When sorted, first and last keys will share prefix with everything in between.
		$array_keys = array_diff_key( $array, array_flip( $retain ) );
		$array_keys = array_keys( $array );
		ksort( $array_keys );
		$s1 = array_key_first( $array_keys );
		$s2 = array_key_last( $array_keys );

		// Determine prefix.
		$prefix = getPrefix( $s1, $s2 );
	}

	// Build the new array ($clean).
	$clean = array();
	array_walk(
		$array,
		function ( $v, $k, $p ) use ( &$clean ) {
			// $deprefixed = substr($k, strlen($p));
			$deprefixed           = str_replace( $p, '', $k );
			$clean[ $deprefixed ] = $v;
		},
		$prefix
	);

	return $clean;
}


/**
 * Insert an icon
 *
 * @param string $icon Slug for the icon
 * @return string Icon tag
 *
 * Libraries and examples:
 * FontAwesome: 'fas fa-cart'        https://fontawesome.com/cheatsheet
 * Ionicons:    'cart-outline'        https://ionicons.com
 * UIkit Icons:    'icon: cart'        https://getuikit.com/docs/icon#library
 */
function get_icon( string $icon = '', $attrs = array(), $size = null ) {

	if ( empty( $icon ) ) {
		return null;
	}

	$attrs = ! is_array( $attrs ) ? array( $attrs ) : $attrs;
	if ( ! empty( $attrs['class'] ) ) {
		$attrs['class'] = to_array( $attrs['class'] );
	}
	$attrs['class'][] = 'uk-icon';
	$tag              = 'i';

	if ( strpos( $icon, ' fa-' ) ) {
		$attrs['class'][] = $icon;
	} elseif ( strpos( $icon, 'icon:' ) === 0 ) {
		$tag                = 'span';
		$attrs['uk-icon'][] = preg_replace( '/icon: */', '', $icon );
		if ( ! empty( $size ) ) {
			if ( \is_string( $size ) ) {
				if ( 'large' === $size ) {
					$size = 2;
				} elseif ( 'small' === $size ) {
					$size = .75;
				}
			}
			if ( \is_numeric( $size ) ) {
				$attrs['uk-icon'][] = 'ratio: ' . $size;
			}
		}
	} else {
		$tag           = 'ion-icon';
		$attrs['name'] = $icon;
		if ( ! empty( $size ) ) {
			$attrs['size'] = esc_html( $size );
		}
	}

	// $attrs['class'] = buildClass( $classes );

	// $attrs = '';
	// foreach ($attrs as $key => $value) {
	// $attrs .= $key . '="' . htmlspecialchars($value) . '" ';
	// }
	// return sprintf('<%s aria-hidden="true" class="%s" %s></%s>', $tag, implode(' ', $classes), $attrs, $tag);
	return buildAttributes( $attrs, $tag, true );
}


/**
 * Create an icon link
 *
 * @param string $icon      Slug for the icon to use (optional).
 * @param string $text      Text of link. Can include tags.
 * @param string $url       Link URL.
 * @param array  $attrs     Extra attributes to add to the link tag.
 * @return string              Link tag
 */
function icon_link( string $icon = '', $text = '', string $url = '#', array $attrs = array(), $hide_text = false ) {
	$class = isset( $attrs['class'] ) ? explode( ' ', $attrs['class'] ) : array();
	if ( ! empty( $icon ) && ! in_array( 'uk-icon-button', $class ) ) {
		$class[] = 'has-icon';
	}
	$attrs['class'] = buildClass( $class );

	$attrs['href'] = $url;

	$text = ( is_array( $text ) ) ? '<span>' . implode( '</span><span>', $text ) . '</span>' : $text;

	$text_attrs = array( 'class' => ( $hide_text ) ? 'screen-reader-text' : null );
	$text       = ( empty( $icon ) ) ? $text : buildAttributes( $text_attrs, 'span', $text );

	return sprintf( '<a %s>%s%s</a>', buildAttributes( $attrs ), get_icon( $icon ), $text );
}

/**
 * Get site contact data, as set in functions.php
 *
 * @param string $name       Name of the data as set in wp_cache_set
 * @param string $icon       Slug for the icon to use (optional)
 * @param mixed  $index      Index of single item, -1 for all items. String for a key search.
 * @param array  $attrs      Extra attributes for the link.
 * @return array                Link tags (optionally containing icon tags)
 */
function get_the_contact_data( string $name, string $icon = '', $index = 0, $attrs = array() ) {
	$data = wp_cache_get( $name );

	if ( is_string( $attrs ) ) {
		$attrs = array( 'class' => $attrs );
	}

	$class   = isset( $attrs['class'] ) ? explode( ' ', $attrs['class'] ) : array();
	$class[] = $name;

	if ( ! empty( $data ) ) {
		if ( is_numeric( $index ) ) {
			// Select the item in $data with index $index. A value of -1 will output all items in $data.
			$data = ( $index === -1 ) ? $data : \array_slice( $data, $index, 1, true );
		} else {
			// Reduce $data by searching its keys for string $index. Keys may be URLs, so $index = 'facebook' would match a Facebook URL.
			$matches = preg_grep( "/$index/", array_keys( $data ) );
			$data    = array_intersect_key( $data, array_flip( $matches ) );
		}

		foreach ( $data as $url => $text ) {
			switch ( $name ) {
				case 'email':
					// url => link text (string)
					$attrs['aria-label'] = $attrs['aria-label'] ?? "Send email to $text";
					break;
				case 'phone':
					// url => link text (string)
					$attrs['aria-label'] = $attrs['aria-label'] ?? 'Telephone ' . sanitize_phone( str_replace( 'tel:', '', $url ), '$2-$3-$4' );
					break;
				case 'fax':
					// url => link text (string)
					$attrs['aria-label'] = $attrs['aria-label'] ?? 'Send fax to ' . sanitize_phone( str_replace( 'fax:', '', $url ), '$2-$3-$4' );
					break;
				case 'address':
					// url => address lines (array)
					$text                = is_array( $text ) ? array( $text['streetAddress'], sprintf( '%s, %s %s', $text['addressLocality'], $text['addressRegion'], $text['postalCode'] ) ) : array( $text );
					$attrs['aria-label'] = $attrs['aria-label'] ?? 'Get directions to ' . implode( ', ', $text );
					$attrs['rel']        = 'noopener';
					break;
				case 'social':
					// url => link text (string)
					$domain = parse_url( $url, PHP_URL_HOST );
					// If no label is given, use text; if no text, create a label using the domain.
					$attrs['aria-label'] = $attrs['aria-label'] ?? "Visit us on $domain";
					$attrs['rel']        = 'noopener';
					$attrs['target']     = '_blank';
					break;
			}
			$attrs = array_merge(
				$attrs,
				array(
					'class' => buildClass( $class ),
				)
			);
			$a[]   = icon_link( $icon, $text, $url, $attrs );
		}

		if ( $index === -1 ) {
			return implode( '', list_items( $a ) );
		} else {
			if ( isset( $a ) ) {
				return $a[0];
			}
		}
	}
}

/**
 * Print site contact email(s)
 *
 * @param string $icon Slug for the icon to use (optional)
 * @param int    $index For multiple emails, index of email (-1 for all)
 */
function the_contact_email( string $icon = '', $index = -1, $attrs = array(), $echo = true ) {
	$contact_data = get_the_contact_data( 'email', $icon, $index, $attrs );
	if ( empty( $contact_data ) ) {
		return;
	}
	if ( $echo ) {
		echo $contact_data;
	} else {
		return $contact_data;
	}
}



/**
 * Print site contact telephone number(s)
 *
 * @param string  $icon   Slug for the icon to use (optional)
 * @param integer $index  For multiple numbers, index of number (-1 for all)
 * @param array   $attrs  Attributes for the link tag.
 */
function the_contact_phone( string $icon = '', $index = -1, $attrs = array(), $echo = true ) {
	$contact_data = get_the_contact_data( 'phone', $icon, $index, $attrs );
	if ( empty( $contact_data ) ) {
		return;
	}
	if ( $echo ) {
		echo $contact_data;
	} else {
		return $contact_data;
	}
}


/**
 * Print site contact fax number(s)
 *
 * @param string  $icon   Slug for the icon to use (optional)
 * @param integer $index  For multiple numbers, index of number (-1 for all)
 * @param array   $attrs  Attributes for the link tag.
 */
function the_contact_fax( string $icon = '', $index = -1, $attrs = array(), $echo = true ) {
	$contact_data = get_the_contact_data( 'fax', $icon, $index, $attrs );
	if ( empty( $contact_data ) ) {
		return;
	}
	if ( $echo ) {
		echo $contact_data;
	} else {
		return $contact_data;
	}
}


/**
 * the_contact_address: Print site contact address(es)
 *
 * @param string  $icon Slug for the icon to use (optional)
 * @param integer $index For multiple addresses, index of address (-1 for all)
 */
function the_contact_address( string $icon = '', $index = -1, $attrs = array(), $echo = true, $name = true ) {
	$data = wp_cache_get( 'address' );
	// TODO: link

	if ( empty( $data ) ) {
		return;
	}

	if ( is_numeric( $index ) ) {
		// Select the item in $data with index $index. A value of -1 will output all items in $data.
		$data = ( $index === -1 ) ? $data : \array_slice( $data, $index, 1, true );
	} else {
		// Reduce $data by searching its keys for string $index. Keys may be URLs, so $index = 'facebook' would match a Facebook URL.
		$matches = preg_grep( "/$index/", array_keys( $data ) );
		$data    = array_intersect_key( $data, array_flip( $matches ) );
	}

	$contact_data = '';

	if ( $name === true ) {
		$site_name = get_bloginfo( 'name' );
	} elseif ( ! empty( $name ) ) {
		$site_name = apply_filters( 'the_content', $name );
	} else {
		$site_name = null;
	}

	foreach ( $data as $data_url => $data_address ) :
		ob_start();

		if ( ! empty( $icon ) ) {
			echo '<div class="has-icon">' . get_icon( $icon );
		}

		$default_attrs = array(
			'itemscope' => null,
			'itemtype'  => 'https://schema.org/LocalBusiness',
		);
		$attrs         = array_filter( array_merge( $default_attrs, $attrs ) );
		echo buildAttributes( $attrs, 'address' );

		// Prepend business name (site name) if only one address
		if ( count( $data ) === 1 && ! empty( $site_name ) ) {
			echo buildAttributes( array( 'itemprop' => 'name' ), 'div', $site_name );
		}
		?>
<div itemprop='address' itemscope itemtype='https://schema.org/PostalAddress'>
		<?php
			$address = array();
		foreach ( $data_address as $itemprop => $item ) {
			$item_html = buildAttributes( array( 'itemprop' => $itemprop ), 'span', $item );
			switch ( $itemprop ) {
				case 'streetAddress':
					$item_html;
					break;
				case 'addressLocality':
					$item_html .= ', ';
					break;
			}
			$address[] = $item_html;
		}
		echo join( ' ', $address );
		?>
</div>
		<?php
		echo '</address>';
		if ( ! empty( $icon ) ) {
			echo '</div>';
		}
		$contact_data .= ob_get_clean();
	endforeach;

	if ( $echo ) {
		echo $contact_data;
	} else {
		return $contact_data;
	}
}


/**
 * the_contact_social: Print site contact social media links
 */
function the_contact_social( string $icon = '', $index = -1, $attrs = array(), $echo = true ) {
	$contact_data = get_the_contact_data( 'social', $icon, $index, $attrs );
	if ( empty( $contact_data ) ) {
		return;
	}
	if ( $echo ) {
		echo $contact_data;
	} else {
		return $contact_data;
	}
}


/**
 * Gets the ID of the post, even if it's not inside the loop.
 *
 * @uses WP_Query
 * @uses get_queried_object()
 * @extends get_the_ID()
 * @see get_the_ID()
 *
 * @return int
 * @link https://gist.github.com/morganestes/5486338
 */
function gt_get_the_ID() {
	if ( in_the_loop() ) {
		$post_id = get_the_ID();
	} else {
		/** @var $wp_query wp_query */
		global $wp_query;
		$post_id = $wp_query->get_queried_object_id();
	}
	return $post_id;
}


/**
 * getACFs: Prepare in a sensible array any Custom Fields data attached to the post.
 *
 * Fields may have more than one value, so each field key contains an array of values:
 *      'field_name'    => array( 0 => [first value], 1 => [second value], 2 => [third value])
 * If $flatten is true, when there is only one value, you will get this instead:
 *      'field_name'    => [first value]
 *
 * @param   int                                        $post_id
 * @param   bool                                       $flatten        Single field values are not stored in numbered arrays. (See example above.)
 * @param           $post_terms     Any other post terms to get.
 *
 *  @return  array  associative array with field slugs as keys (with any common prefix removed), and the prepared metadata as values
 */
function getACFs( $post_id = '', $flatten = true, $post_terms = null ) {
	if ( empty( $post_id ) ) {
		$post_id = gt_get_the_ID();
	}

	$acf = get_field_objects( $post_id );
	if ( empty( $acf ) ) {
		return array();
	}

	// Optimize, make consistent the backing data structure. Return $data as the working data array.
	$data_raw = deprefixKeys( $acf );
	$data     = array();

	foreach ( $data_raw as $f => $o ) {
		// label
		// name
		// value (raw value)
		// view (value plus prepend, append)

		// pp($f);
		// name will produce the data- attribute for the post element, i.e., data-name
		$key    = $o['name'];
		$values = $o['value'];
		$field  = array();

		// $meta will contain any extra data apart from 'slug' and 'view'.
		// $meta = array();
		if ( is_array( $values ) ) {
			if ( $o['type'] === 'group' || $o['type'] === 'repeater' ) {
				foreach ( $o['sub_fields'] as $subfield ) {
					$field[ $subfield['name'] ] = array(
						'type'    => $subfield['type'],
						'label'   => $subfield['label'],
						'prepend' => empty( $subfield['prepend'] ) ? '' : $subfield['prepend'],
						'append'  => empty( $subfield['append'] ) ? '' : $subfield['append'],
					);
				}
			}
			// if($o['type'] === 'repeater') {
			// pp($field);
			// }
			foreach ( $values as $name => $value_data ) {
				$field_key_prev = '';

				// Cycle through each value. Better array structure for multiple values.
				if ( ! is_array( $value_data ) ) {
					$value_data = array( $value_data );
				}
				foreach ( $value_data as $value_key => $value ) {

					// multiple values
					$field_key = is_numeric( $value_key ) ? $name : $value_key;

					if ( ! empty( $value ) ) {
						$metric       = '';
						$value_format = '';
						$prepend      = ! empty( $field[ $field_key ]['prepend'] ) ? $field[ $field_key ]['prepend'] : '';
						$append       = ! empty( $field[ $field_key ]['append'] ) ? $field[ $field_key ]['append'] : '';
						$type         = $field[ $field_key ]['type'];

						// Format values depending on type of field
						if ( 'relationship' === $type ) {
							$value_format = sprintf( '<a href="%s">%s</a>', get_permalink( $value ), get_the_title( $value ) );
							ob_start();
							mp_wc_template_single_sku( $value );
							$sku_html = ob_get_clean();
							$append  .= preg_replace( array( '#<p[^>]*>#', '#</p>#' ), array( '(', ')' ), $sku_html );
						} elseif ( 'true_false' === $type ) {
							$value = boolval( $value ) ? 'yes' : 'no';
						} elseif ( 'number' === $type ) {

							// Format View depending on prepend/append strings
							switch ( wp_strip_all_tags( $append ) ) {
								case '°F':
									$round        = ( ( $value - 32 ) / 1.8 ) < 1 ? 1 : 0; // °C is rounded to 1 decimal only if < 1
									$value_metric = $round === 1 ? bcdiv( ( $value - 32 ) / 1.8, 1, $round ) : round( ( $value - 32 ) / 1.8, $round ); // Fahrenheit to Celsius
									$metric       = sprintf( '(%s °C)', $value_metric );
									$value        = round( $value, 0 ); // °F is rounded to 0 decimals
									break;
								case 'gallons':
									// if less than 200 gallons use pints, not liters
									if ( $value < 200 ) {
										$value_metric = round( $value * 8, -1 ); // gallons -> pints
										$metric       = sprintf( '(%s pints)', number_format( $value_metric ) );
									} else {
										$value_metric = round( $value * 3.78541, -3 ); // gallons -> liters
										$metric       = sprintf( '(%s liters)', number_format( $value_metric ) );
									}
									break;
								case 'gpm':
									$value_metric = round( $value * 3.785411784, 0 );
									$metric       = sprintf( '(%s lpm)', $value_metric );

									// if less than 1 gallon, use quarts / min
									if ( $value < 1 ) {
										$append = 'qt/min';
										$value  = $value * 4; // gallons -> quarts
									}
									break;
								case 'lb':
									// if less than 1 use ounces
									if ( $value <= 1 ) {
										$value_metric = round( $value * 453.59237, -2 ); // lbs -> grams
										$metric       = sprintf( '/ %s g', $value_metric );
										$append       = 'oz.';
										$value        = round( $value * 16, 2 ); // lbs -> oz
									} else {
										if ( ( $value * 0.453592 < 1 ) ) {
											$value_metric = round( $value * 453.59237, -2 ); // lbs -> g
											$metric       = sprintf( '/ %s g', $value_metric );
										} else {
											$value_metric = round( $value * 0.453592, 2 ); // lbs -> kg
											$value_metric = preg_replace( '/.0[1-9]/', '', $value_metric ); // "round" decimals < 0.1
											$metric       = sprintf( '/ %s kg', $value_metric );
										}
									}
									break;
							}

							// Add some number formatting to values that are numbers
							if ( is_numeric( $value ) && $value >= 1000 ) {
								$value_format = number_format( $value );
							}
						}

						// Format View depending on field key
						// switch($field_key) {
						// case 'icon':
						// $value_format = buildAttributes([ 'uk-icon' => $value, 'class' => 'uk-margin-small-right' ], 'span', TRUE);
						// break;
						// case 'text':
						// $value_format_attrs = $field_key_prev === 'icon' ? [ 'class' => 'uk-inline' ] : [];
						// $value_format = buildAttributes($value_format_attrs, 'span', $value);
						// break;
						// }

						if ( empty( $value_format ) ) {
							$value_format = $value;
						}
						$view       = trim_join( ' ', $prepend, $value_format, $append, $metric );
						$field_data = array(
							'label' => ! empty( $field[ $field_key ]['label'] ) ? $field[ $field_key ]['label'] : $value_key,
							'value' => $value,
							'view'  => $view,
						);

						if ( is_numeric( $value_key ) ) {
							$data[ $key ][ $field_key ][] = $field_data;
						} else {
							$data[ $key ][ $name ][ $field_key ] = $field_data;
						}

						$field_key_prev = $field_key;
					}
				}
			}
		}

		// Fields with multiple values. Store the value arrays (with name, view keys) inside a normal numerically-indexed array.
		// if ( is_array($value) ) {
		// $name = $view = array();
		// foreach($value as $v) {

		// Handle different types/classes differently.

		// Object: WP_Post
		// if ( is_object($v) && get_class($v) === 'WP_Post' ) {
		// $data[$f][] = array(
		// 'name' => $v->ID,
		// 'view' => $v->post_title,
		// );
		// } elseif($o['type'] === 'group') {
		// $prepend = !empty($v['prepend']) ? $v['prepend'] : '';
		// $append = !empty($v['append']) ? $v['append'] : '';

		// $data[$f][$v['name']] = array(
		// 'name' => $v['name'],
		// 'view' => is_string($view) ? trim_join(' ', $prepend, $view, $append ) : $view,
		// );
		// }

		// }
		// } else {

		// Some field types (selects, date-pickers, etc.) need extra massaging. Do that here.

		// switch (true) {

		// Select
		// case stristr($o['type'], 'select'):
		// $view = $o['choices'][$value];
		// break;

		// Date/Time Picker
		// case stristr($o['type'], '_picker'):
		// $dt = DateTime::createFromFormat($o['return_format'], $value, wp_timezone());
		// $name = stristr($o['type'], 'date_') ? $dt->format('Y-m-d H:i:s') : $dt->format('H:i:s');
		// $view = $dt->format($o['display_format']);
		// $meta['object'] = $dt;
		// $meta['display_format'] = $o['display_format'];
		// break;
		// }

		// ACF lets you set strings to prepend/append, which lets us know the units, for instance.
		// Example: $value = 60, $append = 'minutes'; $value = 4.5, $append = 'credits'.
		// That way, we don't have to do an if/else if/else or switch block on the field name.

		// $prepend = array_key_exists('prepend', $o) ? $o['prepend'] : '';
		// $append = array_key_exists('append', $o) ? $o['append'] : '';

		// if ( !empty($name) ) {
		// $data[$f][] = array_merge( $meta, array(
		// 'name' => $name,
		// 'view' => is_string($view) ? trim_join(' ', $prepend, $view, $append ) : $view,
		// ));
		// }
		// }
	}

	// if($flatten) {
	// while ( getArrayDepth($data) > 0 ) {
	// $data = array_map(function($i) {
	// return reset($i);
	// }, $data);
	// }
	// }

	// Add in any additional terms
	if ( ! is_array( $post_terms ) ) {
		$post_terms = to_array( $post_terms );
	}
	foreach ( $post_terms as $post_term ) {
		$terms = wp_get_post_terms( $post_id, $post_term );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$data[ $post_term ][] = array(
					'name' => $term->slug,
					'view' => $term->name,
				);
			}
		}
	}

	return $data;
}


/**
 * getMonthstamp: Return number of months between a date and January 1, 1970.
 *
 * @param   string $date
 * @return  int     number of months between $date and January 1, 1970
 */
function getMonthstamp( $date ) {
	$d1 = new DateTime( '1970-01-01' );
	$d2 = $date instanceof DateTime ? $date : new DateTime( $date );
	return ( $d1->diff( $d2 )->m + ( $d1->diff( $d2 )->y * 12 ) );
}


function simplify_array( &$item ) {
	if ( is_array( $item ) && count( $item ) == 1 ) {
		$item = reset( $item );
	}
}

/**
 * buildSchema: Return organization schema JSON record and <script> tag
 *
 * @param   array $schema: Override/add elements to the generate schema record
 * @return  string  <script> tag containing JSON schema record, goes in <head>
 */
function buildSchema() {
	$schema = wp_cache_get( 'schema' );

	$auto_schema = array(
		'@context'    => 'http://schema.org',
		'@type'       => 'Organization',
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'logo'        => get_asset_url( 'images/logo.svg' ),
		'url'         => get_site_url(),
	);

	// Pull from Yoast if possible (WPSEO_Options Class)
	$yoast_data = array(
		'name' => 'company_name',
		'logo' => 'company_logo',
	);
	if ( class_exists( 'WPSEO_Options' ) ) {
		foreach ( $yoast_data as $schema_field => $yoast_field ) {
			$field = WPSEO_Options::get( $yoast_field );
			if ( ! empty( $field ) ) {
				$auto_schema[ $schema_field ] = $field;
			}
		}
	}
	// Use Home meta description as schema description, if present
	$frontpage_id = (int) get_option( 'page_on_front' );
	if ( ! empty( $frontpage_id ) ) {
		$home_meta = get_post_meta( $frontpage_id, '_yoast_wpseo_metadesc', true );
		if ( ! empty( $home_meta ) ) {
			$auto_schema['description'] = $home_meta;
		}
	}

	// These are all defined in the _setup.php file.
	foreach ( wp_cache_get( 'email' ) as $url => $text ) {
		$auto_schema['email'][] = explode( ':', $url )[1];
	}
	foreach ( wp_cache_get( 'phone' ) as $url => $text ) {
		$auto_schema['telephone'][] = explode( ':', $url )[1];
	}
	foreach ( wp_cache_get( 'address' ) as $geo => $address ) {
		$auto_schema['address'][] = array_merge(
			array(
				'@type'          => 'PostalAddress',
				'addressCountry' => 'US',
			// any other defaults?
			),
			$address
		);

		if ( filter_var( $geo, FILTER_VALIDATE_URL ) ) {
			$auto_schema['hasMap'][] = $geo;

			parse_str( parse_url( $geo, PHP_URL_QUERY ), $geo_query );
			if ( array_key_exists( 'll', $geo_query ) ) {
				$auto_schema['geo'][] = array(
					'@type'     => 'GeoCoordinates',
					'latitude'  => explode( ',', $geo_query['ll'] )[0],
					'longitude' => explode( ',', $geo_query['ll'] )[1],
				);
			}
		}
		// TODO: allow for a geoshape
		// elseif (array_key_exists('circle', $geo_query)) {
		// $auto_schema['geo'][] = array_merge([ '@type' => 'GeoShape' ], $geo );
		// }
	}

	// Add SEO/social-requested Schema image sizes. Images can be cropped/selected in Theme Customizer.
	// Image URLs must be crawlable. Crawlers will select the best image to display based on context.
	global $schema_image_sizes;
	foreach ( $schema_image_sizes as $slug => $data ) {
		$image_id = get_theme_mod( "schema_image_{$slug}" );
		if ( $image_id ) {
			$image_url = wp_get_attachment_url( $image_id );
			if ( $image_url ) {
				$auto_schema['image'][] = $image_url;
			}
		}
	}

	// Link to all the defined social media presences. The 'social' object called here is
	// built from the social media links defined in the Yoast SEO plugin options.
	$auto_schema['sameAs'] = array_keys( wp_cache_get( 'social' ) );

	if ( ! empty( $schema ) ) {
		$schema      = (array) json_decode( $schema );
		$auto_schema = array_merge( $auto_schema, $schema );
	}

	array_walk( $auto_schema, 'simplify_array' );

	return json_encode( $auto_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
}

function isJSON( string $string ) {
	json_decode( $string );
	return ( json_last_error() == JSON_ERROR_NONE );
}

function isHTML( $string ) {
	return $string != strip_tags( $string ) ? true : false;
}

function isExternalURL( string $url ) {
	$components = parse_url( $url, PHP_URL_HOST );
	$internal   = parse_url( get_site_url(), PHP_URL_HOST );
	return ! empty( $components ) && strcasecmp( $components, $internal );
}

function isSVG( $image ): bool {
	$type = '';
	if ( is_int( $image ) ) {
		// is an id
		$type = get_post_mime_type( $image );
	} elseif ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
		// is a url
		$type = image_type_to_mime_type( exif_imagetype( $image ) );
	} elseif ( validate_file( $image ) === 0 ) {
		// is a file
		$type = mime_content_type( $image );
	}
	return strpos( $type, 'svg' ) !== false;
}

function svgViewBox( $path ) {
	if ( ! empty( $path ) && file_exists( $path ) && isSVG( $path ) ) {
		$svg          = simplexml_load_file( $path );
		$viewbox_node = $svg->xpath( '@viewBox' );
		if ( ! empty( $viewbox_node ) ) {
			$viewbox_value = (string) $viewbox_node[0]->viewBox;
			$viewbox_array = explode( ' ', $viewbox_value );
			if ( count( $viewbox_array ) === 4 ) {
				$viewbox = array_combine( array( 'x', 'y', 'width', 'height' ), $viewbox_array );
			}
			return $viewbox;
		}
	}
	return false;
}

function has_gutenberg_blocks() {
	if ( is_admin() ) {
		return false;
	}
	$post = get_post();
	return $post ? has_blocks( $post->post_content ) : false;
}

function has_emoji( $string ) {
	$unicodeRegexp = '([*#0-9](?>\\xEF\\xB8\\x8F)?\\xE2\\x83\\xA3|\\xC2[\\xA9\\xAE]|\\xE2..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?(?>\\xEF\\xB8\\x8F)?|\\xE3(?>\\x80[\\xB0\\xBD]|\\x8A[\\x97\\x99])(?>\\xEF\\xB8\\x8F)?|\\xF0\\x9F(?>[\\x80-\\x86].(?>\\xEF\\xB8\\x8F)?|\\x87.\\xF0\\x9F\\x87.|..(\\xF0\\x9F\\x8F[\\xBB-\\xBF])?|(((?<zwj>\\xE2\\x80\\x8D)\\xE2\\x9D\\xA4\\xEF\\xB8\\x8F\k<zwj>\\xF0\\x9F..(\k<zwj>\\xF0\\x9F\\x91.)?|(\\xE2\\x80\\x8D\\xF0\\x9F\\x91.){2,3}))?))';
	return (bool) ( preg_match( $unicodeRegexp, $string ) === 1 );
}

// For mp_sanitize_attrs, we need to adjust the esc_attr rule to allow things like uk-slider='toggle > *'
add_filter( 'attribute_escape', 'mp_sanitize_attrs_esc_attr', 10, 2 );
function mp_sanitize_attrs_esc_attr( $safe_text, $text ) {
	$safe_text = str_replace( ' &gt; ', ' > ', $safe_text );
	return $safe_text;
}

/**
 * mp_sanitize_attrs
 *
 * @param   array $attrs      The attributes array
 * @return  array                Sanitized attributes array
 *
 * Make some smart adjustments to $attrs (HTML element attributes and values)
 */
function mp_sanitize_attrs( $attrs = array() ) {
	// Apart from uk-*, list here any attributes that are allowed to be empty.
	// Otherwise, empty attributes are removed.
	$allowed_empty = array(
		'autocomplete',
		'autofocus',
		'checked',
		'crossorigin',
		'disabled',
		'formnovalidate',
		'hidden',
		'multiple',
		'readonly',
		'required',
	);

	// Assume a string is a classname
	if ( is_string( $attrs ) ) {
		$attrs = array( 'class' => $attrs );
	}

	if ( empty( $attrs ) ) {
		return array();
	}

	foreach ( $attrs as $attr => &$val ) {
		// Ignore empty non-UIkit and non-data attributes
		if ( empty( $val ) && ! in_array( $attr, $allowed_empty ) && strpos( $attr, 'uk-' ) !== 0 && strpos( $attr, 'data' ) !== 0 ) {
			unset( $attrs[ $attr ] );
		}

		// Tired of doing $attrs['uk-grid'] = '', so we can now just do $attrs[] = 'uk-grid'
		// An attribute that has no value can be in the array as a value with a numeric key
		// rather than as a key with empty or null value.
		if ( is_numeric( $attr ) ) {
			unset( $attrs[ $attr ] );

			// $val should never be an array, but if it is, take the first value to quiet the error
			if ( is_array( $val ) ) {
				$val = reset( $val );
			}

			// If attribute doesn't exist or has no value, set it.
			if ( ! array_key_exists( $val, $attrs ) || empty( $attrs[ $val ] ) ) {
				$attrs[ $val ] = null;
				continue;
			}
		}

		if ( is_scalar( $val ) ) {
			// int, float, string, bool: apply proper esc_ filter
			$val = filter_var( $val, FILTER_VALIDATE_URL ) ? esc_url( $val ) : esc_attr( $val );
		} elseif ( is_array( $val ) ) {
			// array: build class attribute or join with ';' and apply esc_attr filter

			// This allows 'class' and 'style', for instance, to be arrays, which makes it
			// much easier to dynamically add to them: $attrs['class'][] = 'example'
			// buildClass allows class names can be negated with '!' prefix: e.g., !remove-me (but leave that out of this function)
			if ( 'class' === $attr ) {
				$val = trim_join( ' ', to_array( $val ) );
			} else {
				$val = associative_to_attr( $val );
			}
		}

		// Don't allow values that are argument lists to contain duplicate arguments.
		// Ignore JavaScript attributes like 'onclick' and 'onblur'.
		// And only when there are multiple arguments.
		if ( strpos( $attr, 'on' ) !== 0 && strpos( $val, '; ' ) !== false ) {
			preg_match_all( '/(?P<keys>[^;: ]+):\s*(?P<values>[^;]+)/', $val, $val_kv_matches );
			if ( ! empty( $val_kv_matches ) ) {
				$val_pairs = array_combine( $val_kv_matches['keys'], $val_kv_matches['values'] );
				$val       = associative_to_attr( $val );
			}
		}

		$val = esc_attr( $val );
	}

	// Perform some smart shortcuts, like adding attributes that always go with certain classes,
	// and classes that always go with certain attributes.
	if ( ! empty( $attrs ) ) {
		if ( ! empty( $attrs['class'] ) ) {

			// uk-grid class, add uk-grid attribute (but not the reverse -- there are certain situations when you don't want uk-grid class)
			if ( hasClass( 'uk-grid', $attrs['class'] ) && ! isset( $attrs['uk-grid'] ) ) {
				$attrs['uk-grid'] = null;
			}

			// .uk-hidden -> [hidden]
			if ( hasClass( 'uk-hidden', $attrs['class'] ) && ! isset( $attrs['hidden'] ) ) {
				$attrs['hidden'] = null;
				$attrs['class'] .= ' !uk-hidden';
			}

			// Add master 'uk-*' class when a secondary uk-*-* class is found; i.e., uk-button-secondary -> uk-button uk-button-secondary
			$attrs['class'] = preg_replace( '/(?<!!)uk-(button|container|section)-(?!group|item)/', 'uk-$1 uk-$1-', $attrs['class'] );

			// Add master 'uk-flex' class to uk-flex-* parent items (NOT to uk-flex- classes that apply to child items)
			$attrs['class'] = preg_replace( '/(?<!!)uk-flex-(?!(first|last|none|auto|1))/', 'uk-flex uk-flex-$1', $attrs['class'] );
		}
	}
	return $attrs;
}

/**
 * key1 => value1, key2 => value2  ->  key1: value1; key2: value2
 *
 * @param mixed $array
 * @return string
 */
function associative_to_attr( $array ) : string {
	if ( ! is_array( $array ) ) {
		return $array;
	}
	if ( is_associative( $array ) ) {
		$array = array_map(
			fn ( string $k, string $v): string => "{$k}: {$v}",
			array_keys( $array ),
			array_values( $array )
		);
	}
	return trim_join( '; ', $array );
}


/**
 * mp_append_attr
 *
 * Append $value to a $node's existing attribute $attr.
 */
function mp_append_attr( $attr, DOMelement $node, $value ) {
	if ( $node->hasAttribute( $attr ) ) {
		$append[] = $node->getAttribute( $attr );
	}

	if ( ! empty( $attr ) ) {
		$append[] = $value;
	}

	if ( ! empty( $append ) ) {
		if ( 'class' === $attr ) {
			$node->setAttribute( $attr, buildClass( $append ) );
		} else {
			$node->setAttribute( $attr, trim_join( '; ', $append ) );
		}
	}
}

/**
 * Sets attributes on a node
 */
function mp_node_attrs( DOMElement $node, $attrs, $keep_existing = false, $tag = null ) {
	if ( is_admin() ) {
		return $node;
	}

	// Attributes that can be appended. These will be joined into a RegEx pattern.
	$appendable = array(
		'class',
		'style',
		'uk-.*',
	);

	$attrs_to_append = preg_grep_keys( '/(' . join( '|', $appendable ) . ')/', $attrs );

	$remove_attrs = array();
	$keep_attrs   = array();

	// [ FALSE ] ... replace all $attrs
	if ( false === $keep_existing ) {
		$remove_attrs = array_keys( $attrs );
	}
	// [ 'attribute' ] ... keep it
	// [ 'attribute' => TRUE ] ... keep it
	// [ 'attribute' => FALSE ] ... remove it
	elseif ( is_array( $keep_existing ) ) {
		foreach ( $keep_existing as $k => $v ) {
			if ( is_int( $k ) ) {
				$keep_attrs[] = $v;
			} elseif ( true === $v ) {
				$keep_attrs[] = $k;
			} elseif ( false === $v ) {
				$remove_attrs[] = $k;
			}
		}
	}

	// Also remove attributes which are not appendable and are not marked as keepers.
	$replace_attrs = array_diff_key( $attrs, $attrs_to_append, array_flip( $keep_attrs ) );
	if ( ! empty( $replace_attrs ) ) {
		$remove_attrs = array_unique( array_merge( $remove_attrs, array_keys( $replace_attrs ) ) );
	}

	// First, remove the attributes we're not preserving/appending
	if ( ! empty( $remove_attrs ) ) {
		foreach ( $remove_attrs as $remove_attr ) {
			$node->removeAttribute( $remove_attr );
		}
	}

	// Append/Replace the provided attributes
	foreach ( $attrs as $attr => $value ) {
		mp_append_attr( $attr, $node, $value );
	}

	// Change tag?
	if ( ! empty( $tag ) ) {
		$node = renameNode( $node, $tag );
	}

	return $node;
}

function mp_get_queries_from_target( string $target ) {
	if ( empty( $target ) ) {
		return false;
	}

	// Sanitization to cut down on false queries
	if ( strpos( $target, '//' ) === 0 ) {
		// It's already an XPath query
		$queries = array( $target );
	} elseif ( strpos( $target, '.' ) !== false ) {
		// It's a class
		$tag_class = explode( '.', ' ' . $target );
		$tag       = ( empty( trim( $tag_class[0] ) ) ) ? '*' : trim( $tag_class[0] );
		$class     = $tag_class[1];
		if ( ! empty( $class ) ) {
			$queries = array( "//{$tag}[contains(concat(' ',normalize-space(@class),' '), ' {$class} ')]" );
		}
	} elseif ( strpos( $target, '#' ) === 0 ) {
		// It's an ID
		$target  = ltrim( $target, '#' );
		$queries = array( "//*[@id='$target']" );
	} else {
		// By default, loop over these in this order until a match is found
		$queries = array(
			"//$target",                // tag name = $target
			"//*[@id='$target']",       // id = $target
			$target,                    // already an XPath query
		);
	}
	return $queries;
}

function mp_get_nodes_from_queries( DOMDocument $dom, array $queries ) {
	$nodes = array();
	$xpath = new \DOMXPath( $dom );
	$query = current( $queries );
	while ( is_countable( $nodes ) && ! count( $nodes ) ) {
		if ( ! $query ) {
			break;
		}
		@$nodes = $xpath->query( $query );
		$query  = next( $queries );
	}
	return $nodes;
}

// Get nodeValue, useful for getting inner text
function mp_get_inner( string $html, string $target, bool $single = false ) {
	if ( is_admin() || empty( $html ) || empty( $target ) ) {
		return $html;
	}

	$dom = mp_load_html( $html );

	$queries = mp_get_queries_from_target( $target );
	$nodes   = mp_get_nodes_from_queries( $dom, $queries );
	$text    = array();
	foreach ( $nodes as $node ) {
		$text[] = $node->nodeValue;
	}
	return $single ? reset( $text ) : $text;
}

// Get attributes of target node(s)
function mp_get_attributes( string $html, string $target, bool $single = false ) {
	if ( is_admin() || empty( $html ) || empty( $target ) ) {
		return $html;
	}

	$dom = mp_load_html( $html );

	$queries     = mp_get_queries_from_target( $target );
	$nodes       = mp_get_nodes_from_queries( $dom, $queries );
	$nodes_attrs = array();
	foreach ( $nodes as $node ) {
		$attrs = array();
		if ( $node->hasAttributes() ) {
			foreach ( $node->attributes as $attr ) {
				$attrs[ $attr->nodeName ] = $attr->nodeValue;
			}
		}
		$nodes_attrs[] = $attrs;
	}
	if ( empty( $nodes_attrs ) ) {
		return false;
	} else {
		return $single ? $nodes_attrs[0] : $nodes_attrs;
	}
}

function mp_wrap_element( string $html, $targets, $tag, $attrs = array() ) {
	if ( is_admin() || empty( $html ) ) {
		return $html;
	}

	$dom = mp_load_html( $html );

	// if $attrs is a string, treat it as a class name
	if ( is_string( $attrs ) ) {
		$attrs = array( 'class' => $attrs );
	}

	$attrs = mp_sanitize_attrs( $attrs );
	// We need to build class because mp_sanitize_attrs doesn't.
	if ( ! empty( $attrs['class'] ) ) {
		$attrs['class'] = buildClass( $attrs['class'] );
	}

	if ( empty( $targets ) ) {
		$targets = '//html/body/*';
	}
	if ( ! is_array( $targets ) ) {
		$targets = array( $targets );
	}
	foreach ( $targets as $target ) {
		$queries = mp_get_queries_from_target( $target );
		$nodes   = mp_get_nodes_from_queries( $dom, $queries );
		foreach ( $nodes as $node ) {
			@$parent = $node->parentNode;
			if ( ! empty( $parent ) && $parent instanceof DOMElement ) {
				$wrap = $dom->createElement( $tag );
				foreach ( $attrs as $name => $value ) {
					$wrap->setAttribute( $name, $value );
				}
				$nodeClone = $node->cloneNode( true );
				$parent->replaceChild( $wrap, $node );
				$wrap->appendChild( $nodeClone );
			}
		}
	}
	if ( ! empty( $nodes ) && count( $nodes ) ) {
		$html = mp_save_html( $dom );
	}
	return $html;
}



// Wrap inner nodes in a wrapper. Useful for creating lists.
function mp_inner_wrap( string $html, $targets, $tag, $attrs = array() ) {
	if ( is_admin() || empty( $html ) ) {
		return $html;
	}

	$dom = mp_load_html( $html );

	// if $attrs is a string, treat it as a class name
	if ( is_string( $attrs ) ) {
		$attrs = array( 'class' => $attrs );
	}

	$attrs = mp_sanitize_attrs( $attrs );
	// We need to build class because mp_sanitize_attrs doesn't.
	if ( ! empty( $attrs['class'] ) ) {
		$attrs['class'] = buildClass( $attrs['class'] );
	}

	if ( empty( $targets ) ) {
		$targets = '//html/body/*';
	}
	if ( ! is_array( $targets ) ) {
		$targets = array( $targets );
	}

	foreach ( $targets as $target ) {
		$queries = mp_get_queries_from_target( $target );
		$nodes   = mp_get_nodes_from_queries( $dom, $queries );
		foreach ( $nodes as $node ) {
			if ( $node->hasChildNodes() ) {
				$wrap = $dom->createElement( $tag );
				foreach ( $attrs as $name => $value ) {
					$wrap->setAttribute( $name, $value );
				}

				while ( $node->childNodes->length > 0 ) {
					$child = $node->childNodes->item( 0 );
					$node->removeChild( $child );
					$wrap->appendChild( $child );
					if ( 'ul' === $tag || 'ol' === $tag ) {
						$child = renameNode( $child, 'li' );
					}
				}
				$node->appendChild( $wrap );
			}
		}
	}
	if ( ! empty( $nodes ) && count( $nodes ) ) {
		$html = mp_save_html( $dom );
	}
	return $html;
}


function mp_insert_element( string $html, string $element, $reference = null, $relationship = 'insertBefore' ) {
	if ( is_admin() || empty( $html ) ) {
		return $html;
	}

	$dom = mp_load_html( $html );

	if ( ! empty( $reference ) ) {
		$referenceQuery = mp_get_queries_from_target( $reference );
		$referenceNodes = mp_get_nodes_from_queries( $dom, $referenceQuery );
	} else {
		$referenceNodes = array( $dom );
		$relationship   = 'lastChild';
	}

	foreach ( $referenceNodes as $referenceNode ) {
		$node = $dom->createDocumentFragment();
		$node->appendXML( $element );

		switch ( $relationship ) {
			case 'firstChild':
				$firstSibling = $referenceNode->firstChild;
				$referenceNode->insertBefore( $node, $firstSibling );
				break;
			case 'lastChild':
				$referenceNode->appendChild( $node );
				break;
			case 'insertBefore':
				$parent = $referenceNode->parentNode;
				if ( $parent instanceof DOMElement ) {
					$parent->insertBefore( $node, $referenceNode );
				}
				break;
			case 'insertAfter':
				$parent = $referenceNode->parentNode;
				if ( $parent instanceof DOMElement ) {
					$parent->appendChild( $node );
				}
				break;
		}
	}

	if ( ! empty( $referenceNodes ) && count( $referenceNodes ) ) {
		$html = mp_save_html( $dom );
	}
	return $html;
}

function mp_move_element( string $html, $targets, $reference = '//html/body', $relationship = 'insertBefore', $tag = null ) {
	if ( is_admin() || empty( $html ) ) {
		return $html;
	}

	$dom = mp_load_html( $html );

	if ( ! is_array( $targets ) ) {
		$targets = array( $targets );
	}
	foreach ( $targets as $target ) {
		$nodes = array();

		// If HTML has been given, import that element into the DOM.
		if ( isHTML( $target ) ) {
			$html = $dom->createDocumentFragment();
			$html->appendXML( $target );
			$nodes[] = $html;
		} else {
			$queries = mp_get_queries_from_target( $target );
			$nodes   = mp_get_nodes_from_queries( $dom, $queries );
		}

		$referenceQuery = mp_get_queries_from_target( $reference );
		$referenceNodes = mp_get_nodes_from_queries( $dom, $referenceQuery );

		foreach ( $nodes as $node ) {
			if ( $referenceNodes instanceof DOMNodeList && $referenceNodes[0] instanceof DOMElement ) {
				// Reference node is specified
				$referenceNode = $referenceNodes[0];
			} elseif ( $node->parentNode instanceof DOMElement ) {
				// Use current parent node by default
				$referenceNode = $node->parentNode;
			} else {
				// If no parent node, bail.
				continue;
				// return $html;
			}

			switch ( $relationship ) {
				case 'firstChild':
					$firstSibling = $referenceNode->firstChild;
					if ( $node !== $firstSibling ) {
						$referenceNode->insertBefore( $node, $firstSibling );
					}
					break;
				case 'lastChild':
					$lastSibling = $referenceNode->lastChild;
					if ( $node !== $lastSibling ) {
						$referenceNode->appendChild( $node );
					}
					break;
				case 'insertBefore':
					$parent = $referenceNode->parentNode;
					if ( $parent instanceof DOMElement ) {
						$parent->insertBefore( $node, $referenceNode );
					}
					break;
			}

			if ( ! empty( $tag ) ) {
				$node = renameNode( $node, $tag );
			}
		}
	}
	if ( ! empty( $nodes ) && count( $nodes ) ) {
		$html = mp_save_html( $dom );
	}
	return $html;
}

 /**
  * Adds attributes ($attrs) to elements targeted by $targets (string/array of xpath queries, tag names, or ids).
  *
  * $keep_existing can be TRUE, FALSE, or an array of attributes to append (keeping original values).
  *
  * Also change the tag of the element to $tag.
  * Uses buildClass, so will not duplicate classnames.
  */
function mp_html_attrs( string $html, $targets, array $attrs, $keep_existing = false, $tag = null, $remove = false ) {
	if ( is_admin() || empty( $html ) || empty( $targets ) ) {
		return $html;
	}

	// Sanitize new attributes
	$attrs = mp_sanitize_attrs( $attrs );

	$dom = mp_load_html( $html );

	if ( ! is_array( $targets ) ) {
		$targets = array( $targets );
	}
	foreach ( $targets as $target ) {
		$queries = mp_get_queries_from_target( $target );
		$nodes   = mp_get_nodes_from_queries( $dom, $queries );

		if ( ! empty( $nodes ) ) {
			foreach ( $nodes as $node ) {
				if ( $remove ) {
					$node->parentNode->removeChild( $node );
				} else {
					$node = mp_node_attrs( $node, $attrs, $keep_existing, $tag );
				}
			}
		}
	}
	if ( ! empty( $nodes ) && count( $nodes ) ) {
		$html = mp_save_html( $dom );
	}
	return $html;
}

function mp_html_remove_by_class( string $html, $class ) : string {
	if ( empty( $class ) ) {
		return $html;
	}

	// Allow $class to include a tag, like: ul.gform_fields
	if ( ! str_contains( $class, '.' ) ) {
		$class = '.' . $class;
	}
	$xpath_query = mp_get_queries_from_target( $class );
	$html        = mp_html_attrs( $html, $xpath_query, array(), false, null, true );
	return $html;
}

function mp_html_remove( string $html, $target ) {
	if ( empty( $target ) ) {
		return $html;
	}

	$html = mp_html_attrs( $html, $target, array(), false, null, true );
	return $html;
}


function mp_html_class( string $html, $target, $add_class, $keep_existing = false, $tag = null ) {
	if ( empty( $add_class ) ) {
		return $html;
	}

	$attrs = array(
		'class' => $add_class,
	);
	$html  = mp_html_attrs( $html, $target, $attrs, $keep_existing, $tag );
	return $html;
}

function mp_html_attrs_by_class( string $html, $class, $attrs, $keep_existing = false, $tag = null ) {
	if ( empty( $class ) ) {
		return $html;
	}

	if ( ! is_array( $attrs ) ) {
		$attrs = to_array( $attrs );
	}

	// Allow $class to include a tag, like: ul.gform_fields
	if ( ! str_contains( $class, '.' ) ) {
		$class = ".{$class}";
	}
	$xpath_query = mp_get_queries_from_target( $class );
	$html        = mp_html_attrs( $html, $xpath_query, $attrs, $keep_existing, $tag );
	return $html;
}

function mp_html_class_by_class( string $html, $class, $add_class, $keep_existing = false, $tag = null ) {
	if ( empty( $add_class ) || empty( $class ) ) {
		return $html;
	}

	$attrs = array( 'class' => $add_class );
	$html  = mp_html_attrs_by_class( $html, $class, $attrs, $keep_existing, $tag );
	return $html;
}



// Wraps images with links, given the custom field 'url' on image attachments.
// Used for having links on Gutenberg Gallery images
function mp_image_element( string $html, $ids, $attrs = array(), $linkTo = null, $link_attrs = array() ) {
	if ( empty( $ids ) ) {
		return $html;
	}

	$attrs = mp_sanitize_attrs( $attrs );

	$dom   = mp_load_html( $html );
	$xpath = new DOMXPath( $dom );

	foreach ( $ids as $i => $id ) {
		$data      = get_post_meta( $id );
		$mime_type = get_post_mime_type( $id );

		// @$image = $xpath->query("//*[not(self::a)]/img[@data-id='{$id}']");
		@$imgs = $xpath->query( "//img[@data-id='{$id}']" );
		if ( ! empty( $imgs ) ) {
			foreach ( $imgs as $img ) {

				// $img->setAttribute('loading', 'lazy');
				$img = mp_node_attrs( $img, $attrs, true );

				// Apply srcset and sizes
				if ( ! $img->hasAttribute( 'srcset' ) && ! $img->hasAttribute( 'data-srcset' ) ) {
					$srcset = wp_get_attachment_image_srcset( $id, 'full' );
					if ( ! empty( $srcset ) ) {
						$img->setAttribute( 'srcset', $srcset );
						// $link_attrs['data-srcset'] = $srcset;
					}

					if ( ! $img->hasAttribute( 'sizes' ) && ! $img->hasAttribute( 'data-sizes' ) ) {
						$sizes = ! empty( $attrs['sizes'] ) ? $attrs['sizes'] : wp_get_attachment_image_sizes( $id, 'full' );
						if ( ! empty( $sizes ) ) {
							$img->setAttribute( 'sizes', $sizes );
							// $link_attrs['data-sizes'] = $sizes;
						}
					}
				}

				// SVGs without height/width attributes will render at 0x0, so we set a width (height is auto)
				if ( $mime_type === 'image/svg+xml' ) {
					if ( ! $img->hasAttribute( 'width' ) ) {
						$img->setAttribute( 'width', '100%' );
					}
				}

				// Use meta key $linkTo, or link to full image.
				if ( ! empty( $linkTo ) && isset( $data[ $linkTo ] ) ) {
					$url = reset( $data[ $linkTo ] );
				} elseif ( ! empty( $linkTo ) ) {
					if ( ! has_image_size( $linkTo ) ) {
						$linkTo = 'full';
					}
					$attachment_src = wp_get_attachment_image_src( $id, $linkTo );
					if ( $attachment_src ) {
						$url = $attachment_src[0];
					}
				}

				// Grab the caption set in the Gallery block
				$figcaption = $xpath->query( './ancestor::figure/figcaption', $img );
				if ( $figcaption->length ) {
					$caption = $figcaption[0]->nodeValue;
				}

				// metadata from Media Library
				$attachment = get_post( $id );
				if ( empty( $attachment ) ) {
					continue;
				}

				$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
				// uncomment below to force the Media Library caption if none is specified by user
				// if(!isset($caption)) $caption = $attachment->post_excerpt;

				// Descriptions will show in the lightbox, with the caption as a heading
				$description = $attachment->post_content;

				// Use caption as alt text if no alt text is present
				if ( ! empty( $attrs['alt'] ) ) {
					$link_attrs['data-alt'] = $attrs['alt'];
				} elseif ( ! empty( $alt ) ) {
					$link_attrs['data-alt'] = $alt;
				} elseif ( ! empty( $caption ) ) {
					$attrs['alt']           = $caption;
					$link_attrs['data-alt'] = $caption;
				}

				if ( ! empty( $caption ) ) {
					// Generate the caption portion of the lightbox caption bar
					ob_start();
					?>
					<h3 class="uk-margin-remove-bottom alt"><?php echo $caption; ?></h3>
					<?php if ( ! empty( $description ) ) : ?>
					<span class="uk-text-meta"><?php echo $description; ?></span>
					<?php endif; ?>

					<?php
					$caption = ob_get_clean();

					// Generate a counter portion of the lightbox caption bar
					ob_start();
					?>
					<div class='fraction'>
						<div class='uk-h3 alt uk-position-relative'><?php echo $i + 1; ?>
							<div class='uk-h5 alt uk-text-meta denominator'><?php echo count( $ids ); ?></div>
						</div>
					</div>
					<?php
					$counter = ob_get_clean();

					// Generate the the lightbox caption bar
					ob_start();
					?>
					<div class='uk-container uk-container-expand'>
						<div class='uk-flex uk-flex-center uk-flex-between@m'>
							<div class='uk-text-left@m uk-margin-right'><?php echo $caption; ?></div>
							<div class='uk-visible@m uk-flex uk-flex-bottom'><?php echo $counter; ?></div>
						</div>
					</div>
					<?php
					$link_attrs['data-caption'] = preg_replace( array( '/>\s+</S', '/\s+/S' ), array( '><', ' ' ), ob_get_clean() );
				}

				// Are we linking? If so, add a link to the image
				if ( ! empty( $url ) ) {
					$link_attrs['href'] = esc_url( $url );

					// Add <a> classes. Needs to be display: block so the <a> fills the available space
					$link_attrs['class'] = ( isset( $link_attrs['class'] ) ) ? buildClass( $link_attrs['class'], 'uk-inline uk-display-block' ) : 'uk-inline uk-display-block';

					// 'rel' attribute
					$rel = array();
					foreach ( array( 'nofollow', 'sponsored', 'noopener', 'noreferrer' ) as $rel_value ) {
						if ( isset( $data[ $rel_value ] ) && ! empty( $data[ $rel_value ][0] ) ) {
							$rel[] = $rel_value;
						}
					}
					// $link_attrs can override the 'rel' attribute set by custom fields
					if ( ! empty( $rel ) ) {
						$link_attrs['rel'] = ( isset( $link_attrs['rel'] ) ) ? $link_attrs['rel'] : trim_join( ' ', $rel );
					}

					// 'target' attribute: true = open new window
					if ( ! empty( $data['target'] ) ) {
						$link_attrs['target'] = ( isset( $link_attrs['target'] ) ) ? $link_attrs['target'] : '_blank';
					}

					// wrap <img> with <a> tag, or apply new attributes if it's already got an <a> tag
					$link = $xpath->query( './ancestor::a', $img );
					if ( empty( $link ) ) {
						$link = $dom->createElement( 'a' );
						$img->parentNode->replaceChild( $link, $img );
						$link->appendChild( $img );
					} else {
						$link = $link[0];
					}

					// apply link attributes
					$link = mp_node_attrs( $link, $link_attrs, false );
				}
			}
		}
	}
	$html = mp_save_html( $dom );
	return $html;
}

function mp_sanitize_filter_data( $data_value ) {
	// Default data values to 0. Empty values cause the js-filter to fail.
	if ( empty( $data_value ) || is_array( $data_value ) ) {
		$data_value = 0;
	}

	// Limit to 2 decimals.
	if ( is_numeric( $data_value ) ) {
		$data_value = number_format( $data_value, 2 );
	}

	return esc_html( $data_value );
}

function mp_get_filter_data( $fields = array(), object $post ) {
	$attrs  = array();
	$fields = to_array( $fields );
	$id     = $post->get_id();

	foreach ( $fields as $field ) {
		$filter_data = get_field( $field, $id );
		if ( is_array( $filter_data ) && ! empty( array_filter( $filter_data ) ) ) {

			// Loop over an array (field group)
			foreach ( array_filter( $filter_data ) as $data_key => $data_value ) {
				$attrs[ 'data_' . esc_html( $data_key ) ] = mp_sanitize_filter_data( $data_value );
			}
		} elseif ( ! empty( array_filter( $filter_data ) ) ) {

			// For single fields, just make the attribute
			$attrs[ 'data_' . esc_html( $field ) ] = mp_sanitize_filter_data( $filter_data );
		}
	}

	// Add price to products.
	if ( $post->get_price() ) {
		$attrs['data_price'] = $post->get_price();
	}

	// Add popularity to products.
	$count = get_post_meta( $id, 'total_sales', true );
	if ( $count > 0 ) {
		$attrs['data_total_sales'] = $count;
	}

	// Add featured to products.
	if ( $post->is_featured() ) {
		$attrs['data_featured'] = 1;
	}

	return $attrs;
}


/**
 * Function to write to the log
 */
if ( ! function_exists( 'mp_write_log' ) ) {
	function mp_write_log( $log ) : void {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}


/**
 * Generate Random Password
 */
if ( ! function_exists( 'mp_random_password' ) ) {
	function mp_random_password(
		$length,
		$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
	) {
		$str = '';
		$max = mb_strlen( $keyspace, '8bit' ) - 1;
		if ( $max < 1 ) {
			throw new Exception( '$keyspace must be at least two characters long' );
		}
		for ( $i = 0; $i < $length; ++$i ) {
			$str .= $keyspace[ random_int( 0, $max ) ];
		}
		return $str;
	}
}

// Given the denominators in $array, returns each possible percentage value for a grid.
function grid_widths( array $array ) {
	$grid_widths = array( 100 );
	foreach ( $array as $den ) {
		for ( $i = 1; $i < $den; $i++ ) {
			$grid_widths[] = ( $i * 100 ) / (int) $den;
		}
	}
	$grid_widths = array_unique( $grid_widths );
	sort( $grid_widths, SORT_NUMERIC );
	return $grid_widths;
}

function closest( int $int, array $array ) {
	$closest = null;
	foreach ( $array as $item ) {
		if ( $closest === null || abs( $int - $closest ) > abs( $item - $int ) ) {
			$closest = $item;
		}
	}
	return $closest;
}


// Given a percentage value (like 50), returns uk-width-* class, optionally with a suffix (for @s, @m ... breakpoint modifiers)
function uk_width( int $int, string $suffix = null ) {
	if ( $int < 1 || $int > 100 ) {
		return;
	}
	$closest  = closest( $int, GRID_WIDTHS );
	$fraction = explode( '/', decimalToFraction( $closest / 100 ) );
	if ( is_array( $fraction ) ) {
		$fraction[1] ??= 1;
		return sprintf( 'uk-width-%s-%s' . $suffix, $fraction[0], $fraction[1] );
	}
	return;
}


/**
 * @see https://stackoverflow.com/questions/14330713/converting-float-decimal-to-fraction
 */
function decimalToFraction( float $decimal, $glue = ' ', int $limes = 10 ): string {
	if ( null === $decimal || $decimal < 0.001 ) {
		return '';
	}

	$wholeNumber      = (int) floor( $decimal );
	$remainingDecimal = $decimal - $wholeNumber;

	[ $numerator, $denominator ] = fareyFraction( $remainingDecimal, $limes ); // phpcs:ignore

	// Values rounded to 1 should be added to base value and returned without fraction part
	if ( is_int( $simplifiedFraction = $numerator / $denominator ) ) {
		$wholeNumber += $simplifiedFraction;
		$numerator    = 0;
	}

	return ( 0 === $wholeNumber && 0 === $numerator )
		// Too small values will be returned in original format
		? (string) $decimal
		// Otherwise let's format value - only non-0 whole value / fractions will be returned
		: trim(
			sprintf(
				'%s%s%s',
				(string) $wholeNumber ?: '',
				$wholeNumber > 0 ? $glue : '',
				0 === $numerator ? '' : ( $numerator . '/' . $denominator )
			)
		);
}

/**
 * @see https://stackoverflow.com/a/14330799/842480
 *
 * @return int[] Numerator and Denominator values
 */
function fareyFraction( float $value, int $limes ): array {
	if ( $value < 0 ) {
		[ $numerator, $denominator ] = fareyFraction( -$value, $limes ); // phpcs:ignore

		return array( -$numerator, $denominator );
	}

	$zero  = $limes - $limes;
	$lower = array( $zero, $zero + 1 );
	$upper = array( $zero + 1, $zero );

	while ( true ) {
		$mediant = array( $lower[0] + $upper[0], $lower[1] + $upper[1] );

		if ( $value * $mediant[1] > $mediant[0] ) {
			if ( $limes < $mediant[1] ) {
				return $upper;
			}
			$lower = $mediant;
		} elseif ( $value * $mediant[1] === $mediant[0] ) {
			if ( $limes >= $mediant[1] ) {
				return $mediant;
			}
			if ( $lower[1] < $upper[1] ) {
				return $lower;
			}

			return $upper;
		} else {
			if ( $limes < $mediant[1] ) {
				return $lower;
			}

			$upper = $mediant;
		}
	}
}
