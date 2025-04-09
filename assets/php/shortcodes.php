<?php
/**
 * buildDotnavDots
 * Helper function to build dot navigation for sliders.
 *
 * @param string $suffix        Slider suffix
 * @param int    $items         Number of items
 * @param string $aria_label    Format string for aria_label, where %s is 'nth'
 */

function buildDotnavDots( string $slider_id, int $items = 2, string $aria_label = 'Jump to %s item' ) {
	ob_start();
	for ( $i = 1; $i <= $items; $i++ ) {
		if ( $i == $items ) {
			$nth = 'last';
		} elseif ( $i == 1 ) {
			$nth = 'first';
		} else {
			$nth = o( $i );
		}

		$button = buildAttributes(
			array(
				'aria-controls' => "$slider_id",
				'aria-label'    => sprintf( $aria_label, $nth ),
			),
			'button'
		);

		echo buildAttributes( array( 'uk-slideshow-item' => ( $i - 1 ) ), 'li', $button );
	}
	return ob_get_clean();
}

/**
 * parse_template
 *
 * @param string $html                  template HTML
 * @param array  &$start                stuff before items
 * @param array  &$end                  stuff after items
 * @param array  &$items                the items
 * @param string $tag                   {tag} surrounding items, default is 'items'
 *
 * @return bool false for fail. variables should be passed be reference
 */
function parse_template( $html, &$start, &$end, &$items, string $tag = 'items' ) {
	if ( ! empty( $html ) ) {
		// Loop start with everything before {items}
		if ( empty( $start ) ) {
			preg_match( '/(?s)^.+?(?=\{' . $tag . '\})/', $html, $start );
		}

		// Loop end with everything after {/items}
		if ( empty( $end ) ) {
			preg_match( '/(?<=\{\/' . $tag . '\})(?s).+$/', $html, $end );
		}

		if ( preg_match( '/\{' . $tag . '\}(.+?)\{\/' . $tag . '\}/s', $html, $item ) ) {
			$items[] = $item[1];
		}

		return empty( $item[1] ) ? false : true;
	}
	return false;
}

/**
 * The [dynamic_content_by_term] shortcode.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function dynamic_content_by_term_shortcode( $atts = array() ) {
	$defaults = array(
		'template'       => 'default',
		'columns'        => 2,
		'post_type'      => array_key_exists( 'taxonomy', $atts ) ? null : 'post',
		'posts_per_page' => array_key_exists( 'taxonomy', $atts ) ? null : -1,
		'order'          => 'desc',
		'orderby'        => 'date',
		'meta_key'       => null,
		'meta_value'     => null,
		'meta_compare'   => null,
		'relation'       => null,
		'ignore_sticky'  => false,
		'taxonomy'       => null,
		'hide_empty'     => null,
		'tax_slug'       => null,
		'tax_terms'      => null,
		'heading'        => 'h3',
	);

	// Append our accepted filters, set to null (the default). These filters are intelligently(?) handled in query_params().
	$accepted_filters = array( 'category', 'tag', 'post', 'author' );
	$defaults         = array_merge( $defaults, array_fill_keys( $accepted_filters, null ) );

	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a = shortcode_atts( $defaults, $atts );

	// Remove NULL values.
	$a = array_filter(
		$a,
		static function ( $var ) {
			return $var !== null;
		}
	);

	$output = '';

	// Go through a taxonomy's terms one-by-one and echo each found post via the template.
	if ( ! empty( $a['taxonomy'] ) ) {
		$terms      = get_terms(
			array(
				'taxonomy'   => $a['taxonomy'],
				'hide_empty' => true,
				'orderby'    => 'term_order',
			)
		);
		$term_slugs = wp_list_pluck( $terms, 'name', 'slug' );
		if ( ! empty( $term_slugs ) ) {
			$atts['taxonomy'] = null;
			$atts['tax_slug'] = $a['taxonomy'];
			foreach ( $term_slugs as $term_slug => $term_name ) {
				$atts['tax_terms'] = $term_slug;
				$output           .= buildAttributes( array(), $a['heading'], $term_name );
				$output           .= dynamic_content_shortcode( $atts );
			}
		}
	}

	return $output;
}



/**
 * The [dynamic_content] shortcode.
 *
 * @param array  $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function dynamic_content_shortcode( $atts = [] ) {
    $defaults = [
        'template'                  => 'default',
        'columns'                   => 2,
        'post_type'                 => array_key_exists('taxonomy', $atts) ? null : 'post',
        'posts_per_page'            => array_key_exists('taxonomy', $atts) ? null : -1,
        'order'                     => 'desc',
        'orderby'                   => 'date',
        'meta_key'                  => null,
        'meta_value'                => null,
        'meta_compare'              => null,
        'meta_relation'             => null,
        'ignore_sticky'             => false,
        'taxonomy'                  => null,
        'taxonomy_terms'            => null,
        'hide_empty'                => null,
        'p'                         => null
    ];
    // Append our accepted filters, set to null (the default). These filters are intelligently(?) handled in query_params().
    $accepted_filters = [ 'category', 'tag', 'post', 'author' ];
    $defaults = array_merge( $defaults, array_fill_keys( $accepted_filters, null ) );

    // Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
    $a = shortcode_atts( $defaults, $atts );

    // Remove NULL values
    $a = array_filter($a, static function($var){return $var !== null;} );


    // Process multiple values for certain fields
    $explodes = [ 'order', 'orderby', 'meta_key', 'meta_value', 'meta_compare' ];
    $exploded = array();

    foreach( $explodes as $explode ) {
        $exploded[$explode] = array_key_exists($explode, $a) ? explode(',', $a[$explode]) : [];
        $exploded[$explode] = array_map(function($v) use ($defaults, $explode) {
            return $v ?: $defaults[$explode];
        }, $exploded[$explode]);
    }

    // Generate 'orderby' optionally using 'order' and/or defaults.
    // Example: orderby="title:desc,date:asc"
    //      or: orderby="title,date" order="desc,asc"
    foreach( $exploded['orderby'] as $i => $orderby_str ) {
        $orderby_arr = explode(":", $orderby_str, 2);
        $orderby_param = $orderby_arr[0];
        if( !empty($orderby_arr[1]) ) {
            $orderby_order = $orderby_arr[1];
        } elseif ( !empty($exploded['order'][$i]) ) {
            $orderby_order = $exploded['order'][$i];
        } else {
            $orderby_order = $defaults['order'];
        }

        // 'order': catch/correct invalid
        $orderby_order = preg_match('/^(a|de)sc/', $orderby_order) ? $orderby_order : $defaults['order'];
        $orderby[$orderby_param] = strtoupper($orderby_order);
    }

    // Template naming convention: /assets/php/shortcode-templates/template-{post-type}-{template}.php
    if( array_key_exists('post_type', $atts) ) {
        $type_query = array('post_type' => $a['post_type']);
    } elseif( array_key_exists('taxonomy', $atts) ) {
        $type_query = array('taxonomy' => $a['taxonomy']);
    } else {
        $type_query = array('post_type' => $defaults['post_type']);
    }
    $template = sprintf('template-%s-%s.php', reset($type_query), $a['template']);
    $template = get_stylesheet_directory() . '/assets/php/shortcode-templates/' . $template;
    if (!is_file($template)) return;

    // Simple metadata filtering
    if(!empty($exploded['meta_key'])) {

        foreach($exploded['meta_key'] as $i => $meta_key) {
            // $meta[$meta_key]['order'] = !empty($meta_data['order'][$i]) ? $meta_data['order'][$i] : '';
            $meta_clause[$meta_key]['compare'] = empty($exploded['meta_compare'][$i]) ? '=': $exploded['meta_compare'][$i];
            if(!empty($meta_clause[$meta_key]['compare'])) {
                $meta_clause[$meta_key]['value'] = empty($exploded['meta_value'][$i]) ? null: $exploded['meta_value'][$i];
            }
        }

        // Make sure the metadata field actually exists.
        // Get all field groups for this post type.
        $groups = function_exists('acf_get_field_groups') ? acf_get_field_groups($type_query) : false;

        // Loop over all the field groups.
        if($groups):
        foreach(array_column($groups, 'key') as $group_key) {

            // Get all the fields for each field group.
            $fields = acf_get_fields($group_key);

            // A field named 'sticky' is used to make the custom post stick to the top of the results.
            $has_sticky = array_filter($fields, static function($v, $k) {
                return !empty($v['name']) && $v['name'] == 'sticky';
            }, ARRAY_FILTER_USE_BOTH);

            if( $a['ignore_sticky'] != true && !empty($has_sticky)) {
                $meta_query['sticky'] = array(
                    'key'       => 'sticky',
                    'compare'   => 'EXISTS',
                    // 'value'     => '',
                );
                // Sticky ordering must always be first.
                $orderby = array('sticky' => 'DESC') + $orderby;
            }

            // Loop over all the meta keys we're querying on.
            foreach( $meta_clause as $key => $data ) {

                // Match the meta key name. We're only using the first match.
                $matched = array_filter($fields, static function($v, $k) use($key) {
                    return !empty($v['name']) && $v['name'] == $key;
                }, ARRAY_FILTER_USE_BOTH);

                // When a match is found, get the type and assign some default processes for each type.
                if(!empty($matched)) {
                    // Get the type of the matched field
                    $type = !empty(reset($matched)['type']) ? reset($matched)['type'] : 'string';

                    $data['key'] = $key;
                    if( strtolower($data['value']) === 'true' || strtolower($data['value']) === 'false' ) $data['value'] = (bool)$data['value'];

                    if (strpos($type, 'date') !== false) {
                        // A 'date' type field defaults to fetching all dates from today on, in ascending order.
                        if( empty($data['value']) ) $data['value'] = date('Y-m-d 23:59:59');
                        if( empty($data['compare']) ) $data['compare'] = '>';
                        if( empty($orderby[$key]) ) $orderby[$key] = 'asc';
                    }
                    if ('text' === $type || 'true_false' === $type) {
                        // A 'text' type field defaults to matching the 'value' unless a 'compare' is given.
                        if( empty($data['compare']) ) {
                            $data['compare'] = empty($data['value']) ? 'EXISTS' : '=';
                        }
                    }
                    if ('checkbox' === $type || 'select' === $type) {
                        // A 'checkbox' type will not match unless 'compare' is 'LIKE'
                        $data['compare'] = 'LIKE';
                    }
                    if ('number' === $type || 'range' === $type) {
                        $data['compare'] = empty($data['compare']) ? '=' : $data['compare'];
                        $data['value'] = (int)$data['value'];
                    }
                }

                // Build the meta_query and orderby clauses.
                $meta_query[$key] = array_intersect_key($data, array_flip(['key', 'value', 'compare']));
                // if( !empty($data['order']) ) $orderby[$key] = $data['order'];
            }

        }
        endif;
        // pre($meta_query);

        // If only one meta_query don't use 'meta_query'. Instead use 'meta_key', 'meta_value', 'meta_compare'.
        // The meta_* method may be a holdover from earlier WordPress, or it could be a performance thing?
        // If there's no performance gain, why not just always use 'meta_query'?
        if( !empty($meta_query) && count($meta_query) > 1 ) {
            $a['meta_query'] = $meta_query;
            if( !empty($orderby) ) $a['orderby'] = $orderby;
            $a['meta_query']['relation'] = !empty($a['relation']) ? $a['relation'] : 'AND';

            $removeKeys = ['meta_key', 'meta_value', 'meta_compare', 'order'];
            foreach($removeKeys as $key) unset($a[$key]);

        } elseif(!empty($key)) {
            // if (!empty($orderby)) $a['order'] = reset($orderby);
            // $a['orderby'] = 'meta_value';

            // $meta_query[0]['key'] = preg_replace('/_clause$/', '', array_key_first($meta_query));
            // $meta_query[0]['key'] = array_key_first($meta_query);
            array_walk($meta_query[$key], function($v, $k) use(&$a) {
                $a["meta_{$k}"] = $v;
            });
        }

        var_dump($a['meta_query']);
        
        if(count($a['meta_query']) === 2) {
            unset($a['meta_query']['relation']);
            $a['meta_query'] = reset($a['meta_query']);
        }

    }

    // Simple tax_query
    if(!empty($a['taxonomy_terms'])) {
        // if(!is_array($a['tax_terms'])) $a['tax_terms'] = array($a['tax_terms']);
        $tax_query = array(
            'taxonomy'  => $a['taxonomy'],
            'field'     => 'slug',
            'terms'     => explode(',', $a['taxonomy_terms']),
            // 'terms' => $a['taxonomy_terms'],
        );
        $a['tax_query'][] = $tax_query;
    }

    $query_params = query_params($a);
    // pre($query_params);
    // Query posts.
    // if ($a['post_type'] === 'product') {
    //     $query = new WC_Product_Query( $query_params );
    //     $products = $query->get_products();
    //     $found_posts = count($products);
    // } else {
    // if (!empty($a['taxonomy'])) {
    //     // pre($query_params);
    //     $query = get_terms( $query_params );
    //     $found_posts = count($query);
    // } else {
        $query = new WP_Query( $query_params );
        $found_posts = $query->found_posts;
    // }
    // ]pre($found_posts);

    if( $found_posts ) {

        // Generate a unique ID for the element.
        // This facilitates aria-controls attributes for arrows & dots, since the target needs a unique ID.
        $element_id = sprintf('%05x', mt_rand(0, 999999));

        // $html = '';
        // // Grab everything before the {items} tag in the template.
        // ob_start();
        // try {
        //     include $template;
        //     $html .= reset(explode('{items}', ob_get_contents(), 2));
        // // } catch (Exception $e) {
        // //     echo 'Caught exception: ',  $e->getMessage(), "\n";
        // } finally {
        //     ob_end_clean();
        // }

        $template_loop_start = $template_loop_end = $template_loop = [];

        if(gettype($query) === 'object') {
            // Loop over found posts.
            while ($query->have_posts()) : $query->the_post();

                // The variables set here will be available in the template file between the
                // {items} tags. They will not be available outside the {items} tags.
                // Grab post data that is (just about) always used. Other post data can be grabbed in the template file.
                $id = $query->post->ID;
                $the_title = apply_filters('the_title', get_the_title());
                $the_content = apply_filters('the_content', get_the_content());
                $the_excerpt = apply_filters('the_excerpt', get_the_excerpt());
                $thumbnail_id = get_post_thumbnail_id($id);


                // Get the Advanced Custom Fields attached to this post.
                $fields = get_fields($id);
                if( $fields ) {
                    // extract($fields);
                    $link_target = array_key_exists('open_new_window', $fields) ? '_blank' : '';
                }

                ob_start();
                include $template;
                $template_include = ob_get_clean();

                if(!parse_template( $template_include, $template_loop_start, $template_loop_end, $template_loop )) continue;

            endwhile;
            wp_reset_postdata();
        } elseif (gettype($query) === 'array') {
            foreach($query as $index => $query_item) {
                if(gettype($query_item) === 'object') {
                    if(get_class($query_item) === 'WP_Term') {
                        // pre($query_item);

                        // The variables set here will be available in the template file between the
                        // {items} tags. They will not be available outside the {items} tags.
                        $term_id = $query_item->term_id;
                        $the_title = $query_item->name;
                        $the_content = $query_item->description;
                        $thumbnail_id = get_term_meta( $term_id, 'thumbnail_id', true );

                        $fields = get_fields(reset($type_query) . '_' . $query_item->term_id);

                        if( $fields ) {
                            // extract($fields);
                            $link_target = array_key_exists('open_new_window', $fields) ? '_blank' : '';
                        }

                        // Process the template
                        ob_start();
                        include $template;
                        $template_include = ob_get_clean();
        
                        if(!parse_template( $template_include, $template_loop_start, $template_loop_end, $template_loop )) continue;
                    }
                }
            }
        }

        $html = trim_join('', $template_loop_start, $template_loop, $template_loop_end);
        return $html;
    }
}

/**
 * The [feature] shortcode.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function feature_shortcode( $atts = array() ) {
	$defaults = array(
		'template' => 'default',
		'columns'  => 3,
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a = shortcode_atts( $defaults, $atts );

	// Grab the features attached to this post.
	$features = get_field( 'featured' );
	if ( ! $features ) {
		return;
	}

	// Get page type
	global $wp_query;
	if ( $wp_query->is_page ) {
		$page_type = is_front_page() ? 'front' : 'page';
	} elseif ( $wp_query->is_shop ) {
		$page_type = 'shop';
	} elseif ( $wp_query->is_product_category ) {
		$page_type = 'product_cat';
	} elseif ( $wp_query->is_product ) {
		$page_type = 'product';
	} elseif ( $wp_query->is_category ) {
		$page_type = 'category';
	} elseif ( $wp_query->is_tag ) {
		$page_type = 'tag';
	} elseif ( $wp_query->is_tax ) {
		$page_type = 'tax';
	} elseif ( $wp_query->is_archive ) {
		$page_type = 'archive';
	} elseif ( $wp_query->is_search ) {
		$page_type = 'search';
	} elseif ( $wp_query->is_404 ) {
		$page_type = 'notfound';
	}
	$template = sprintf( 'template-%s-%s.php', $page_type, $a['template'] );
	// pre($template);

	// Generate a unique ID for the element.
	// This facilitates aria-controls attributes for arrows & dots, since the target needs a unique ID.
	$element_id = sprintf( '%05x', mt_rand( 0, 999999 ) );

	$html = '';

	// Grab everything before the {items} tag in the template.
	ob_start();
	try {
		include $template;
		$html .= reset( explode( '{items}', ob_get_contents(), 2 ) );
	} finally {
		ob_end_clean();
	}

	foreach ( $features as $index => $feature ) :
		$type   = $feature['type'];
		$object = $feature[ $type ];
		if ( gettype( $object ) !== 'object' ) {
			continue;
		}
		// pre($object);

		if ( 'WP_Term' === get_class( $object ) ) {
			// The variables set here will be available in the template file between the
			// {items} tags. They will not be available outside the {items} tags.
			$id            = $object->term_id;
			$the_title     = $object->name;
			$the_content   = $object->description;
			$the_permalink = get_category_link( $id );
			$thumbnail_id  = get_term_meta( $id, 'thumbnail_id', true );

			// Get the Advanced Custom Fields attached to this taxonomy/term.
			$fields = get_fields( 'term_' . $id );
		} elseif ( 'WP_Post' === get_class( $object ) ) {
			$id            = $object->ID;
			$the_title     = apply_filters( 'the_title', $object->post_title );
			$the_content   = apply_filters( 'the_content', $object->post_content );
			$the_excerpt   = apply_filters( 'the_excerpt', $object->post_excerpt );
			$the_permalink = apply_filters( 'the_permalink', get_permalink( $id ) );
			$thumbnail_id  = get_post_thumbnail_id( $id );

			// Get the Advanced Custom Fields attached to this post.
			$fields = get_fields( $id );
		}

		if ( $fields ) {
			extract( $fields );
		}

		ob_start();
		try {
			include $template;
			$buffer = ob_get_contents();
			$t      = substr( $buffer, strpos( $buffer, '{items}' ) + 7 );
			$html  .= substr( $t, 0, strrpos( $t, '{/items}' ) );
		} finally {
			ob_end_clean();
		}

	endforeach;

	// Append everything after '{/items}' in the template
	ob_start();
	try {
		include $template;
		$html .= end( explode( '{/items}', ob_get_contents() ) );
	} finally {
		ob_end_clean();
	}
	return $html;
}

/**
 * The [tel] shortcode. Insert the contact phone number as a link. Optionally with an icon.
 *
 * @param array  $atts      Shortcode attributes. Default empty.
 * @param string $content   Content to use instead of the formatted telephone number.
 *
 * @return string
 */
function tel_shortcode( $atts = array(), $content = '' ) {
	// Use index -1 to return all tel numbers wrapped in <li> tags
	$defaults = array(
		'icon'  => '',
		'index' => 0,
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a    = shortcode_atts( $defaults, $atts );
	$html = the_contact_phone( $a['icon'], $a['index'], array(), false );

	if ( ! empty( $content ) ) {
		$html = preg_replace( '#(<a [^>]*>).*(</a>)#', '${1}' . $content . '${2}', $html );
	}
	return $html;
}


/**
 * The [fax] shortcode. Insert the contact phone number as a link. Optionally with an icon.
 *
 * @param array  $atts      Shortcode attributes. Default empty.
 * @param string $content   Content to use instead of the formatted fax number.
 *
 * @return string
 */
function fax_shortcode( $atts = array(), $content = '' ) {
	// Use index -1 to return all fax numbers wrapped in <li> tags
	$defaults = array(
		'icon'  => '',
		'index' => 0,
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a    = shortcode_atts( $defaults, $atts );
	$html = the_contact_fax( $a['icon'], $a['index'], array(), false );

	if ( ! empty( $content ) ) {
		$html = preg_replace( '#(<a [^>]*>).*(</a>)#', '${1}' . $content . '${2}', $html );
	}
	return $html;
}


/**
 * The [email] shortcode. Insert the contact email as a link. Optionally with an icon.
 *
 * @param array  $atts      Shortcode attributes. Default empty.
 * @param string $content   Content to use instead of the email address.
 *
 * @return string
 */
function email_shortcode( $atts = array(), $content = '' ) {

	// Use index -1 to return all emails wrapped in <li> tags
	$defaults = array(
		'icon'  => '',
		'index' => 0,
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a    = shortcode_atts( $defaults, $atts );
	$html = the_contact_email( $a['icon'], $a['index'], array(), false );

	if ( ! empty( $content ) ) {
		$html = preg_replace( '#(<a [^>]*>).*(</a>)#', '${1}' . $content . '${2}', $html );
	}
	return $html;
}


/**
 * The [physical_address] shortcode. Insert the contact address.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function physical_address_shortcode( $atts = array(), $content = '' ) {

	// Use index -1 to return all addresses wrapped in <li> tags
	$defaults = array(
		'index' => -1,
		'class' => '',
		'style' => '',
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a     = shortcode_atts( $defaults, $atts );
	$attrs = array_diff( $a, array( 'index' ) );
	$html  = the_contact_address( '', $a['index'], $attrs, false, $content );

	return $html;
}



/**
 * The [site_name] shortcode. Insert the site name.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function site_name_shortcode( $atts = array() ) {
	$defaults = array(
		'class' => '',
		'style' => '',
		'tag'   => 'span',
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a = shortcode_atts( $defaults, $atts );

	$tag = $a['tag'];
	unset( $a['tag'] );

	$content = get_bloginfo( 'name' );

	return buildAttributes( $a, $tag, $content );
}


/**
 * The [url] shortcode. Insert the site name.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function url_shortcode( $atts = array() ) {
	$defaults = array(
		'class' => '',
		'style' => '',
		'tag'   => 'span',
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a = shortcode_atts( $defaults, $atts );

	$tag = $a['tag'];
	unset( $a['tag'] );

	$content = get_site_url();

	return buildAttributes( $a, $tag, $content );
}


/**
 * The [icon] shortcode. Insert an icon.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function icon_shortcode( $atts = array() ) {
	$defaults = array(
		'icon'  => '',
		'class' => '',
		'size'  => '',
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a = shortcode_atts( $defaults, $atts );

	$content = get_icon( $a['icon'], $a['class'], $a['size'] );

	return $content;
}

/**
 * The [x] shortcode. Insert a checkmark.
 *
 * @param array $atts      Shortcode attributes. Default empty.
 *
 * @return string
 */
function x_shortcode( $atts = array() ) {
	$defaults = array(
		'icon'  => 'checkmark-circle',
		'class' => 'uk-text-secondary',
		'size'  => 'large',
	);
	// Overwrite defaults with user-provided attriubtes ($atts). Also fires a hook.
	$a = shortcode_atts( $defaults, $atts );

	$content = get_icon( $a['icon'], $a['class'], $a['size'] );

	return $content;
}


function privacy_policy_shortcode() {
	return get_the_privacy_policy_link();
}

function returns_policy_shortcode() {
	if ( class_exists( 'woocommerce' ) ) {
		$shipping_returns_id = get_option( 'woocommerce_shipping_returns_page_id' );
		if ( $shipping_returns_id ) {
			return buildAttributes( array( 'href' => get_permalink( $shipping_returns_id ) ), 'a', get_the_title( $shipping_returns_id ) );
		}
	}
}
