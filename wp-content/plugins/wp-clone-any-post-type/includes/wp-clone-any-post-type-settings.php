<?php 
/*
*   General WP Page Cloner options page
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (! current_user_can('manage_options')) {
    wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-clone-any-post-type' ) );
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'WP clone for any post type', 'wp-clone-any-post-type' ); ?></h1>	

    <div class="notice wp-clone-dpffm--notice">
        <div>
            <h3><?php esc_html_e( "WP clone for any post type", "wp-clone-any-post-type" ); ?></h3>
            <p><?php esc_html_e( "Here's a link to the documentation for the plugin. This will help you learn more about its features and how to use it.", "wp-clone-any-post-type" ); ?></p>
			<div class="e-notice__actions">
				<a href="https://wp-plugins.galaxyweblinks.com/wp-plugins/wp-clone-any-post-type/doc/" class="e-button--cta cta-secondary" target="_blank"><span>Documentation</span></a>
            </div>
			<p class="e-note">For any feedback or queries regarding this plugin, please contact our <a href="https://wp-plugins.galaxyweblinks.com/contact/" target="_blank">Support team</a>.</p>
        </div>
    </div>

	<?php 
		if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'):
            echo    '<div id="setting-error-settings_updated" class="updated settings-error"> 
            <p><strong>Settings saved.</strong></p></div>';
	    endif;

        echo _e( '<h4>Enable clone to the following post types</h4>', 'wp-clone-any-post-type' );

        $getarg = array(
                'public'	=> true,
                '_builtin'	=>  true
                );
        $getptyp = get_post_types($getarg,'names','or');
        $getopt = get_option('wcapt_clone_post_types');
        $searcharry = array('attachment','revision','nav_menu_item','custom_css','customize_changeset','oembed_cache','user_request','wp_block','wp_template','wp_template_part','wp_global_styles','wp_navigation');
        echo '<form method="post" action="options.php">';
        settings_fields( 'gwl-clone-posts-page-options-group' ); 
        do_settings_sections( 'gwl-clone-posts-page-options-group' );  
        if(!empty($getptyp)){
                foreach ($getptyp as $gttype) {
                        if(!in_array($gttype, $searcharry)){ 
                           $strposty = str_replace('-',' ',$gttype);
                            ?>
                            <div class="maindivset">
                                <div class="leftlab">
                                    <lable for="<?php echo $gttype; ?>"><?php echo ucwords($strposty); ?></lable>
                                </div>
                                <div class="rytlab">
                                    <label class="switch">
                                        <input type="checkbox" name="wcapt_clone_post_types[]" value="<?php echo $gttype; ?>" <?php if(!empty($getopt)){ 
                                        if(in_array($gttype, get_option('wcapt_clone_post_types'))){ echo "checked"; }} ?> >
                                        <span class="slider round"></span>
                                    </label>                                
                                </div>
                            </div>
                            <div class="clearfix"></div>
                                 
                        <?php }
                }		
        }
        submit_button();
        wp_nonce_field( basename(__FILE__), 'wcapt_clone_enable_post_type_nonce' );

        echo '</form>';	

	?>		
</div>
