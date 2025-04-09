<?php
// Height of header, a UIkit uk-height- class
$header_height          = 'uk-height-medium';
$h1_with_image_class    = 'uk-heading-medium';
$h1_without_image_class = 'uk-heading-small';

$header_default_background_color = 'uk-background-primary';

// the wrapper is flex-column, so position subtitle above or below with uk-flex-first/uk-flex-last
$subtitle_attrs = array(
	'class' => array( 'uk-text-bold', 'uk-flex-first' ),
);

$use_parallax      = get_field( 'parallax_header' );
$uk_parallax_outer = 'y: 80vh';
$uk_parallax_inner = 'target: !*; y: -40vh';


if ( ! empty( $args['id'] ) ) {
	$id = $args['id'];
} elseif ( is_home() ) {
	// is_home is actually the posts page, not 'home'
	$id = get_option( 'page_for_posts' );
} elseif ( class_exists( 'woocommerce' ) && is_product_category() ) {
	$category = get_queried_object();
	$id       = $category->term_id;
	$fields   = get_fields( $category );
	// Does this category have a content page assigned to it? If so, use the header fields from that page.
	// This makes it more straightforward to deal with the content for product categories.
	if ( ! empty( $fields['the_content'] ) ) {
		$fields = get_fields( $fields['the_content'] );
	}
	$header_title = single_term_title( '', false );
} elseif ( class_exists( 'woocommerce' ) && is_shop() ) {
	$id           = wc_get_page_id( 'shop' );
	$header_title = woocommerce_page_title( false );
	if ( empty( $header_title ) ) {
		$header_title = __( 'Shop', 'woocommerce' );
	}
} elseif ( class_exists( 'woocommerce' ) && is_woocommerce() ) {
	$id = get_queried_object_id();
	// $header_title = woocommerce_page_title( false );
} elseif ( is_404() ) {
	$header_title    = __( '404', 'text_domain' );
	$header_subtitle = __( 'That\'s an error!', 'text_domain' );
} else {
	$id = get_queried_object_id();
	// $id = get_the_id();
}

// Process Page custom fields
if ( empty( $fields ) ) {
	$fields = get_fields( $id );
}

// Header Title, either the custom field (if set) or the actual page title
if ( ! empty( $fields['page_title'] ) ) {
	$header_title = apply_filters( 'the_title', $fields['page_title'] );
} elseif ( empty( $header_title ) ) {
	$header_title = get_the_title( $id );
}

if ( empty( $header_subtitle ) ) {
	$header_subtitle = ! empty( $fields['page_subtitle'] ) ? $fields['page_subtitle'] : '';
}

// Header Image
if ( ! empty( $fields['page_image'] ) ) {
	// $header_attrs['data-src'] = wp_get_attachment_image_url($fields['page_image'], '2048x2048');
	$img_attrs = array(
		'role'     => 'presentation',
		'uk-cover' => null,
		'sizes'    => '100vw',
		'loading'  => 'eager',
	);
	$img       = wp_get_attachment_image( $fields['page_image'], 'full', false, $img_attrs );

	// Apply imgix filters to the header image.
	// Custom fields should be named like: imgix_[param_name], e.g. imgix_bri or imgix_blend_color
	// * Use underscores instead of hyphens in field names.
	$imgix_filters = preg_grep( '/^imgix_/', array_keys( $fields ) );
	foreach ( $imgix_filters as $imgix_filter ) {
		if ( ! empty( $fields[ $imgix_filter ] ) ) {
			$imgix_param                 = str_replace( '_', '-', substr( $imgix_filter, 6 ) );
			$imgix_attrs[ $imgix_param ] = $fields[ $imgix_filter ];
		}
	}

	// Add the imgix parameters to src and srcset query strings
	if ( ! empty( $imgix_attrs ) ) {
		$img = mp_imgix_attrs( $img, $imgix_attrs );
	}
}

// Create the page header/hero block
// $h1_class[] = 'uk-margin-large-right uk-margin-large-left';
$h1_class[] = 'uk-text-uppercase';
if ( empty( $header_subtitle ) ) {
	$h1_class[] = 'uk-margin-remove-bottom';
}

$header_attrs            = array();
$header_attrs['class'][] = join( '-', array_filter( array( 'header', get_post_type( $id ) ) ) );

$header_content_attrs            = array();
$header_content_attrs['class'][] = 'uk-flex uk-flex-column uk-flex-center uk-container';

if ( ! empty( $img ) ) {
	// $header_attrs['uk-height-viewport'] = 'offset-top: true; offset-bottom: 25; min-height: 300';
	// Expand to 100vw (container must be centered)
	$header_attrs['style'][] = 'width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw;';
	// $header_attrs['class'][] = 'uk-container-break';

	// Image and content positioning
	$header_attrs['class'][]         = 'uk-cover-container';
	$header_content_attrs['class'][] = 'uk-position-cover';

	// Can override whether to use uk-light or uk-dark. Default is uk-light (white text for contrasting dark image)
	$header_content_attrs['class'][] = empty( $fields['page_title_contrast'] ) ? 'uk-light' : $fields['page_title_contrast'];

	// Content animation
	$header_content_attrs['class'][] = 'uk-animation-slide-bottom-medium uk-animation-slow';

	// Heading & header size
	if ( ! empty( $header_height ) ) {
		$header_attrs['class'][] = $header_height;
	}
	$h1_class[] = $h1_with_image_class;
} else {
	$h1_class[] = $h1_without_image_class;
}

// Optionally hide the title content, allowing one to remove the header entirely or just show an image.
if ( $fields && ( empty( $fields['page_title_visible'] ) || $fields['page_title_visible'] !== true ) && empty( $fields['page_image'] ) ) {
	$header_attrs['class'][] = 'sr-only';
} elseif ( $fields && ( empty( $fields['page_title_visible'] ) || $fields['page_title_visible'] !== true ) ) {
	$header_content_attrs['class'][] = 'sr-only';
} elseif ( empty( $img ) ) {
	$header_attrs['class'][] = 'uk-padding uk-margin-medium-bottom';
	$header_attrs['class'][] = $header_default_background_color;
}

$header_attrs['class'][] = 'overlay-pattern-grid uk-light';

if ( ! empty( $header_title ) ) :
	// <div>
	echo buildAttributes( $header_attrs, 'div' );
	?>
	<?php if ( ! empty( $img ) ) : ?>
		<?php
		if ( ! empty( $use_parallax ) ) {
			echo buildAttributes(
				array( 'uk-parallax' => $uk_parallax_outer ),
				'div',
				buildAttributes(
					array(
						'uk-parallax' => $uk_parallax_inner,
						'class'       => $header_height,
					),
					'div',
					$img
				)
			);
		} else {
			echo $img;
		}
		?>
<?php endif; ?>
	<?php
	// <div>
	echo buildAttributes( $header_content_attrs, 'div' );
	?>
<h1 class='<?php echo buildClass( $h1_class ); ?>'><?php echo $header_title; ?>
</h1>
	<?php if ( ! empty( $header_subtitle ) ) : ?>
		<?php echo buildAttributes( $subtitle_attrs, 'div', esc_html__( $header_subtitle ) ); ?>
	<?php endif; ?>
</div>
</div>
	<?php
endif;
