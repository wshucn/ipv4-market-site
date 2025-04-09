<?php if ( has_nav_menu( 'footer' ) ): ?>
<ul class='<?= buildClass('uk-list uk-padding-remove uk-margin-remove', $args) ?>'>
    <?php
    wp_nav_menu(array(
        'menu'                  => __('Footer Menu', 'text_domain'),
        'container'             => false,
        'theme_location'        => 'footer',   // must be registered with register_nav_menu()
        'walker'                => new Walker_UIkit(),
        'items_wrap'            => '%3$s',
    ));
    ?>
</ul>
<?php endif;