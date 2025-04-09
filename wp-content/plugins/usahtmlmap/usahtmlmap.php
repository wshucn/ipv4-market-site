<?php
/*
Plugin Name: Interactive Map of the USA for WP
Plugin URI: https://www.fla-shop.com
Update URI: https://www.fla-shop.com
Description: High-quality map plugin of the USA for WordPress. The map depicts states and features color, font, landing page and popup customization
Text Domain: usa-html5-map
Domain Path: /languages
Version: 3.3.0
Author: Fla-shop.com
Author URI: https://www.fla-shop.com
License:
*/

/**
 * Temporary workaround for tinymce bug, when it's not focusable in modal windows.
 * When comressed_scriptes  is disabled - compat3x plugin for tinymcy will be added,
 * this will prevent bug from occurring.
 */
if (is_admin() and ! defined('COMPRESS_SCRIPTS')) define('COMPRESS_SCRIPTS', false);

require_once('popupbuilder.php');
add_action('plugins_loaded', 'usa_html5map_plugin_load_domain' );
function usa_html5map_plugin_load_domain() {
    load_plugin_textdomain( 'usa-html5-map', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
if (isset($_REQUEST['action']) && $_REQUEST['action']=='usa-html5-map-export') { usa_html5map_plugin_export(); }
if (isset($_REQUEST['action']) && $_REQUEST['action']=='usa-html5-map-export-csv') { usa_html5map_plugin_export_csv(); }

add_action('admin_menu', 'usa_html5map_plugin_menu');


function usa_html5map_plugin_menu() {

    global $wp_roles;

    $role = "usahtml5map_manage_role";

    add_menu_page(__('USA Map', 'usa-html5-map'), __('USA Map', 'usa-html5-map'), $role, 'usa-html5-map-options', 'usa_html5map_plugin_options' );

    add_submenu_page('usa-html5-map-options', __('General Settings', 'usa-html5-map'), __('General Settings', 'usa-html5-map'), $role, 'usa-html5-map-options', 'usa_html5map_plugin_options' );
    add_submenu_page('usa-html5-map-options', __('Detailed settings', 'usa-html5-map'), __('Detailed settings', 'usa-html5-map'), $role, 'usa-html5-map-states', 'usa_html5map_plugin_states');
    add_submenu_page('usa-html5-map-options', __('Groups settings', 'usa-html5-map'), __('Groups settings', 'usa-html5-map'), $role, 'usa-html5-map-groups', 'usa_html5map_plugin_groups');
    add_submenu_page('usa-html5-map-options', __('Points settings', 'usa-html5-map'), __('Points settings', 'usa-html5-map'), $role, 'usa-html5-map-points', 'usa_html5map_plugin_points');
    add_submenu_page('usa-html5-map-options', __('Tools', 'usa-html5-map'), __('Tools', 'usa-html5-map'), $role, 'usa-html5-map-tools', 'usa_html5map_plugin_tools');
    add_submenu_page('usa-html5-map-options', __('Map Preview', 'usa-html5-map'), __('Map Preview', 'usa-html5-map'), $role, 'usa-html5-map-view', 'usa_html5map_plugin_view');

    add_submenu_page('usa-html5-map-options', __('Maps dashboard', 'usa-html5-map'), __('Maps', 'usa-html5-map'), $role, 'usa-html5-map-maps', 'usa_html5map_plugin_maps');



}

function usa_html5map_plugin_nav_tabs($page, $map_id)
{
?>
<h2 class="nav-tab-wrapper">
    <a href="?page=usa-html5-map-options&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'options' ? 'nav-tab-active' : '' ?>"><?php _e('General settings', 'usa-html5-map') ?></a>
    <a href="?page=usa-html5-map-states&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'states' ? 'nav-tab-active' : '' ?>"><?php _e('Detailed settings', 'usa-html5-map') ?></a>
    <a href="?page=usa-html5-map-groups&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'groups' ? 'nav-tab-active' : '' ?>"><?php _e('Groups settings', 'usa-html5-map') ?></a>
    <a href="?page=usa-html5-map-points&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'points' ? 'nav-tab-active' : '' ?>"><?php _e('Points settings', 'usa-html5-map') ?></a>
    <a href="?page=usa-html5-map-tools&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'tools' ? 'nav-tab-active' : '' ?>"><?php _e('Tools', 'usa-html5-map') ?></a>
    <a href="?page=usa-html5-map-view&map_id=<?php echo $map_id ?>" class="nav-tab <?php echo $page == 'view' ? 'nav-tab-active' : '' ?>"><?php _e('Preview', 'usa-html5-map') ?></a>
</h2>
<?php
}

function usa_html5map_plugin_map_selector($page, $map_id, &$options) {
?>
<script type="text/javascript">
jQuery(function($){
    $('select[name=map_id]').change(function() {
        location.href='admin.php?page=usa-html5-map-<?php echo $page ?>&map_id='+$(this).val();
    });
    $('.tipsy-q').tipsy({gravity: 'w'}).find('span').css('cursor', 'default');
});
</script>
<span class="title" style="width: 100px"><?php echo __('Select a map:', 'usa-html5-map'); ?> </span>
    <select name="map_id" style="width: 185px;">
        <?php foreach($options as $id => $map_data) { ?>
            <option value="<?php echo $id; ?>" <?php echo ($id==$map_id) ? 'selected' : '';?>><?php echo $map_data['name'] . (isset($map_data['type']) ? " ($map_data[type])" : ''); ?></option>
        <?php } ?>
    </select>
    <span class="tipsy-q" original-title="<?php esc_attr_e('Select a map for editing and previewing', 'usa-html5-map'); ?>">[?]</span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="admin.php?page=usa-html5-map-maps" class="page-title-action tipsy-q" original-title="<?php esc_attr_e('List of all maps, creating a new map, backup', 'usa-html5-map'); ?>"><?php _e('Maps dashboard', 'usa-html5-map'); ?></a>
<?php
}

function usa_html5map_plugin_messages($successes, $errors) {
    if ($successes and is_array($successes)) {
        echo "<div class=\"updated\"><ul>";
        foreach ($successes as $s) {
            echo "<li>" . (is_array($s) ? "<strong>$s[0]</strong>$s[1]" : $s) . "</li>";
        }
        echo "</ul></div>";
    }

    if ($errors and is_array($errors)) {
        echo "<div class=\"error\"><ul>";
        foreach ($errors as $s) {
            echo "<li>" . (is_array($s) ? "<strong>$s[0]</strong>$s[1]" : $s) . "</li>";
        }
        echo "</ul></div>";
    }
}

function usa_html5map_plugin_options() {
    include('editmainconfig.php');
}

function usa_html5map_plugin_states() {
    include('editstatesconfig.php');
}

function usa_html5map_plugin_tools() {
    include('maptools.php');
}

function usa_html5map_plugin_groups() {
    include('editgroupsconfig.php');
}
function usa_html5map_plugin_points() {
    include('editpointsconfig.php');
}
function usa_html5map_plugin_maps() {
    include('mapslist.php');
}

function usa_html5map_plugin_view() {

    $options = usa_html5map_plugin_get_options();
    $option_keys = is_array($options) ? array_keys($options) : array();
    $map_id  = (isset($_REQUEST['map_id'])) ? intval($_REQUEST['map_id']) : array_shift($option_keys) ;

?>
<div class="wrap usa-html5-map main full">
    <h2><?php _e('Map Preview', 'usa-html5-map') ?></h2>
    <br />
<div class="left-block">
    <form method="POST" class="">
    <?php usa_html5map_plugin_map_selector('view', $map_id, $options) ?>
    <br /><br />
    </form>
    <style type="text/css">
        .usaHtml5MapBold {font-weight: bold}
    </style>
<?php
    usa_html5map_plugin_nav_tabs('view', $map_id);
    echo '<p>'.sprintf(__('Use shortcode %s for install this map', 'usa-html5-map'), '<span class="usaHtml5MapBold">[usahtml5map id="'.$map_id.'"]</span>').'</p>';

    echo do_shortcode('<div style="width: 99%">[usahtml5map id="'.$map_id.'"]</div>'); ?>
        </div>
        <div class="qanner">
        </div>

        <div class="clear"></div>
    </div>
<?php
}

add_action('admin_init','usa_html5map_plugin_scripts');

function usa_html5map_plugin_scripts(){
    if ( is_admin() ){

        wp_register_style('jquery-tipsy', plugins_url('/static/css/tipsy.css', __FILE__));
        wp_enqueue_style('jquery-tipsy');
        wp_register_style('usa-html5-map-adm', plugins_url('/static/css/mapadm.css', __FILE__), array(), 'r3.3.0');
        wp_enqueue_style('usa-html5-map-adm');
        wp_register_style('usa-html5-map-style', plugins_url('/static/css/map.css', __FILE__), array(), 'r3.3.0');
        wp_enqueue_style('usa-html5-map-style');
        wp_enqueue_style('farbtastic');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_script('farbtastic');
        wp_enqueue_script('tiny_mce');
        wp_register_script('jquery-tipsy', plugins_url('/static/js/jquery.tipsy.js', __FILE__));
        wp_enqueue_script('jquery-tipsy');
    }
    else {

        wp_register_style('usa-html5-map-style', plugins_url('/static/css/map.css', __FILE__), array(), 'r3.3.0');
        wp_enqueue_style('usa-html5-map-style');

        wp_register_script('raphael', plugins_url('/static/js/raphael.min.js', __FILE__));
        wp_enqueue_script('raphael');

        wp_enqueue_script('jquery');

    }
}

add_action('wp_enqueue_scripts', 'usa_html5map_plugin_scripts_method');

function usa_html5map_plugin_scripts_method() {
    wp_enqueue_script('jquery');
    wp_register_style('usa-html5-map-style', plugins_url('/static/css/map.css', __FILE__));
    wp_enqueue_style('usa-html5-map-style');
}


add_shortcode('usahtml5map', 'usa_html5map_plugin_content');

function usa_html5map_plugin_enqueue_js($js = null) {
    static $arr = array();
    if ($js) {
        $arr[] = $js;
    } else {
        echo implode("", $arr);
    }
}

function usa_html5map_plugin_chk_color($val) {
    $val = sanitize_text_field($val);
    if ($val and $val[0] != '#')
        $val = '#' . $val;
    
    return $val;
}

function usa_html5map_plugin_chk_url($val) {
    if (strpos($val, 'javascript:') === 0)
        return $val;
    return esc_url($val, null, 'url');
}

function usa_html5map_plugin_escape_fonts($fonts, $addFonts = false) {
    $fonts = $fonts ? explode(',', $fonts) : array();
    if ($addFonts)
        $fonts = array_merge($fonts, explode(',', $addFonts));

    foreach ($fonts as &$f) {
        $f = '\''.trim($f, ' \'"').'\'';
    }
    return implode(',', $fonts);
}

function usa_html5map_plugin_strcmp($a, $b) {
    if (is_array($a) and isset($a['name'])) {
        $a = $a['name'];
        $b = $b['name'];
    } else if (is_object($a) and isset($a->name)) {
        $a = $a->name;
        $b = $b->name;
    } else {
        return 0;
    }
    if (class_exists('\Collator')) {
        static $coll = null;
        if (is_null($coll)) {
            $coll = new \Collator("");
            $coll->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);
        }
        return $coll->compare($a, $b);
    }
    if (function_exists('strcoll')) {
        return strcoll($a, $b);
    }
    return strnatcmp($a, $b);
}

function usa_html5map_plugin_rstrcmp($a, $b) {
    return usa_html5map_plugin_strcmp($b, $a);
}
function usa_html5map_plugin_sort_regions_list(&$list, $sort = null) {
    $old = @setlocale(LC_COLLATE, 0);
    @setlocale(LC_COLLATE, 'en_US.utf8');
    if ($sort == 'asc') {
        uasort($list, 'usa_html5map_plugin_strcmp');
    } else if ($sort == 'desc') {
        uasort($list, 'usa_html5map_plugin_rstrcmp');
    }
    @setlocale(LC_COLLATE, $old);
}



function usa_html5map_plugin_prepare_tooltip_css($options, $prefix) {
    $commentCss = '';
    if ( ! empty($options['popupCommentColor'])) {
        $commentCss .= "\t\t\t\tcolor: $options[popupCommentColor];\n";
    }
    if ( ! empty($options['popupCommentFontSize'])) {
        $commentCss .= "\t\t\t\tfont-size: $options[popupCommentFontSize]px;\n";
    }
    if ( ! empty($options['popupCommentFontFamily'])) {
        $commentCss .= "\t\t\t\tfont-family: ".usa_html5map_plugin_escape_fonts($options['popupCommentFontFamily']).";\n";
    }

    $popupTitleCss = '';
    if ( ! empty($options['popupNameColor'])) {
        $popupTitleCss .= "\t\t\t\tcolor: $options[popupNameColor];\n";
    }
    if ( ! empty($options['popupNameFontSize'])) {
        $popupTitleCss .= "\t\t\t\tfont-size: $options[popupNameFontSize]px;\n";
    }
    if ( ! empty($options['popupNameFontFamily'])) {
        $popupTitleCss .= "\t\t\t\tfont-family: ".usa_html5map_plugin_escape_fonts($options['popupNameFontFamily']).";\n";
    }
    $result = "$prefix .fm-tooltip-name {
$popupTitleCss
}
$prefix .fm-tooltip-comment {
$commentCss
}
$prefix .fm-tooltip-comment p {
$commentCss
}";
    return $result;
}

function usa_html5map_plugin_escape_js_string($s, $q = '"') {
    return "$q" . str_replace($q, "\\$q", $s) . "$q";
}

function usa_html5map_plugin_get_map_js_url($options)
{
    $map_file          = plugins_url('/static/', __FILE__)."js/map.js";
    return $map_file;
}

function usa_html5map_plugin_get_static_url($url)
{
    return plugins_url('/static/', __FILE__).ltrim($url, '/');
}

function usa_html5map_plugin_get_raphael_js_url()
{
    return plugins_url('/static/', __FILE__)."js/raphael.min.js";
}

function usa_html5map_plugin_content($atts, $content) {
    static $firstRun = true;
    $dir               = plugins_url('/static/', __FILE__);
    $siteURL           = get_site_url();
    $options           = usa_html5map_plugin_get_options();
    $option_keys       = is_array($options) ? array_keys($options) : array();
    $toSelect          = array();
    $selectColors      = array();
    
    $map_id = isset($atts['id']) ? intval($atts['id']) : array_shift($option_keys);

    if (isset($options[$map_id])) {
        $options = $options[$map_id];
    } else {
        $map_id  = array_shift($option_keys);
        $options = array_shift($options);
    }
    if (isset($atts['select'])) {
        $toSelect = array_map('trim', explode(',', $atts['select']));
        $toSelect = preg_grep('/\S+/', $toSelect);

        if (count($toSelect) and (preg_match('/^#([\da-f]{3}|[\da-f]{6})/i', $toSelect[count($toSelect)-1]) or !preg_match('/^[a-z]{1,}\d+$/i', $toSelect[count($toSelect)-1])))
            array_unshift($selectColors, array_pop($toSelect));

        if (count($toSelect) and (preg_match('/^#([\da-f]{3}|[\da-f]{6})/i', $toSelect[count($toSelect)-1]) or !preg_match('/^[a-z]{1,}\d+$/i', $toSelect[count($toSelect)-1])))
            array_unshift($selectColors, array_pop($toSelect));
    }
    $defOptions = usa_html5map_plugin_map_defaults('', 1, true);
    foreach ($defOptions as $k => $v) {
        if (!isset($options[$k]))
            $options[$k] = $v;
    }
    $prfx              = "_$map_id";
    $isResponsive      = $options['isResponsive'];
    $stateInfoArea     = $options['statesInfoArea'];
    $respInfo          = $isResponsive ? ' htmlMapResponsive' : '';
    $type_id           = 0;
    $style             = (!empty($options['maxWidth']) && $isResponsive) ? 'max-width:'.intval($options['maxWidth']).'px; width: 100%' : '';

    static $count = 0;
    static $print_action_registered = false;

    $settings_file = usa_html5map_plugin_settings_url($map_id, $options);

    wp_register_script('raphaeljs', usa_html5map_plugin_get_raphael_js_url(), array(), 'r3.3.0');
    wp_register_script('usa-html5-map-mapjs_'.$type_id, usa_html5map_plugin_get_map_js_url($options), array('raphaeljs'), 'r3.3.0');
    wp_register_script('usa-html5-map-map_cfg_'.$map_id, $settings_file, array('raphaeljs', 'usa-html5-map-mapjs_'.$type_id));
    wp_enqueue_script('usa-html5-map-map_cfg_'.$map_id);

    $select_options = "";
    $states = json_decode($options['map_data'], true);
    usa_html5map_plugin_sort_regions_list($states, 'asc');
    foreach ($states as $sid => $s) {
        if ($options['areaListOnlyActive'] and !$s['link'])
            continue;
        if (isset($s['hidden']) and $s['hidden'])
            continue;
        $select_options .= "\t<option value=\"$sid\">".htmlspecialchars($s['name'])."</option>\n";
    }

    if ($options['useAjaxUrls']) {
        usa_html5map_plugin_popup_builder_enable_scripts($options, $states);
        $popupUrl = usa_html5map_plugin_ajax_url('popup');
        $stateInfoUrl = usa_html5map_plugin_ajax_url('state_info', array('map_id' => $map_id, 'sid' => ''));
        $groupInfoUrl = usa_html5map_plugin_ajax_url('group_info', array('map_id' => $map_id, 'gid' => ''));
    } else {
        usa_html5map_plugin_popup_builder_enable_scripts($options, $states);
        $popupUrl = "{$siteURL}/' + 'index.php' + '?map_id={$map_id}' + '&usahtml5map_get_popup"; // splitting urls in attempt to prevent this links from beeing parsed by google
        $stateInfoUrl = "{$siteURL}/' + 'index.php' + '?map_id={$map_id}' + '&usahtml5map_get_state_info="; // splitting urls in attempt to prevent this links from beeing parsed by google
        $groupInfoUrl = "{$siteURL}/' + 'index.php' + '?map_id={$map_id}' + '&usahtml5map_get_group_info="; // splitting urls in attempt to prevent this links from beeing parsed by google
    }
    
    

    $mapInit = "
        <!-- start Fla-shop.com HTML5 Map -->";
    $mapInit .= "
        <div class='usaHtml5Map$stateInfoArea$respInfo' style='$style'>";

    $containerStyle  = '';
    $areasJs = '';
    $dropDownHtml = '';
    $dropDownJS = '';
    $selectJs = '';
    if ($options['areasList']) {

        $options['listWidth'] = intval($options['listWidth']) ;
        if ($options['listWidth']<=0) { $options['listWidth'] = 20; }

        $areasList = usa_html5map_plugin_areas_list($options, $count);

        $niceScrollJsFile = (defined('WP_DEBUG') && (bool)WP_DEBUG === true) ? '/static/js/jquery.nicescroll.js' : '/static/js/jquery.nicescroll.min.js';
        wp_register_script('usa-html5-map-nicescroll', plugins_url($niceScrollJsFile, __FILE__), array(), '3.7.2'); // version from jquery.nicescroll.js
        wp_enqueue_script('usa-html5-map-nicescroll');

        if ($areasList) {
            $areasJs = '
                jQuery(document).ready(function($) {

                    $( window ).resize(function() {
                        $("#usa-html5-map-areas-list_'.$count.'").show().css({height: jQuery("#usa-html5-map-map-container_'.$count.' .fm-map-container").height() + "px"}).niceScroll({cursorwidth:"8px"});
                    });

                    $("#usa-html5-map-areas-list_'.$count.'").show().css({height: jQuery("#usa-html5-map-map-container_'.$count.' .fm-map-container").height() + "px"}).niceScroll({cursorwidth:"8px"});

                    $("#usa-html5-map-areas-list_'.$count.' a").click(function() {

                        var id  = $(this).data("key");
                        var map = usahtml5map_map_'.$count.';

                        html5map_onclick(null,id,map);

                        return false;
                    });

                    $("#usa-html5-map-areas-list_'.$count.' a").on("mouseover",function() {

                        var id  = $(this).data("key");
                        var map = usahtml5map_map_'.$count.';

                        map.stateHighlightIn(id);

                    });

                    $("#usa-html5-map-areas-list_'.$count.' a").on("mouseout",function() {

                        var id  = $(this).data("key");
                        var map = usahtml5map_map_'.$count.';

                        map.stateHighlightOut(id);

                    });

                });';

            $containerStyle = 'width: '.($options['statesInfoArea']!='right' ? 100-$options['listWidth'].'%' : 60-$options['listWidth'].'%' ).'; float: left';

            if ($options['areasListShowDropDown']) {
                $showOnMobile = '';
                if ($options['areasListShowDropDown'] == 'mobile') {
                    $showOnMobile = 'mobile-only';
                } else {
                    $areasList = '';
                    $areasJs = '';
                    $containerStyle = '';
                }
                $dropDownHtml = "<div class='usaHtml5MapSelector $showOnMobile'><select id='usa-html5-map-selector_{$count}'>
                    <option value=''>".__('Select an area', 'usa-html5-map')."</option>
                    $select_options
                </select></div>";

                $dropDownJS = "jQuery('#usa-html5-map-selector_{$count}').change(function() {
                        var sid = jQuery(this).val();
                        if (hightlighted)
                                usahtml5map_map_{$count}.stateHighlightOut(hightlighted);

                        hightlighted = sid;

                        if (sid) {
                            usahtml5map_map_{$count}.stateHighlightIn(sid);

                            html5map_onclick(null,sid,usahtml5map_map_{$count});
                        }
                    });\n";
            }

            $mapInit.= $areasList;
        }
    }

    if (count($toSelect)) {
        $arr = array(
            'color' => usa_html5map_plugin_escape_js_string($selectColors ? $selectColors[0] : '#ff0000'),
            'colorOver' => usa_html5map_plugin_escape_js_string(count($selectColors) == 2 ? $selectColors[1] : '#8f1d21')
        );

        foreach ($toSelect as $stId) {
            $stId = usa_html5map_plugin_escape_js_string($stId);
            $selectJs .= "if (usahtml5map_map_{$count}.mapConfig.map_data[$stId]) {usahtml5map_map_{$count}.mapConfig.map_data[$stId].color = $arr[color]; usahtml5map_map_{$count}.mapConfig.map_data[$stId].colorOver = $arr[colorOver]; }\n";
            $selectJs .= "else if (usahtml5map_map_{$count}.mapConfig.points[$stId]) {usahtml5map_map_{$count}.mapConfig.points[$stId].color = $arr[color]; usahtml5map_map_{$count}.mapConfig.points[$stId].colorOver = $arr[colorOver]; }\n";
        }
    }


    $mapInit.="$dropDownHtml<div id='usa-html5-map-map-container_{$count}' class='usaHtml5MapContainer' data-map-variable='usahtml5map_map_{$count}'></div>";

    if ($options['statesInfoArea']=='bottom') { $mapInit.="<div style='clear:both; height: 20px;'></div>"; }

    $customJs = "";
    if ($options['customJs']) {
        $customJs = "(function (map, containerId, mapId) {\n{$options['customJs']}\n})(usahtml5map_map_{$count}, 'usa-html5-map-map-container_{$count}', $map_id);";
    }

    $mapInit.= "
            <style>
                #usa-html5-map-map-container_{$count} {
                    $containerStyle
                }
                ".usa_html5map_plugin_prepare_tooltip_css($options, "#usa-html5-map-map-container_{$count}")."
                @media only screen and (max-width: 480px) {
                    #usa-html5-map-map-container_{$count} {
                        float: none;
                        width: 100%;
                    }
                }
            </style>";
    $mapJs =  "<script type=\"text/javascript\">
            jQuery(function(){
                var hightlighted = null;
                usahtml5map_map_{$count} = new FlaShopUSAMap(usahtml5map_map_cfg_{$map_id});
                $selectJs
                usahtml5map_map_{$count}.draw('usa-html5-map-map-container_{$count}');
                usahtml5map_map_{$count}.on('mousein', function(ev, sid, map) {
                    if (hightlighted && sid != hightlighted) {
                        map.stateHighlightOut(hightlighted);
                        hightlighted = null;
                    }
                });
                $areasJs
                $customJs

                var html5map_onclick = function(ev, sid, map) {
                var cfg      = usahtml5map_map_cfg_{$map_id};
                var link     = map.fetchStateAttr(sid, 'link');
                var is_group = map.fetchStateAttr(sid, 'group');
                var popup_id = map.fetchStateAttr(sid, 'popup-id');
                var is_group_info = false;

                if (typeof cfg.map_data[sid] !== 'undefined')
                        jQuery('#usa-html5-map-selector_{$count}').val(sid);
                    else
                        jQuery('#usa-html5-map-selector_{$count}').val('');

                if (is_group==undefined) {

                    if (sid.substr(0,1)=='p') {
                        popup_id = map.fetchPointAttr(sid, 'popup_id');
                        link         = map.fetchPointAttr(sid, 'link');
                    }

                } else if (typeof cfg.groups[is_group]['ignore_link'] == 'undefined' || ! cfg.groups[is_group].ignore_link)  {
                    link = cfg.groups[is_group].link;
                    popup_id = cfg.groups[is_group]['popup_id'];
                    is_group_info = true;
                }
                if (link=='#popup') {

                    if (typeof SG_POPUP_DATA == \"object\") {
                        if (popup_id in SG_POPUP_DATA) {

                            SGPopup.prototype.showPopup(popup_id,false);

                        } else {

                            jQuery.ajax({
                                type: 'POST',
                                url: '$popupUrl',
                                data: {popup_id:popup_id},
                            }).done(function(data) {
                                jQuery('body').append(data);
                                SGPopup.prototype.showPopup(popup_id,false);
                            });

                        }
                    }
                    else if (typeof SGPBPopup == \"function\") {
                        var popup = SGPBPopup.createPopupObjById(popup_id);
                        popup.prepareOpen();
                        popup.open();
                    }

                    return false;
                }
                if (link == '#info') {
                    var id = is_group_info ? is_group : (sid.substr(0,1)=='p' ? sid : map.fetchStateAttr(sid, 'id'));
                    jQuery('#usa-html5-map-state-info_{$count}').html('". __('Loading...', 'usa-html5-map') ."');
                    jQuery.ajax({
                        type: 'POST',
                        url: (is_group_info ? '$groupInfoUrl' : '$stateInfoUrl') + id,
                        success: function(data, textStatus, jqXHR){
                            jQuery('#usa-html5-map-state-info_{$count}').html(data);
                            " . (($options['statesInfoArea'] == 'bottom' AND $options['autoScrollToInfo']) ? "
                            jQuery(\"html, body\").animate({ scrollTop: jQuery('#usa-html5-map-state-info_{$count}').offset().top - ".$options['autoScrollOffset']." }, 1000);" : "") . "
                        },
                        dataType: 'text'
                    });

                    return false;
                }

                    if (ev===null && link!='') {
                        if (!jQuery('.html5dummilink').length) {
                            jQuery('body').append('<a href=\"#\" class=\"html5dummilink\" style=\"display:none\"></a>');
                        }

                        jQuery('.html5dummilink').attr('href',link).attr('target',(map.fetchStateAttr(sid, 'isNewWindow') ? '_blank' : '_self'))[0].click();

                    }

                };
                usahtml5map_map_{$count}.on('click',html5map_onclick);

                $dropDownJS

            });
            </script>";
    if ( ! $options['delayCodeOutput'])
        $mapInit .= $mapJs;
    $mapInit.= "<div id='usa-html5-map-state-info_{$count}' class='usaHtml5MapStateInfo'>".
            (empty($options['defaultAddInfo']) ? '' : apply_filters('the_content',$options['defaultAddInfo']))
            ."</div>
            </div>
            <div style='clear: both'></div>
            <!-- end HTML5 Map -->
    ";

    $count++;

    if ($options['delayCodeOutput']) {
        usa_html5map_plugin_enqueue_js($options['minimizeOutput'] ? preg_replace('/\s+/', ' ', $mapJs) : $mapJs);

        if ( ! $print_action_registered) {
            if (is_admin()) {
                add_action('admin_footer', 'usa_html5map_plugin_enqueue_js', 1000);
            } else {
                add_action('wp_footer', 'usa_html5map_plugin_enqueue_js', 1000);
            }
            $print_action_registered = true;
        }
    }

    if ($options['minimizeOutput'])
        $mapInit = preg_replace('/\s+/', ' ', $mapInit);
    return $mapInit;
}


$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'usa_html5map_plugin_settings_link' );

function usa_html5map_plugin_settings_link($links) {
    $settings_link = '<a href="admin.php?page=usa-html5-map-options">'.__('Settings', 'usa-html5-map').'</a>';
    array_push($links, $settings_link);
    return $links;
}

function usa_html5map_plugin_ajax_url($action, $params = array()) {
    $url = admin_url('admin-ajax.php');

    $urlParams = array('action=usahtml5map_' . $action);
    foreach ($params as $key => $value) {
        $urlParams[] = $key.'='.urlencode($value);
    }

    $url .= (strpos('?', $url) === false) ? '?' : '&';
    $url .= implode('&', $urlParams);

    return $url;
}

function usa_html5map_plugin_ajax_get_settings_js() {
    $mapId  = intval($_REQUEST['map_id']);
    $options = usa_html5map_plugin_get_options();
    $options = isset($options[$mapId]) ? $options[$mapId] : null;
    header( 'Content-Type: application/javascript' );

    if (!$options)
        wp_die();

    $options['map_data'] = str_replace('\\\\n','\\n',$options['map_data']);
    usa_html5map_plugin_print_map_settings($mapId, $options);

    wp_die();
}
add_action('wp_ajax_usahtml5map_settings_js', 'usa_html5map_plugin_ajax_get_settings_js');
add_action('wp_ajax_nopriv_usahtml5map_settings_js', 'usa_html5map_plugin_ajax_get_settings_js');

function usa_html5map_plugin_ajax_get_state_info() {
    $mapId  = intval($_REQUEST['map_id']);
    $options = usa_html5map_plugin_get_options();
    $options = isset($options[$mapId]) ? $options[$mapId] : null;
    if (!$options)
        wp_die();

    $stateId = $_GET['sid'];

    $info = $options['state_info'][$stateId];
    echo apply_filters('the_content',$info);

    wp_die();
}
add_action('wp_ajax_usahtml5map_state_info', 'usa_html5map_plugin_ajax_get_state_info');
add_action('wp_ajax_nopriv_usahtml5map_state_info', 'usa_html5map_plugin_ajax_get_state_info');

function usa_html5map_plugin_ajax_get_group_info() {
    $mapId  = intval($_REQUEST['map_id']);
    $options = usa_html5map_plugin_get_options();
    $options = isset($options[$mapId]) ? $options[$mapId] : null;
    if (!$options)
        wp_die();

    $gid = $_GET['gid'];

    $info = isset($options['groups'][$gid]['info']) ? $options['groups'][$gid]['info'] : '';
    echo apply_filters('the_content', $info);

    wp_die();
}
add_action('wp_ajax_usahtml5map_group_info', 'usa_html5map_plugin_ajax_get_group_info');
add_action('wp_ajax_nopriv_usahtml5map_group_info', 'usa_html5map_plugin_ajax_get_group_info');
function usa_html5map_plugin_ajax_get_popup() {
    $popup = do_shortcode('[sg_popup id="'.intval($_REQUEST['popup_id']).'"][/sg_popup]');
    //$popup = substr($popup,0,strpos($popup,'</script>')+9);
    echo $popup;

    wp_die();
}
add_action('wp_ajax_usahtml5map_popup', 'usa_html5map_plugin_ajax_get_popup');
add_action('wp_ajax_nopriv_usahtml5map_popup', 'usa_html5map_plugin_ajax_popup');
add_action('init', 'usa_html5map_plugin_settings', 100);

function usa_html5map_plugin_settings() {

    $is_map_call = false;
    foreach($_REQUEST as $key => $value) { if (strpos($key,'usahtml5map')!==false) { $is_map_call = true; break; } }
    if (!$is_map_call) { return false; } else {
        remove_all_actions( 'wp_head' );
        remove_all_actions( 'wp_footer' );
    }

    $req_start = microtime(TRUE);
    if (isset($_REQUEST['usahtml5map_js_data']) or
        isset($_REQUEST['usahtml5map_get_state_info']) or
        isset($_REQUEST['usahtml5map_get_group_info'])) {
        $map_id  = intval($_REQUEST['map_id']);
        $options = usa_html5map_plugin_get_options();
        $options = $options[$map_id];
        if ($options)
            $options['map_data'] = str_replace('\\\\n','\\n',$options['map_data']);
    } else if (isset($_REQUEST['usahtml5map_get_popup']) ) {

        $popup = do_shortcode('[sg_popup id="'.intval($_REQUEST['popup_id']).'"][/sg_popup]');
        //$popup = substr($popup,0,strpos($popup,'</script>')+9);
        echo $popup; exit();
    }


    if( isset($_GET['usahtml5map_js_data']) ) {

        header( 'Content-Type: application/javascript' );
        usa_html5map_plugin_print_map_settings($map_id, $options);
        echo '// Generated in '.(microtime(TRUE)-$req_start).' secs.';
        exit;
    }

    if(isset($_GET['usahtml5map_get_state_info'])) {
        $stateId = $_GET['usahtml5map_get_state_info'];

        $info = $options['state_info'][$stateId];
        echo apply_filters('the_content',$info);

        exit;
    }

    if(isset($_GET['usahtml5map_get_group_info'])) {
        $gid = $_GET['usahtml5map_get_group_info'];

        $info = isset($options['groups'][$gid]['info']) ? $options['groups'][$gid]['info'] : '';
        echo apply_filters('the_content',$info);

        exit;
    }
}

function usa_html5map_plugin_prepare_comment($comment) {
    if (! $comment)
        return $comment;
        return apply_filters('the_content',$comment);
}

function usa_html5map_plugin_print_map_settings($map_id, &$map_options) {
    if ( ! $map_options) {
        ?>
        var	map_cfg = {
            map_data: {}
        };
        <?php
        return;
    }
    $data = json_decode($map_options['map_data'], true);
    $protected_shortnames = array('st7', 'st8', 'st9', 'st12', 'st21', 'st22', 'st30', 'st31', 'st40', 'st46');
    $siteURL           = get_site_url();
    foreach ($data as $sid => &$d)
    {
        if (isset($d['comment']))
            $d['comment'] = usa_html5map_plugin_prepare_comment($d['comment']);
        if (isset($d['_hide_name'])) {
            unset($d['_hide_name']);
            $d['name'] = '';
        }
        if (isset($map_options['hideSN']) AND ! in_array($sid, $protected_shortnames))
            $d['shortname'] = '';
        if (isset($d['link']))
            $d['link'] = strpos($d['link'], 'javascript:usahtml5map_set_state_text') === 0 ? '#info' : $d['link'];
    }
    unset($d);
    $map_options['map_data'] = json_encode($data);
    $grps = array();
    if (isset($map_options['groups']) AND is_array($map_options['groups'])) {
        foreach ($map_options['groups'] as $gid => $grp) {
            $grps[$gid] = array();
            if ($grp['_popup_over']) {
                $grps[$gid]['name'] = $grp['name'];
                $grps[$gid]['comment'] = usa_html5map_plugin_prepare_comment($grp['comment']);
                $grps[$gid]['image'] = $grp['image'];
            }
            if ($grp['_act_over']) {
                $grps[$gid]['link'] = strpos($grp['link'], 'javascript:usahtml5map_set_state_text') === 0 ? '#info' : $grp['link'];
                $grps[$gid]['isNewWindow'] = empty($grp['isNewWindow']) ? FALSE : TRUE;
                $grps[$gid]['popup_id']    = isset($grp['popup-id']) ? intval($grp['popup-id']) : -1;
            } else {
                $grps[$gid]['ignore_link'] = true;
            }
            if ($grp['_clr_over']) {
                $grps[$gid]['color'] = $grp['color_map'];
                $grps[$gid]['colorOver'] = $grp['color_map_over'];
            }
            if ($grp['_ignore_group']) {
                $grps[$gid]['ignoreMouse'] = true;
            }
            if (!$grps[$gid])
                unset($grps[$gid]);
        }
    }
    $defOptions = usa_html5map_plugin_map_defaults('', 1, true);
    foreach ($defOptions as $k => $v) {
        if (!isset($map_options[$k]))
            $map_options[$k] = $v;
    }
    $jsEncodeOption = 0;
    if (isset($map_options['points']) AND is_array($map_options['points'])) {
        foreach ($map_options['points'] as $pid => &$p) {
            if (isset($p['comment']))
                $p['comment'] = usa_html5map_plugin_prepare_comment($p['comment']);
            if (isset($p['link']))
                $p['link'] = strpos($p['link'], 'javascript:usahtml5map_set_state_text') === 0 ? '#info' : $p['link'];
        }
        unset($p);
    }
    if (defined('JSON_PRETTY_PRINT')) {
        $jsEncodeOption = $jsEncodeOption | JSON_PRETTY_PRINT;
    }
    if (defined('JSON_UNESCAPED_UNICODE')) {
        $jsEncodeOption = $jsEncodeOption | JSON_UNESCAPED_UNICODE;
    }
    $mapCfg = array();
    if($map_options['isResponsive']) {
        $mapCfg += array(
            'mapWidth' => 0
        );
    } else {
        $mapCfg += array(
            'mapWidth'  => intval($map_options['mapWidth']),
            'mapHeight' => intval($map_options['mapHeight'])
        );
    }
    $mapCfg += array(
        'zoomEnable'            => !!$map_options['zoomEnable'],
        'zoomOnlyOnMobile'      => !!$map_options['zoomOnlyOnMobile'],
        'zoomEnableControls'    => !!$map_options['zoomEnableControls'],
        'zoomIgnoreMouseScroll' => !!$map_options['zoomIgnoreMouseScroll'],
        'zoomMax'               => round($map_options['zoomMax'], 4),
        'zoomStep'              => round($map_options['zoomStep'], 4),
        'pointColor'                => $map_options['pointColor'],
        'pointColorOver'            => $map_options['pointColorOver'],
        'pointNameColor'            => $map_options['pointNameColor'],
        'pointNameColorOver'        => $map_options['pointNameColorOver'],
        'pointNameStrokeColor'      => $map_options['pointNameStrokeColor'],
        'pointNameStrokeColorOver'  => $map_options['pointNameStrokeColorOver'],
        'pointNameStrokeWidth'      => (is_null($map_options['pointNameStrokeWidth']) ? $map_options['nameStrokeWidth'] : $map_options['pointNameStrokeWidth']),
        'pointNameStrokeOpacity'    => (is_null($map_options['pointNameStrokeOpacity']) ? $map_options['nameStrokeOpacity'] : $map_options['pointNameStrokeOpacity']),
        'pointNameFontFamily'       => usa_html5map_plugin_escape_fonts($map_options['pointNameFontFamily'] ? $map_options['pointNameFontFamily'] : $map_options['nameFontFamily'], 'Arial, sans-serif'),
        'pointNameFontSize'         => intval($map_options['pointNameFontSize']).'px',
        'pointNameFontWeight'       => 'bold',
        'pointNameStroke'           => is_null($map_options['pointNameStrokeWidth']) ? ($map_options['nameStrokeWidth'] ? true : false) : ($map_options['pointNameStrokeWidth'] ? true : false),
        'pointBorderWidth'          => $map_options['pointBorderWidth'],
        'pointBorderColor'          => $map_options['pointBorderColor'],
        'pointBorderColorOver'      => $map_options['pointBorderColorOver'],
        'shadowAllow'       => !!$map_options['shadowAllow'],
        'shadowWidth'       => round($map_options['shadowWidth'], 2),
        'shadowOpacity'     => round($map_options['shadowOpacity'], 2),
        'shadowColor'       => $map_options['shadowColor'],
        'shadowX'           => round($map_options['shadowX'], 2),
        'shadowY'           => round($map_options['shadowY'], 2),

        'iPhoneLink'        => $map_options['iPhoneLink'] === 'false' ? false : (!!$map_options['iPhoneLink']),
        'isNewWindow'       => $map_options['isNewWindow'] === 'false' ? false : (!!$map_options['isNewWindow']),

        'borderWidth'       => round($map_options['borderWidth'], 2),
        'borderColor'       => $map_options['borderColor'],
        'borderColorOver'   => $map_options['borderColorOver'],

        'nameColor'         => $map_options['nameColor'],
        'nameColorOver'     => $map_options['nameColorOver'],
        'nameFontFamily'    => $map_options['nameFontFamily'],
        'nameFontSize'      => $map_options['nameFontSize'] . 'px',
        'nameFontWeight'    => $map_options['nameFontWeight'],

        'overDelay'         => $map_options['overDelay'],

        'nameStroke'        => !!$map_options['nameStroke'],
        'nameStrokeColor'   => $map_options['nameStrokeColor'],
        'nameStrokeColorOver'   => $map_options['nameStrokeColorOver'],
        'nameStrokeWidth'   => round($map_options['nameStrokeWidth'], 2),
        'nameStrokeOpacity' => round($map_options['nameStrokeOpacity'], 2),

        'freezeTooltipOnClick'      => !!$map_options['freezeTooltipOnClick'],
        'tooltipOnHighlightIn'      => !!$map_options['tooltipOnHighlightIn'],
        'tooltipOnMobileCentralize' => !!$map_options['tooltipOnMobileCentralize'],
        'tooltipOnMobileWidth'      => $map_options['tooltipOnMobileWidth'],
        'tooltipOnMobileVPosition'  => $map_options['tooltipOnMobileVPosition'],

        'mapId'         => $map_options['mapId'],
        'map_data'      => json_decode($map_options['map_data'], true),
        'groups'        => $grps ? $grps : new stdClass,
        'points'        => (isset($map_options['points']) AND $map_options['points']) ? $map_options['points'] : new stdClass,
    );
    ?>

    var usahtml5map_map_cfg_<?php echo $map_id ?> = <?php echo json_encode($mapCfg, $jsEncodeOption) ?>;

    <?php
    if (file_exists($params_file = dirname(__FILE__).'/static/paths.json')) {
        echo "usahtml5map_map_cfg_$map_id.map_params = ".file_get_contents($params_file).";\n";
    }
}


function usa_html5map_plugin_map_defaults($name='New map', $type=1, $baseOnly=false) {
    $defaults = array(
        'mapWidth'          =>530,
        'mapHeight'         =>410,
        'maxWidth'          =>980,
        'shadowAllow'       => true,
        'zoomEnable'            => false,
        'zoomEnableControls'    => true,
        'zoomIgnoreMouseScroll' => false,
        'zoomOnlyOnMobile'      => false,
        'zoomMax'               => 2,
        'zoomStep'              => 0.2,
        'initialZoom'           => null,
        'defaultPointRadius'    => 4,
        'pointColor'            => "#FFC480",
        'pointColorOver'        => "#DC8135",
        'pointNameColor'        => "#000",
        'pointNameColorOver'    => "#222",
        'pointNameFontFamily'   => '',
        'pointNameFontSize'     => "8",
        'pointNameFontWeight'   => "bold",
        'pointNameStroke'       => null,
        'pointNameStrokeColor'  => "#FFFFFF",
        'pointNameStrokeColorOver'  => "#FFFFFF",
        'pointNameStrokeWidth'  => null,
        'pointNameStrokeOpacity'=> null,

        'pointBorderWidth'      => 0.5,
        'pointBorderColor'      => "#ffffff",
        'pointBorderColorOver'  => "#eeeeee",
        'shadowWidth'       => 1.5,
        'shadowOpacity'     => 0.2,
        'shadowColor'       => "black",
        'shadowX'           => 0,
        'shadowY'           => 0,
        'iPhoneLink'        => true,
        'isNewWindow'       => false,
        'borderWidth'       => 1.01,
        'borderColor'       => "#ffffff",
        'borderColorOver'   => "#ffffff",
        'nameColor'         => "#ffffff",
        'nameColorOver'     => "#ffffff",
        'nameFontFamily'    => '',
        'nameFontSize'      =>10,
        'nameFontWeight'    => "bold",
        'overDelay'         => 300,
        'statesInfoArea'    => "bottom",
        'autoScrollToInfo'  => 0,
        'autoScrollOffset'  => 0,
        'isResponsive'      => "1",
        'nameStroke'        => false,
        'nameStrokeColor'   => "#000000",
        'nameStrokeColorOver'=> "#000000",
        'nameStrokeWidth'   =>  1.5,
        'nameStrokeOpacity' => 0.5,
        'freezeTooltipOnClick' => false,

        'areasList'         =>false,
        'areasListShowDropDown' => false,
        'areaListOnlyActive'=> false,
        'listWidth'         => '20',
        'listFontSize'      => '14',
        'popupNameColor'    => "#000000",
        'popupNameFontFamily'   => "",
        'popupNameFontSize'     => "20",
        'popupCommentColor'     => '',
        'popupCommentFontFamily'=> '',
        'popupCommentFontSize'  => '',
        'tooltipOnHighlightIn'  => true,
        'tooltipOnMobileCentralize' => true,
        'tooltipOnMobileWidth' => '80%',
        'tooltipOnMobileVPosition' => 'bottom',
        'minimizeOutput' => true,
        'delayCodeOutput' => false,
        'useAjaxUrls' => false,
        'customJs' => '',

    );

    $initialStatesPath = dirname(__FILE__).'/static/settings_tpl.json';
    $defaults['mapId'] = 'vcg';
    if ($baseOnly)
        return $defaults;
    $defaults['name']           = $name;
    $defaults['update_time']    = time();
    $defaults['map_data']       = file_get_contents($initialStatesPath);
    $defaults['cacheSettings']  = is_writable(dirname(__FILE__).'/static');
    $arr = json_decode($defaults['map_data'], true);
    foreach ($arr as $i) {
        $defaults['state_info'][$i['id']] = '';
    }

    return $defaults;
}

function usa_html5map_plugin_settings_url($map_id, &$map_options) {
    $cacheURL   = plugins_url('/static/cache', __FILE__);
    $siteURL    = get_site_url();

    if ($map_options['useAjaxUrls']) {
        $phpURL = usa_html5map_plugin_ajax_url('settings_js', array('map_id' => $map_id, 'r' => rand(11111,99999)));
    } else {
        $phpURL = "{$siteURL}/index.php?usahtml5map_js_data=true&map_id=$map_id&r=".rand(11111,99999);
    }

    if ( ! $map_options['update_time'])
        return $phpURL;

    if ( ! (isset($map_options['cacheSettings']) and $map_options['cacheSettings']))
        return $phpURL;

    $cache_name = "usa-html5-map-{$map_id}-{$map_options['update_time']}.js";
    $static_path = dirname(__FILE__).'/static';
    $cache_path  = "$static_path/cache";

    if (!is_writable($static_path))
        return $phpURL;

    if (file_exists("$cache_path/$cache_name"))
        return "$cacheURL/$cache_name";

    if (!file_exists($cache_path)) {
        if (is_writable($static_path))
            mkdir($cache_path);
        else
            return $phpURL;
    }

    if (usa_html5map_plugin_generate_cache($map_id, $map_options, $cache_path, $cache_name))
        return "$cacheURL/$cache_name";
    else
        return $phpURL;
}

function usa_html5map_plugin_generate_cache($map_id, &$map_options, $cache_path, $cache_name) {
    $name_prefix = "usa-html5-map-{$map_id}";
    $dh = opendir($cache_path);
    if (!$dh)
        return false;
    while ($file = readdir($dh)) {
        if (strpos($file, $name_prefix) !== false)
            unlink("$cache_path/$file");
    }
    closedir($dh);

    ob_start();
    usa_html5map_plugin_print_map_settings($map_id, $map_options);
    $cntnt = ob_get_clean();
    if (file_put_contents("$cache_path/$cache_name", $cntnt))
        return true;
    else
        return false;
}

function usa_html5map_plugin_group_defaults($name) {
    return array(
        'group_name' => $name,
        '_popup_over' => false,
        '_act_over' => false,
        '_clr_over' => false,
        '_ignore_group' => false,
        'name' => $name,
        'comment' => '',
        'info' => '',
        'image' => '',
        'link' => '',
        'color_map' => '#ffffff',
        'color_map_over' => '#ffffff'
    );
}


function usa_html5map_plugin_wp_editor_for_tooltip($content, $name = 'tooltip', $id = 'tooltip_editor') {
    wp_editor($content, $id, array(
        'wpautop'       => 1,
        'media_buttons' => 1,
        'textarea_name' => $name,
        'textarea_rows' => 5,
        'tabindex'      => null,
        'editor_css'    => '',
        'editor_class'  => '',
        'teeny'         => 0,
        'dfw'           => 0,
        'tinymce'       => 1,
        'quicktags'     => 1,
        'drag_drop_upload' => false
    ));
}
function usa_html5map_plugin_get_options($blog_id = null, $option_name = 'usahtml5map_options') {
    $res = is_multisite() ?
        get_blog_option(is_null($blog_id) ? get_current_blog_id() : $blog_id, $option_name) :
        get_site_option($option_name);
    return $res ? $res : array();
}

function usa_html5map_plugin_save_options(&$options, $blog_id = null, $option_name = 'usahtml5map_options') {
    if ( is_multisite() ) {
        update_blog_option(is_null($blog_id) ? get_current_blog_id() : $blog_id, $option_name, $options);
    } else {
        update_site_option($option_name, $options);
    }
}

function usa_html5map_plugin_delete_options($blog_id = null, $option_name = 'usahtml5map_options') {
    if ( is_multisite() ) {
        delete_blog_option(is_null($blog_id) ? get_current_blog_id() : $blog_id, $option_name);
    } else {
        delete_site_option($option_name);
    }
}

register_activation_hook(__FILE__, 'usa_html5map_plugin_activation');

function usa_html5map_plugin_activation() {

    $options = array(0 => usa_html5map_plugin_map_defaults());

    if (is_multisite()) {
        add_blog_option(get_current_blog_id(), 'usahtml5map_options', $options);
    } else {
        add_site_option('usahtml5map_options', $options);
    }

}

register_deactivation_hook( __FILE__, 'usa_html5map_plugin_deactivation' );

function usa_html5map_plugin_deactivation() {

}

register_uninstall_hook( __FILE__, 'usa_html5map_plugin_uninstall' );

function usa_html5map_plugin_uninstall() {
    if (is_multisite()) {
        if (function_exists('get_sites')) {
            $res = get_sites();
            foreach ($res as $blog) {
                delete_blog_option($blog->blog_id, 'usahtml5map_options');
                delete_blog_option($blog->blog_id, 'usahtml5map_goptions');
            }
        }
    } else {
        delete_site_option('usahtml5map_options');
        delete_site_option('usahtml5map_goptions');
    }
}

add_filter('widget_text', 'do_shortcode');


function usa_html5map_plugin_export() {
    $maps = array();
    if (isset($_REQUEST['map_id']) and is_array($_REQUEST['map_id']))
        $maps = $_REQUEST['map_id'];
    elseif (isset($_REQUEST['maps']))
        $maps = explode(',', $_REQUEST['maps']);
    if ( ! $maps)
        return;
    $options = usa_html5map_plugin_get_options();

    foreach($options as $map_id => $option) {
        if (!in_array($map_id,$maps)) {
            unset($options[$map_id]);
        }
        unset($options[$map_id]['point_editor_settings']);
    }

    if (count($options)>0) {
        $options = json_encode($options);

        header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
        header('Content-Type: text/json');
        header('Content-Length: ' . (strlen($options)));
        header('Connection: close');
        header('Content-Disposition: attachment; filename="maps.json";');
        echo $options;

        exit();
    }

}

function usa_html5map_plugin_get_csv_import_export_keys($type) {
    switch ($type) {
        case 'states':
        return array(
            'id'            => 'id',
            'shortname'     => 'shortname',
            'name'          => 'name',
            'comment'       => 'comment',
            'info'          => 'info',
            'image'         => 'image',
            'link'          => 'link',
            'isNewWindow'   => 'isNewWindow',
            'clickAction'   => 'clickAction',
            'popupId'       => 'popup-id',
            'color'         => 'color_map',
            'colorOver'     => 'color_map_over',
            'class'         => 'class',
            'hideName'      => '_hide_name',
            'hideArea'      => 'hidden',
            'groupId'       => 'group'
        );
        case 'groups':
        return array(
            'groupId'       => 'id',
            'groupName'     => 'group_name',
            'name'          => 'name',
            'comment'       => 'comment',
            'info'          => 'info',
            'image'         => 'image',
            'link'          => 'link',
            'isNewWindow'   => 'isNewWindow',
            'clickAction'   => 'clickAction',
            'popupId'       => 'popup-id',
            'color'         => 'color_map',
            'colorOver'     => 'color_map_over',
            'overridePopup' => '_popup_over',
            'overrideAction'=> '_act_over',
            'overrideColors'=> '_clr_over',
            'ignoreMouse'   => '_ignore_group',
        );
        case 'points':
        return array(
            'pointId'       => 'id',
            'shortname'     => 'shortname',
            'name'          => 'name',
            'comment'       => 'comment',
            'info'          => 'info',
            'image'         => 'image',
            'link'          => 'link',
            'isNewWindow'   => 'isNewWindow',
            'clickAction'   => 'clickAction',
            'popupId'       => 'popup_id',
            'color'         => 'color',
            'colorOver'     => 'colorOver',
            'borderColor'           => 'borderColor',
            'borderColorOver'       => 'borderColorOver',
            'nameColor'             => 'nameColor',
            'nameColorOver'         => 'nameColorOver',
            'nameStrokeColor'       => 'nameStrokeColor',
            'nameStrokeColorOver'   => 'nameStrokeColorOver',
            'class'         => 'class',
            'nameFontSize'  => 'nameFontSize',
            'textPos'       => 'textPos',
            'textX'         => 'tx',
            'textY'         => 'ty',
            'x'             => 'x',
            'y'             => 'y',
            'radius'        => 'radius',
            'pointType'     => 'pointType'
        );
    }
    return array();
}

function usa_html5map_plugin_detect_export_click_action(&$row) {
    $action = "none";
    if (!isset($row['link'])) $action = "none";
    elseif(stripos($row['link'], "javascript:[\w_]+_set_state_text") !== false or $row['link'] == '#info' ) $action = "info";
    elseif(trim($row['link']) == "#popup") $action = "popup";
    elseif(trim($row['link']) != "") $action = "link";
    else $action = "none";
    $row['clickAction'] = $action;
}

function usa_html5map_plugin_export_csv() {
    if ( ! is_admin())
        return;
    remove_all_actions('wp_head');
    remove_all_actions('wp_footer');

    $all_options = usa_html5map_plugin_get_options();
    $options_keys = array_keys($all_options);
    $def_map_id = reset($options_keys);

    $map_id = isset($_GET['map_id']) ? (int)$_GET['map_id'] : $def_map_id;

    $map_options = &$all_options[$map_id];

    $field_delimiters = array(
        ',' => ',',
        ';' => ';',
        ':' => ':',
        'sp'=> ' ',
        'tb'=> "\t"
    );
    $text_delimiters = array(
        "'" => "'",
        '"' => '"',
        'n' => null
    );

    $fd = stripslashes($_REQUEST['field-delimiter']);
    $td = stripslashes($_REQUEST['text-delimiter']);
    if ( ! array_key_exists($fd, $field_delimiters)) {
        $fd = ',';
    }
    else {
        $fd = $field_delimiters[$fd];
    }
    if ( ! array_key_exists($td, $text_delimiters)) {
        $td = '"';
    }
    else {
        $td = $text_delimiters[$td];
    }

    $tmp_name = tempnam(sys_get_temp_dir(), 'mapcsv');
    $fh = fopen($tmp_name, 'w');
    $import_export_keys = usa_html5map_plugin_get_csv_import_export_keys('states');
    $header = array_keys($import_export_keys);
    $fields = array_values($import_export_keys);
    fputcsv($fh, $header, $fd, $td);
    $st_params = json_decode($map_options['map_data'], true);
    foreach ($st_params as $id => $params) {
        $params['id'] = $id;
        $params['info'] = isset($map_options['state_info'][$iid = preg_replace('/\D+/', '', $id)]) ?$map_options['state_info'][$iid] : '';
        usa_html5map_plugin_detect_export_click_action($params);
        $data = array();
        foreach(array('_hide_name', 'isNewWindow', 'hidden') as $f) $params[$f] = empty($params[$f]) ? '' : 'yes';
        foreach ($fields as $f) if ($f)
            $data[$f] = isset($params[$f]) ? $params[$f] : '';
        if ($params['clickAction'] !== 'link') $data['link'] = '';
        fputcsv($fh, $data, $fd, $td);
    }
    if (!empty($map_options['groups'])) {
        $import_export_keys = usa_html5map_plugin_get_csv_import_export_keys('groups');
        $header = array_keys($import_export_keys);
        $fields = array_values($import_export_keys);
        fputcsv($fh, $header, $fd, $td);
        foreach ($map_options['groups'] as $id => $params) {
            $params['id'] = $id;
            usa_html5map_plugin_detect_export_click_action($params);
            $data = array();
            foreach(array('isNewWindow', '_popup_over', '_act_over', '_clr_over', '_ignore_group') as $f) $params[$f] = empty($params[$f]) ? '' : 'yes';
            foreach ($fields as $f) if ($f)
                $data[$f] = isset($params[$f]) ? $params[$f] : '';
            if ($params['clickAction'] !== 'link') $data['link'] = '';
            fputcsv($fh, $data, $fd, $td);
        }
    }
    if (!empty($map_options['points'])) {
        $import_export_keys = usa_html5map_plugin_get_csv_import_export_keys('points');
        $header = array_keys($import_export_keys);
        $fields = array_values($import_export_keys);
        fputcsv($fh, $header, $fd, $td);
        foreach ($map_options['points'] as $id => $params) {
            $params['id'] = $id;
            $params['info'] = isset($map_options['state_info'][$id]) ?$map_options['state_info'][$id] : '';
            usa_html5map_plugin_detect_export_click_action($params);
            $data = array();
            foreach(array('isNewWindow') as $f) $params[$f] = empty($params[$f]) ? '' : 'yes';
            foreach ($fields as $f) if ($f)
                $data[$f] = isset($params[$f]) ? $params[$f] : '';
            if ($params['clickAction'] !== 'link') $data['link'] = '';
            fputcsv($fh, $data, $fd, $td);
        }
    }
    fclose($fh);
    header('Content-type: text/csv');
    header('Content-length: '.filesize($tmp_name));
    header('Connection: close');
    header('Content-Disposition: attachment; filename="usahtml5map-'.$map_id.'.csv";');
    readfile($tmp_name);
    unlink($tmp_name);
    exit;
}


function usa_html5map_plugin_import(&$errors) {
    $errors = array();
    $csv_types = array('text/csv','text/comma-separated-values','application/vnd.ms-excel');
    if(is_uploaded_file($_FILES['import_file']["tmp_name"])) {

        if (in_array($_FILES['import_file']['type'], $csv_types))
        {
            $errors[] = sprintf(__('CSV import should be done on the "<a href="%s">Import / Export</a>" tab', 'usa-html5-map'), "admin.php?page=usa-html5-map-tools");
            return false;
        }

        $hwnd = fopen($_FILES['import_file']["tmp_name"],'r');
        $data = fread($hwnd,filesize($_FILES['import_file']["tmp_name"]));
        fclose($hwnd);

        $data    = json_decode($data, true);

        if ($data) {
            $def_settings = file_get_contents(dirname(__FILE__).'/static/settings_tpl.json');
            $def_settings = json_decode($def_settings, true);
            $states_count = count($def_settings);
            $options = usa_html5map_plugin_get_options();

            foreach($data as $map_id => $map_data) {
                if (isset($map_data['map_data']) and $map_data['map_data']) {

                    $data = json_decode($map_data['map_data'], true);
                    $cur_count = $data ? count($data) : 0;
                    $c = $options ? max(array_keys($options))+1 : 0;
                    if ($cur_count != $states_count) {
                        $errors[] = sprintf(__('Failed to import "%s", looks like it is a wrong map. Got %d states when expected states count was: %d', 'usa-html5-map'), $map_data['name'], $cur_count, $states_count);
                        continue;
                    }
                    $map_data['update_time'] = time();
                    $map_data['map_data'] = preg_replace("/javascript:[\w_]+_set_state_text[^\(]*\([^\)]+\);/", "#info", $map_data['map_data']);
                    $options[]              = $map_data;
                } else {
                   $errors[] = sprintf(__('Section "%s" skipped cause it has no "map_data" block.', 'usa-html5-map'), $map_id);
                }

            }
            usa_html5map_plugin_save_options($options);
        } else {
            $errors[] = __('Failed to parse uploaded file. Is it JSON?', 'usa-html5-map');
        }

        unlink($_FILES['import_file']["tmp_name"]);

    } else {
        $errors[] = __('File uploading error!', 'usa-html5-map');
    }
    return !count($errors);
}

function usa_html5map_plugin_areas_list($options,$count) {

    $map_data = (array)json_decode($options['map_data']);
    $areas    = array();
    foreach($map_data as $key => $area) {
        if ($options['areaListOnlyActive'] and !$area->link)
            continue;
        if (isset($area->hidden) and $area->hidden)
            continue;
        $areas[$area->name] = array(
            'id'   => $area->id,
            'key'  => $key,
            'name' => $area->name,
        );
    }

    if (empty($areas))
        return '';

    usa_html5map_plugin_sort_regions_list($areas, 'asc');

    $options['listFontSize'] = intval($options['listFontSize'])>0 ? intval($options['listFontSize']) : 16;

    $html = "<div class=\"usaHtml5Map-areas-list\" id=\"usa-html5-map-areas-list_{$count}\" style=\"width: ".$options['listWidth']."%;\" data-count=\"$count\">";

    foreach ($areas as $area) {
        $html.="<div class=\"usaHtml5Map-areas-item\"><a href=\"#\" style=\"font-size: ".$options['listFontSize']."px\" data-key=\"".$area['key']."\" data-id=\"".$area['id']."\" >".$area['name']."</a></div>";
    }

    $html.= "</div>";

    return $html;
}

function usa_html5map_plugin_user_has_cap($allcaps, $cap, $args) {

    $user_id      = get_current_user_id();
    $current_user = get_user_by('id', $user_id);
    $allowed      = usa_html5map_plugin_get_options(null, 'usahtml5map_goptions');

    if (!(isset($allowed['roles']) and is_array($allowed['roles']))) {
        $allowed['roles'] = array();
    }

    $allowed['roles']['administrator'] = true;

    foreach($allowed['roles'] as $role => $val) {
        if ($val && $current_user && in_array($role, (array)$current_user->roles)) {
            $allcaps['usahtml5map_manage_role'] = true;
            break;
        }
    }

    return $allcaps;
}
add_filter('user_has_cap', 'usa_html5map_plugin_user_has_cap', 10, 3 );

add_action('init', 'usa_html5map_plugin_convert_old_popup_ids', 100);

function usa_html5map_plugin_convert_old_popup_ids() {
	$is_converted = get_option('usahtml5map_popup_ids_converted');
	if ($is_converted)
		return;

	if (usa_html5map_plugin_popup_bulder_type() == 1)
		return;

	$maps = usa_html5map_plugin_get_options();
	$modified = false;
	foreach ($maps as &$mapOptions) {
		$res = usa_html5map_plugin_popup_builder_cover_old_ids($mapOptions);
		$modified = $res || $modified;
	}
	
	if ($modified) {
		usa_html5map_plugin_save_options($maps);
	}
	update_option('usahtml5map_popup_ids_converted', 1);
}
