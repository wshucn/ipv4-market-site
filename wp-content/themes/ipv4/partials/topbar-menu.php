<?php if ( has_nav_menu( 'top' ) ): ?>
    <?php
    wp_nav_menu(array(
        'menu'                  => __('Top Menu', 'text_domain'),
        'container'             => false,
        'theme_location'        => 'top',   // must be registered with register_nav_menu()
        'walker'                => new Walker_UIkit(),
        'items_wrap'            => '%3$s',
    ));
    ?>
<?php endif;