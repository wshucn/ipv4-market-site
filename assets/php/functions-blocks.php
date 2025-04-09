<?php
/*
Section: Gutenberg and other block customizations
Purpose: Filters that change the HTML output of Gutenberg blocks

Author: Media Proper
Last updated: 12 November 2021

*/

/**
 * Remove Gutenberg <style> and <svg> elements that are created to help certain blocks.
 *
 * @return void
 */
function mp_remove_gutenberg_layout_support() {
	global $wp_filter;
	foreach ( $wp_filter['wp_footer'] as $wp_footer_action ) {
		foreach ( $wp_footer_action as $wp_footer_action_callback ) {
			if ( empty( $wp_footer_action_callback['function'] ) || ! is_object( $wp_footer_action_callback['function'] ) ) {
				continue;
			}
			$class = get_class( $wp_footer_action_callback['function'] );
			if ( 'Closure' !== $class ) {
				continue;
			}
			$r      = new ReflectionFunction( $wp_footer_action_callback['function'] );
			$r_vars = $r->getStaticVariables();
			foreach ( $r_vars as $key => $value ) {
				if ( 'style' !== $key && 'svg' !== $key ) {
					continue;
				}
				if ( 'svg' === $key ) {
					remove_action( 'wp_footer', $wp_footer_action_callback['function'] );
				} elseif ( strpos( $value, '.wp-container-' ) !== false ) {
					remove_action( 'wp_footer', $wp_footer_action_callback['function'] );
				}
			}
		}
	}
}


// Replace WP classes with UIkit classes
// add_filter('the_content', 'mp_uikit_classes', PHP_INT_MAX);
function mp_uikit_classes( string $content ): string {
	// WordPress class => UIkit class
	$wp_to_uikit = array(
		'/has-([^ ]+)-background-color/' => 'has-$1-background-color uk-background-$1',
		'/has-text-align-([^ ]+)/'       => 'uk-text-$1',
		'/size-medium_large/'            => 'uk-width-3-5@m',
		'/size-medium/'                  => 'uk-width-2-5@m',
		'/size-large/'                   => 'uk-width-5-6@m',
		'/size-thumbnail/'               => 'uk-width-small',
		'/is-style-alt/'                 => 'alt',
		// '/uk-container-([^ ]+)/'			=> 'uk-container uk-container-$1',
		'/[^ ]+__width-100/'             => 'uk-width-1-1',
		'/[^ ]+__width-75/'              => 'uk-width-3-4',
		'/[^ ]+__width-50/'              => 'uk-width-1-2',
		'/[^ ]+__width-25/'              => 'uk-width-1-4',
	);
	return preg_replace( array_keys( $wp_to_uikit ), array_values( $wp_to_uikit ), $content );
}

add_filter( 'render_block', 'mp_block_special_classes', PHP_INT_MAX, 2 );
function mp_block_special_classes( string $block_content, $block ): string {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// WordPress class => UIkit class
	$wp_to_uikit   = array(
		'/has-([^ ]+)-background-color/' => 'has-$1-background-color uk-background-$1',
        '/has-([^ ]+)-color/' => 'uk-text-$1',
		'/has-text-align-([^ ]+)/'       => 'uk-text-$1',
		'/size-medium_large/'            => 'uk-width-3-5@m',
		'/size-medium/'                  => 'uk-width-2-5@m',
		'/size-large/'                   => 'uk-width-5-6@m',
		'/size-thumbnail/'               => 'uk-width-small',
		'/is-style-alt/'                 => 'alt',
		// '/uk-container-([^ ]+)/'			=> 'uk-container uk-container-$1',
		'/[^ ]+__width-100/'             => 'uk-width-1-1',
		'/[^ ]+__width-75/'              => 'uk-width-3-4',
		'/[^ ]+__width-50/'              => 'uk-width-1-2',
		'/[^ ]+__width-25/'              => 'uk-width-1-4',
	);
	$block_content = preg_replace( array_keys( $wp_to_uikit ), array_values( $wp_to_uikit ), $block_content );

	// Parse .parallax-fade classname.
	$block_content = mp_html_attrs( $block_content, '//*[contains(@class, "parallax-fade")]/parent::*', array( 'uk-parallax' => 'target: !*; opacity: 0.25, 1; blur: 5,0; viewport: 0.5' ), true );

	// Remove Gutenberg's .wp-container-* layout support classes.
	$block_content = preg_replace( '/wp-container-[^ ]*/', '', $block_content );

	return $block_content;
}


/*
 * Apply our container-breaking method when 'full' classname is used.
 */
add_filter( 'render_block', 'mp_block_container_break', PHP_INT_MAX, 2 );
function mp_block_container_break( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() || empty( $block_content ) ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	// $layout_class = array_filter($classArray, function($v){ return str_contains($v, 'uk-container'); });
	// $block_attrs['class'][] = preg_filter('/^/', '!', $layout_class);

	// classname 'full' to break out of page container
	if ( in_array( 'full', $classArray ) ) {
		$block_attrs['style'][] = 'width: 100vw;
									position: relative;
									left: 50%;
									right: 50%;
									margin-left: -50vw;
									margin-right: -50vw;';
		$block_attrs['class'][] = mp_negate_class( 'full' );
		$block_attrs['class'][] = 'full-width';
		$block_content          = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	}
	return $block_content;
}

/*
 * Many Gutenberg blocks (not just Group blocks) often have 'inner blocks', which are
 * contained within an inner-container, which is automatically created. The problem is
 * that when you add uk-grid or uk-flex to the parent, the children don't pick it up
 * because they aren't direct children anymore. This fixes that by moving those types
 * of classes to the inner container.
 */

// Remove the inner container.
remove_filter( 'render_block_core/group', 'wp_restore_group_inner_container', 10 );

add_filter( 'render_block_core/group', 'mp_block_container_classes', 9, 2 );
function mp_block_container_classes( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() || empty( $block_content ) ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	// $layout_class = array_filter($classArray, function($v){ return str_contains($v, 'uk-container'); });
	// $block_attrs['class'][] = preg_filter('/^/', '!', $layout_class);

	if ( ! empty( $block['innerBlocks'] ) && ! empty( $block_content ) ) {
		// Move uk-grid*, uk-flex*, and uk-child-width-* classes to inner container
		$layout_class          = mp_array_starts_with( $classArray, 'uk-container', 'uk-child-width-', 'uk-grid', 'uk-flex', 'uk-card', 'is-style-container' );
		$ignored               = array( 'uk-flex-last', 'uk-flex-first', 'uk-card-header', 'uk-card-body', 'uk-card-footer' );
		$layout_class_filtered = array_diff( $layout_class, $ignored );
		if ( ! empty( $layout_class_filtered ) ) {
			$inner_container = '//div[contains(@class, "__inner-container") and not(ancestor::div[contains(@class, "__inner-container")])]';
			// $inner_container = '//*[contains(@class, "__inner-container")]';
			// $inner_container = '//html/body/div/div[contains(@class, "__inner-container")]';

			// Try getting attributes from the inner container to see if it's there.
			// If so, remove layout classes from outer container. If not, apply them to the outer container.
			$inner_container_attrs = mp_get_attributes( $block_content, $inner_container, true );
			if ( ! empty( $inner_container_attrs ) ) {
				$block_attrs['class'][] = mp_negate_class( $layout_class_filtered );
				$block_content          = mp_html_class( $block_content, $inner_container, $layout_class_filtered, true );
			} else {
				// Create an inner wrapper. WP 6.0 doesn't create the inner container until after this hook.
                foreach ( array( 'small', 'medium', 'large', 'xlarge' ) as $uk_container ) {
                    if ( stripos(json_encode($layout_class_filtered),$uk_container) !== false ) {
                        $layout_class_filtered[] = "uk-container-{$uk_container}";
                    }
                }
				$block_attrs['class'][] = mp_negate_class( $layout_class_filtered );
				$block_content          = mp_inner_wrap( $block_content, '//html/body/*', 'div', array( 'class' => $layout_class_filtered ) );
			}
			// $block_content = mp_html_class( $block_content, $inner_container, $layout_class_filtered, true );
		}
		$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	}
	return $block_content;
}


// WordPress block Focal Point -> UIkit background position.
// This is not going to be as precise as it is in WordPress.
// $isBackground when TRUE returns class for background image; otherwise, for <img>
function mp_block_focal_point( array $focalPoint, $isBackground = true ) {
	$x = (float) $focalPoint['x'];
	$y = (float) $focalPoint['y'];

	if ( $x > 0.66 ) {
		$posX = 'right';
	} elseif ( $x < 0.33 ) {
		$posX = 'left';
	} else {
		$posX = 'center';
	}

	if ( $y > 0.66 ) {
		$posY = 'bottom';
	} elseif ( $y < 0.33 ) {
		$posY = 'top';
	} else {
		$posY = 'center';
	}

	return ( ! empty( $isBackgound ) ) ? "uk-background-{$posY}-{$posX}" : "uk-position-{$posY}-{$posX}";
}

// Parses block attrs in useful variables, for use with every block
function mp_create_block_variables( $block ) {
	$attrs              = $block['attrs'];
	$vars['attrs']      = $attrs;
	$vars['className']  = isset( $attrs['className'] ) ? strtolower( $attrs['className'] ) : '';
	$vars['classArray'] = to_array( $vars['className'] );
	$vars['blockStyle'] = preg_filter( '/^is-style-/', '', $vars['classArray'] );

	$vars['block_attrs'] = array();
	// $vars['block_attrs']['class'] = $vars['classArray'];
	$vars['block_attrs']['class'][] = mp_negate_class( preg_filter( '/^/', 'is-style-', $vars['blockStyle'] ) );
	$vars['block_attrs']['class']   = array_filter( $vars['block_attrs']['class'] );

	return $vars;
}

add_filter( 'render_block_core/cover', 'mp_extend_cover_block', 10, 2 );
function mp_extend_cover_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$img_attrs = array();

	$uk_position            = mp_array_starts_with( $classArray, 'uk-position' );
	$block_attrs['class'][] = array_diff( $classArray, $uk_position );

	// Add the image.
	if ( ! empty( $attrs['url'] ) ) {
		// $block_attrs['class'][] = 'uk-light';

		// Fixed Background?
		if ( ! empty( $attrs['hasParallax'] ) ) {
			$block_attrs['data-src'] = $attrs['url'];
			$block_attrs[]           = 'uk-img';
			$block_attrs['class'][]  = 'uk-background-fixed uk-background-cover';

			// Focal Point? This is not going to be as precise as it is in WordPress.
			if ( ! empty( $attrs['focalPoint'] ) ) {
				$block_attrs['class'][] = mp_block_focal_point( $attrs['focalPoint'], true );
			}
		} else {
			$block_attrs['class'][] = 'uk-cover-container';
            
			if ( ! empty( $attrs['focalPoint'] ) ) {
				$img_attrs['style'][] = 'object-fit: cover; width: initial';
			}

            if(str_contains($className, 'bg-full')) {
                $img_attrs['class'][]   = 'uk-position-center';
            } else {
                $img_attrs[] = 'uk-cover';
            }

			// if(!empty($attrs['focalPoint'])) $img_attrs['class'][] = mp_block_focal_point($attrs['focalPoint']);
		}
		// Repeated?
		if ( ! empty( $attrs['isRepeated'] ) ) {
			$block_attrs['class'][] = 'uk-background-repeat';
		}
	}

	// center inner blocks by default
	if ( ! empty( $block['innerBlocks'] ) ) {
		// Position inner content
		$content_position = ( ! empty( $attrs['contentPosition'] ) ) ? str_replace( ' ', '-', $attrs['contentPosition'] ) : 'center';
		if ( $content_position === 'center-center' ) {
			$content_position = 'center';
		}

		// $inner_attrs['class'][] = "uk-position-z-index uk-position-{$content_position}";
        $inner_attrs['class'][] = "uk-container uk-container-large uk-position-relative uk-position-z-index";
        $inner_attrs['class'][] = $uk_position;

		$block_content          = mp_html_attrs( $block_content, "//div[contains(@class, '__inner-container')]", $inner_attrs, true );
		$block_attrs['class'][] = 'uk-position-relative uk-section';

		// Remove empty inner blocks
		// $block_content = mp_html_remove( $block_content, "//*[not(node()[not(self::text())]) and not(normalize-space) and contains('|p|div|', concat('|', name(), '|'))]" );

		// Remove empty inner container
		$block_content = mp_html_remove( $block_content, "//*[not(node()[not(self::text())]) and not(normalize-space) and contains(@class, '__inner-container')]" );

		// Remove bottom margin on last element
		$block_content = mp_html_class( $block_content, '//div[contains(@class, "wp-block-cover__inner-container")]/*[last()]', 'uk-margin-remove-bottom', true );
	}
	if ( empty( $block['innerBlocks'] ) ) {
		$block_content = mp_html_remove_by_class( $block_content, 'wp-block-cover__inner-container' );
	}

	// min-height?
	if ( ! empty( $attrs['minHeight'] ) ) {
		$minHeightUnit = ! empty( $attrs['minHeightUnit'] ) ? $attrs['minHeightUnit'] : 'px';
		// Don't let us use vw as a unit
		if ( $minHeightUnit === 'vh' ) {
			if ( $attrs['minHeight'] < 100 ) {
				$block_attrs['uk-height-viewport'] = 'offset-top: true; offset-bottom: ' . ( 100 - $attrs['minHeight'] );
			} else {
				$block_attrs['uk-height-viewport'] = 'offset-top: true';
			}
			$img_attrs['style'][] = 'min-height: ' . $attrs['minHeight'] . $minHeightUnit;
		} else {
			// min-height is already present when this attribute is set, but we're replacing all the original attributes
			$block_attrs['style'][] = 'height: 100%; min-height: ' . $attrs['minHeight'] . $minHeightUnit;
			$img_attrs['style'][]   = 'min-height: ' . $attrs['minHeight'] . $minHeightUnit;
		}
	} else {
		$block_attrs['style'][] = 'height: 100%';
		$img_attrs['style'][]   = 'height: 100%';
	}

	// Add Overlay
	// dimRatio doesn't even appear if it's set to 50.
	$dim_ratio = ( ! isset( $attrs['dimRatio'] ) ) ? 50 : $attrs['dimRatio'];

	if ( $dim_ratio > 0 ) {
		if ( ! empty( $attrs['customOverlayColor'] ) ) {
			$overlay_color = $attrs['customOverlayColor'];
		} elseif ( ! empty( $attrs['gradient'] ) ) {
			// For gradient support, do not dequeue the wp-block-library in functions.php
		} else {
			// we need to get the color from the palette
			$editor_colors           = get_theme_support( 'editor-color-palette' );
			$editor_colors_reference = array_combine(
				array_column( reset( $editor_colors ), 'slug' ),
				array_column( reset( $editor_colors ), 'color' )
			);
			if ( ! empty( $attrs['overlayColor'] ) && ! empty( $editor_colors_reference[ $attrs['overlayColor'] ] ) ) {
				$overlay_color = $editor_colors_reference[ $attrs['overlayColor'] ];
				// Remove the background color from the parent
				$block_attrs['class'][] = '!has-' . $attrs['overlayColor'] . '-background-color';
			} else {
				$overlay_color = '#000';
			}
		}

		// Overlay div attrs
		$opacity       = $dim_ratio / 100;
		$overlay_attrs = array(
			'class' => 'uk-overlay uk-position-cover',
		);
		if ( $opacity < 1 ) {
			$overlay_attrs['style'][] = "opacity: {$opacity}";
		}
		if ( ! empty( $overlay_color ) ) {
			$overlay_attrs['style'][] = "background-color: {$overlay_color}";
		}

		$overlay = buildAttributes( $overlay_attrs, 'div', null );
		// Insert the overlay div
		$block_content          = preg_replace( '/<\/div>$/', $overlay . '</div>', $block_content );
		$block_attrs['class'][] = 'uk-position-relative';
	}

	// Rounded/Circle/Pill
	if ( in_array( 'rounded', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-border-rounded';
	}
	if ( in_array( 'circle', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-border-circle';
	}
	if ( in_array( 'pill', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-border-pill';
	}

	// Square images
	if ( in_array( 'square', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-cover-container square';
	}

	// Art Direction
	// $block_content = mp_attachment_image_art_direction($block_content, $attrs['id'], 'full', false, []);
	$block_content = mp_html_attrs( $block_content, '//img[contains(@class, "wp-block-cover__image")]', $img_attrs, true );

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );

	return $block_content;
}

add_filter( 'render_block_core/file', 'mp_extend_file_block', 10, 2 );
function mp_extend_file_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	// uk-button classes need to go on the <a> element, not the wrapper div
	$uk_button              = mp_array_starts_with( $classArray, 'uk-button' );
	$uk_width               = mp_array_starts_with( $classArray, 'uk-width-' );
	$block_attrs['class'][] = array_diff( $classArray, $uk_button, $uk_width );
	$block_content          = mp_html_class( $block_content, "//div[contains(@class, 'wp-block-file')]/a", array( $uk_button, $uk_width ) );
	$block_content          = mp_html_attrs( $block_content, '/html/body/*', $block_attrs );
	return $block_content;
}


add_filter( 'render_block_core/gallery', 'mp_extend_gallery_block', 10, 2 );
function mp_extend_gallery_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	if ( ! array_key_exists( 'ids', $attrs ) ) {
		$inner_blocks_attrs = wp_list_pluck( $block['innerBlocks'], 'attrs' );
		$ids                = wp_list_pluck( $inner_blocks_attrs, 'id' );
	} else {
		$ids = $attrs['ids'];
	}

	$grid_attrs = array();
	$item_attrs = array();
	$img_attrs  = array();

	$blocks_gallery_grid_tag = 'div';
	$blocks_gallery_item_tag = 'div';

	// If columns is unset, use number of images (maximum 6) or 3 (default)
	$columns = empty( $attrs['columns'] ) ? min( 3, count( $attrs['ids'] ) ) : $attrs['columns'];
	if ( $columns > 6 ) {
		$columns = 6;
	}

	// Add classes
	if ( ! empty( $className ) ) {
		$block_attrs['class'][] = $className;

		// uk-height- class: we don't yet know what to attach it to
		$uk_height              = mp_array_starts_with( $classArray, 'uk-height-' );
		$block_attrs['class'][] = mp_negate_class( $uk_height );

		// uk-lightbox- class
		// use uk-lightbox-xx where xx is a lightbox animation
		$uk_lightbox            = mp_array_starts_with( $classArray, 'uk-lightbox-' );
		$block_attrs['class'][] = mp_negate_class( $uk_lightbox );

		if ( ! empty( $uk_lightbox ) ) {
			$grid_attrs['uk-lightbox'] = 'animation: ' . str_replace( 'uk-lightbox-', '', reset( $uk_lightbox ) );
			$item_attrs['class'][]     = 'uk-inline';
		}

		// uk-slider class
		// use uk-slider-xx where xx is a slider option that will be set to true
		if ( in_array_any( array( 'dotnav', 'thumbnav', 'slider' ), $blockStyle ) ) {
			$blocks_gallery_grid_tag = 'ul';
			$blocks_gallery_item_tag = 'li';
			$grid_attrs['class'][]   = 'uk-slider-items uk-flex-middle uk-grid-match';

			$block_attrs['uk-slider'][] = null;

			// center the slider
			$block_attrs['class'][] = 'uk-margin-auto';

			$uk_slider              = mp_array_starts_with( $classArray, 'uk-slider-' );
			$block_attrs['class'][] = mp_negate_class( $uk_slider );
			if ( ! empty( $uk_slider ) ) {
				foreach ( $uk_slider as $uk_slider_option ) {
					$uk_slider_option           = str_replace( 'uk-slider-', '', $uk_slider_option );
					$block_attrs['uk-slider'][] = sprintf( '%s: true', $uk_slider_option );

					// Apply 3/4 width to items with 'center' option, so a little of the adjacent items shows
					if ( 'center' === $uk_slider_option ) {
						$item_attrs['class'][] = 'uk-width-3-4';
					}
				}
			}

			// Clear out height/width and make image 100% height of parent
			// $block_content = mp_html_attrs($block_content, "//img", [ 'class' => 'uk-height-1-1 uk-width-auto' ], TRUE);

			$figure_attrs            = array();
			$figure_attrs['class'][] = 'uk-panel uk-text-center';

			if ( ! empty( $uk_height ) ) {
				$figure_attrs['class'][] = $uk_height;
			}

			$block_content = mp_html_attrs( $block_content, "//*[contains(@class, 'blocks-gallery-item')]/figure", $figure_attrs, true, 'div' );

			$grid_attrs['class'][] = 'uk-margin-bottom';

			// Thumb or Dot navigation?
			if ( in_array( 'dotnav', $blockStyle ) ) {
				// Add a dot navigation
				$slider_nav = '<ul class="uk-slider-nav uk-dotnav uk-flex-center"></ul>';
			} elseif ( in_array( 'thumbnav', $blockStyle ) ) {
				$thumbnail_slug = 'thumbnail-1x';

				// Add a thumbnail navigation
				$wp_additional_image_sizes = wp_get_additional_image_sizes();
				$thumbnail_width           = isset( $wp_additional_image_sizes[ $thumbnail_slug ] ) ? $wp_additional_image_sizes[ $thumbnail_slug ]['width'] : get_option( 'thumbnail_size_w' );

				// Build the thumb navigation list
				$slider_nav = '<ul class="uk-thumbnav uk-flex-center" uk-margin>';
				foreach ( $attrs['ids'] as $this_thumb => $id ) {
					$thumbnail   = wp_get_attachment_image( $id, $thumbnail_slug, false, array( 'sizes' => "{$thumbnail_width}px" ) );
					$slider_nav .= "<li uk-slider-item='{$this_thumb}'><a href='#'>{$thumbnail}</a></li>";
				}
			} else {
				// Default to using arrows
				// Note that using 'Replace URLS' in Media Cloud Settings will for whatever reason clear the classes on these arrow links.
				ob_start(); ?>
<a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous
	uk-slider-item="previous"></a>
<a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next
	uk-slider-item="next"></a>
				<?php
				$slider_arrows = ob_get_clean();
			}

			if ( ! empty( $slider_arrows ) ) {
				$block_content = mp_wrap_element( $block_content, '.blocks-gallery-grid', 'div', array( 'class' => 'uk-slider-container' ) );
				$block_content = mp_wrap_element( $block_content, '.uk-slider-container', 'div', array( 'class' => 'uk-position-relative' ) );
				$block_content = mp_html_class_by_class( $block_content, '.uk-slider-container', 'uk-margin-xlarge-left uk-margin-xlarge-right', true );
				$block_content = str_replace( '</ul></div>', '</ul></div>' . $slider_arrows, $block_content );
			}

			if ( ! empty( $slider_nav ) ) {
				$block_content = str_replace( '</ul>', '</ul>' . $slider_nav, $block_content );
			}
		}
	}

	// do columns
	$grid_attrs['class'][] = $columns > 1 ? sprintf( 'uk-grid uk-child-width-1-%s@m', $columns ) : 'uk-grid uk-child-width-1-1';

	// Add links when images have a link URL
	$item_attrs['class'][] = 'uk-link-text';

	$sizeSlug = ( ! empty( $attrs['sizeSlug'] ) ) ? $attrs['sizeSlug'] : 'full';
	if ( ! empty( $attrs['linkTo'] ) ) {
		switch ( $attrs['linkTo'] ) {
			case 'file':
				$linkTo = $sizeSlug;
				break;
			case 'post':
				$linkTo = 'attachment_url_url';
				break;
			default:
				$linkTo = null;
		}
	}

	// Try to optimize 'sizes' attribute considering image size and container width
	$sizes = mp_sizes_attribute( 'medium', $columns, buildClass( $grid_attrs['class'] ) );
	if ( ! empty( $sizes ) ) {
		$img_attrs['sizes'] = $sizes;
	}
	$block_content = mp_image_element( $block_content, $ids, $img_attrs, $linkTo, $item_attrs );

	// vertically center images
	$grid_attrs['class'][] = 'uk-flex-middle';

	// uk-animation-* class adds the uk-scrollspy attribute with a delay
	$uk_animation           = mp_array_starts_with( $classArray, 'uk-animation-' );
	$block_attrs['class'][] = mp_negate_class( $uk_animation );

	if ( ! empty( $uk_animation ) ) {
		$grid_attrs['uk-scrollspy'][] = 'cls: uk-animation-' . reset( $uk_animation );
		$grid_attrs['uk-scrollspy'][] = 'target: > *; offset-top: 30; delay: 250';
	}

	// Alignment
	if ( isset( $attrs['align'] ) && ! empty( $attrs['align'] ) ) {
		foreach ( array( 'left', 'right', 'center' ) as $align ) {
			if ( $align === $attrs['align'] ) {
				$grid_attrs['class'][] = "uk-text-{$align}";
			}
		}
	}

	// Crop
	$imageCrop = isset( $attrs['imageCrop'] ) ? $attrs['imageCrop'] : true;
	if ( $imageCrop === true ) {
		$grid_attrs['uk-height-match'] = 'target: img';
		$block_content                 = mp_html_class( $block_content, '//*[contains(@class, "blocks-gallery-item")]/*/a', 'uk-overflow-hidden', true );
		$block_content                 = mp_html_attrs( $block_content, '//img', array( 'style' => 'object-fit: cover' ), true );
	}

	// Caption styling
	$block_content = mp_html_class_by_class( $block_content, 'blocks-gallery-item__caption', 'uk-text-muted-darker uk-text-center', true );

	// Any CSS classes added to the Gallery block need to go on the grid wrapper, not the outer element.
	// This lets you use UIkit classes like uk-child-width-1-2@s to fine-tune the columns
	preg_match_all( '/[^!](uk-child-width-[^ ]*|uk-grid[^ ]*|uk-flex[^ ]*)/', $className, $layout_classes );
	if ( ! empty( $layout_classes ) ) {
		// $layout_classes will contain two arrays, and we use the first one
		// Remove (!) only the inner element classes from the outer element, keeping any others:
		$block_attrs['class'][] = mp_negate_class( reset( $layout_classes ) );
		// Add the matched classes to the inner element
		$grid_attrs['class'][] = reset( $layout_classes );
	}

	// Build the block-gallery-grid element
	$block_content = mp_html_attrs_by_class( $block_content, 'blocks-gallery-grid', $grid_attrs, true, $blocks_gallery_grid_tag );

	// Rename the blocks-gallery-item element
	$block_content = mp_html_attrs_by_class( $block_content, 'blocks-gallery-item', array(), true, $blocks_gallery_item_tag );

	// Add any attributes/classes to the wp-block-gallery element.
	// $block_content = mp_html_attrs_by_class($block_content, 'wp-block-gallery', $block_attrs, TRUE);
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs );

	return $block_content;
}

add_filter( 'render_block_yoast/faq-block', 'mp_extend_faq_block', 10, 2 );
function mp_extend_faq_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}
	$block_content = mp_html_attrs_by_class( $block_content, 'schema-faq', array( 'uk-accordion' ), true, 'ul' );
	$block_content = mp_html_attrs_by_class( $block_content, 'schema-faq-section', array(), true, 'li' );
	$block_content = mp_html_attrs_by_class(
		$block_content,
		'schema-faq-question',
		array(
			'class' => 'uk-accordion-title',
			'href'  => '#',
		),
		true,
		'a'
	);
	$block_content = mp_html_class_by_class( $block_content, 'schema-faq-answer', 'uk-accordion-content', true, 'div' );

	// Paragraphs are separated by <br>, not <p>. These two lines fix that.
	$block_content = mp_inner_wrap( $block_content, '//div[contains(@class, "schema-faq-answer")]', 'p' );
	$block_content = preg_replace( '#(.*?)<br>#', '$1</p><p>', $block_content );

	// An answer can be a list, start elements with * -- though this will make every line into a list item.
	$block_content = mp_inner_wrap( $block_content, '//div[contains(@class, "schema-faq-answer")]/*[starts-with(., "* ")]/parent::*', 'ul', array( 'class' => 'uk-list list-theme' ) );
	$block_content = str_replace( '<li>* ', '<li>', $block_content );
	return $block_content;
}

add_filter( 'render_block_core/social-links', 'mp_extend_social_links_block', 10, 2 );
function mp_extend_social_links_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$block_attrs['class'][] = 'uk-grid';

	// Alignment
	if ( isset( $attrs['align'] ) && ! empty( $attrs['align'] ) ) {
		foreach ( array( 'left', 'right', 'center' ) as $align ) {
			if ( $align === $attrs['align'] ) {
				$block_attrs['class'][] = "uk-flex-{$align}";
			}
		}
	}

	// Size
	if ( ! empty( $attrs['size'] ) ) {
		switch ( $attrs['size'] ) {
			case 'has-small-icon-size':
				$ratio = .75;
				break;
			case 'has-large-icon-size':
				$ratio = 1.5;
				break;
			case 'has-huge-icon-size':
				$ratio = 2;
				break;
			default:
				$ratio = 1;
		}
	}

	// wp-social-link-* -> * = UIkit icon
	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $innerBlock ) {
			$innerAttrs = $innerBlock['attrs'];

			// Icon slug is the service name
			@$icon = $innerAttrs['service'];

			// ... but allow user to override using uk-icon-* classname
			if ( ! empty( $innerAttrs['className'] ) ) {
				preg_match( '/uk-icon-([^ ]*)/', $innerAttrs['className'], $uk_icon );
				if ( ! empty( $uk_icon[1] ) ) {
					$icon = $uk_icon[1];
				}
			}

			// Set the icon using size $ratio from above.
			if ( ! empty( $icon ) ) {
				// Remove existing icon.
				$block_content = mp_html_remove( $block_content, '//a/svg' );

				// Apply icon to the <a> tag
				$link_attrs['uk-icon'] = ! empty( $ratio ) ? sprintf( 'icon: %s; ratio: %s', $icon, $ratio ) : $icon;
				// Use uk-icon-button -- but not for logos only style
				if ( ! in_array( 'logos-only', $blockStyle ) ) {
					$link_attrs['class'][] = 'uk-icon-button';
				}
				$block_content = mp_html_attrs( $block_content, "//li[contains(@class, 'wp-social-link-" . $innerAttrs['service'] . "')]/a", $link_attrs, true );
			}
		}

		$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs );
	}

	return $block_content;
}

// add_filter( 'render_block_core/media-text', 'mp_extend_media_text_block', 50, 2 );
function mp_extend_media_text_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$card_media_attrs = array();
	$card_body_attrs  = array();

	// We need a wrapping div to avoid the grid collapsing the left padding on card-body.
	$card_body_wrap_attrs = array();

	$block_attrs['class'][]     = 'uk-card';
	$card_body_attrs['class'][] = 'uk-card-body';

	// Defaults
	$mediaWidth        = ( ! empty( $attrs['mediaWidth'] ) ) ? $attrs['mediaWidth'] : '50';
	$mediaPosition     = ( ! empty( $attrs['mediaPosition'] ) ) ? $attrs['mediaPosition'] : 'left';
	$verticalAlignment = ( ! empty( $attrs['verticalAlignment'] ) ) ? $attrs['verticalAlignment'] : 'top';
	$mediaSizeSlug     = ( ! empty( $attrs['mediaSizeSlug'] ) ) ? $attrs['mediaSizeSlug'] : 'full';
	$isStackedOnMobile = ( ! empty( $attrs['isStackedOnMobile'] ) ) ? $attrs['isStackedOnMobile'] : true;

	// Fallback when no intermediate image size height can be determined.
	// Always applies when size is set to Full
	$stackedHeight = 400;

	$block_attrs['class'][] = 'uk-grid uk-grid-collapse uk-margin-medium';

	// Media Width

	// Stacked on mobile?
	if ( $isStackedOnMobile ) {
		$card_media_attrs['class'][]     = 'uk-width-1-1';
		$card_body_wrap_attrs['class'][] = 'uk-width-1-1';
		$width_breakpoint                = '@s';
	}
	$width_breakpoint              ??= null;
	$card_media_attrs['class'][]     = uk_width( $mediaWidth, $width_breakpoint );
	$card_body_wrap_attrs['class'][] = 'uk-width-expand';

	// Add a padding around the text content
	$card_body_wrap_attrs['class'][] = 'uk-padding-small';

	// Run Media through filters
	if ( ! empty( $attrs['mediaId'] ) ) {
		$img       = wp_get_attachment_image( $attrs['mediaId'], $mediaSizeSlug );
		$img_attrs = mp_get_attributes( $img, '//*[contains(@class, "wp-block-media-text__media")]/img[0]' );
	}

	// Media Alignment
	$card_media_attrs['class'][] = 'uk-card-media-' . $mediaPosition;

	// Flip the columns when media is on the righthand side.
	if ( 'right' === $mediaPosition ) {
		$card_media_attrs['class'][] = 'uk-flex-last@s';
	}

	// Crop image to fill column?
	if ( ! empty( $attrs['imageFill'] ) ) {

		// When the media is cropped to fill, the content container will always match the content height.
		// When the container might be larger, use flex to vertically align the content.
		if ( $verticalAlignment !== 'top' ) {
			$card_body_wrap_attrs['class'][] = 'uk-flex uk-flex-column uk-flex-' . $verticalAlignment;
		}

		// Add uk-cover to the image.
		$img_attrs[]                 = 'uk-cover';
		$card_media_attrs['class'][] = 'uk-cover-container';

		// Add a <canvas> element to protect image height when stacked. The height will be the height of the selected image size,
		// or a default value.
		if ( $isStackedOnMobile ) {
			// $intermediate_size = image_get_intermediate_size($attrs['mediaId'], $mediaSizeSlug);
			$intermediate_size = wp_get_registered_image_subsizes();
			$card_media_height = ( ! empty( $intermediate_size[ $mediaSizeSlug ] ) ) ? $intermediate_size[ $mediaSizeSlug ]['height'] : $stackedHeight;
			// $card_media_height = (!empty($intermediate_size['height'])) ? $intermediate_size['height'] : $stackedHeight;
			$canvas_attrs = array(
				'height' => $card_media_height,
			);
			if ( in_array( 'autoheight', $blockStyle ) ) {
				$canvas_attrs['class'][] = 'uk-hidden' . $width_breakpoint;
			}
			$block_content = mp_insert_element( $block_content, buildAttributes( $canvas_attrs, 'canvas', true ), '//*[contains(@class, "wp-block-media-text__media")]/img', 'insertBefore' );
		}
	}

	// Card Media: Remove style, which by default sets media as background image. (We don't want that because it doesn't support srcset)
	$block_content = mp_html_attrs_by_class(
		$block_content,
		'wp-block-media-text__media',
		$card_media_attrs,
		array(
			'class',
			'style' => false,
		)
	);

	// <img> element
	$block_content = mp_html_attrs( $block_content, '//*[contains(@class, "wp-block-media-text__media")]/img', $img_attrs, array( 'class' ) );

	// Card Body & Card Body Wrapper
	$block_content = mp_wrap_element( $block_content, '.wp-block-media-text__content', 'div', $card_body_wrap_attrs );
	$block_content = mp_html_attrs_by_class( $block_content, 'wp-block-media-text__content', $card_body_attrs, true );

	// Block element
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, array( 'class' ) );

	return $block_content;
}

// Image Block
// Blocks don't trigger the get_attachment_image filters, so we're doing that here.
// Also testing for certain conditions and adding imgix params to the query.
add_filter( 'render_block_core/image', 'mp_extend_image_block', 10, 2 );
function mp_extend_image_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	// $block_attrs = <figure>
	// $img_attrs = <img> or <source>

    $img_attrs = array();
	$imgix_attrs = array();

	@$id       = $attrs['id'];
	@$sizeSlug = empty( $attrs['sizeSlug'] ) ? 'full' : $attrs['sizeSlug'];
	@$align    = $attrs['align'];
	@$duotone  = $attrs['style']['color']['duotone'];

	// Run the wp_get_attachment_image filters, which does not by default happen with image blocks
	if ( ! empty( $id ) ) {
		$img                = wp_get_attachment_image( $id, $sizeSlug );
		$img_attrs          = mp_get_attributes( $img, '//img', true );
		$img_attrs['class'] = array( $img_attrs['class'] );
		// $img_attrs = mp_get_attributes($img, '//*[contains(@class, "wp-block-image")]/img', TRUE);
		// Our columns block adjusts the 'sizes' attribute using the data-id attribute on images.
		$img_attrs['data-id'] = $id;

		// The dimensions are needed esp. for SVGs, which otherwise collapse.
		foreach ( array( 'width', 'height' ) as $img_dimension ) {
			if ( ! empty( $attrs[ $img_dimension ] ) ) {
				$img_attrs[ $img_dimension ] = $attrs[ $img_dimension ];
			}
		}
	}

	// Caption
	$caption_class = array(
		// 'uk-text-muted-darker',
		// 'uk-text-bolder',
		// 'uk-text-uppercase',
		// 'uk-text-small',
		'uk-label',
		'uk-position-bottom-left',
	);
	$block_content = mp_html_class( $block_content, '//figure[contains(@class, "wp-block-image")]/figcaption', $caption_class, true );

	// Duotone: Use imgix instead of Gutenberg's method
	if ( ! empty( $duotone ) && is_array( $duotone ) && count( $duotone ) === 2 ) {
		$tone = array();
		foreach ( $duotone as $color ) {
			if ( ! sanitize_hex_color( $color ) ) {
				preg_match_all( '/\d+/', $color, $rgb );
				$tone[] = sprintf( '%02x%02x%02x', $rgb[0][0], $rgb[0][1], $rgb[0][2] );
			} else {
				$tone[] = trim( $color, '#' );
			}
		}
		$imgix_attrs['duotone'] = join( ',', $tone );
		// Remove Gutenberg's duotone method: <style> and <svg> blocks
		$block_content = mp_html_remove( $block_content, '//*[self::style or self::svg]' );
	}

	// Alignment
	if ( ! empty( $align ) ) {
		if ( 'center' === $align ) {
			$block_attrs['class'][] = 'uk-flex uk-flex-' . $align;
		} else {
			$img_attrs['class'][] = 'left' === $align ? 'uk-margin-right' : 'uk-margin-left';
			$img_attrs['class'][] = 'uk-float-' . $align;
		}
	}

	// Width
	$uk_width = mp_array_starts_with( $classArray, 'uk-width-' );
	if ( $uk_width ) {
		$block_attrs['class'][] = $uk_width;
	}

	// Object Fit
	if ( in_array( 'cropped', $blockStyle ) ) {
		$img_attrs['style'][] = 'object-fit: cover; height: 100%';
	}

	// Object Position
	$object_position = mp_array_starts_with( $classArray, 'object-position@' );
	if ( $object_position ) {
		$block_attrs['class'][] = mp_negate_class( $object_position );
		$img_attrs['style'][]   = sprintf( 'object-position: %s%% center', str_replace( 'object-position@', '', reset( $object_position ) ) );
	}

	// Transition
	$uk_transition = mp_array_starts_with( $classArray, 'uk-transition' );
	if ( $uk_transition ) {
		$block_attrs['class'][] = mp_negate_class( $uk_transition );
		$img_attrs['class'][]   = $uk_transition;
	}

	// Rounded/Circle/Pill
	// NOTE: uk-inline-clip applies overflow: hidden so the border effect works. Use uk-overflow-hidden
	// if there are problems with what else uk-inline-clip adds.
	if ( in_array( 'rounded', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-border-rounded uk-inline-clip';
	}
	if ( in_array( 'circle', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-border-circle uk-inline-clip';
	}
	if ( in_array( 'pill', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-border-pill uk-inline-clip';
	}

	// Square images - fix for when there is a caption
	if ( in_array( 'square', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-cover-container square';
		$img_attrs[]            = 'uk-cover';
		// Link needs to be display block
		$block_content = mp_html_class( $block_content, '//figure/a', 'uk-display-block', true );
	}

	// inline SVGs: pass !uk-svg class to not do this (for instance, if you are animating the SVG with an external style sheet)
	if ( ! in_array( '!uk-svg', $classArray ) ) {
		$url_path = parse_url( $img_attrs['src'], PHP_URL_PATH );
		if ( str_ends_with( $url_path, '.svg' ) ) {
			$img_attrs[] = 'uk-svg';
		}
		// pass uk-preserve class to preserve fill/stroke colors
		$uk_preserve = in_array( 'uk-preserve', $classArray );
		if ( $uk_preserve ) {
			$block_attrs['class'][] = mp_negate_class( 'uk-preserve' );
			$img_attrs['class'][]   = 'uk-preserve';
		}
	}

	// <img>
	if ( ! empty( $imgix_attrs ) ) {
		$img_attrs = mp_imgix_attrs( $img_attrs, $imgix_attrs );
	}
	// Cover
	if ( in_array( 'uk-cover', $classArray ) ) {
		$img_attrs[]          = 'uk-cover';
		$img_attrs['class'][] = '!uk-position-relative';
	}
	$block_content = mp_html_attrs( $block_content, '//*[local-name()="img" or local-name()="source"]', $img_attrs, true );

	if ( in_array( 'uk-cover', $classArray ) ) {
		$block_content = mp_move_element( $block_content, '//*[@uk-cover]' );
		$block_content = mp_html_remove_by_class( $block_content, 'wp-block-image' );
	} else {
		// Not sure why this is needed anymore. Relative position is not compatible with uk-cover.
		$block_attrs['class'][] = 'uk-position-relative';
	}

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, array( 'class' ) );

	return $block_content;
}


// Must run AFTER mp_extend_image_block, because it fine-tunes the 'sizes' attribute for images, based on number of columns.
add_filter( 'render_block_core/column', 'mp_extend_column_block', 10, 2 );
function mp_extend_column_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	@$width           = $attrs['width'];
	$width_breakpoint = '@m';

	$backgroundColor = ! empty( $attrs['backgroundColor'] ) ? $attrs['backgroundColor'] : false;
	$textColor       = ! empty( $attrs['textColor'] ) ? $attrs['textColor'] : false;

	// Background Color
	if ( ! empty( $backgroundColor ) ) {
		$block_attrs['class'][] = "uk-background-{$backgroundColor}";
	}

	// Text Color: Gutenberg color palette includes 'default' but uk-text-default is not for text.
	// Therefore, interpret 'default' as 'global' ($global-color, which is the body color).
	if ( ! empty( $textColor ) ) {
		$block_attrs['class'][] = 'default' === $textColor ? 'uk-text-global' : "uk-text-{$textColor}";
	}

	$verticalAlignment = ( ! empty( $attrs['verticalAlignment'] ) ) ? $attrs['verticalAlignment'] : 'top';
	if ( $verticalAlignment !== 'top' ) {
		$block_attrs['class'][] = 'uk-flex uk-flex-column uk-flex-' . $verticalAlignment;
	}

	if ( ! empty( $width ) ) {
		$unit = trim( $width, '0..9.' );
		switch ( $unit ) {
			case '%':
				$block_attrs['class'][] = uk_width( (int) $width, $width_breakpoint );
				break;
			default:
				$block_attrs['style'][] = "flex-basis: {$width}";
		}
	}

	// Block, removing style attribute, and not targeting child columns.
	$block_content = mp_html_attrs(
		$block_content,
		"//div[contains(concat(' ',normalize-space(@class),' '), ' wp-block-column ') and not(ancestor::div[contains(concat(' ',normalize-space(@class),' '), ' wp-block-column ')])]",
		$block_attrs,
		array(
			'class',
			'style' => false,
		)
	);

	return $block_content;
}

add_filter( 'render_block_core/columns', 'mp_extend_columns_block', 10, 2 );
function mp_extend_columns_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$columns = isset( $block['innerBlocks'] ) ? count( $block['innerBlocks'] ) : 1;
	if ( $columns > 6 ) {
		$columns = 6;
	}

	$uk_child_width   = mp_array_starts_with( $classArray, 'uk-child-width-' );
	$width_breakpoint = '@m';

	if ( $columns > 1 ) {
		// Don't assume uk-grid if we've already got a uk-flex class. Sometimes uk-flex is preferable.
		$blockAttributes = mp_get_attributes( $block_content, '.wp-block-columns', true );
		if ( ! hasClass( 'uk-flex', $block_attrs['class'] ) ) {
			$block_attrs['class'][] = 'uk-grid';
		}
		if ( empty( $uk_child_width ) ) {
			$block_attrs['class'][] = sprintf( 'uk-child-width-1-%s%s', $columns, $width_breakpoint );
		}
	}

	// Try to size images automatically with the 'sizes' attribute
	if ( ! empty( $block['innerBlocks'] ) ) {
		$columnBlockTypes = array();
		// uk-margin creates wrong spacing with grid-stack and grid-divider
		// $block_attrs['uk-margin'] = NULL;
		foreach ( $block['innerBlocks'] as $innerBlock ) {
			if ( ! empty( $innerBlock['innerBlocks'] ) ) {
				// Array of all the block types in this column, e.g., core/image, core/heading, core/paragraph, etc.
				$columnBlockNames   = array_column( $innerBlock['innerBlocks'], 'blockName' );
				$columnBlockTypes[] = $columnBlockNames[0]; // only using the first inner block

				if ( in_array( 'core/image', $columnBlockNames ) ) {
					$has_inner_images = true;
					foreach ( $innerBlock['innerBlocks'] as $columnBlock ) {
						if ( empty( $columnBlock['attrs']['id'] ) ) {
							continue;
						}
						$attachment_id = $columnBlock['attrs']['id'];
						// If there's a column width, use that for sizes calculation; otherwise, assume equal width columns.
						if ( ! empty( $innerBlock['attrs']['width'] ) ) {
							$columns = (float) $innerBlock['attrs']['width'];
						}
						$sizes         = mp_sizes_attribute( 'medium', $columns, buildClass( $block_attrs['class'] ) );
						$block_content = mp_html_attrs( $block_content, "//source[@data-id='{$attachment_id}']", array( 'sizes' => $sizes ), true );
						$block_content = mp_html_attrs( $block_content, "//img[@data-id='{$attachment_id}' and @sizes]", array( 'sizes' => $sizes ), true );
					}
				}
			}
		}

		// If parent block has a height, set inner block's height to 100%
		if ( ! empty( $has_inner_images ) && in_array( 'uk-height-', $classArray ) ) {
			$block_content = mp_html_attrs( $block_content, "//div[contains(concat(' ',normalize-space(@class),' '), ' wp-block-column ') and not(ancestor::div[contains(concat(' ',normalize-space(@class),' '), ' wp-block-column ')])]", array( 'style' => 'height: 100%' ), true );
			// Prevent weird issues when columns are stacked
			$block_attrs['style'][] = 'overflow-y: hidden';
		}
		if ( in_array( 'height-matched', $blockStyle ) ) {
			// Match target = class name 'match@[target]'
			if ( preg_match( '#match@([^ ]+)#', $className, $match_target ) ) {
				$block_attrs['uk-height-match'] = sprintf( 'target: .%s', $match_target[1] );
			} elseif ( ! empty( $columnBlockTypes ) && count( array_flip( $columnBlockTypes ) ) === 1 && end( $columnBlockTypes ) === 'core/image' ) {
				// If all the inner blocks are images, set the match height target to the img.
				$block_attrs['uk-height-match'] = 'target: img';
				$block_content                  = mp_html_attrs( $block_content, '//img[not(contains(@style, "object-fit"))]', array( 'style' => 'object-fit: cover' ), true );
			}
		}
	}

	// Block
	$block_content = mp_html_attrs( $block_content, "//div[contains(@class, 'wp-block-columns') and not(ancestor::div[contains(concat(' ',normalize-space(@class),' '), ' wp-block-column ')])]", $block_attrs, true );

	return $block_content;
}


add_filter( 'render_block_core/separator', 'mp_extend_separator_block', 10, 2 );
function mp_extend_separator_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$block_attrs['class'][] = array( $classArray, 'uk-divider' );

	// Color
	$color = ! empty( $attrs['backgroundColor'] ) ? $attrs['backgroundColor'] : false;
	if ( ! empty( $color ) ) {
		$block_attrs['class'][] = 'default' === $color ? 'uk-divider-global' : "uk-divider-{$color}";
		// Need to remove the background color class.
		$block_attrs['class'][] = mp_negate_class( "has-{$color}-background-color", "has-{$color}-color", 'has-background', 'has-text-color' );
	}

    // Align
    $align = ! empty( $attrs['align'] ) ? $attrs['align'] : false;
    if ( ! empty( $align ) ) {
		$block_attrs['class'][] = 'center' === $align ? 'uk-margin-auto' : "";
	}

	// uk-divider has variations uk-divider-icon, uk-divider-small, uk-divider-vertical
	foreach ( array( 'icon', 'small', 'vertical' ) as $uk_divider ) {
		if ( in_array( $uk_divider, $blockStyle ) ) {
			$block_attrs['class'][] = "uk-divider-{$uk_divider}";
		}
	}

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, array( 'class' => false ) );
	return $block_content;
}


add_filter( 'render_block_core/buttons', 'mp_extend_buttons_block', 10, 2 );
function mp_extend_buttons_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// Button wrapper
	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	@$contentJustification = $attrs['contentJustification'];

	// $uk_flex = mp_array_starts_with( $classArray, 'uk-flex-' );

	if ( count( $block['innerBlocks'] ) > 1 ) {
		// uk-button-group: inline-flex, align middle, no space between. Add 'uk-flex-gap' or 'uk-flex-gap-small' for space.
		$block_attrs['class'][] = 'uk-button-group';
	} else {
		$block_attrs['class'][] = 'uk-flex';
		// $block_attrs['class'][] = $uk_flex;
	}

	// Alignment
	if ( ! empty( $contentJustification ) ) {
		if ( 'space-between' === $contentJustification ) {
			$contentJustification = 'between';
		}
		$block_attrs['class'][] = "uk-flex-{$contentJustification}";
	}
	/* WP 5.9 */
	if ( ! empty( $attrs['layout'] ) ) {
		foreach ( $attrs['layout'] as $layout_key => $layout_value ) {
			if ( 'type' === $layout_key ) {
				continue;
			}

			// Replace WordPress attribute value with uk-flex-* part of the classname. Use null for defaults.
			$replacements = array(
				'horizontal'    => null,
				'left'          => null,
				'space-between' => 'between',
				'vertical'      => 'column',

			);
			$layout_value = str_replace( array_keys( $replacements ), array_values( $replacements ), $layout_value );
			if ( ! empty( $layout_value ) ) {
				$block_attrs['class'][] = "uk-flex-{$layout_value}";
			}
		}
	}

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	return $block_content;
}

add_filter( 'render_block_core/button', 'mp_extend_button_block', 10, 2 );
function mp_extend_button_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// Button wrapper
	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$a_attrs = array();

	// $block_attrs		Outer wrapper
	// $a_attrs			<a> the button

	@$width          = $attrs['width'];
	$backgroundColor = ! empty( $attrs['backgroundColor'] ) ? $attrs['backgroundColor'] : 'default';
	$textColor       = ! empty( $attrs['textColor'] ) ? $attrs['textColor'] : false;

	// Button itself
	$a_attrs['class'][] = $className;
	$a_attrs['class'][] = 'uk-button';

	// Remove all classes from the outer wrapper
	// if(!empty($classArray)) $block_attrs['class'][] = mp_negate_class($classArray);

	$uk_toggle = mp_array_starts_with( $classArray, 'toggle-' );
	if ( ! empty( $uk_toggle ) ) {
		$a_attrs['class'][]     = mp_negate_class( $uk_toggle );
		$a_attrs['uk-toggle'][] = 'target: ' . join( '', str_replace( array( 'toggle-', '>' ), array( '', ' > ' ), $uk_toggle ) );

		// Use the animation class(es) for the toggle
		$uk_animation = mp_array_starts_with( $classArray, 'uk-animation-' );
		if ( ! empty( $uk_animation ) ) {
			$a_attrs['class'][]     = mp_negate_class( $uk_animation );
			$a_attrs['uk-toggle'][] = 'animation: ' . join( ', ', $uk_animation );
		}
	}

	// Width
	if ( ! empty( $width ) ) {
		$a_attrs['class'][] = uk_width( $width );
	}

	// uk-button has variations uk-button-link, uk-button-text, uk-button-small, uk-button-large (https://getuikit.com/docs/button)
	foreach ( array( 'link', 'text', 'small', 'large' ) as $uk_button ) {
		if ( in_array( $uk_button, $blockStyle ) ) {
			$a_attrs['class'][] = "uk-button-{$uk_button}";
		}
	}

	// Background Color
	if ( ! hasClass( array( 'uk-button-text', 'uk-button-link' ), $a_attrs['class'] ) ) {
		$a_attrs['class'][] = sprintf( 'uk-button-%s', $backgroundColor );
	}

	// Text Color: Gutenberg color palette includes 'default' but uk-text-default is not for text.
	// Therefore, interpret 'default' as 'global' ($global-color, which is the body color).
	if ( ! empty( $textColor ) ) {
		$a_attrs['class'][] = 'default' === $textColor ? 'uk-text-global' : "uk-text-{$textColor}";
	}

	$block_content = mp_html_attrs_by_class( $block_content, 'wp-block-button__link', $a_attrs, false );

	// Move button out of wp-block-button wrapper and remove the wrapper
	$block_content = mp_move_element( $block_content, '.uk-button' );
	$block_content = mp_html_remove_by_class( $block_content, 'wp-block-button' );
	// $block_content = mp_html_attrs($block_content, '/html/body/*', $block_attrs, TRUE);

	return $block_content;
}

add_filter( 'render_block_core/heading', 'mp_extend_heading_block', 10, 2 );
function mp_extend_heading_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$level            = ! empty( $attrs['level'] ) ? $attrs['level'] : 2;
	@$fontSize        = $attrs['fontSize'];
	@$backgroundColor = $attrs['backgroundColor'];
	@$textColor       = $attrs['textColor'];

	// Font Size
	if ( ! empty( $fontSize ) ) {
		$unit = trim( $fontSize, '0..9.' );
		switch ( $unit ) {
			case 'px':
			case 'em':
			case 'rem':
				$block_attrs['style'][] = "font-size: $fontSize";
				break;
			default:
				switch ( $fontSize ) {
					case 'small':
						$block_attrs['class'][] = 'uk-heading-small';
						break;
					case 'medium':
						$block_attrs['class'][] = 'uk-heading-medium';
						break;
					case 'large':
						$block_attrs['class'][] = 'uk-heading-large';
						break;
					case 'xlarge':
						$block_attrs['class'][] = 'uk-heading-xlarge';
						break;
					case '2xlarge':
						$block_attrs['class'][] = 'uk-heading-2xlarge';
						break;
				}
		}
	}

	// Background Color
	if ( ! empty( $backgroundColor ) ) {
		$block_attrs['class'][] = "uk-background-{$backgroundColor}";
	}

	// Text Color: Gutenberg color palette includes 'default' but uk-text-default is not for text.
	// Therefore, interpret 'default' as 'global' ($global-color, which is the body color).
	if ( ! empty( $textColor ) ) {
		$block_attrs['class'][] = 'default' === $textColor ? 'uk-text-global' : "uk-text-{$textColor}";
	}

	// uk-heading has variations uk-heading-divider, uk-heading-bullet, uk-heading-line (https://getuikit.com/docs/heading)
	foreach ( array( 'divider', 'bullet', 'line' ) as $uk_heading ) {
		if ( in_array( $uk_heading, $blockStyle ) ) {
			$block_attrs['class'][] = "uk-heading-{$uk_heading}";
		}
	}

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	return $block_content;
}


add_filter( 'render_block_core/paragraph', 'mp_extend_paragraph_block', 10, 2 );
function mp_extend_paragraph_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	@$fontSize        = $attrs['fontSize'];
	@$backgroundColor = $attrs['backgroundColor'];
	@$textColor       = $attrs['textColor'];

	$block_attrs['class'][] = $classArray;

	// Font Size
	if ( ! empty( $fontSize ) ) {
		$unit = trim( $fontSize, '0..9.' );
		switch ( $unit ) {
			case 'px':
			case 'em':
			case 'rem':
				$block_attrs['style'][] = "font-size: $fontSize";
				break;
			default:
				switch ( $fontSize ) {
					case 'small':
						$block_attrs['class'][] = 'uk-text-small';
						break;
					case 'medium':
						$block_attrs['class'][] = 'uk-text-medium';
						break;
					case 'large':
						$block_attrs['class'][] = 'uk-text-large';
						break;
					case 'xlarge':
						$block_attrs['class'][] = 'uk-text-xlarge';
						break;
					case '2xlarge':
						$block_attrs['class'][] = 'uk-text-2xlarge';
						break;
				}
		}
	}

	// Background Color
	if ( ! empty( $backgroundColor ) ) {
		$block_attrs['class'][] = "uk-background-{$backgroundColor}";
	}

	// Text Color: Gutenberg color palette includes 'default' but uk-text-default is not for text.
	// Therefore, interpret 'default' as 'global' ($global-color, which is the body color).
	if ( ! empty( $textColor ) ) {
		$block_attrs['class'][] = 'default' === $textColor ? 'uk-text-global' : "uk-text-{$textColor}";
	}

	// Drop Cap
	if ( ! empty( $attrs['dropCap'] ) ) {
		$block_attrs['class'][] = 'uk-dropcap';
	}

	// uk-text has variations uk-text-lead, uk-text-meta, uk-text-light, uk-text-bold, uk-text-lighter, uk-text-bolder (https://getuikit.com/docs/text)
	foreach ( array( 'lead', 'meta', 'light', 'lighter', 'bold', 'bolder' ) as $uk_text ) {
		if ( in_array( $uk_text, $blockStyle ) ) {
			$block_attrs['class'][] = "uk-text-{$uk_text}";
		}
	}

	// uk-icon:icon
	// will prepend icon to the paragraph. Useful for info circles and warning triangles.
	if ( preg_match( '/uk\-icon:([^ ]*)/', $className, $icon ) ) {
		$block_attrs['class'][] = 'has-icon !uk-icon:' . $icon[1];
		$icon_html              = get_icon( $icon[1] );
		if ( ! empty( $icon_html ) ) {
			$block_content = mp_insert_element( $block_content, $icon_html, '//p', 'firstChild' );
		}
	}

	// Alert - uk-alert
	$alert = preg_grep( '/uk\-alert([^ ]*)/', $classArray );
	if ( $alert ) {
		$block_attrs['class'][] = 'uk-alert';
		$block_attrs[]          = 'uk-alert';
		$tag                    = 'div';
	}

	// Leave <p> tag by default, but send $tag to function in case we need a <div>
	$tag         ??= null;
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true, $tag );

	return $block_content;
}


add_filter( 'render_block_core/list', 'mp_extend_list_block', 10, 2 );
function mp_extend_list_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}
	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	@$fontSize        = $attrs['fontSize'];
	@$backgroundColor = $attrs['backgroundColor'];
	@$textColor       = $attrs['textColor'];
	@$ordered         = ( isset( $attrs['ordered'] ) && $attrs['ordered'] === true );

	$block_attrs['class'][] = 'uk-list';

	// Font Size
	if ( ! empty( $fontSize ) ) {
		$unit = trim( $fontSize, '0..9.' );
		switch ( $unit ) {
			case 'px':
			case 'em':
			case 'rem':
				$block_attrs['style'][] = "font-size: $fontSize";
				break;
			default:
				switch ( $fontSize ) {
					case 'small':
						$block_attrs['class'][] = 'uk-text-small';
						break;
					case 'medium':
						$block_attrs['class'][] = 'uk-text-medium';
						break;
					case 'large':
						$block_attrs['class'][] = 'uk-text-large';
						break;
					case 'xlarge':
						$block_attrs['class'][] = 'uk-text-xlarge';
						break;
					case '2xlarge':
						$block_attrs['class'][] = 'uk-text-2xlarge';
						break;
				}
		}
	}

	// Background Color
	if ( ! empty( $backgroundColor ) ) {
		$block_attrs['class'][] = "uk-background-{$backgroundColor}";
	}

	// Text Color: Gutenberg color palette includes 'default' but uk-text-default is not for text.
	// Therefore, interpret 'default' as 'global' ($global-color, which is the body color).
	if ( ! empty( $textColor ) ) {
		if ( 'default' === $textColor ) {
			$textColor = 'global';
		}
		$block_attrs['class'][] = "uk-text-{$textColor}";
	}

	// uk-list has variations uk-list-disc, uk-list-circle, uk-list-square, uk-list-hyphen, uk-list-decimal (https://getuikit.com/docs/list)
	// 'theme' style is a custom theme style, for instance if you want special bullets, defined in default.scss
	$tag = null;
	if ( $ordered ) {
		$block_attrs['class'][] = 'uk-list-decimal';
		$tag                    = 'ul'; // override default <ol>
	} elseif ( in_array( 'theme', $blockStyle ) ) {
		// Theme style defined in _default.scss
		$block_attrs['class'][] = 'list-theme';
		// } elseif(!in_array('none', $blockStyle)) {
		// Default style
		// $block_attrs['class'][] = 'uk-list-disc';
	} else {
        if(!in_array('none', $blockStyle)) {
            foreach ( array( 'circle', 'square', 'hyphen' ) as $uk_list ) {
                if ( in_array( $uk_list, $blockStyle ) ) {
                    $block_attrs['class'][] = "uk-list-{$uk_list}";
                } else {
                    $block_attrs['class'][] = "uk-list-disc";
                }
            }
        }
	}

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true, $tag );
	return $block_content;
}


add_filter( 'render_block_woocommerce/product-category', 'mp_extend_woocommerce_category_block', 10, 2 );
function mp_extend_woocommerce_category_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$grid_attrs      = array();
	$grid_item_attrs = array();
	$button_attrs    = array();

	$uk_child_width = mp_array_starts_with( $classArray, 'uk-child-width-' );
	$uk_button      = mp_array_starts_with( $classArray, 'uk-button-' );

	// Any classes go on the grid item or grid, not the block
	$block_attrs['class'][]     = mp_negate_class( $classArray );
	$grid_item_attrs['class'][] = $classArray;
	$grid_item_attrs['class'][] = mp_negate_class( $uk_child_width );
	$grid_item_attrs['class'][] = mp_negate_class( $uk_button );

	// Columns: absent value is 3
	$width_breakpoint = '@m';
	$columns          = ! empty( $attrs['columns'] ) ? $attrs['columns'] : 3;

	if ( $columns > 1 ) {
		$grid_attrs['class'][] = 'uk-grid';
		$grid_attrs[]          = 'uk-margin';

		// User can override columns to fine-tune column width
		if ( empty( $uk_child_width ) ) {
			$grid_attrs['class'][] = "uk-child-width-1-{$columns}{$width_breakpoint}";
		} else {
			$grid_attrs['class'][] = $uk_child_width;
		}
	}

	// Add-to-Cart Buttons: use 'uk-button-*' classname to style button.
	if ( $uk_button ) {
		$button_attrs['class'][] = $uk_button;
	} else {
		$button_attrs['class'][] = 'uk-button-default';
	}

	// Styling
	$block_content = mp_html_class_by_class( $block_content, 'star-rating', 'uk-margin-remove-right', true );

	// Grid
	$block_content = mp_html_attrs_by_class( $block_content, 'wc-block-grid__products', $grid_attrs, true );

	// Product
	$block_content = mp_html_attrs_by_class( $block_content, 'wc-block-grid__product', $grid_item_attrs, true );

	// Add-to-Cart Button
	$block_content = mp_html_class_by_class( $block_content, 'wp-block-button__link', 'uk-button-default', true );

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	return $block_content;
}


add_filter( 'render_block_core/embed', 'mp_extend_embed_block', 10, 2 );
function mp_extend_embed_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$inner_attrs = array(); // .wp-block-embed__wrapper
	$embed_attrs = array(); // <iframe>

	// Get dimensions of iframe, so we can size it properly
	// WP seems to automatically add a class with the aspect ratio.
	// $wp_embed_aspect = mp_array_starts_with($classArray, 'wp-embed-aspect-');

	$embed = mp_get_attributes( $block_content, '//div[contains(@class, "wp-block-embed__wrapper")]/iframe', true );
	if ( ! empty( $embed['width'] ) && ! empty( $embed['height'] ) ) {
		$aspect = $embed['width'] / $embed['height'];
		// Ensure correct aspect ratio
		$inner_attrs['style'][] = "padding-bottom: calc(100% / {$aspect}";
		$inner_attrs['class'][] = 'uk-cover-container';
		// uk-cover has pointer-events disabled, so don't use that
		$embed_attrs['class'][] = 'uk-position-cover uk-width-1-1 uk-height-1-1';
	}

	// Autoplay, etc.
	$embed_attrs['uk-video'][] = in_array( 'autoplay', $classArray ) ? 'autoplay: inview' : 'autoplay: false';
	$embed_attrs['uk-video'][] = in_array( 'automute', $classArray ) ? 'automute: true' : 'automute: false';

	// .wp-block-embed__wrapper
	$block_content = mp_html_attrs( $block_content, '.wp-block-embed__wrapper', $inner_attrs, true );

	// <iframe>
	$block_content = mp_html_attrs( $block_content, '//div[contains(@class, "wp-block-embed__wrapper")]/iframe', $embed_attrs, array( 'allow' => false ) );

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );

	return $block_content;
}

add_filter( 'render_block_core/video', 'mp_extend_video_block', 10, 2 );
function mp_extend_video_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$block_attrs = array(); // .wp-block-video
	$video_attrs = array( 'uk-video' => array() ); // <video>

	// Apply all classes to <video> rather than block div
	$block_attrs['class'][] = mp_negate_class( $classArray );
	$video_attrs['class']   = $classArray;

	$video = mp_get_attributes( $block_content, '//video', true );

	// Autoplay, etc.
	$video_attrs['uk-video'][] = array_key_exists( 'autoplay', $video ) ? 'autoplay: inview' : 'autoplay: false';
	$video_attrs['uk-video'][] = array_key_exists( 'muted', $video ) ? 'automute: true' : 'automute: false';

	// Alignment.
	if ( ! empty( $attrs['align'] ) ) {
		switch ( $attrs['align'] ) {

			case 'center':
				$block_attrs['class'][] = 'uk-flex-center'; break;
			case 'right':
				$block_attrs['class'][] = 'uk-flex-right'; break;

		}
	}

	// <video>
	$block_content = mp_html_attrs(
		$block_content,
		'//video',
		$video_attrs,
		array(
			'autoplay' => false,
			'muted'    => false,
		)
	);

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );

	return $block_content;
}


add_filter( 'render_block_core/table', 'mp_extend_table_block', 10, 2 );
function mp_extend_table_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$layout_attrs['class'][] = 'uk-table';

	// Striped
	if ( in_array( 'striped', $blockStyle ) ) {
		$layout_attrs['class'][] = 'uk-table-striped';
	}

	$uk_table = mp_array_starts_with( $classArray, 'uk-table-' );
	if ( $uk_table ) {
		$layout_attrs['class'][] = $uk_table;
		$block_attrs['class'][]  = mp_negate_class( $uk_table );
	}

	$block_content = mp_html_attrs( $block_content, '//table', $layout_attrs, true );

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	return $block_content;
}


add_filter( 'render_block_core/pullquote', 'mp_extend_pullquote_block', 10, 2 );
function mp_extend_pullquote_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	// extract( mp_create_block_variables($block) );

	// Wrap <cite> in <footer>
	$block_content = mp_wrap_element( $block_content, '//cite', 'footer' );

	// $block_content = mp_html_attrs($block_content, '/html/body/*', $block_attrs, TRUE);
	return $block_content;
}

// Search and WooCommerce Product Search
add_filter( 'render_block_woocommerce/product-search', 'mp_extend_search_block', 10, 2 );
add_filter( 'render_block_core/search', 'mp_extend_search_block', 10, 2 );
function mp_extend_search_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$grid_attrs   = array();
	$input_attrs  = array();
	$button_attrs = array();

	$uk_button              = mp_array_starts_with( $classArray, 'uk-button-' );
	$block_attrs['class'][] = mp_negate_class( $uk_button );

	$grid_attrs['class'][]  = 'uk-flex uk-flex-stretch uk-flex-gap-small@s';
	$input_attrs['class'][] = 'uk-search-input uk-flex-1';
	$block_attrs['class'][] = 'uk-search uk-search-default';

	if ( $uk_button ) {
		$button_attrs['class'][] = $uk_button;
	} else {
		$button_attrs['class'][] = 'uk-button-default';
	}

	// uk-search has variations uk-search-default, uk-search-large (https://getuikit.com/docs/search)
	if ( in_array( 'large', $blockStyle ) ) {
		$block_attrs['class'][] = 'uk-search-large';
	}

	// Button size
	if ( in_array( 'large', $blockStyle ) ) {
		$button_attrs['class'][] = 'uk-button-large';
	}

	// Remove children of <button>
	$block_content = mp_html_remove( $block_content, '//button[@type="submit"]/*' );

	// Add a search icon
	$button_icon   = buildAttributes(
		array(
			'name'  => 'search',
			'class' => 'uk-icon',
		),
		'ion-icon',
		true
	);
	$block_content = mp_move_element( $block_content, $button_icon, '//button[@type="submit"]', 'firstChild' );
	$block_content = mp_move_element( $block_content, '<span class="uk-visible@s">' . __( 'Search', 'woocommerce' ) . '</span>', '//button[@type="submit"]', 'lastChild' );

	$button_attrs['class'][] = 'has-icon';

	// Layout
	$block_content = mp_html_attrs( $block_content, '//form/div', $grid_attrs, true );

	// Button
	$block_content = mp_html_attrs( $block_content, '//button[@type="submit"]', $button_attrs, true );

	// Input
	$block_content = mp_html_attrs( $block_content, '//input[@type="search"]', $input_attrs, true );

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );

	return $block_content;
}


add_filter( 'render_block_woocommerce/product-categories', 'mp_extend_wc_product_categories_block', 10, 2 );
function mp_extend_wc_product_categories_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$grid_attrs      = array();
	$grid_item_attrs = array();

	$uk_child_width = mp_array_starts_with( $classArray, 'uk-child-width-' );
	$uk_link        = mp_array_starts_with( $classArray, 'uk-link-' );
	$uk_card        = mp_array_starts_with( $classArray, 'uk-card-' );

	// Any classes go on the grid item or grid, not the block
	$block_attrs['class'][]     = mp_negate_class( $uk_child_width );
	$grid_item_attrs['class'][] = $classArray;
	$grid_item_attrs['class'][] = mp_negate_class( $uk_child_width );
	$grid_item_attrs['class'][] = mp_negate_class( $uk_link );
	$grid_item_attrs['class'][] = mp_negate_class( $uk_card );

	// Columns: use WC loop setting or override with classname uk-child-width-*
	$width_breakpoint      = '@m';
	$columns               = esc_attr( wc_get_loop_prop( 'columns' ) );
	$grid_attrs['class'][] = empty( $uk_child_width ) ? "uk-child-width-1-{$columns}@m" : $uk_child_width;

	$grid_attrs['class'][] = 'uk-grid';
	$grid_attrs[]          = 'uk-margin';

	// Grid Item (Category)
	$grid_item_attrs['class'][] = 'uk-card uk-display-block';
	// Default styles can be overridden by providing uk-card-* classnames
	$grid_item_attrs['class'][] = empty( $uk_card ) ? 'uk-card-secondary uk-card-hover uk-card-small' : $uk_card;

	// Image
	// 1. Change the category image tag to a <div> and apply card-media classes
	$block_content = mp_html_class( $block_content, '.wc-block-product-categories-list-item__image', 'uk-card-media-top uk-cover-container', true, 'div' );
	// 2. Category image is cropped to fix parent (uk-cover)
	$block_content = mp_html_attrs( $block_content, '//*[contains(@class, "wc-block-product-categories-list-item__image")]/descendent::img', array( 'uk-cover' ), true );
	// 3. Since uk-cover does absolute positioning, add a <canvas> element to preserve height when stacked
	$block_content = mp_insert_element( $block_content, '<canvas height="230"></canvas>', '.wc-block-product-categories-list-item__image', 'lastChild' );

	// Grid Item
	// 1. Wrap category title in a div.uk-card-title
	$block_content = mp_wrap_element( $block_content, '//*[contains(@class, "wc-block-product-categories-list-item")]/a/text()', 'div', array( 'class' => 'uk-card-title' ) );
	// 2. Wrap div.uk-card-title in div.uk-card-body
	$block_content = mp_wrap_element( $block_content, '.uk-card-title', 'div', array( 'class' => 'uk-card-body' ) );
	// 3. Apply $card_attrs to the category link (the whole card becomes a link)
	$block_content = mp_html_attrs( $block_content, '//*[contains(@class, "wc-block-product-categories-list-item")]/a', $card_attrs, true );
	// 4. Change the category item tag to a <div>
	$block_content = mp_html_attrs( $block_content, '.wc-block-product-categories-list-item', array(), true, 'div' );

	// Grid
	$block_content = mp_html_attrs( $block_content, '.wc-block-product-categories-list', $grid_attrs, true, 'div' );

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );
	return $block_content;
}


add_filter( 'render_block_core/group', 'mp_extend_group_block', 10, 2 );
function mp_extend_group_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$grid_attrs      = array();
	$grid_item_attrs = array();
	$component_attrs = array(); // not yet known what component this will be

	$uk_animation = mp_array_starts_with( $classArray, 'uk-animation-' );

	if ( ! empty( $uk_animation ) ) {
		$block_attrs['class'][] = mp_negate_class( $uk_animation );
		$component_attrs[]      = 'animation: ' . buildClass( $uk_animation );
	}

	$backgroundColor = ! empty( $attrs['backgroundColor'] ) ? $attrs['backgroundColor'] : false;
	$textColor       = ! empty( $attrs['textColor'] ) ? $attrs['textColor'] : false;

	// Background Color
	if ( ! empty( $backgroundColor ) ) {
		$block_attrs['class'][] = "uk-background-{$backgroundColor}";
	}

	// Text Color: Gutenberg color palette includes 'default' but uk-text-default is not for text.
	// Therefore, interpret 'default' as 'global' ($global-color, which is the body color).
	if ( ! empty( $textColor ) ) {
		$block_attrs['class'][] = 'default' === $textColor ? 'uk-text-global' : "uk-text-{$textColor}";
    }

	// Tabs/Switcher
	if ( ! empty( $block['innerBlocks'] ) ) {
		$is_subnav          = in_array( 'subnav', $classArray );
		$is_tabs            = in_array( 'tabs', $classArray );
		$is_switcher        = in_array( 'switcher', $classArray );
		$is_switcher_toggle = in_array( 'switcher-toggle', $classArray );

		if ( $is_subnav || $is_tabs || $is_switcher || $is_switcher_toggle ) {
			if ( $is_switcher ) {
				// For switcher, any classes go on the grid item, not the block
				$block_attrs['class'][]     = mp_negate_class( $classArray );
				$grid_item_attrs['class'][] = $classArray;
				$grid_item_attrs['class'][] = mp_negate_class( $uk_animation, 'switcher' );
			} else {
				// For tabs/subnav, any classes ARE applied to the block element (<ul>)
				// For toggle, switcher is applied to the inner block element
				$block_attrs['class'][] = $classArray;
				$block_attrs['class'][] = mp_negate_class( 'subnav', 'tabs', 'switcher-toggle' );

				$toggle = mp_array_starts_with( $classArray, 'toggle-' );
				if ( ! empty( $toggle ) ) {
					$block_attrs['class'][] = mp_negate_class( $toggle );
					$component_attrs[]      = 'toggle: > * > ' . join( '', str_replace( array( 'toggle-', '>' ), array( '', ' > ' ), $toggle ) );
				}
			}

			// Get the inner container, but not inner-inner containers
			$inner_container = '//div[contains(@class, "wp-block-group__inner-container") and not(ancestor::div[contains(@class, "wp-block-group__inner-container")])]';

			if ( $is_tabs || $is_subnav ) {
				foreach ( $block['innerBlocks'] as $innerBlock ) {
					$tab_items[] = sprintf( '<li><a href="#">%s</a></li>', strip_tags( $innerBlock['innerHTML'] ) );
				}
			} elseif ( $is_switcher ) {

				// 1. Wrap the switcher tabs in <li> tags and apply $grid_item_attrs.
				$block_content = mp_wrap_element( $block_content, "{$inner_container}/*[not(self::text())]", 'li', $grid_item_attrs );
				// 2. Move them out of the inner container.
				$block_content = mp_move_element( $block_content, "{$inner_container}/li", $inner_container, 'insertBefore' );
				// 3. Remove the empty inner container.
				$block_content = mp_html_remove( $block_content, '//div[not(normalize-space()) and contains(@class, "wp-block-group__inner-container") and not(ancestor::div[contains(@class, "wp-block-group__inner-container")])]' );
				// 4. Add switcher attributes to the parent element.
				$block_content = mp_html_class( $block_content, '.switcher', 'uk-switcher', false, 'ul' );
			} elseif ( $is_switcher_toggle ) {
				if ( empty( $toggle ) ) {
					$component_attrs[] = 'toggle: > *';
				}

				// 1. Add uk-switcher attribute to child of inner-container
				$block_content = mp_html_attrs( $block_content, "{$inner_container}/*", array( 'uk-switcher' => $component_attrs ), true );
				// 2. Move the uk-switcher div to the root level
				$block_content = mp_move_element( $block_content, "{$inner_container}/*", '.wp-block-group' );
				// 3. Remove the now-empty group element
				$block_content = mp_html_remove_by_class( $block_content, 'wp-block-group' );
			}

			// Both tabs and subnav are wrapped in <ul>
			if ( ! empty( $tab_items ) ) {
				if ( $is_tabs ) {
					$block_content = buildAttributes( array( 'uk-tab' => $component_attrs ), 'ul', $tab_items );
				} elseif ( $is_subnav ) {
					$block_content = buildAttributes(
						array(
							'class'       => 'uk-subnav uk-subnav-pill',
							'uk-switcher' => $component_attrs,
						),
						'ul',
						$tab_items
					);
				}
			}
		}
	}

	// $layout_class = mp_array_starts_with($classArray, 'uk-container', 'uk-child-width-', 'uk-grid', 'uk-flex', 'uk-card');
	// $ignored = [ 'uk-flex-last', 'uk-flex-first', 'uk-card-header', 'uk-card-body', 'uk-card-footer' ];
	// $layout_class_filtered = array_diff($layout_class, $ignored);
	// if(!empty($layout_class_filtered)) {
	// $inner_container = '//div[contains(@class, "__inner-container") and not(ancestor::div[contains(@class, "__inner-container")])]';
	// $inner_container = '//*[contains(@class, "__inner-container")]';
	// $inner_container = '//html/body/div/div[contains(@class, "__inner-container")]';

	// $block_attrs['class'][] = mp_negate_class($layout_class_filtered);
	// $block_content = mp_html_class($block_content, $inner_container, $layout_class_filtered, TRUE);
	// }

	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );

	return $block_content;
}


// Hand-picked Products. This function may also work on other WooCommerce product blocks.
add_filter( 'render_block_woocommerce/handpicked-products', 'mp_extend_wc_handpicked_products_block', 10, 2 );
function mp_extend_wc_handpicked_products_block( $block_content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
	if ( $is_gb_editor || is_admin() ) {
		return $block_content;
	}

	// The $block['attrs'] contains a list of product IDs, but not in the specified order.
	// We can either re-create the WP_Query, or find a way to match the rendered product IDs,
	// and loop over those while calling the standard WC product loop template.
	// The only place where the product IDs appear is the add-to-cart button. This isn't acceptable
	// because the button can be disabled. Therefore, we need a hook (woocommerce_blocks_product_grid_item_html)
	// to alter the product <li> and add the product ID as a data attribute there.

	if ( ! function_exists( 'wc_get_products' ) ) {
		return $block_content;
	}

	// $attrs, $className, $classArray, $blockStyle
	// $block_attrs,
	extract( mp_create_block_variables( $block ) );

	$grid_attrs      = array();
	$grid_item_attrs = array();
	$component_attrs = array();

	$block_attrs['class'][] = $classArray;

	$uk_flex = mp_array_starts_with( $classArray, 'uk-flex-' );

	extract( $attrs );

	// Defaults
	$columns           ??= 3;
	$contentVisibility ??= array(
		'title'  => 1,
		'price'  => 1,
		'rating' => 1,
		'button' => 1,
	);
	$alignButtons      ??= 1;

	// Run the products through the standard WC product loop template, rather than the block template.
	// 1. Get the product IDs. We set 'data-product' in the 'woocommerce_blocks_product_grid_item_html' filter hook, so we can easily extract it here.
	$block_item_attrs = mp_get_attributes( $block_content, '.wc-block-grid__product' );
	if ( ! empty( $block_item_attrs ) ) {
		$product_ids = array_column( $block_item_attrs, 'data-product' );

		// Allow filtering the products IDs
		$product_ids = apply_filters( 'woocommerce_handpicked_products_block_products', $product_ids );
	}

	// For each product, generate the HTML in the normal way, using the shop loop actions.
	if ( ! empty( $product_ids ) ) {
		$products = array_filter( array_map( 'wc_get_product', $product_ids ) );

		ob_start();
		if ( ! empty( $products ) ) {

			// Re-create this block using the product loop template.
			wc_set_loop_prop( 'columns', $columns );
			wc_set_loop_prop( 'name', str_replace( 'woocommerce/', 'block-', $block['blockName'] ) );

			wc_set_loop_prop( 'current_page', 1 );
			wc_set_loop_prop( 'is_paginated', wc_string_to_bool( false ) );
			wc_set_loop_prop( 'page_template', get_page_template_slug() );
			wc_set_loop_prop( 'per_page', -1 );
			wc_set_loop_prop( 'total', count( $products ) );
			wc_set_loop_prop( 'total_pages', 1 );
			wc_set_loop_prop( 'is_slider', false );

			// This will actually fail for sliders. We need the center: true attribute
			if ( ! empty( $uk_flex ) ) {
				$block_attrs['class'][] = mp_negate_class( $uk_flex );
				if ( wc_get_loop_prop( 'is_slider', false ) ) {
					$component_attrs['uk-slider'][] = 'center: true; finite: true';
				} else {
					$grid_attrs['class'][] = $uk_flex;
				}
			}

			do_action( 'woocommerce_before_shop_loop' );

			// WooCommerce function to display orderby.php bails because $wp_query->found_posts isn't applicable to this loop.
			// So we have to recreate most of that function here before woocommerce_product_loop_start().
			// $orderby = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
			// $show_default_orderby = 'menu_order' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
			// $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
			// 'menu_order' => __( 'Default sorting', 'woocommerce' ),
			// 'popularity' => __( 'Sort by popularity', 'woocommerce' ),
			// 'rating' => __( 'Sort by average rating', 'woocommerce' ),
			// 'date' => __( 'Sort by newness', 'woocommerce' ),
			// 'price' => __( 'Sort by price: low to high', 'woocommerce' ),
			// 'price-desc' => __( 'Sort by price: high to low', 'woocommerce' ),
			// ) );

			// if ( ! $show_default_orderby ) {
			// unset( $catalog_orderby_options['menu_order'] );
			// }

			// if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
			// unset( $catalog_orderby_options['rating'] );
			// }
			// wc_get_template( 'loop/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby, 'show_default_orderby' => $show_default_orderby ) );

			woocommerce_product_loop_start();

			global $product;
			foreach ( $products as $product ) {
				// setup_postdata() is needed so things like get_the_title() work
				$post_object = get_post( $product->get_id() );
				setup_postdata( $GLOBALS['post'] =& $post_object );

				// Calling the WooCommerce product loop content template
				wc_get_template_part( 'content', 'product' );
			}

			wp_reset_postdata();

			woocommerce_product_loop_end();

			do_action( 'woocommerce_after_shop_loop' );
		} else {
			do_action( 'woocommerce_no_products_found' );
		}

		$products_ul = ob_get_clean();

		// Remove the original product list
		$block_content = mp_html_remove_by_class( $block_content, 'wc-block-grid__products' );

		// Append our new product list
		if ( wc_get_loop_prop( 'is_slider', false ) ) {
			$products_ul = mp_html_attrs( $products_ul, '//*[@uk-slider]', $component_attrs, false );
		}

		if ( $alignButtons == true && $columns > 1 ) {
			$grid_attrs['uk-height-match'] = 'target: .uk-card-body';
		}
		$products_ul = mp_html_attrs( $products_ul, 'ul.products', $grid_attrs, array( 'uk-height-match' => false ) );
		$products_ul = mp_html_attrs( $products_ul, 'li.product', $grid_item_attrs, true );

		// $block_attrs['uk-filter'] = 'target: .products'; // enable sorting with uk-filter

		$block_content = str_replace( '</div>', $products_ul . '</div>', $block_content );

		// Hide elements if the block settings indicate
		if ( ! empty( $contentVisibility ) ) {
			if ( ! (bool) $contentVisibility['title'] ) {
				$block_content = mp_html_remove_by_class( $block_content, 'woocommerce-loop-product__title' );
			}
			if ( ! (bool) $contentVisibility['price'] ) {
				$block_content = mp_html_remove_by_class( $block_content, 'price' );
			}
			if ( ! (bool) $contentVisibility['rating'] ) {
				$block_content = mp_html_remove_by_class( $block_content, 'woocommerce-product-rating' );
			}
			if ( ! (bool) $contentVisibility['button'] ) {
				$block_content = mp_html_remove( $block_content, '//*[contains(@href, "add-to-cart")]' );
			}
		}
	}

	// Block
	$block_content = mp_html_attrs( $block_content, '/html/body/*', $block_attrs, true );

	return $block_content;
}
