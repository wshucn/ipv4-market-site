<?php
add_action('acf/init', 'mp_acf_contact');
function mp_acf_contact() {
    /* Email address(es). */
    $contact_email = get_field( 'email_address', 'options');

    if($contact_email) {
        $site_variables['email'] = array(
            $contact_email
        );
    }

    /**
     * Business telephone humber(s).
     *
     * full number => link text format
     *
     * Full number will appear after tel: in the link href attribute.
     * Common link text formats: $2.$3.$4, $2-$3-$4, ($2) $3-$4
     * Only US country code works for now.
     */
    $contact_phone = get_field( 'phone_number', 'options' );
    if($contact_phone) {
        $site_variables['phone'] = array(
            esc_attr( $contact_phone['phone'] ) => esc_attr( $contact_phone['format'] ),
        );
    }

    $contact_fax = get_field( 'fax', 'options' );
    $site_variables['fax'] = array(
        $contact_fax => '($2) $3-$4',
    );

    /**
     *  Physical Address(es)
     *
     *  A Google Maps API key would save the step of looking up geocoordinates.
     *
     *  To supply the Maps URL yourself, just use the URL as the array key.
     *  You would do this if there were a proper Maps record for the business,
     *  and you wanted to link to it rather than to the coordinates/street address.
     *  (Example is below.)
     *
     * Note: Input the town/city, state/region, and postal code as separate array elements.
     */
    $contact_address = get_field( 'address', 'options' );

    if($contact_address) {
        $site_variables['address_pin'] = array(
            esc_attr( $contact_address['address_coordinates'] )
            => array(
                'streetAddress'   => esc_attr( $contact_address['street_address'] ),
                'addressLocality' => esc_attr( $contact_address['city'] ),
                'addressRegion'   => esc_attr( $contact_address['state'] ),
                'postalCode'      => esc_attr( $contact_address['zip_code'] ),
            ),
        );
    }

    /**
     *  Social media links. Yoast SEO option key => link text.
     *
     *  UPDATE: No longer depending on Yoast, since some sites won't use it.
     *  url => link text
     */
    // url           link <span> text (hidden for icon links)

    if(have_rows('social_media_links', 'options')) {
        $site_variables['seo_data'] = array();
        while(have_rows('social_media_links', 'options')) : the_row();
            $site_variables['seo_data'] += [get_sub_field('social_media_link') => get_sub_field('link_type')];
        endwhile;
    }

    if ( ! empty( $site_variables ) ) {
        mp_parse_site_variables( $site_variables );
    }
}

/*
Section: Site info
Purpose: Store this reusable data in WP and use variables throughout so we
        don't have to change it everywhere.

Author: Media Proper
Last updated: 8 February 2021

*/
function mp_parse_site_variables( $site_variables ) {
    extract( $site_variables );

    /**
     * Generate schema data
     */
    $schema_data = ! empty( $schema ) ? strip_tags( $schema ) : '';

    /**
     * Site Analytics
     * https://developers.google.com/tag-manager/quickstart
     */
    $analytics_data = ! empty( $analytics ) ? $analytics : array();

    /**
     * Generate social media data
     */
    $social_data = array();
    if($seo_data) {
        foreach ( $seo_data as $url => $link_text ) {
            if ( $url ) {
                $url                 = filter_var( $url, FILTER_SANITIZE_URL );
                $social_data[ $url ] = $link_text;
            }
        }
    }

    /**
     * Generate formatted email address. Any masking or other modification to the
     * email links should happen here.
     */
    $email_data = array();
    foreach ( $email as $u ) {
        $u_formatted                  = $u;
        $email_data[ 'mailto:' . $u ] = $u_formatted;
    }

    /**
     * Generate formatted phone number.
     */
    $phone_data = array();
    foreach ( $phone as $u => $format ) {
        $u_formatted               = sanitize_phone( $u, $format );
        $phone_data[ 'tel:' . $u ] = $u_formatted;
    }

    /**
     * Generate formatted fax number.
     */
    $fax_data = array();
    foreach ( $fax as $u => $format ) {
        $u_formatted             = sanitize_phone( $u, $format );
        $fax_data[ 'fax:' . $u ] = $u_formatted;
    }

    /**
     * Generate URL links to Apple Maps (redirects to Google Maps on Android),
     * if needed. (You can also supply the Maps URL directly to $address_data,
     * instead of the Lat/Long coordinates.)
     */
    $address_data = array();
    foreach ( $address_pin as $pin => $address ) {
        // Check if Maps URL has already been supplied.
        if ( filter_var( $pin, FILTER_VALIDATE_URL ) ) {
            $map_url = $pin;
        } else {
            $map_url = map_url(
                $address,
                $pin,
                get_bloginfo( 'name' ),
            );
        }
        $address_data[ $map_url ] = $address;
    }

    /**
     * Store values the WordPress way. We use helper functions to insert the
     * elements into our pages. For example:
     *      the_contact_phone('receiver')
     *  ... inserts the contact phone number link with the Ionicon 'receiver'
     *
     * See the functions in /assets/php/helpers.php.
     */
    wp_cache_set( 'social', $social_data );
    wp_cache_set( 'email', $email_data );
    wp_cache_set( 'phone', $phone_data );
    wp_cache_set( 'fax', $fax_data );
    wp_cache_set( 'address', $address_data );
    wp_cache_set( 'schema', $schema_data );
    wp_cache_set( 'analytics', $analytics_data );

    // add_action(
    // 'wp_head',
    // function () {
    // echo buildAttributes( array( 'type' => 'application/ld+json' ), 'script', buildSchema() );
    // }
    // );
}
