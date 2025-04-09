<?php
/*
Plugin Name: Gravity Forms - Salesforce CRM Integration
Description: Gravity Forms - Salesforce CRM Integration plugin allows you to connect WordPress Gravity Forms and Salesforce CRM.
Version:     2.3.1
Author:      Obtain Code
Author URI:  https://obtaincode.net/
License:     GPLv2 or later
Text Domain: ocgfsf
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

define( 'OCGFSF_PRO_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/*
 * This is a class file for Salesforce CRM API
 */
include_once OCGFSF_PRO_PLUGIN_PATH . 'includes/class-salesforce.php';

/*
 * This is a admin file
 */
include_once OCGFSF_PRO_PLUGIN_PATH . 'admin/admin.php';

/*
 * This is a core functions file
 */
include_once OCGFSF_PRO_PLUGIN_PATH . 'public/functions.php';

/*
 * This is a function that run during active plugin
 */
if ( ! function_exists( 'ocgfsf_activation' ) ) {
    register_activation_hook( __FILE__, 'ocgfsf_activation' );
    function ocgfsf_activation() {
        
        update_option( 'ocgfsf_modules', 'a:3:{s:4:"Case";s:4:"Case";s:7:"Contact";s:7:"Contact";s:4:"Lead";s:4:"Lead";}' );
        
        $webto_modules = get_option( 'ocgfsf_webto_modules' );
        if ( ! $webto_modules ) {
            update_option( 'ocgfsf_webto_modules', 'a:2:{s:4:"Case";a:10:{s:4:"name";a:3:{s:5:"label";s:12:"Contact Name";s:4:"type";s:6:"string";s:8:"required";i:0;}s:5:"email";a:3:{s:5:"label";s:5:"Email";s:4:"type";s:5:"email";s:8:"required";i:0;}s:5:"phone";a:3:{s:5:"label";s:5:"Phone";s:4:"type";s:6:"string";s:8:"required";i:0;}s:7:"subject";a:3:{s:5:"label";s:7:"Subject";s:4:"type";s:6:"string";s:8:"required";i:0;}s:11:"description";a:3:{s:5:"label";s:11:"Description";s:4:"type";s:8:"textarea";s:8:"required";i:0;}s:7:"company";a:3:{s:5:"label";s:7:"Company";s:4:"type";s:6:"string";s:8:"required";i:0;}s:4:"type";a:3:{s:5:"label";s:4:"Type";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:6:"status";a:3:{s:5:"label";s:6:"Status";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:6:"reason";a:3:{s:5:"label";s:11:"Case Reason";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:8:"priority";a:3:{s:5:"label";s:8:"Priority";s:4:"type";s:8:"picklist";s:8:"required";i:0;}}s:4:"Lead";a:24:{s:10:"first_name";a:3:{s:5:"label";s:10:"First Name";s:4:"type";s:6:"string";s:8:"required";i:0;}s:9:"last_name";a:3:{s:5:"label";s:9:"Last Name";s:4:"type";s:6:"string";s:8:"required";i:0;}s:5:"email";a:3:{s:5:"label";s:5:"Email";s:4:"type";s:5:"email";s:8:"required";i:0;}s:7:"company";a:3:{s:5:"label";s:7:"Company";s:4:"type";s:6:"string";s:8:"required";i:0;}s:4:"city";a:3:{s:5:"label";s:4:"City";s:4:"type";s:6:"string";s:8:"required";i:0;}s:5:"state";a:3:{s:5:"label";s:14:"State/Province";s:4:"type";s:6:"string";s:8:"required";i:0;}s:10:"salutation";a:3:{s:5:"label";s:10:"Salutation";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:5:"title";a:3:{s:5:"label";s:5:"Title";s:4:"type";s:6:"string";s:8:"required";i:0;}s:3:"url";a:3:{s:5:"label";s:7:"Website";s:4:"type";s:3:"url";s:8:"required";i:0;}s:5:"phone";a:3:{s:5:"label";s:5:"Phone";s:4:"type";s:5:"phone";s:8:"required";i:0;}s:6:"mobile";a:3:{s:5:"label";s:6:"Mobile";s:4:"type";s:5:"phone";s:8:"required";i:0;}s:3:"fax";a:3:{s:5:"label";s:3:"Fax";s:4:"type";s:5:"phone";s:8:"required";i:0;}s:6:"street";a:3:{s:5:"label";s:6:"Street";s:4:"type";s:6:"string";s:8:"required";i:0;}s:3:"zip";a:3:{s:5:"label";s:3:"Zip";s:4:"type";s:6:"string";s:8:"required";i:0;}s:7:"country";a:3:{s:5:"label";s:7:"Country";s:4:"type";s:6:"string";s:8:"required";i:0;}s:11:"description";a:3:{s:5:"label";s:11:"Description";s:4:"type";s:8:"textarea";s:8:"required";i:0;}s:11:"lead_source";a:3:{s:5:"label";s:11:"Lead Source";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:8:"industry";a:3:{s:5:"label";s:8:"Industry";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:6:"rating";a:3:{s:5:"label";s:6:"Rating";s:4:"type";s:8:"picklist";s:8:"required";i:0;}s:7:"revenue";a:3:{s:5:"label";s:14:"Annual Revenue";s:4:"type";s:8:"currency";s:8:"required";i:0;}s:9:"employees";a:3:{s:5:"label";s:9:"Employees";s:4:"type";s:3:"int";s:8:"required";i:0;}s:11:"emailOptOut";a:3:{s:5:"label";s:13:"Email Opt Out";s:4:"type";s:7:"boolean";s:8:"required";i:0;}s:9:"faxOptOut";a:3:{s:5:"label";s:11:"Fax Opt Out";s:4:"type";s:7:"boolean";s:8:"required";i:0;}s:9:"doNotCall";a:3:{s:5:"label";s:11:"Do Not Call";s:4:"type";s:7:"boolean";s:8:"required";i:0;}}}' );
        }
        
        $method = get_option( 'ocgfsf_method' );
        if ( ! $method ) {
            update_option( 'ocgfsf_method', 'api' );
        }
    }
}