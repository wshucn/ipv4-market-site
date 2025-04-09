<?php
/**
 * Plugin Name: IPv4 Ticker
 * Plugin URI: https://ipv4.global/
 * Description: Price ticker for IPv4 Global, use shortcode [prior-sales] to display ticker.
 * Version: 1.2
 * Author: Concordsoft
 * Author URI: https://ipv4.global/
 */

function get_prior_sales($sales = '') {
    static $running = false;
    if($running !== true) {
        include(plugin_dir_path(__FILE__) . 'engine.php');
        $output = '';
        $output .= register_script_ticker();
        $output .= enqueue_style_ticker();
        $output .= load_data_ipv4();
        $running = true;
        return $output;
    }
    return '';
}
add_shortcode('prior-sales', 'get_prior_sales');