<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

/*
 * Delete options when plugin uninstall
 */
$uninstall = get_option( 'ocgfsf_uninstall' );
if ( $uninstall ) {
    delete_option( 'ocgfsf_method' );
    delete_option( 'ocgfsf_account' );
    delete_option( 'ocgfsf_client_id' );
    delete_option( 'ocgfsf_client_secret' );
    delete_option( 'ocgfsf_username' );
    delete_option( 'ocgfsf_password' );
    delete_option( 'ocgfsf_organization_id' );
    delete_option( 'ocgfsf_url' );
    delete_option( 'ocgfsf_modules' );
    delete_option( 'ocgfsf_webto_modules' );
}