<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that create menu
 */
if ( ! function_exists( 'ocgfsf_main_menu' ) ) {
    add_action( 'admin_menu', 'ocgfsf_main_menu' );
    function ocgfsf_main_menu() {
        
        add_menu_page( esc_html__( 'Gravity Forms - Salesforce CRM Integration', 'ocgfsf' ), esc_html__( 'GF - Salesforce', 'ocgfsf' ), 'manage_options', 'ocgfsf_integration', 'ocgfsf_integration_callback', 'dashicons-migrate' );
        add_submenu_page( 'ocgfsf_integration', esc_html__( 'GF - Salesforce: Integration', 'ocgfsf' ), esc_html__( 'Integration', 'ocgfsf' ), 'manage_options', 'ocgfsf_integration', 'ocgfsf_integration_callback' );
        add_submenu_page( 'ocgfsf_integration', esc_html__( 'GF - Salesforce: Configuration', 'ocgfsf' ), esc_html__( 'Configuration', 'ocgfsf' ), 'manage_options', 'ocgfsf_configuration', 'ocgfsf_configuration_callback' );
        add_submenu_page( 'ocgfsf_integration', esc_html__( 'GF - Salesforce: API Error Logs', 'ocgfsf' ), esc_html__( 'API Error Logs', 'ocgfsf' ), 'manage_options', 'ocgfsf_api_error_logs', 'ocgfsf_api_error_logs_callback' );
        add_submenu_page( 'ocgfsf_integration', esc_html__( 'GF - Salesforce: Settings', 'ocgfsf' ), esc_html__( 'Settings', 'ocgfsf' ), 'manage_options', 'ocgfsf_settings', 'ocgfsf_settings_callback' );
        add_submenu_page( 'ocgfsf_integration', esc_html__( 'GF - Salesforce: Licence Verification', 'ocgfsf' ), esc_html__( 'Licence Verification', 'ocgfsf' ), 'manage_options', 'ocgfsf_licence_verification', 'ocgfsf_licence_verification_callback' );
    }
}

/*
 * This is a function for integration
 */
if ( ! function_exists( 'ocgfsf_integration_callback' ) ) {
    function ocgfsf_integration_callback() {
        
        global $wpdb;
        
        $gf_db_version = get_option( 'gf_db_version' );
        $licence = get_site_option( 'ocgfsf_licence' );
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Salesforce CRM Integration', 'ocgfsf' ); ?></h1>
                <hr>
                <?php
                    if ( $licence ) {
                        if ( isset( $_REQUEST['id'] ) ) {
                            $form_id = (int) $_REQUEST['id'];
                            if ( isset( $_POST['submit'] ) ) {
                                $ocgfsf = (int) $_POST['ocgfsf'];
                                update_option( 'ocgfsf_'.$form_id, $ocgfsf );

                                $gf_sf_fields = ( isset( $_POST['ocgfsf_fields'] ) ? $_POST['ocgfsf_fields'] : array() );
                                update_option( 'ocgfsf_fields_'.$form_id, $gf_sf_fields );

                                $action = sanitize_text_field( $_POST['ocgfsf_action'] );
                                update_option( 'ocgfsf_action_'.$form_id, $action );

                                $campaign = sanitize_text_field( $_POST['ocgfsf_campaign'] );
                                update_option( 'ocgfsf_campaign_'.$form_id, $campaign );
                                ?>
                                    <div class="notice notice-success is-dismissible">
                                        <p><?php esc_html_e( 'Integration settings saved.', 'ocgfsf' ); ?></p>
                                    </div>
                                <?php
                            } else if ( isset( $_POST['filter'] ) ) { 
                                $module = sanitize_text_field( $_POST['ocgfsf_module'] );
                                update_option( 'ocgfsf_module_'.$form_id, $module );
                            }

                            $ocgfsf = get_option( 'ocgfsf_'.$form_id );
                            $module = get_option( 'ocgfsf_module_'.$form_id );
                            $campaign = get_option( 'ocgfsf_campaign_'.$form_id );
                            $gf_sf_fields = get_option( 'ocgfsf_fields_'.$form_id );
                            $sf_fields = get_option( 'ocgfsf_modules' );
                            $sf_fields = unserialize( $sf_fields );
                            $campaigns = get_option( 'ocgfsf_campaigns' );
                            $method = get_option( 'ocgfsf_method' );
                            if ( $method == 'webto' ) {
                                $sf_fields = get_option( 'ocgfsf_webto_modules' );
                                $sf_fields = unserialize( $sf_fields );
                                $case_fields = $sf_fields['Case'];
                                $case_fields['recordType'] = array(
                                    'label'     => 'Record Type ID',
                                    'type'      => 'reference',
                                    'required'  => 0,
                                );
                                asort( $case_fields );
                                $lead_fields = $sf_fields['Lead'];
                                $lead_fields['Campaign_ID'] = array(
                                    'label'     => 'Campaign ID',
                                    'type'      => 'reference',
                                    'required'  => 0,
                                );
                                $lead_fields['recordType'] = array(
                                    'label'     => 'Record Type ID',
                                    'type'      => 'reference',
                                    'required'  => 0,
                                );
                                asort( $lead_fields );
                                $sf_fields['Case'] = $case_fields;
                                $sf_fields['Lead'] = $lead_fields;
                            }

                            $action = get_option( 'ocgfsf_action_'.$form_id );
                            if ( ! $action ) {
                                $action = 'create';
                            }

                            if ( $gf_db_version && version_compare( $gf_db_version, '2.3', '>=' ) ) {
                                $form_meta = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'gf_form_meta WHERE form_id='.$form_id.' LIMIT 1' );
                            } else {
                                $form_meta = $wpdb->get_row( 'SELECT * FROM '.$wpdb->prefix.'rg_form_meta WHERE form_id='.$form_id.' LIMIT 1' );
                            }

                            $form = json_decode( $form_meta->display_meta );
                            ?>
                                <p style="font-size: 17px;"><strong><?php esc_html_e( 'Form Name', 'ocgfsf' ); ?>:</strong> <?php echo $form->title; ?></p>
                                <hr>
                                <form method="post">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label><?php esc_html_e( 'Object', 'ocgfsf' ); ?></label></th>
                                                <td>
                                                    <select name="ocgfsf_module">
                                                        <option value=""><?php esc_html_e( 'Select a object', 'ocgfsf' ); ?></option>
                                                        <?php
                                                            if ( $sf_fields != null ) {
                                                                foreach ( $sf_fields as $sf_field_key => $sf_field_value ) {
                                                                    $selected = '';
                                                                    if ( $sf_field_key == $module ) {
                                                                        $selected = ' selected="selected"';
                                                                    }
                                                                    ?>
                                                                        <option value="<?php echo $sf_field_key; ?>"<?php echo $selected; ?>><?php echo $sf_field_key; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php esc_html_e( 'Filter object fields', 'ocgfsf' ); ?></th>
                                                <td><button type="submit" name="filter" class='button-secondary'><?php esc_html_e( 'Filter', 'ocgfsf' ); ?></button></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label><?php esc_html_e( 'Salesforce CRM Integration?', 'ocgfsf' ); ?></label></th>
                                                <td>
                                                    <input type="hidden" name="ocgfsf" value="0" />
                                                    <input type="checkbox" name="ocgfsf" value="1"<?php echo ( $ocgfsf ? ' checked' : '' ); ?> />
                                                </td>
                                            </tr>
                                            <?php
                                                if ( $method == 'api' ) {
                                                    ?>
                                                        <tr>
                                                            <th scope="row"><label><?php esc_html_e( 'Action Event', 'ocgfsf' ); ?></label></th>
                                                            <td>
                                                                <fieldset>
                                                                    <label><input type="radio" name="ocgfsf_action" value="create"<?php echo ( $action == 'create' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Create Object Record', 'ocgfsf' ); ?></label>&nbsp;&nbsp;
                                                                    <label><input type="radio" name="ocgfsf_action" value="create_or_update"<?php echo ( $action == 'create_or_update' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Create/Update Object Record', 'ocgfsf' ); ?></label>
                                                                </fieldset>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    if ( $module == 'Contact' || $module == 'Lead' ) {
                                                        ?>
                                                            <tr>
                                                                <th scope="row"><label><?php esc_html_e( 'Campaign', 'ocgfsf' ); ?></label></th>
                                                                <td>
                                                                    <?php
                                                                        if ( $campaigns != null ) {
                                                                            ?>
                                                                                <select name="ocgfsf_campaign">
                                                                                    <option value=""><?php esc_html_e( 'Select a campaign', 'ocgfsf' ); ?></option>
                                                                                    <?php
                                                                                        foreach ( $campaigns as $campaign_key => $campaign_value ) {
                                                                                            $selected = '';
                                                                                            if ( $campaign_key == $campaign ) {
                                                                                                $selected = ' selected="selected"';
                                                                                            }

                                                                                            ?>
                                                                                                <option value="<?php echo $campaign_key; ?>"<?php echo $selected; ?>><?php echo $campaign_value; ?></option>
                                                                                            <?php
                                                                                        }
                                                                                    ?>
                                                                                </select>
                                                                            <?php
                                                                        } else {
                                                                            ?><p><?php esc_html_e( 'No campaigns found.', 'ocgfsf' ); ?></p><?php
                                                                        }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                        <input type="hidden" name="ocgfsf_action" value="<?php echo $action; ?>" />
                                                        <input type="hidden" name="ocgfsf_campaign" value="<?php echo $campaign; ?>" />
                                                    <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                    <?php
                                        $gf_fields = array();
                                        if ( $form->fields != null ) {
                                            foreach ( $form->fields as $field ) {
                                                if ( $field->inputs != '' && ( $field->type == 'name' || $field->type == 'address' ) ) {
                                                    foreach ( $field->inputs as $input ) {
                                                        if ( isset( $input->isHidden ) && $input->isHidden ) {
                                                            //
                                                        } else {
                                                            $gf_fields[$input->id] = array(
                                                                'key'   => $input->id,
                                                                'type'  => $field->type,
                                                                'label' => $input->label.' ('.$field->label.')',
                                                            );
                                                        }
                                                    }
                                                } else {
                                                    $gf_fields[$field->id] = array(
                                                        'key'   => $field->id,
                                                        'type'  => $field->type,
                                                        'label' => $field->label,
                                                    );
                                                }
                                            }
                                        }

                                        if ( $gf_fields != null ) {
                                            ?>
                                                <table class="widefat striped">
                                                    <thead>
                                                        <tr>
                                                            <th><?php esc_html_e( 'Gravity Forms Form Field', 'ocgfsf' ); ?></th>
                                                            <th><?php esc_html_e( 'Salesforce CRM Object Field', 'ocgfsf' ); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tfoot>
                                                        <tr>
                                                            <th><?php esc_html_e( 'Gravity Forms Form Field', 'ocgfsf' ); ?></th>
                                                            <th><?php esc_html_e( 'Salesforce CRM Object Field', 'ocgfsf' ); ?></th>
                                                        </tr>
                                                    </tfoot>
                                                    <tbody>
                                                        <?php
                                                            $sf_module_fields = ( isset( $sf_fields[$module] ) ? $sf_fields[$module] : array() );
                                                            foreach ( $gf_fields as $gf_field_key => $gf_field_value ) {
                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo $gf_field_value['label']; ?></td>
                                                                        <td>
                                                                            <select name="ocgfsf_fields[<?php echo $gf_field_key; ?>][key]">
                                                                                <option value=""><?php esc_html_e( 'Select a field', 'ocgfsf' ); ?></option>
                                                                                <?php
                                                                                    $type = '';
                                                                                    if ( $sf_module_fields != null ) {
                                                                                        foreach ( $sf_module_fields as $sf_module_field_key => $sf_module_field_value ) {
                                                                                            $selected = '';
                                                                                            if ( isset( $gf_sf_fields[$gf_field_key]['key'] ) && $gf_sf_fields[$gf_field_key]['key'] == $sf_module_field_key ) {
                                                                                                $selected = ' selected="selected"';
                                                                                                $type = $sf_module_field_value['type'];
                                                                                            }
                                                                                            ?>
                                                                                                <option value="<?php echo $sf_module_field_key; ?>"<?php echo $selected; ?>>
                                                                                                    <?php echo $sf_module_field_value['label']; ?> (<?php esc_html_e( 'Data Type:', 'ocgfsf' ); ?> <?php echo $sf_module_field_value['type']; echo ( $sf_module_field_value['required'] ? esc_html__( ' and Field: required', 'ocgfsf' ) : '' ); ?>)                                                                                                      
                                                                                                </option>
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                            <input type="hidden" name="ocgfsf_fields[<?php echo $gf_field_key; ?>][type]" value="<?php echo $type; ?>" />
                                                                            <input type="hidden" name="ocgfsf_fields[<?php echo $gf_field_key; ?>][field_type]" value="<?php echo $gf_field_value['type']; ?>" />
                                                                        </td>
                                                                    </tr>
                                                                <?php
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            <?php
                                        } else {
                                            ?><p><?php esc_html_e( 'No fields found.', 'ocgfsf' ); ?></p><?php
                                        }
                                    ?>
                                    <p>
                                        <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Save Changes', 'ocgfsf' ); ?>" />
                                    </p>
                                </form>
                            <?php
                        } else {
                            $method = get_option( 'ocgfsf_method' );
                            if ( $method == 'api' ) {
                                $client_id = get_option( 'ocgfsf_client_id' );
                                $client_secret = get_option( 'ocgfsf_client_secret' );
                                $username = get_option( 'ocgfsf_username' );
                                $password = ocgfsf_crypt( get_option( 'ocgfsf_password' ), 'd', $client_secret );
                                $salesforce = new OCGFSF_API( $client_id, $client_secret, $username, $password );
                                $authentication = $salesforce->authentication();
                                if ( ! isset( $authentication->error ) ) {
                                    $modules = get_option( 'ocgfsf_modules' );
                                    $modules = unserialize( $modules );
                                    $modules_fields = array();
                                    if ( $modules != null ) {
                                        foreach ( $modules as $key => $value ) {
                                            $fields = $salesforce->getModuleFields( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $key );
                                            asort( $fields );
                                            $modules_fields[$key] = $fields;
                                        }
                                    }

                                    $modules_fields = serialize( $modules_fields );
                                    update_option( 'ocgfsf_modules', $modules_fields );

                                    $campaigns = array();
                                    $records = $salesforce->getRecords( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'Campaign', array( 'Id', 'Name' ), '' );
                                    if ( isset( $records->records ) && $records->records != null ) {
                                        foreach ( $records->records as $record ) {
                                            $campaigns[$record->Id] = $record->Name;
                                        }
                                    }

                                    update_option( 'ocgfsf_campaigns', $campaigns );
                                }
                            } else {
                                //
                            }

                            ?>
                                <table class="widefat striped">
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e( 'Form Name', 'ocgfsf' ); ?></th>
                                            <th><?php esc_html_e( 'Integration Status', 'ocgfsf' ); ?></th>       
                                            <th><?php esc_html_e( 'Action', 'ocgfsf' ); ?></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><?php esc_html_e( 'Form Name', 'ocgfsf' ); ?></th>
                                            <th><?php esc_html_e( 'Integration Status', 'ocgfsf' ); ?></th>       
                                            <th><?php esc_html_e( 'Action', 'ocgfsf' ); ?></th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            if ( $gf_db_version && version_compare( $gf_db_version, '2.3', '>=' ) ) {
                                                $forms = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'gf_form WHERE is_trash=0' );
                                            } else {
                                                $forms = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'rg_form WHERE is_trash=0' );
                                            }

                                            if ( $forms != null ) {
                                                foreach ( $forms as $form ) {
                                                    $form_id = $form->id;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $form->title; ?></td>
                                                            <td><?php echo ( get_option( 'ocgfsf_'.$form_id ) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no"></span>' ); ?></td>
                                                            <td><a href="<?php echo menu_page_url( 'ocgfsf_integration', 0 ); ?>&id=<?php echo $form_id; ?>"><span class="dashicons dashicons-edit"></span></a></td>
                                                        </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                    <tr>
                                                        <td colspan="3"><?php esc_html_e( 'No forms found.', 'ocgfsf' ); ?></td>
                                                    </tr>
                                                <?php
                                            }

                                            wp_reset_postdata();
                                        ?>
                                    </tbody>
                                </table>
                            <?php
                        }
                    } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p><?php esc_html_e( 'Please verify purchase code.', 'ocgfsf' ); ?></p>
                            </div>
                        <?php
                    }
                ?>
            </div>
        <?php
    }
}

/*
 * This is a function for configuration
 */
if ( ! function_exists( 'ocgfsf_configuration_callback' ) ) {
    function ocgfsf_configuration_callback() {
        
        if ( isset( $_POST['submit'] ) ) {
            $account = sanitize_text_field( $_POST['ocgfsf_account'] );
            update_option( 'ocgfsf_account', $account );
            
            $method = sanitize_text_field( $_POST['ocgfsf_method'] );
            update_option( 'ocgfsf_method', $method );
            
            $organization_id = sanitize_text_field( $_POST['ocgfsf_organization_id'] );
            update_option( 'ocgfsf_organization_id', $organization_id );
            
            $client_id = sanitize_text_field( $_POST['ocgfsf_client_id'] );
            update_option( 'ocgfsf_client_id', $client_id );
            
            $client_secret = sanitize_text_field( $_POST['ocgfsf_client_secret'] );
            update_option( 'ocgfsf_client_secret', $client_secret );
            
            $username = sanitize_text_field( $_POST['ocgfsf_username'] );
            update_option( 'ocgfsf_username', $username );
            
            $password = sanitize_text_field( $_POST['ocgfsf_password'] );
            if ( $password ) {
                update_option( 'ocgfsf_password', ocgfsf_crypt( $password, 'e', $client_secret ) );
            } else {
                $password = ocgfsf_crypt( get_option( 'ocgfsf_password' ), 'd', $client_secret );
            }
            
            $url = sanitize_text_field( $_POST['ocgfsf_url'] );
            update_option( 'ocgfsf_url', $url );
            
            if ( $method == 'api' ) {
                $salesforce = new OCGFSF_API( $client_id, $client_secret, $username, $password );
                $authentication = $salesforce->authentication();
                if ( isset( $authentication->error ) ) {
                    ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong><?php esc_html_e( 'Error:', 'ocgfsf' ); ?></strong> <?php echo $authentication->error; ?></p>
                            <p><strong><?php esc_html_e( 'Error Description:', 'ocgfsf' ); ?></strong> <?php echo $authentication->error_description; ?></p>
                        </div>
                    <?php
                } else if ( $authentication != null ) {
                    $modules = get_option( 'ocgfsf_modules' );
                    $modules = unserialize( $modules );
                    $modules_fields = array();
                    if ( $modules != null ) {
                        foreach ( $modules as $key => $value ) {
                            $fields = $salesforce->getModuleFields( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $key );
                            asort( $fields );
                            $modules_fields[$key] = $fields;
                        }
                    }
                    
                    $modules_fields = serialize( $modules_fields );
                    update_option( 'ocgfsf_modules', $modules_fields );
                    
                    $campaigns = array();
                    $records = $salesforce->getRecords( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'Campaign', array( 'Id', 'Name' ), '' );
                    if ( isset( $records->records ) && $records->records != null ) {
                        foreach ( $records->records as $record ) {
                            $campaigns[$record->Id] = $record->Name;
                        }
                    }
                    
                    update_option( 'ocgfsf_campaigns', $campaigns );
                    ?>
                        <div class="notice notice-success is-dismissible">
                            <p><?php esc_html_e( 'Configuration successful.', 'ocgfsf' ); ?></p>
                        </div>
                    <?php
                }
            }
        } else if ( isset( $_POST['add_custom_field'] ) ) {
            $field_object = sanitize_text_field( $_POST['field_object'] );
            $field_label = sanitize_text_field( $_POST['field_label'] );
            $field_name = sanitize_text_field( $_POST['field_name'] );
            $field_type = sanitize_text_field( $_POST['field_type'] );
            $sf_fields = get_option( 'ocgfsf_webto_modules' );
            $sf_fields = unserialize( $sf_fields );
            $sf_fields[$field_object][$field_name] = array(
                'label'     => $field_label,
                'type'      => $field_type,
                'required'  => 0,
            );
            $sf_fields = serialize( $sf_fields );
            update_option( 'ocgfsf_webto_modules', $sf_fields );
        } else if ( isset( $_REQUEST['trash'] ) && $_REQUEST['trash'] ) {
            $sf_fields = get_option( 'ocgfsf_webto_modules' );
            $sf_fields = unserialize( $sf_fields );
            
            unset( $sf_fields[$_REQUEST['module']][$_REQUEST['field']] );

            $sf_fields = serialize( $sf_fields );
            update_option( 'ocgfsf_webto_modules', $sf_fields );
        } else {
            //
        }
        
        $account = get_option( 'ocgfsf_account' );
        if ( ! $account ) {
            $account = 'production';
        }
        
        $method = get_option( 'ocgfsf_method' );
        $organization_id = get_option( 'ocgfsf_organization_id' );
        $client_id = get_option( 'ocgfsf_client_id' );
        $client_secret = get_option( 'ocgfsf_client_secret' );
        $username = get_option( 'ocgfsf_username' );
        $url = get_option( 'ocgfsf_url' );
        $licence = get_site_option( 'ocgfsf_licence' );
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Salesforce CRM Configuration', 'ocgfsf' ); ?></h1>
                <hr>
                <?php
                    if ( $licence ) {
                        ?>
                            <form method="post">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label><?php esc_html_e( 'Integration Method', 'ocgfsf' ); ?></label></th>
                                            <td>
                                                <fieldset>
                                                    <label><input type="radio" name="ocgfsf_method" value="api"<?php echo ( $method == 'api' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'API', 'ocgfsf' ); ?></label>&nbsp;&nbsp;
                                                    <label><input type="radio" name="ocgfsf_method" value="webto"<?php echo ( $method == 'webto' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Web-to-Lead or Web-to-Case', 'ocgfsf' ); ?></label>
                                                </fieldset>
                                                <p class="description"><?php esc_html_e( 'API: Enterprise Edition, Unlimited Edition, Developer Edition and Performance Edition.', 'ocgfsf' ); ?><br><?php esc_html_e( 'Web-to-Lead or Web-to-Case: Professional Edition and Essential Edition.', 'ocgfsf' ); ?></p>
                                                <p class="description"><a href="https://help.salesforce.com/articleView?id=000326486&type=1&mode=1" target="_blank"><?php esc_html_e( 'Click here', 'ocgfsf' ); ?></a> <?php esc_html_e( 'more details.', 'ocgfsf' ); ?></p>
                                            </td>
                                        </tr>
                                        <tr id="ocgfsf_account">
                                            <th scope="row"><label><?php esc_html_e( 'Salesforce CRM Environment', 'ocgfsf' ); ?></label></th>
                                            <td>
                                                <fieldset>
                                                    <label><input type="radio" name="ocgfsf_account" value="production"<?php echo ( $account == 'production' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Production', 'ocgfsf' ); ?></label>&nbsp;&nbsp;
                                                    <label><input type="radio" name="ocgfsf_account" value="sandbox"<?php echo ( $account == 'sandbox' ? ' checked="checked"' : '' ); ?> /> <?php esc_html_e( 'Sandbox', 'ocgfsf' ); ?></label>
                                                </fieldset>
                                            </td>
                                        </tr>
                                        <tr id="ocgfsf_client_id" style="display: <?php echo ( $method == 'api' ? 'table-row' : 'none' ); ?>">
                                            <th scope="row"><label><?php esc_html_e( 'Consumer Key', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                            <td>
                                                <input class="regular-text" type="text" name="ocgfsf_client_id" value="<?php echo $client_id; ?>" />
                                            </td>
                                        </tr>
                                        <tr id="ocgfsf_client_secret" style="display: <?php echo ( $method == 'api' ? 'table-row' : 'none' ); ?>">
                                            <th scope="row"><label><?php esc_html_e( 'Consumer Secret', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                            <td>
                                                <input class="regular-text" type="text" name="ocgfsf_client_secret" value="<?php echo $client_secret; ?>" />
                                            </td>
                                        </tr>
                                        <tr id="ocgfsf_username" style="display: <?php echo ( $method == 'api' ? 'table-row' : 'none' ); ?>">
                                            <th scope="row"><label><?php esc_html_e( 'Username', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                            <td>
                                                <input class="regular-text" type="text" name="ocgfsf_username" value="<?php echo $username; ?>" />
                                            </td>
                                        </tr>
                                        <tr id="ocgfsf_password" style="display: <?php echo ( $method == 'api' ? 'table-row' : 'none' ); ?>">
                                            <th scope="row"><label><?php esc_html_e( 'Password', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                            <td>
                                                <input class="regular-text" type="password" name="ocgfsf_password" value="" />
                                            </td>
                                        </tr>
                                        <tr id="ocgfsf_organization_id" style="display: <?php echo ( $method == 'webto' ? 'table-row' : 'none' ); ?>">
                                            <th scope="row"><label><?php esc_html_e( 'Organization ID', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                            <td>
                                                <input class="regular-text" type="text" name="ocgfsf_organization_id" value="<?php echo $organization_id; ?>" />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p>
                                    <input type="hidden" name="ocgfsf_url" value="<?php echo $url; ?>" />
                                    <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Save Changes', 'ocgfsf' ); ?>" />
                                </p>
                            </form>
                            <div id="ocgfsf_custom_fields" style="display: <?php echo ( $method == 'webto' ? 'table-row' : 'none' ); ?>">
                                <br>
                                <br>
                                <h2><?php esc_html_e( 'Custom Fields', 'ocgfsf' ); ?></h2>
                                <hr>
                                <form method="post">
                                    <table class="form-table">
                                        <tbody>
                                            <tr>
                                                <th scope="row"><label><?php esc_html_e( 'Object', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                                <td>
                                                    <select name="field_object" required>
                                                        <option value=""><?php esc_html_e( 'Select an object', 'ocgfsf' ); ?></option>
                                                        <option value="Case"><?php esc_html_e( 'Case', 'ocgfsf' ); ?></option>
                                                        <option value="Lead"><?php esc_html_e( 'Lead', 'ocgfsf' ); ?></option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label><?php esc_html_e( 'Field Label', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                                <td>
                                                    <input class="regular-text" type="text" name="field_label" value="" required />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label><?php esc_html_e( 'Field Name', 'ocgfsf' ); ?> <span class="description">(required)</span></label></th>
                                                <td>
                                                    <input class="regular-text" type="text" name="field_name" value="" required />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><label><?php esc_html_e( 'Field Type', 'ocgfsf' ); ?></label></th>
                                                <td>
                                                    <input class="regular-text" type="text" name="field_type" value="" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p>
                                        <input type='submit' class='button-primary' name="add_custom_field" value="<?php esc_html_e( 'Add Custom Field', 'ocgfsf' ); ?>" />
                                    </p>
                                </form>
                                <?php
                                    $page_url = menu_page_url( 'ocgfsf_configuration', 0 );
                                    $sf_fields = get_option( 'ocgfsf_webto_modules' );
                                    $sf_fields = unserialize( $sf_fields );
                                    if ( $sf_fields != null ) {
                                        ?>
                                            <br><br>
                                            <h3><?php esc_html_e( 'Case', 'ocgfsf' ); ?></h3>
                                            <hr>
                                            <table class="widefat striped">
                                                <thead>
                                                    <tr>
                                                        <th><?php esc_html_e( 'Field Label', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Field Name', 'ocgfsf' ); ?></th>       
                                                        <th><?php esc_html_e( 'Field Type', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Action', 'ocgfsf' ); ?></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th><?php esc_html_e( 'Field Label', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Field Name', 'ocgfsf' ); ?></th>       
                                                        <th><?php esc_html_e( 'Field Type', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Action', 'ocgfsf' ); ?></th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                        foreach ( $sf_fields['Case'] as $sf_field_name => $sf_field ) {
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $sf_field['label']; ?></td>
                                                                    <td><?php echo $sf_field_name; ?></td>
                                                                    <td><?php echo $sf_field['type']; ?></td>
                                                                    <td><a href="<?php echo $page_url; ?>&trash=1&module=Case&field=<?php echo $sf_field_name; ?>"><span class="dashicons dashicons-trash"></span></a></td>
                                                                </tr>
                                                            <?php
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                            <br>
                                            <h3><?php esc_html_e( 'Lead', 'ocgfsf' ); ?></h3>
                                            <hr>
                                            <table class="widefat striped">
                                                <thead>
                                                    <tr>
                                                        <th><?php esc_html_e( 'Field Label', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Field Name', 'ocgfsf' ); ?></th>       
                                                        <th><?php esc_html_e( 'Field Type', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Action', 'ocgfsf' ); ?></th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th><?php esc_html_e( 'Field Label', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Field Name', 'ocgfsf' ); ?></th>       
                                                        <th><?php esc_html_e( 'Field Type', 'ocgfsf' ); ?></th>
                                                        <th><?php esc_html_e( 'Action', 'ocgfsf' ); ?></th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php
                                                        foreach ( $sf_fields['Lead'] as $sf_field_name => $sf_field ) {
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $sf_field['label']; ?></td>
                                                                    <td><?php echo $sf_field_name; ?></td>
                                                                    <td><?php echo $sf_field['type']; ?></td>
                                                                    <td><a href="<?php echo $page_url; ?>&trash=1&module=Lead&field=<?php echo $sf_field_name; ?>"><span class="dashicons dashicons-trash"></span></a></td>
                                                                </tr>
                                                            <?php
                                                        }
                                                    ?>
                                                </tbody>
                                            </table>
                                        <?php
                                    }
                                ?>
                            </div>
                            <script>
                                jQuery( document ).ready( function( $ ) {
                                    $( 'input[name="ocgfsf_method"]' ).on( 'change', function() {
                                        var method = $( this ).val();
                                        if ( method == 'api' ) {
                                            $( '#ocgfsf_client_id' ).show();
                                            $( '#ocgfsf_client_secret' ).show();
                                            $( '#ocgfsf_username' ).show();
                                            $( '#ocgfsf_password' ).show();

                                            $( '#ocgfsf_organization_id' ).hide();
                                            $( '#ocgfsf_custom_fields' ).hide();
                                        } else {
                                            $( '#ocgfsf_organization_id' ).show();
                                            $( '#ocgfsf_custom_fields' ).show();
                                            
                                            $( '#ocgfsf_client_id' ).hide();
                                            $( '#ocgfsf_client_secret' ).hide();
                                            $( '#ocgfsf_username' ).hide();
                                            $( '#ocgfsf_password' ).hide();
                                        }
                                    });
                                });
                            </script>
                        <?php
                    } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p><?php esc_html_e( 'Please verify purchase code.', 'ocgfsf' ); ?></p>
                            </div>
                        <?php
                    }
                ?>
            </div>
        <?php
    }
}

/*
 * This is a function for api error logs
 */
if ( ! function_exists( 'ocgfsf_api_error_logs_callback' ) ) {
    function ocgfsf_api_error_logs_callback() {
        
        $file_path = OCGFSF_PRO_PLUGIN_PATH.'debug.log';
        if ( isset( $_POST['submit'] ) ) {
            $file = fopen( $file_path, 'w' );
            fclose( $file );
        }
        
        $licence = get_site_option( 'ocgfsf_licence' );
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Salesforce CRM API Error Logs', 'ocgfsf' ); ?></h1>
                <hr>
                <?php
                    if ( $licence ) {
                        $file = fopen( $file_path, 'r' );
                            $file_size = filesize( $file_path );
                            if ( $file_size ) {
                                $file_data = fread( $file, $file_size );
                                if ( $file_data ) {
                                    echo '<pre style="overflow: scroll;">'; print_r( $file_data ); echo '</pre>';
                                    ?>
                                        <form method="post">
                                            <p>
                                                <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Clear API Error Logs', 'ocgfsf' ); ?>" />
                                            </p>
                                        </form>
                                    <?php
                                }
                            } else {
                                ?><p><?php esc_html_e( 'No API error logs found.', 'ocgfsf' ); ?></p><?php
                            }
                        fclose( $file );
                    } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p><?php esc_html_e( 'Please verify purchase code.', 'ocgfsf' ); ?></p>
                            </div>
                        <?php
                    }
                ?>
            </div>
        <?php
    }
}

/*
 * This is a function for settings
 */
if ( ! function_exists( 'ocgfsf_settings_callback' ) ) {
    function ocgfsf_settings_callback() {
        
        if ( isset( $_POST['submit'] ) ) {
            $notification_subject = sanitize_text_field( $_POST['ocgfsf_notification_subject'] );
            update_option( 'ocgfsf_notification_subject', $notification_subject );
            
            $notification_send_to = sanitize_text_field( $_POST['ocgfsf_notification_send_to'] );
            update_option( 'ocgfsf_notification_send_to', $notification_send_to );
            
            $ignore_spam_entry = (int) $_POST['ocgfsf_ignore_spam_entry'];
            update_option( 'ocgfsf_ignore_spam_entry', $ignore_spam_entry );

            $uninstall = (int) $_POST['ocgfsf_uninstall'];
            update_option( 'ocgfsf_uninstall', $uninstall );
        }
        
        $notification_subject = get_option( 'ocgfsf_notification_subject' );
        if ( ! $notification_subject ) {
            $notification_subject = esc_html__( 'API Error Notification', 'ocgfsf' );
        }
        $notification_send_to = get_option( 'ocgfsf_notification_send_to' );
        $uninstall = get_option( 'ocgfsf_uninstall' );
        $ignore_spam_entry = get_option( 'ocgfsf_ignore_spam_entry' );
        $licence = get_site_option( 'ocgfsf_licence' );
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Settings', 'ocgfsf' ); ?></h1>
                <hr>
                <?php
                    if ( $licence ) {
                        ?>
                            <form method="post">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label><?php esc_html_e( 'API Error Notification', 'ocgfsf' ); ?></label></th>
                                            <td>
                                                <label><?php esc_html_e( 'Subject', 'ocgfsf' ); ?></label><br>
                                                <input class="regular-text" type="text" name="ocgfsf_notification_subject" value="<?php echo $notification_subject; ?>" />
                                                <p class="description"><?php esc_html_e( 'Enter the subject.', 'ocgfsf' ); ?></p><br><br>
                                                <label><?php esc_html_e( 'Send To', 'ocgfsf' ); ?></label><br>
                                                <input class="regular-text" type="text" name="ocgfsf_notification_send_to" value="<?php echo $notification_send_to; ?>" />
                                                <p class="description"><?php esc_html_e( 'Enter the email address. For multiple email addresses, you can add email address by comma separated.', 'ocgfsf' ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label><?php esc_html_e( 'Ignore spam entry?', 'ocgfsf' ); ?></label></th>
                                            <td>
                                                <input type="hidden" name="ocgfsf_ignore_spam_entry" value="0" />
                                                <input type="checkbox" name="ocgfsf_ignore_spam_entry" value="1"<?php echo ( $ignore_spam_entry ? ' checked' : '' ); ?> />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label><?php esc_html_e( 'Delete data on uninstall?', 'ocgfsf' ); ?></label></th>
                                            <td>
                                                <input type="hidden" name="ocgfsf_uninstall" value="0" />
                                                <input type="checkbox" name="ocgfsf_uninstall" value="1"<?php echo ( $uninstall ? ' checked' : '' ); ?> />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p>
                                    <input type='submit' class='button-primary' name="submit" value="<?php esc_html_e( 'Save Changes', 'ocgfsf' ); ?>" />
                                </p>
                            </form>
                        <?php
                    } else {
                        ?>
                            <div class="notice notice-error is-dismissible">
                                <p><?php esc_html_e( 'Please verify purchase code.', 'ocgfsf' ); ?></p>
                            </div>
                        <?php
                    }
                ?>
            </div>
        <?php
    }
}

/*
 * This is a function for licence verification
 */
if ( ! function_exists( 'ocgfsf_licence_verification_callback' ) ) {
    function ocgfsf_licence_verification_callback() {
        
        if ( isset( $_POST['ocgfsf_purchase_code'] ) && isset( $_POST['verify'] ) ) {
            update_site_option( 'ocgfsf_purchase_code', $_POST['ocgfsf_purchase_code'] );
            $data = array(
                'sku'           => '20174431',
                'purchase_code' => $_POST['ocgfsf_purchase_code'],
                'domain'        => site_url(),
                'status'        => 'verify',
            );
            $url = 'https://obtaincode.net/extension/';
            $args = array(
                'timeout'       => 30,
                'httpversion'   => '1.0',
                'body'          => $data,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_post( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            if ( $response->success ) {
                update_site_option( 'ocgfsf_licence', 1 );
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php echo $response->message; ?></p>
                        <?php
                            if ( isset( $response->activated ) && $response->activated ) {
                                ?><p><strong><?php esc_html_e( 'Activated', 'ocgfsf' ); ?></strong>: <?php echo $response->activated; ?></p><?php
                            }
                        ?>
                    </div>
                <?php
            } else {
                update_site_option( 'ocgfsf_licence', 0 );
                ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo $response->message; ?></p>
                        <?php
                            if ( isset( $response->activated ) && $response->activated ) {
                                ?><p><strong><?php esc_html_e( 'Activated', 'ocgfsf' ); ?></strong>: <?php echo $response->activated; ?></p><?php
                            }
                        ?>
                    </div>
                <?php
            }
        } else if ( isset( $_POST['ocgfsf_purchase_code'] ) && isset( $_POST['unverify'] ) ) {
            $data = array(
                'sku'           => '20174431',
                'purchase_code' => $_POST['ocgfsf_purchase_code'],
                'domain'        => site_url(),
                'status'        => 'unverify',
            );
            $url = 'https://obtaincode.net/extension/';
            $args = array(
                'timeout'       => 30,
                'httpversion'   => '1.0',
                'body'          => $data,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_post( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            if ( $response->success ) {
                update_site_option( 'ocgfsf_licence', 0 );
                update_site_option( 'ocgfsf_purchase_code', '' );
                ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php echo $response->message; ?></p>
                        <?php
                            if ( isset( $response->activated ) && $response->activated ) {
                                ?><p><strong><?php esc_html_e( 'Activated', 'ocgfsf' ); ?></strong>: <?php echo $response->activated; ?></p><?php
                            }
                        ?>
                    </div>
                <?php
            } else {
                ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php echo $response->message; ?></p>
                        <?php
                            if ( isset( $response->activated ) && $response->activated ) {
                                ?><p><strong><?php esc_html_e( 'Activated', 'ocgfsf' ); ?></strong>: <?php echo $response->activated; ?></p><?php
                            }
                        ?>
                    </div>
                <?php
            }
        }
        
        $purchase_code = get_site_option( 'ocgfsf_purchase_code' );
        ?>
            <div class="wrap">
                <h2><?php esc_html_e( 'Licence Verification', 'ocgfsf' ); ?></h2>
                <hr>
                <form method="post">
                    <table class="form-table">                    
                        <tbody>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Purchase Code', 'ocgfsf' ); ?></th>
                                <td>
                                    <input name="ocgfsf_purchase_code" type="text" class="regular-text" value="<?php echo $purchase_code; ?>" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p>
                        <input type='submit' class='button-primary' name="verify" value="<?php esc_html_e( 'Verify', 'ocgfsf' ); ?>" />
                        <input type='submit' class='button-primary' name="unverify" value="<?php esc_html_e( 'Unverify', 'ocgfsf' ); ?>" />
                    </p>
                </form>
            </div>
        <?php
    }
}