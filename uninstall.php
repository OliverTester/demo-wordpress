<?php
/**
 * Created by IntelliJ IDEA.
 * User: etimbuk
 * Date: 23/05/16
 * Time: 12:07
 */

//block direct access to plugin file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function FEEFO_Setup_on_uninstall()
{
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    check_admin_referer( 'bulk-plugins' );

    // Important: Check if the file is the one
    // that was registered during the uninstall hook.
    if ( __FILE__ != WP_UNINSTALL_PLUGIN ) {
        global $wpdb;

        //get current user Object
        $user = wp_get_current_user();

        $table_name = $wpdb->prefix . 'woocommerce_api_keys';
        $user_id = $user -> $user->ID;

        $wpdb->flush();
        $woocommerce_delete_details = $wpdb->get_row( "SELECT * FROM $table_name WHERE user_id = $user_id" );
    }
}