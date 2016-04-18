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
    $merchantLanguage = get_bloginfo( $show = 'language');;

    echo $merchantName;
    echo $merchantDescription;
    echo $merchantUrl;
    echo $merchantAdminEmail;
    echo $merchantLanguage;
//    }
}


//now activate and do whats needs doing
//register_activation_hook( __FILE__, 'demo_wordpress_activate');
?>