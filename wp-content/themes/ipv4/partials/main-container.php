<!-- Container -->
<?php
    // Get the correct page/taxonomy ID
    // Override this by passing the ID ([ 'id' => xx ]) as the second argument in get_template_part
    if( !empty($args['id']) ) {
        // ID passed as an argument
        $id = $args['id'];
        unset($args['id']);
    } elseif( is_tax( 'product_cat' ) ){
        // archive-product.php
        $category = get_queried_object();
        $id = $category->term_id;
        $fields = get_fields($category);
        $header_title = single_term_title('', false);
    } elseif( is_home() ) {
        // index.php
        $id = get_option( 'page_for_posts' );
    } else {
        // normal pages and posts
        $id = get_the_id();
    }

    // Any other arguments passed to the template via get_template_part are considered wrapper attributes.
    $wrap_attrs = !is_array($args) ? [] : $args;
    if(!empty($wrap_attrs['class'])) $wrap_attrs['class'] = to_array($wrap_attrs['class']);

    // Allow the wrapped elements to be positioned with CSS by making it a vertical flexbox
    // $wrap_attrs['class'][] = 'uk-flex uk-flex-column uk-flex-wrap';
    $wrap_attrs['style'][] = 'display: grid; grid-template-columns: 100%';

    // max_width custom field may wrap the whole page in a uk-container.
    $max_width = empty(get_field('max_width', $id)) ? 'medium' : get_field('max_width', $id);
    switch($max_width) {
        case 'none': break;
        default: $wrap_attrs['class'][] = sprintf('uk-container uk-container-%s', $max_width);
    }
    echo buildAttributes($wrap_attrs, 'div');
