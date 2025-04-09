<!--- Mobile Navigation --->
<nav aria-label='Mobile Navigation' id='offcanvas-overlay' role='navigation' uk-offcanvas='mode: slide; flip: true; overlay: true'>
    <div class='uk-offcanvas-bar'>
        <button class='uk-offcanvas-close' type='button' uk-close></button>

        <?php
        // Static items
        if ( has_nav_menu( 'mobile' ) )
            wp_nav_menu(array(
                'menu'                => __('Mobile Menu', 'wpbase'),
                'menu_class'		  => 'uk-nav-default uk-nav-parent-icon',
                'items_wrap'          => '<ul id="%1$s-offcanvas" class="%2$s" uk-nav>%3$s</ul>',
                'container'           => false,
                'theme_location'      => 'mobile',
                'walker'              => new Walker_UIkit(),
            ));
        ?>
    </div>
</nav><!-- /offcanvas navigation -->
