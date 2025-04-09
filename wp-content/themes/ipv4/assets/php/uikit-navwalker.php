<?php
/**
 * Nav Menu API: Walker_UIkit class
 *
 * @package Media Proper
 * @since 1.0.0
 */

/**
 * Core class used to implement an HTML list of nav menu items.
 *
 * @since 3.0.0
 *
 * @see Walker_Nav_Menu
 */
class Walker_UIkit extends Walker_Nav_Menu
{
    /**
     * What the class handles.
     *
     * @since 3.0.0
     * @var string
     *
     * @see Walker::$tree_type
     */
    public $tree_type = array( 'post_type', 'taxonomy', 'custom' );


    /*
     * function start_lvl(&$output, $depth=0, $args=null) { }
     * function end_lvl(&$output, $depth=0, $args=null) { }
     * function start_el(&$output, $item, $depth=0, $args=null, $id=0) { }
     * function end_el(&$output, $item, $depth=0, $args=null) { }
     * function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) { }
     */

    /*
     * CUSTOM ARGUMENTS to pass to this menu
     * show_carets = bool       whether to show carets for items with dropdowns
     * drop_levels = int        number of dropdown levels, after which we do a mega-menu
     */




    /**
     * Database fields to use.
     *
     * @since 3.0.0
     * @todo Decouple this.
     * @var array
     *
     * @see Walker::$db_fields
     */
    public $db_fields = array(
        'parent' => 'menu_item_parent',
        'id'     => 'db_id',
    );


    // public function submenu_columns( $classes, $args, $depth ){}
    
    public function has_dropdown($args)
    {
        global $theme_location_has_dropdown;
        if (is_array($theme_location_has_dropdown) && !empty($theme_location_has_dropdown)) {
            return in_array($args->theme_location, $theme_location_has_dropdown);
        }
        return false;
    }

    /**
     * Starts the list before the elements are added.
     *
     * @since 3.0.0
     *
     * @see Walker::start_lvl()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);
        $attrs = array();

        $has_dropdown = $this->has_dropdown($args);

        // Main Menu dropdown element attributes: see https://getuikit.com/docs/dropdown
        // Here change the animation, position, etc. %s will be the menu item id
        $dropdown_attrs = [
            'class'         => [ 'uk-navbar-dropdown' ],
            'uk-drop'       => 'boundary: !.menu-item; boundary-align: true',
        ];
        // Was the dropdown_class set when the parent element was generated (start_el)?
        if (isset($args->dropdown_class)) {
            $dropdown_attrs['class'][] = $args->dropdown_class;
            unset($args->dropdown_class);
        }


        // Use dropdowns for theme locations specified in global $theme_location_has_dropdown
        if ($has_dropdown) {
            $attrs['class'][] = 'uk-nav uk-navbar-dropdown-nav';

            // Side dropdown for sub-submenus
            if ($depth > 0) {
                $dropdown_attrs['uk-drop'] .= '; pos: right-top';
            }

            $dropdown_element = buildAttributes($dropdown_attrs, 'div');
            $output .= "{$n}{$indent}{$dropdown_element}";
        } else {
            $attrs['class'][] = 'uk-nav-sub';
        }


        /**
         * Filters the CSS class(es) applied to a menu list element.
         *
         * @since 4.8.0
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
         * @param stdClass $args    An object of `wp_nav_menu()` arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        
        // Accordion-style menus for mobile
        // if ( $args->theme_location === 'mobile' && $depth == 0 ) {
        //     $attrs['uk-nav'] = '';
        //     $attrs['class'][] = 'uk-nav-parent-icon';
        // }

        // Has this level's parent element (see start_el()) got CSS classes set for its submenus? (uk-column-1-*)
        if (!empty($args->submenu_class)) {
            $attrs['class'][] = $args->submenu_class;
            unset($args->submenu_class);
        }

        $attrs['class'] = buildClass(apply_filters('nav_menu_submenu_css_class', to_array($attrs['class']), $args, $depth));

        $output .= "{$n}{$indent}" . buildAttributes($attrs, 'ul') . "{$n}";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @since 3.0.0
     *
     * @see Walker::end_lvl()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent  = str_repeat($t, $depth);
        $output .= "{$indent}</ul>{$n}";

        // Close the dropdown
        if ($this->has_dropdown($args)) {
            $output .= '</div>';
        }
    }

    /**
     * Starts the element output.
     *
     * @since 3.0.0
     * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
     *
     * @see Walker::start_el()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';
        $item_attrs = [];       // this is for the <li> tag
        $atts = [];             // this is for the <a> tag
        $atts_inner = [];       // this is for any <span> tag within the <a>, such as for buttons


        // update: does this item have children?
        $has_children = $args->walker->has_children;

        $has_dropdown = $this->has_dropdown($args);

        $menu_class = isset($args->menu_class) ? explode(' ', $args->menu_class) : array();
        $classes   = isset($item->classes) ? (array) $item->classes : array();
        $classes[] = 'menu-item-' . $item->ID;

        // We can add the 'uk-column-1-*' class to a parent menu item to render the dropdown submenu in that many columns.
        // Regardless of whether it's used, we need to remove it from the parent element or it'll mess things up.
        $match_column = preg_grep('/(uk-column-[^ (span)]*)/', to_array(buildClass($classes)));
        if ($match_column) {
            // Remove column class from parent element.
            $classes = preg_grep('/(uk-column-[^ (span)]*)/', to_array(buildClass($classes)), PREG_GREP_INVERT);

            if ($has_dropdown) {
                // Set $args->submenu_class so that it can be read when the sub-menu is created in start_lvl()
                $args->submenu_class = $match_column;
                // Get the number of columns. Adds `uk-navbar-dropdown-width-*` class to the dropdown to bump its width.
                if (preg_match_all('/uk-column-1-([2-6])/', buildClass($match_column), $num_column)) {
                    if (!empty($num_column[1])) {
                        $dropdown_width = end($num_column[1]);
                        if ($has_dropdown) {
                            $args->dropdown_class = sprintf('uk-navbar-dropdown-width-%s', $dropdown_width);
                        }
                    }
                }
            }
        }

        // Style a menu item as a button by giving it the uk-button-{primary, secondary, default} class.
        // (No need to add 'uk-button' separately.)
        $match_button = preg_grep('/(uk-button[^ ]*)/', $classes);
        if ($match_button) {
            // Remove button class from outer element.
            $classes = preg_grep('/(uk-button[^ ]*)/', $classes, PREG_GREP_INVERT);
            $atts_inner['class'][] = [ 'uk-button', $match_button ];
        }

        // Don't make headings and dividers into links
        $nonlinks = [
            'header',
            'divider',
        ];
        $match_nonlink = preg_grep('/(' . join('|', $nonlinks) . ')/', $classes);
        if ($match_nonlink) {
            unset(
                $item->target,
                $item->rel,
                $item->url,
                $item->current
            );

            foreach ($match_nonlink as $nonlink_class) {
                switch ($nonlink_class) {
                    // Don't output anything for dividers, just change class.
                    case 'divider':
                        unset($item->title, $tag);
                        break;
                    // Add uk-nav-header class to <span> element, rather than <li>, so that
                    // menu children do not take on its styling.
                    case 'header':
                        $atts['class'][] = 'uk-nav-header uk-h3';
                        break;
                }
            }
        }



        // Replace WordPressy classes with UIkit ones
        // See https://developer.wordpress.org/reference/functions/wp_nav_menu/#menu-item-css-classes
        //     https://getuikit.com/docs/nav
        $replacement = [
            'menu-item-has-children'    => 'uk-parent',
            'current-menu-item'         => 'uk-active',
        ];
        // if(in_array('uk-navbar-nav', $menu_class)) $replacement['menu-item'] = 'uk-navbar-item';
        $classes = array_map(function ($v) use ($replacement) {
            return isset($replacement[$v]) ? $replacement[$v] : $v;
        }, $classes);

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 4.4.0
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        /**
         * Filters the CSS classes applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */

        // These classes, set in the WordPress menu item settings, will be converted to attributes
        // if the menu item url begins with '#'.
        $uk_attrs = [
            'uk-scroll',
            'uk-toggle',
        ];
        // Add the uk-scroll/uk-toggle attribute to items with anchor links and CSS classes
        $match_uk_attrs = preg_grep('/(' . join('|', $uk_attrs) . ')/', $classes);
        // Remove the UK attribute classes from the list of classes.
        $classes = preg_grep('/(' . join('|', $uk_attrs) . ')/', $classes, PREG_GREP_INVERT);
        if (!empty($match_uk_attrs) && strpos($item->url, '#') === 0) {
            $atts = array_merge($atts, $match_uk_attrs);
        }


        // Generate <li> tag class
        $item_attrs['class'][] = apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth);

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $item_attrs['id'] = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);

        $output .= $indent . buildAttributes($item_attrs, 'li');

        if (!empty($item->attr_title)) {
            $atts['title'] = $item->attr_title;
        }
        if (!empty($item->target)) {
            $atts['target'] = $item->target;
        }
        if ('_blank' === $item->target && empty($item->xfn)) {
            $atts['rel'] = 'noopener';
        } elseif (!empty($item->xfn)) {
            $atts['rel'] = $item->xfn;
        }
        if (!empty($item->url)) {
            $atts['href'] = $item->url;
        }
        if ($item->current) {
            $atts['aria-current'] = 'page';
        }


        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title        Title attribute.
         *     @type string $target       Target attribute.
         *     @type string $rel          The rel attribute.
         *     @type string $href         The href attribute.
         *     @type string $aria_current The aria-current attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters('the_title', $item->title, $item->ID);

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        // Hide links when $args['hide_parents'] is true. Do not hide the <li> -- it will hide any submenus.
        if (isset($args->hide_parents) && $args->hide_parents === true && in_array('uk-parent', $classes)) {
            $atts[] = 'hidden';
        }

        $tag = empty($item->url) ? 'span' : 'a';

        // wrap the $title in <span> tags, if there's an inner element here (like for a button)
        if (!empty($atts_inner)) {
            $title = buildAttributes($atts_inner, 'span', $title);
        }

        $item_output_link   = isset($args->link_before) ? $args->link_before : '';
        $item_output_link  .= $title;
        $item_output_link  .= isset($args->link_after) ? $args->link_after : '';

        $item_output        = isset($args->before) ? $args->before : '';
        $item_output       .= buildAttributes($atts, $tag, $item_output_link);
        $item_output       .= isset($args->after) ? $args->after : '';


        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output, if needed.
     *
     * @since 3.0.0
     *
     * @see Walker::end_el()
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Page data object. Not used.
     * @param int      $depth  Depth of page. Not Used.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $output .= "</li>{$n}";
    }
}
