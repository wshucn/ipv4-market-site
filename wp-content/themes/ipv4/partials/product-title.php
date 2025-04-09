<?php
$id = get_the_id();

// Process Page custom fields
if( empty($fields) ) $fields = get_fields($id);

// Header Title, either the custom field (if set) or the actual page title
if(!empty($fields['page_title'])) {
    $header_title = apply_filters('the_title', $fields['page_title']);
} elseif(empty($header_title)) {
    $header_title = apply_filters('the_title', get_the_title($id));
}

if(empty($header_subtitle))
    $header_subtitle = !empty($fields['page_subtitle']) ? $fields['page_subtitle'] : '';


// Header Image
if( !empty($fields['page_image']) ) {
    // $header_attrs['data-src'] = wp_get_attachment_image_url($fields['page_image'], '2048x2048');
    $img_attrs = [
        'role'      => 'presentation',
        'uk-cover'  => NULL,
        'sizes'     => '100vw',
        'loading'   => 'eager',
    ];
    $img = wp_get_attachment_image($fields['page_image'], 'full', false, $img_attrs);

    // Apply imgix filters to the header image.
    // Custom fields should be named like: imgix_[param_name], e.g. imgix_bri or imgix_blend_color
    // * Use underscores instead of hyphens in field names.
    $imgix_filters = preg_grep('/^imgix_/', array_keys($fields));
    foreach($imgix_filters as $imgix_filter) {
        if(!empty($fields[$imgix_filter])) {
            $imgix_param = str_replace('_', '-', substr($imgix_filter, 6));
            $imgix_attrs[$imgix_param] = $fields[$imgix_filter];
        }
    }

    // Add the imgix parameters to src and srcset query strings
    if(!empty($imgix_attrs)) $img = mp_imgix_attrs($img, $imgix_attrs);
}


// Create the page header/hero block
$h1_class[] = 'uk-margin-large-right uk-margin-large-left uk-margin-large-top';
$h1_class[] = 'uk-heading-small';

$header_attrs = [];
$header_attrs['class'][] = join('-', array_filter([ 'header', get_post_type($id) ]));
$header_attrs['class'][] = 'uk-margin-medium-bottom';

$header_content_attrs = [];
$header_content_attrs['class'][] = 'uk-flex uk-flex-column uk-flex-center uk-flex-middle uk-container';

if( empty($header_subtitle) ) {
    $h1_class[] = 'uk-margin-large-bottom';
} else {
    $header_content_attrs['class'][] = 'uk-margin-large-bottom';
}

if(!empty($img)){
    // $header_attrs['uk-height-viewport'] = 'offset-top: true; offset-bottom: 25; min-height: 300';
    // Expand to full width (container must be centered)
    $header_attrs['style'][] = 'width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw;';

    // Image positioning
    // $header_attrs['class'][] = 'uk-cover-container';

    // Heading & header size
    $header_attrs['class'][] = 'uk-height-large uk-overflow-hidden';

    // Output the header image
    echo buildAttributes($header_attrs, 'div');
    echo '<div class="uk-animation-fade uk-cover-container uk-height-1-1">';
    echo $img;
    echo '</div></div>';

}
?>
<?php if( !empty($header_title) ): ?>
<?= buildAttributes($header_content_attrs, 'div'); ?>
    <h1 class='<?= buildClass($h1_class) ?>'><?= $header_title ?></h1>
    <?php if( !empty($header_subtitle) ): ?>
    <div class='subtitle uk-text-uppercase'><?php esc_html_e($header_subtitle) ?></div>
    <?php endif; ?>
</div>
<?php endif;
