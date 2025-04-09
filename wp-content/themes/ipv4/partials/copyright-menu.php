<?php if ( has_nav_menu( 'bottom' ) ): ?>
<ul id='bottom-menu' class='<?= buildClass('uk-grid', $args) ?>' uk-grid>
    <?php
    wp_nav_menu(array(
        'menu'                  => __('Bottom Menu', 'text_domain'),
        'container'             => false,
        'theme_location'        => 'bottom',   // must be registered with register_nav_menu()
        'walker'                => new Walker_UIkit(),
        'items_wrap'            => '%3$s',
    ));

    $bottom_menu_items = [];

    // Privacy Policy
    $bottom_menu_items[] = get_the_privacy_policy_link();

    // Add a Sitemap menu item
    if ( class_exists('WPSEO_Options') && WPSEO_Options::get( 'enable_xml_sitemap' ) ) {
        $bottom_menu_items[] = '<a href="' . esc_url( WPSEO_Sitemaps_Router::get_base_url( 'sitemap_index.xml' ) )
            . '" target="_blank">' . esc_html__( 'Sitemap', 'wordpress-seo' ) . '</a>';
        }
    echo join('', list_items($bottom_menu_items));

    ?>
</ul>
<?php endif;
