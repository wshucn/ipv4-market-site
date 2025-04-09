<?php
/*

  Section: WordPress plugins configuration/customizations
  Purpose: Scripts, filters, actions to change how theme plugins work and look.

  Author: Media Proper
  Last updated: 14 June 2021

*/

add_filter('searchwp_live_search_configs', 'mp_searchwp_live_search_configs');
function mp_searchwp_live_search_configs($configs)
{
    // override some defaults
    $configs['navbar'] = array(
        'engine' => 'default',                      // search engine to use (if SearchWP is available)
        'input' => array(
            'delay'     => 500,                 // wait n ms before triggering a search
            'min_chars' => 4,                   // wait for at least n characters before triggering a search
        ),
        // 'parent_el' => '.navbar-search-results',   // selector of the parent element for the results container
        'results' => array(
            'position'  => 'bottom',            // where to position the results (bottom|top)
            'width'     => 'css',              // whether the width should automatically match the input (auto|css)
            'offset'    => array(
                'x' => 0,                   // x offset (in pixels)
                'y' => 0                    // y offset (in pixels)
            ),
        ),
        'spinner' => array( // Powered by http://spin.js.org/
            'lines'     => 9,                                 // The number of lines to draw
            'length'    => 6,                                 // The length of each line
            'width'     => 3,                                 // The line thickness
            'radius'    => 6,                                 // The radius of the inner circle
            'scale'     => 1,                                  // Scales overall size of the spinner
            'corners'   => 1,                                  // Corner roundness (0..1)
            'color'     => '#ffffff',                          // CSS color or array of colors
            'fadeColor' => 'transparent',                      // CSS color or array of colors
            'speed'     => 1,                                  // Rounds per second
            'rotate'    => 0,                                  // The rotation offset
            'animation' => 'searchwp-spinner-line-fade-quick', // The CSS animation name for the lines
            'direction' => 1,                                  // 1: clockwise, -1: counterclockwise
            'zIndex'    => 2e9,                                // The z-index (defaults to 2000000000)
            'className' => 'spinner',                          // The CSS class to assign to the spinner
            'top'       => '50%',                              // Top position relative to parent
            'left'      => '50%',                              // Left position relative to parent
            'shadow'    => '0 0 1px transparent',              // Box-shadow for the lines
            'position'  => 'absolute'                          // Element positioning
        ),
    );

    return $configs;
}

add_filter('searchwp_live_search_get_search_form_config', 'mp_searchwp_live_search_get_search_form_config');
function mp_searchwp_live_search_get_search_form_config()
{
    return 'navbar';
}


/**
 * AJAX Load More Filters
 *
 * Disable category, tag, and author archives and instead redirect to the Posts page,
 * with the page query changed to auto-select the approriate AJAX Load More filters
 * from the filtering element.
 */
if (in_array('ajax-load-more-filters/ajax-load-more-filters.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    // Change all category links to point to the AJAX Load More filtered list instead of category archive pages.
    function filter_category_link($termlink, $term_term_id)
    {
        $page_for_posts_url = get_permalink(get_option('page_for_posts'));
        $terminfo = get_category($term_term_id);
        $termlink = $page_for_posts_url . '?category=' . $terminfo->slug;
        return $termlink;
    };
    add_filter('category_link', 'filter_category_link', 10, 2);

    // Change all tag links to point to the AJAX Load More filtered list instead of tag archive pages.
    function filter_tag_link($termlink, $term_term_id)
    {
        $page_for_posts_url = get_permalink(get_option('page_for_posts'));
        $terminfo = get_tag($term_term_id);
        $termlink = $page_for_posts_url . '?_tag=' . $terminfo->slug;
        return $termlink;
    };
    add_filter('tag_link', 'filter_tag_link', 10, 2);
    
    // Change all author links to point to the AJAX Load More filtered list instead of author archive pages.
    function filter_author_link($link, $author_id, $author_nicename)
    {
        $page_for_posts_url = get_permalink(get_option('page_for_posts'));
        $link = $page_for_posts_url . '?_author=' . $author_id;
        return $link;
    };
    add_filter('author_link', 'filter_author_link', 10, 3);
}


/* Yoast Breadcrumbs */
add_filter('wpseo_breadcrumb_output_wrapper', function ($wrapper) {
    return 'ul';
});
add_filter('wpseo_breadcrumb_separator', '__return_null');

add_filter('wpseo_breadcrumb_single_link', 'mp_breadcrumb_single_link', 10, 2);
function mp_breadcrumb_single_link($html, $data)
{
    if (!str_contains($html, '<a ')) {
        $html = preg_replace([ '#<span([^>]*)>#', '#</span>#' ], [ '<span><span$1>', '</span></span>' ], $html);
    }
    return mp_html_attrs($html, 'span', [], true, 'li');
}

add_filter('wpseo_breadcrumb_output', 'mp_breadcrumb_output', 10, 2);
function mp_breadcrumb_output($html, $post)
{
    return mp_html_class($html, 'ul', 'uk-breadcrumb');
}



/**
 * Filter to change breadcrumb args.
 *
 * @param  array $args Breadcrumb args.
 * @return array $args.
 */
add_filter('rank_math/frontend/breadcrumb/args', function ($args) {
    $args = array(
        'delimiter'   => '&nbsp;/&nbsp;',
        'wrap_before' => '<nav class="rank-math-breadcrumb"><ul class="uk-breadcrumb">',
        'wrap_after'  => '</ul></nav>',
        'before'      => '<li>',
        'after'       => '</li>',
    );
    return $args;
});

/**
 * Filter to change breadcrumb settings.
 *
 * @param  array $settings Breadcrumb Settings.
 * @return array $setting.
 */
add_filter('rank_math/frontend/breadcrumb/settings', function ($settings) {
    $settings['separator'] = '';
    return $settings;
});

/**
 * Filter to change breadcrumb html.
 *
 * @param  string  $html Breadcrumb html.
 * @param  array $crumbs Breadcrumb items
 * @param  class $class Breadcrumb class
 * @return string  $html.
 */
add_filter('rank_math/frontend/breadcrumb/html', function ($html, $crumbs, $class) {
    $html = mp_html_remove_by_class($html, 'separator');
    return $html;
}, 10, 3);
