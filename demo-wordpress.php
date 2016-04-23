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


if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

//add action
add_action('admin_menu', 'demo_wordpress_setup_menu');

//set up plugin menu
function demo_wordpress_setup_menu() {
    add_menu_page( 'Demo WordPress Page', 'Demo WordPress', 'manage_options', 'demo-wordpress', 'demo_wordpress_activate' );
}

/**
 * Check if WooCommerce is active
 */
function demo_wordpress_activate() {
//    if ( in_array( '/woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        // Put your plugin code here
    $merchantName = get_bloginfo( $show = 'name');
    $merchantDescription = get_bloginfo( $show = 'description');
    $merchantUrl = get_bloginfo( $show = 'url');
    $merchantAdminEmail = get_bloginfo( $show = 'admin_email');
    $merchantLanguage = get_bloginfo( $show = 'language');

    echo $merchantName."<br>";
    echo $merchantDescription."<br>";
    echo $merchantUrl."<br>";
    echo $merchantAdminEmail."<br>";
    echo $merchantLanguage."<br>";

    $store_url = $merchantUrl;
    $endpoint  = '/wc-auth/v1/authorize';
    $params    = array(
        'app_name'     => 'demo-wordpress',
        'scope'        => 'read_write',
        'user_id'      => '123_Test_001',
        'return_url'   => 'https://wcwptest.localtunnel.me/xmlfeedback?merchantidentifier=modern-rugs-ltd',
        'callback_url' => 'https://wcwptest.localtunnel.me/ecommerce/plugin/woocommerce/register/callback'
    );

    echo $store_url . $endpoint . '?' . http_build_query( $params )."<br />";

    processMerchantCreation();
//    }
}

function processMerchantCreation() {

    $createMerchantRoute = 'http://wcwptest.localtunnel.me/ecommerce/plugin/woocommerce/register';
    $parameters = array('merchantName' => get_bloginfo( $show = 'name'),
        'merchantDescription' => get_bloginfo( $show = 'description'),
        'merchantUrl' => get_bloginfo( $show = 'url'),
        'merchantLanguage' => get_bloginfo( $show = 'language'),
        'merchantAdminEmail' => get_bloginfo( $show = 'admin_email')
    );

    $requestHeaders = array(
        'Content-Type' => 'application/json'
    );

    $response = wp_remote_post( $createMerchantRoute, array(
            'method' => 'POST',
            'body' => json_decode(json_encode( $parameters )),
            'headers' => $requestHeaders,
            'cookies' => array()
        )
    );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: $error_message";
    } else {
        echo 'Response:<pre>';
        print_r( $response );
        echo '</pre>';
    }

}

//now activate and do whats needs doing
//register_activation_hook( __FILE__, 'demo_wordpress_activate');
?>
