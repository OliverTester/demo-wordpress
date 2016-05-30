<?php
/**
 * Plugin Name: Demo WordPress
 * Plugin URI:  http://euedofia.appspot.com
 * Description: This is just a demonstration of possibilities
 * Version:     1.5
 * Author:      Etimbuk Udofia
 * Author URI:  http://euedofia.appspot.com
 * License:     GPL2
 * Demo WordPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.

 * Demo WordPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Demo WordPress. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: demo-wordpress
 */


//block direct access to plugin file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

//register_activation_hook( __FILE__, 'demo_wordpress_activate');

//add action
add_action('admin_menu', 'demo_wordpress_setup_menu');

//add_action('plugins_loaded', 'FEEFO_wc_wp_init');

//add_action( 'wp_footer', 'includeHomePageWidget' );
//add_action( 'wp_header', 'feefo_product_review_widget_div' );

//add_filter( 'woocommerce_product_tabs', 'add_feefo_review_product_tab', 98 );


//add_action( 'admin_init', redirectToAuthenticationScreen() );

/**
 * Check if WooCommerce is active
 */
function demo_wordpress_activate() {
//    if ( in_array( '/woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here

    $current_user = wp_get_current_user();

    $merchantName = get_bloginfo( $show = 'name');
    $merchantDescription = get_bloginfo( $show = 'description');
    $merchantUrl = get_bloginfo( $show = 'url');
    $merchantAdminEmail = $current_user->user_email;
    $merchantLanguage = get_bloginfo( $show = 'language');

    $pluginsUrl = plugins_url();

    $woocommerceActivated = is_plugin_active( 'woocommerce/woocommerce.php' );
    $woocommerceActivatedSiteWide = is_plugin_active_for_network( 'woocommerce/woocommerce.php');

    redirectToAuthenticationScreen();

    if ( isset( $_REQUEST['success'] ) && $_REQUEST['success'] == 1 && isset( $_REQUEST['page'] )  && $_REQUEST['page'] == "demo-wordpress" ) {
        FEEFO_wc_wp_init();
    }

//    processMerchantCreation();

//    }
}

//set up plugin menu
function demo_wordpress_setup_menu() {
    add_menu_page( 'Demo WordPress Page', 'Demo WordPress', 'manage_options', 'demo-wordpress', 'demo_wordpress_activate' );
}

function FEEFO_wc_wp_create_temp_info( $route ) {

    //define and load current user
    $current_user = wp_get_current_user();

    $parameters = array(
        'merchantDomain' => FEEFO_wc_wp_merchant_domain(),
        'merchantName' => get_bloginfo( 'name' ),
        'merchantDescription' => get_bloginfo( 'description' ),
        'merchantUrl' => FEEFO_wc_wp_merchant_domain(),
        'merchantLanguage' => get_bloginfo( 'language' ),
        'merchantAdminUserEmail' => $current_user->user_email,
        'merchantShopOwner' => $current_user->display_name
    );

    $requestHeaders = array(
        'Content-Type' => 'application/json'
    );

    $response = wp_remote_post( $route, array(
            'method' => 'POST',
            'body' => json_encode( $parameters ),
            'headers' => $requestHeaders,
            'cookies' => array()
        )
    );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        //echo $response;
        exit( var_dump( $error_message) );

    } else {
        return json_decode( wp_remote_retrieve_body( $response ) );
    }
}

/**
 *
 */
function FEEFO_wc_wp_init() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'woocommerce_api_keys';

    $verification_response =  FEEFO_wc_wp_verify();
    $verification_access_key = $verification_response->consumer_key;
    $verification_access_key_id = $verification_response->key_id;

    $wpdb->flush();
    $woocommerce_details = $wpdb->get_row( "SELECT * FROM $table_name WHERE key_id = $verification_access_key_id" );

    //confirm no errors on query execution
    if ( !empty( $wpdb->last_error ) ) {
        //show notice?
        var_dump( $wpdb->last_error );

    } else {

        $access_key = $woocommerce_details->truncated_key;
        $access_key_id = $woocommerce_details->key_id;



        //Merchant has just granted feefo authorisation to their woocommerce orders etc
        //Now verify credentials

        if ( !empty( $verification_access_key ) && ( strpos($verification_access_key, $access_key) !== false ) ) {

            $create_temp_info_route = 'https://wcwptest.localtunnel.me/ecommerce/plugin/woocommerce/register/temp/' . FEEFO_wc_wp_merchant_domain() .'/' . $access_key;

            $create_temp_info_response = FEEFO_wc_wp_create_temp_info( $create_temp_info_route );

            $url_to_render = $create_temp_info_response->registrationUri;
            FEEFO_wc_load_in_frame( $url_to_render );

        }

    }
}

function FEEFO_wc_wp_merchant_domain() {
   return parse_url( get_bloginfo( 'url' ) , PHP_URL_HOST );
}

function FEEFO_wc_wp_verify() {
    $verify_merchant_route = 'https://wcwptest.localtunnel.me/ecommerce/plugin/woocommerce/register/entry';

    $parameters = array(
        'merchant_domain' => FEEFO_wc_wp_merchant_domain()
    );

    $requestHeaders = array(
        'Content-Type' => 'application/json'
    );

    $response = wp_remote_post( $verify_merchant_route, array(
            'method' => 'POST',
            'body' => json_encode( $parameters ),
            'headers' => $requestHeaders,
            'cookies' => array()
        )
    );


    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        //echo $response;
        exit( var_dump( $error_message) );

    } else {
        return json_decode( wp_remote_retrieve_body( $response ) );
    }
}

function authenticateFeefo() {
    $store_url = get_bloginfo( $show = 'url');
    $endpoint  = '/wc-auth/v1/authorize';
    $params    = array(
        'app_name'     => 'demo-wordpress',
        'scope'        => 'read_write',
        'user_id'      => FEEFO_wc_wp_merchant_domain(),
        'return_url'   => admin_url( 'admin.php?page=' . 'demo-wordpress' ) ,
        'callback_url' => 'https://wcwptest.localtunnel.me/ecommerce/plugin/woocommerce/register/callback'
    );

    $redirectEndPoint = $store_url . $endpoint . '?' . http_build_query( $params );;

    return $redirectEndPoint;
}

function redirectToAuthenticationScreen() {

    echo '<br>' . authenticateFeefo() . '<br>';

//    wp_redirect( $redirectUrl, 200 );
//    exit();
}

function includeHomePageWidget() {
    echo '<script type="text/javascript" id="feefo-plugin-widget-bootstrap" src="//register.feefo.com/api/ecommerce/plugin/shopify/widget/merchant/example-shopify-merchant"></script>';
}


function add_feefo_review_product_tab() {

    // Adds the new tab
//    $tabs['test_tab'] = array(
//        'title' 	=> __ ( 'Feefo Reviews', 'woocommerce' ),
//        'priority' 	=> 60,
//        'callback' 	=> 'feefo_product_review_widget_div'
//    );

    $tabs['reviews']['title'] = __( 'Feefo Ratings' );
    $tabs['reviews']['callback'] = 'feefo_product_review_widget_div';
    //remove current reviews tab
//    unset( $tabs['reviews'] );

    return $tabs;
}

function feefo_product_review_widget_div() {

    // Echo content.
    echo '<div id="feefo-product-review-widgetId" class="feefo-review-widget-product" data-feefo-product-id="4917853446"></div>';

}

/**
 * renders the returned url page
 */
function FEEFO_wc_load_in_frame( $url_to_render ) {

    ?>
    <style>
        body {
            margin: 0px;
            padding: 0px;
        }

        /* iframe's parent node */
        div#root {
            position: fixed;
            width: 100%;
            height: 100%;
        }

        /* iframe itself */
        div#root > iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>

    <div id="root">
        <iframe src="<?php echo $url_to_render; ?>" >
            Your browser does not support inline frames.
        </iframe>
    </div>
    <?php
}