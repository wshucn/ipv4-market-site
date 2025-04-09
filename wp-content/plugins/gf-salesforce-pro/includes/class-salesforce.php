<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a class for Salesforce CRM API
 */
if ( ! class_exists( 'OCGFSF_API' ) ) {
    class OCGFSF_API {
        
        var $client_id;
        var $client_secret;
        var $username;
        var $password;
        
        function __construct( $client_id, $client_secret, $username, $password ) {
            
            $this->client_id = $client_id;
            $this->client_secret = $client_secret;
            $this->username = $username;
            $this->password = $password;
        }
        
        function authentication() {
            
            $account = get_option( 'ocgfsf_account' );
            if ( $account == 'sandbox' ) {
                $token_url = 'https://test.salesforce.com/services/oauth2/token';
            } else {
                $url = get_option( 'ocgfsf_url' );
                if ( $url ) {
                    $token_url = $url.'services/oauth2/token';
                } else {
                    $token_url = 'https://login.salesforce.com/services/oauth2/token';
                }
            }
            
            $data = array(
                'grant_type'    => 'password',
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'username'      => $this->username,
                'password'      => $this->password,
            );
            $url = $token_url;
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
            if ( isset( $response->error ) ) {
                $log = "errorCode: ".$response->error."\n";
                $log .= "message: ".$response->error_description."\n";
                $log .= "Date: ".date( 'Y-m-d H:i:s' )."\n\n";
                
                $send_to = get_option( 'ocgfsf_notification_send_to' );
                if ( $send_to ) {
                    $to = $send_to;
                    $subject = get_option( 'ocgfsf_notification_subject' );
                    $body = "errorCode: ".$response->error."<br>";
                    $body .= "message: ".$response->error_description."<br>";
                    $body .= "Date: ".date( 'Y-m-d H:i:s' );
                    $headers = array(
                        'Content-Type: text/html; charset=UTF-8',
                    );
                    wp_mail( $to, $subject, $body, $headers );
                }
                
                file_put_contents( OCGFSF_PRO_PLUGIN_PATH.'debug.log', $log, FILE_APPEND );
            }
            
            return $response;
        }
        
        function addRecord( $instance_url, $token_type, $access_token, $module, $data, $form_id ) {
            
            $url = $instance_url.'/services/data/v45.0/sobjects/'.$module;
            $header = array(
                'Authorization' => "$token_type $access_token",
                'Content-Type'  => 'application/json',
            );
            $data = json_encode( $data );
            $args = array(
                'timeout'       => 30,
                'httpversion'   => '1.0',
                'headers'       => $header,
                'body'          => $data,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_post( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            if ( ! isset( $response->id ) && isset( $response[0] ) ) {
                $log = "Form ID: ".$form_id."\n";
                $log .= "errorCode: ".$response[0]->errorCode."\n";
                $log .= "message: ".$response[0]->message."\n";
                $log .= "fields: ".implode( ',', ( isset( $response[0]->fields ) ? $response[0]->fields : array() ) )."\n";
                $log .= "Date: ".date( 'Y-m-d H:i:s' )."\n\n";
                
                $send_to = get_option( 'ocgfsf_notification_send_to' );
                if ( $send_to ) {
                    $to = $send_to;
                    $subject = get_option( 'ocgfsf_notification_subject' );
                    $body = "Form ID: ".$form_id."<br>";
                    $body .= "errorCode: ".$response[0]->errorCode."<br>";
                    $body .= "message: ".$response[0]->message."<br>";
                    $body .= "fields: ".implode( ',', ( isset( $response[0]->fields ) ? $response[0]->fields : array() ) )."<br>";
                    $body .= "Date: ".date( 'Y-m-d H:i:s' );
                    $headers = array(
                        'Content-Type: text/html; charset=UTF-8',
                    );
                    wp_mail( $to, $subject, $body, $headers );
                }
                
                file_put_contents( OCGFSF_PRO_PLUGIN_PATH.'debug.log', $log, FILE_APPEND );
            }
            
            return $response;
        }
        
        function updateRecord( $instance_url, $token_type, $access_token, $module, $data, $record_id, $form_id ) {
            
            $url = $instance_url.'/services/data/v45.0/sobjects/'.$module.'/'.$record_id;
            $header = array(
                'Authorization' => "$token_type $access_token",
                'Content-Type'  => 'application/json',
            );
            $data = json_encode( $data );
            $args = array(
                'method'        => 'PATCH',
                'timeout'       => 30,
                'httpversion'   => '1.0',
                'headers'       => $header,
                'body'          => $data,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_post( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            if ( ! isset( $response->id ) && isset( $response[0] ) ) {
                $log = "Form ID: ".$form_id."\n";
                $log .= "errorCode: ".$response[0]->errorCode."\n";
                $log .= "message: ".$response[0]->message."\n";
                $log .= "fields: ".implode( ',', ( isset( $response[0]->fields ) ? $response[0]->fields : array() ) )."\n";
                $log .= "Date: ".date( 'Y-m-d H:i:s' )."\n\n";
                
                $send_to = get_option( 'ocgfsf_notification_send_to' );
                if ( $send_to ) {
                    $to = $send_to;
                    $subject = get_option( 'ocgfsf_notification_subject' );
                    $body = "Form ID: ".$form_id."<br>";
                    $body .= "errorCode: ".$response[0]->errorCode."<br>";
                    $body .= "message: ".$response[0]->message."<br>";
                    $body .= "fields: ".implode( ',', ( isset( $response[0]->fields ) ? $response[0]->fields : array() ) )."<br>";
                    $body .= "Date: ".date( 'Y-m-d H:i:s' );
                    $headers = array(
                        'Content-Type: text/html; charset=UTF-8',
                    );
                    wp_mail( $to, $subject, $body, $headers );
                }
                
                file_put_contents( OCGFSF_PRO_PLUGIN_PATH.'debug.log', $log, FILE_APPEND );
            }
            
            return $response;
        }
        
        function getRecords( $instance_url, $token_type, $access_token, $module, $fields, $where = '' ) {
            
            $query = 'SELECT+'.implode( ',', $fields ).'+FROM+'.$module;
            if ( $where ) {
                $query .= '+WHERE+'.$where;
            }
            
            $url = $instance_url.'/services/data/v45.0/query/?q='.$query;
            $header = array(
                'Authorization' => "$token_type $access_token",
                'Content-Type'  => 'application/json',
            );
            $args = array(
                'timeout'       => 30,
                'httpversion'   => '1.0',
                'headers'       => $header,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_get( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            
            return $response;
        }
        
        function getModuleFields( $instance_url, $token_type, $access_token, $module ) {
            
            $url = $instance_url.'/services/data/v45.0/sobjects/'.$module.'/describe';
            $header = array(
                'Authorization' => "$token_type $access_token",
                'Content-Type'  => 'application/json',
            );
            $args = array(
                'timeout'       => 30,
                'httpversion'   => '1.0',
                'headers'       => $header,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_get( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            if ( ! isset( $response->fields ) && isset( $response[0] ) ) {
                $log = "errorCode: ".$response[0]->errorCode."\n";
                $log .= "message: ".$response[0]->message."\n";
                $log .= "Date: ".date( 'Y-m-d H:i:s' )."\n\n";
                
                $send_to = get_option( 'ocgfsf_notification_send_to' );
                if ( $send_to ) {
                    $to = $send_to;
                    $subject = get_option( 'ocgfsf_notification_subject' );
                    $body = "errorCode: ".$response[0]->errorCode."<br>";
                    $body .= "message: ".$response[0]->message."<br>";
                    $body .= "Date: ".date( 'Y-m-d H:i:s' );
                    $headers = array(
                        'Content-Type: text/html; charset=UTF-8',
                    );
                    wp_mail( $to, $subject, $body, $headers );
                }
                
                file_put_contents( OCGFSF_PRO_PLUGIN_PATH.'debug.log', $log, FILE_APPEND );
            }
            
            $fields = array();
            if ( isset( $response->fields ) && $response->fields != null ) {
                $fields = $response->fields;
            }
            
            $filter_fields = array();
            if ( $fields != null ) {
                foreach( $fields as $field ) {
                    if ( $field->createable ) {
                        $filter_fields[$field->name] = array(
                            'label'     => $field->label,
                            'type'      => $field->type,  
                            'required'  => 1,
                        );
                        
                        if ( $field->nillable ) {
                            $filter_fields[$field->name]['required'] = 0;
                        }
                    }
                }
                
                $filter_fields['attachment_field'] = array(
                    'label'     => 'Files',
                    'type'      => 'relate',
                    'required'  => 0,
                );
            }
            
            return $filter_fields;
        }
        
        function addFile( $instance_url, $token_type, $access_token, $data ) {
            
            $url = $instance_url.'/services/data/v45.0/sobjects/ContentVersion';
            $header = array(
                'Authorization' => "$token_type $access_token",
                'Content-Type'  => 'multipart/form-data; boundary=add_file',
            );
            $post_data = '--add_file
Content-Disposition: form-data; name="entity_document";
Content-Type: application/json

{  
    
    "ReasonForChange" : "'.$data['Name'].'",
    "PathOnClient" : "'.$data['Name'].'"
}

--add_file
Content-Type: '.$data['Type'].'
Content-Disposition: form-data; name="VersionData"; filename="'.$data['Name'].'"

'.$data['Body'].'

--add_file--';
            $args = array(
                'timeout'       => 60,
                'httpversion'   => '1.0',
                'headers'       => $header,
                'body'          => $post_data,
                'sslverify'     => false,
            );
            $wp_remote_response = wp_remote_post( $url, $args );
            $json_response = '';
            if ( ! is_wp_error( $wp_remote_response ) ) {
                $json_response = $wp_remote_response['body'];
            }
            
            $response = json_decode( $json_response );
            
            return $response;
        }
    }
}