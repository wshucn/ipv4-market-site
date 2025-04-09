<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that crypt data
 * $string variable return original data
 * $action variable return crypt type
 * $secret variable return secret data
 */
if ( ! function_exists( 'ocgfsf_crypt' ) ) {
    function ocgfsf_crypt( $string, $action, $secret ) {
        
        if ( ! $action ) {
            $action = 'e';
        }
        
        if ( extension_loaded( 'openssl' ) ) {
            $secret_key = $secret.'gf_sf_key';
            $secret_iv = $secret.'gf_sf_iv';

            $output = false;
            $encrypt_method = 'AES-256-CBC';
            $key = hash( 'sha256', $secret_key );
            $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

            if( $action == 'e' ) {
                $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
            }
            else if( $action == 'd' ){
                $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
            }

            return $output;
        } else {
            return $string;
        }
    }
}

/*
 * This is a function that integrate form
 * $entry variable return entry data
 * $form variable return form data
 */
if ( ! function_exists( 'ocgfsf_integration' ) ) {
    add_action( 'gform_after_submission', 'ocgfsf_integration', 20, 2 );
    function ocgfsf_integration( $entry, $form ) {
        
        $licence = get_site_option( 'ocgfsf_licence' );
        if ( $licence ) {
            $posted_data = $entry;

            $form_id = 0;
            if ( isset( $form['id'] ) ) {
                $form_id = intval( $form['id'] );
            }

            $entry_id = 0;
            if ( isset( $posted_data['id'] ) ) {
                $entry_id = intval( $posted_data['id'] );
            }
            
            $not_ignore_spam_entry = 1;
            $ignore_spam_entry = get_option( 'ocgfsf_ignore_spam_entry' );
            if ( $ignore_spam_entry ) {
                if ( $posted_data['status'] == 'spam' ) {
                    $not_ignore_spam_entry = 0;
                }
            }
            
            if ( $form_id && $not_ignore_spam_entry ) {
                $ocgfsf = get_option( 'ocgfsf_'.$form_id );
                if ( $ocgfsf ) {
                    $gf_sf_fields = get_option( 'ocgfsf_fields_'.$form_id );
                    if ( $gf_sf_fields != null ) {
                        $data = array();
                        $attachment_fields = array();
                        foreach ( $gf_sf_fields as $gf_field_name => $gf_sf_field ) {
                            if ( isset( $gf_sf_field['key'] ) && $gf_sf_field['key'] ) {
                                $sf_field_name = $gf_sf_field['key'];
                                if ( isset( $gf_sf_field['type'] ) && $gf_sf_field['type'] == 'boolean' ) {
                                    if ( isset( $posted_data[$gf_field_name] ) ) {
                                        if ( $posted_data[$gf_field_name] == '1' || $posted_data[$gf_field_name] == 'True' ) {
                                            $posted_data[$gf_field_name] = 'true';
                                        } else {
                                            $posted_data[$gf_field_name] = 'false';
                                        }
                                    }
                                } else if ( isset( $gf_sf_field['type'] ) && $gf_sf_field['type'] == 'date' ) {
                                    if ( $posted_data[$gf_field_name] ) {
                                        $posted_data[$gf_field_name] = date( 'Y-m-d', strtotime( $posted_data[$gf_field_name] ) );
                                    }
                                } else if ( isset( $gf_sf_field['type'] ) && $gf_sf_field['type'] == 'datetime' ) {
                                    $posted_data[$gf_field_name] = date( 'c', strtotime( $posted_data[$gf_field_name] ) );
                                } else if ( isset( $gf_sf_field['type'] ) && $gf_sf_field['type'] == 'int' ) {
                                    $posted_data[$gf_field_name] = (int) $posted_data[$gf_field_name];
                                } else if ( isset( $gf_sf_field['type'] ) && $gf_sf_field['type'] == 'multiselect' ) {
                                    $posted_data[$gf_field_name] = json_decode( $posted_data[$gf_field_name] );
                                }

                                if ( isset( $gf_sf_field['field_type'] ) && $gf_sf_field['field_type'] == 'checkbox' ) {
                                    $posted_data[$gf_field_name] = array();
                                    for( $i = 1; $i <= 20; $i++ ) {
                                        if ( isset( $posted_data[$gf_field_name.'.'.$i] ) ) {
                                            if ( $posted_data[$gf_field_name.'.'.$i] ) {
                                                $posted_data[$gf_field_name][] = $posted_data[$gf_field_name.'.'.$i];
                                            }
                                        }
                                    }
                                } else if ( isset( $gf_sf_field['field_type'] ) && $gf_sf_field['field_type'] == 'date' ) {
                                    if ( $posted_data[$gf_field_name] ) {
                                        $posted_data[$gf_field_name] = date( 'Y-m-d', strtotime( $posted_data[$gf_field_name] ) );
                                    }
                                }

                                if ( is_array( $posted_data[$gf_field_name] ) ) {
                                    $posted_data[$gf_field_name] = implode( ';', $posted_data[$gf_field_name] );
                                }

                                if ( $sf_field_name == 'attachment_field' ) {
                                    $attachment_fields[] = $gf_field_name;
                                } else {
                                    if ( isset( $gf_sf_field['field_type'] ) && $gf_sf_field['field_type'] == 'date' && ! $posted_data[$gf_field_name] ) {
                                        //
                                    } else {
                                        $data[$sf_field_name] = ( isset( $posted_data[$gf_field_name] ) ? strip_tags( $posted_data[$gf_field_name] ) : '' );
                                    }
                                }
                            }
                        }
                        
                        if ( $data != null ) {
                            $module = get_option( 'ocgfsf_module_'.$form_id );
                            $method = get_option( 'ocgfsf_method' );
                            if ( $method == 'api' ) {
                                $client_id = get_option( 'ocgfsf_client_id' );
                                $client_secret = get_option( 'ocgfsf_client_secret' );
                                $username = get_option( 'ocgfsf_username' );
                                $password = ocgfsf_crypt( get_option( 'ocgfsf_password' ), 'd', $client_secret );
                                $salesforce = new OCGFSF_API( $client_id, $client_secret, $username, $password );
                                $authentication = $salesforce->authentication();
                                if ( ! isset( $authentication->error ) ) {
                                    $ids = array();
                                    $record_id_for_campaign = 0;
                                    $action = get_option( 'ocgfsf_action_'.$form_id );
                                    if ( $action == 'create' ) {
                                        $record = $salesforce->addRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $module, $data, $form_id );
                                        if ( isset( $record->id ) ) {
                                            $ids[] = $record->id;
                                            $record_id_for_campaign = $record->id;
                                        }
                                    } else if ( $action == 'create_or_update' ) {
                                        $email = ( isset( $data['Email'] ) ? $data['Email'] : '' );
                                        if ( $module == 'Case' ) {
                                            $email = ( isset( $data['SuppliedEmail'] ) ? $data['SuppliedEmail'] : '' );
                                        }

                                        if ( $email ) {
                                            if ( $module == 'Case' ) {
                                                $records = $salesforce->getRecords( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $module, array( 'Id' ), "SuppliedEmail='$email'" );
                                            } else {
                                                $records = $salesforce->getRecords( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $module, array( 'Id' ), "Email='$email'" );
                                            }

                                            if ( isset( $records->records ) && $records->records != null ) {
                                                foreach ( $records->records as $record ) {
                                                    $ids[] = $record->Id;
                                                    $record_id = $record->Id;
                                                    $salesforce->updateRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $module, $data, $record_id, $form_id );
                                                }
                                            } else {
                                                $record = $salesforce->addRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $module, $data, $form_id );
                                                if ( isset( $record->id ) ) {
                                                    $ids[] = $record->id;
                                                    $record_id_for_campaign = $record->id;
                                                }
                                            }
                                        } else {
                                            $record = $salesforce->addRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $module, $data, $form_id );
                                            if ( isset( $record->id ) ) {
                                                $ids[] = $record->id;
                                                $record_id_for_campaign = $record->id;
                                            }
                                        }
                                    }

                                    $campaign = get_option( 'ocgfsf_campaign_'.$form_id );
                                    if ( $campaign && $record_id_for_campaign && ( $module == 'Contact' || $module == 'Lead' ) ) {
                                        foreach ( $ids as $id ) {
                                            $campaign_member_data = array(
                                                'CampaignId'    => $campaign,
                                                $module.'Id'    => $id,
                                            );
                                            $salesforce->addRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'CampaignMember', $campaign_member_data, $form_id );
                                        }
                                    }

                                    if ( $attachment_fields != null && $ids != null ) {
                                        foreach ( $ids as $id ) {
                                            foreach ( $attachment_fields as $attachment_field ) {
                                                $files = json_decode( $posted_data[$attachment_field] );
                                                if ( $files != null ) {
                                                    foreach ( $files as $file ) {
                                                        if ( $file ) {
                                                            $file = str_replace( site_url( '/' ), ABSPATH, $file );
                                                            $attachment_data = array(
                                                                'Name'      => basename( $file ),
                                                                'Type'      => mime_content_type( $file ),
                                                                'Body'      => file_get_contents( $file ),
                                                            );
                                                            $file_record = $salesforce->addFile( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $attachment_data );
                                                            if ( isset( $file_record->id ) ) {
                                                                $records = $salesforce->getRecords( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'ContentVersion', array( 'ContentDocumentId' ), "Id='$file_record->id'" );
                                                                if ( isset( $records->records ) && $records->records != null ) {
                                                                    $data = array(
                                                                        'ContentDocumentId' => $records->records[0]->ContentDocumentId,
                                                                        'LinkedEntityId'    => $id,
                                                                        'Visibility'        => 'AllUsers',
                                                                    );
                                                                    
                                                                    $salesforce->addRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'ContentDocumentLink', $data, $form_id );
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $file = $posted_data[$attachment_field];
                                                    if ( $file ) {
                                                        $file = str_replace( site_url( '/' ), ABSPATH, $file );
                                                        $attachment_data = array(
                                                            'Name'      => basename( $file ),
                                                            'Type'      => mime_content_type( $file ),
                                                            'Body'      => file_get_contents( $file ),
                                                        );
                                                        $file_record = $salesforce->addFile( $authentication->instance_url, $authentication->token_type, $authentication->access_token, $attachment_data );
                                                        if ( isset( $file_record->id ) ) {
                                                            $records = $salesforce->getRecords( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'ContentVersion', array( 'ContentDocumentId' ), "Id='$file_record->id'" );
                                                            if ( isset( $records->records ) && $records->records != null ) {
                                                                $data = array(
                                                                    'ContentDocumentId' => $records->records[0]->ContentDocumentId,
                                                                    'LinkedEntityId'    => $id,
                                                                    'Visibility'        => 'AllUsers',
                                                                );
                                                                
                                                                $salesforce->addRecord( $authentication->instance_url, $authentication->token_type, $authentication->access_token, 'ContentDocumentLink', $data, $form_id );
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $organization_id = get_option( 'ocgfsf_organization_id' );
                                $data['oid'] = $organization_id;
                                $account = get_option( 'ocgfsf_account' );
                                if ( $account == 'sandbox' ) {
                                    $url = 'https://test.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
                                    if ( $module == 'Case' ) {
                                        $url = 'https://test.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8';
                                        $data['orgid'] = $organization_id;
                                        unset( $data['oid'] );
                                    }
                                } else {
                                    $url = 'https://webto.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';
                                    if ( $module == 'Case' ) {
                                        $url = 'https://webto.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8';
                                        $data['orgid'] = $organization_id;
                                        unset( $data['oid'] );
                                    }
                                }

                                $args = array(
                                    'timeout'       => 30,
                                    'httpversion'   => '1.0',
                                    'body'          => $data,
                                    'sslverify'     => false,
                                );
                                wp_remote_post( $url, $args );
                            }
                        }
                    }
                }
            }
        }
    }
}