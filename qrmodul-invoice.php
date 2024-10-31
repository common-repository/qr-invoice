<?php

/*
 * Plugin Name: QR Invoice Plugin
 * Plugin URI: https://qrmodul.ch
 * Description: Create invoices with the official Swiss QR payment part via QRModul.ch
 * Version: 1.0.10
 * Author: KMU Digitalisierung for QRModul.ch
 * Author URI: https://kmu-digitalisierung.agency
 * Text Domain: qr-invoice
 * Requires at least: 5.5
 * Tested up to: 6.0
 * Requires PHP: 7.0
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least: 5.5.0
 * WC tested up to: 5.5.2
 */

$wqrmi_reg_url           = "https://qrmodul.ch/step-by-step/";
$wqrmi_auth_url          = "https://api.qrmodul.ch/v1/access_token";
$wqrmi_qr_bill_url       = "https://api.qrmodul.ch/v1/qr_bill";
$wqrmi_qr_reference_url  = "https://api.qrmodul.ch/v1/reference";
$wqrmi_api_docs_url      = "https://qrmodul.ch/api-documentation";
$wqrmi_template_url      = "https://qrmodul.ch/en/cockpit/template";
$wqrmi_settings_url      = "/wp-admin/options-general.php?page=wqrmi-options";
$wqrmi_email             = "info@qrmodul.ch";
$wqrmi_plugin_dir        = plugin_dir_path( __FILE__ );
$wqrmi_options           = get_option( 'wqrmi_option' );


function wqrmi_settings_link( $links_array, $plugin_file_name ) {
	global $wqrmi_api_docs_url, $wqrmi_settings_url;
        
	if ( class_exists( 'WooCommerce' ) ) {
            $links_array['settings'] = '<a href="' . esc_url( $wqrmi_settings_url ) . '" aria-label="' . esc_attr__( "Settings", "qr-invoice" ) . '">' . esc_html__( "Settings", "qr-invoice" ) . '</a>';
	}
	$links_array['apidocs'] = '<a target="_blank" href="' . esc_url( $wqrmi_api_docs_url ) . '" aria-label="' . esc_attr__( "View API docs", "qr-invoice" ) . '">' . esc_html__( "API docs", "qr-invoice" ) . '</a>';


	return $links_array;
}

add_filter( 'plugin_action_links_'.plugin_basename(__FILE__), 'wqrmi_settings_link', 10, 2 );


function wqrmi_connect_auth() {

	global $wqrmi_auth_url, $wqrmi_options;

	$params = array(
		'client_id'     => $wqrmi_options['wqrmi_client_id'],
		'client_secret' => $wqrmi_options['wqrmi_client_secret'],
		'duration'      => $wqrmi_options['wqrmi_token_duration'],
	);
	$params = json_encode( $params );

	$body_request = array(
		'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
		'body'    => $params,
		'method'  => 'POST'
	);


	$response = wp_remote_post( $wqrmi_auth_url, $body_request );
	$response = json_decode( $response['body'] );


	if ( $response->status == '200' && $response->message == 'Success' && $response->data->access_token ) {
		echo json_encode( array(
			'wqrmi_access_token' => $response->data->access_token,
			'status'             => $response->status,
			'message'            => $response->message
		) );
	} else {
		echo json_encode( array(
			'wqrmi_access_token' => 0,
			'status'             => $response->status,
			'message'            => $response->message
		) );
	}

	exit;

}

add_action( 'wp_ajax_wqrmi_connect_auth', 'wqrmi_connect_auth' );
add_action( 'wp_ajax_nopriv_wqrmi_connect_auth', 'wqrmi_connect_auth' );


function wqrmi_create_directory_invoices() {

	$upload_dir = wp_upload_dir();

	$invoices_folder = $upload_dir['basedir'] . '/qrmodul-invoice';

	if ( ! file_exists( $invoices_folder ) ) {
		mkdir( $invoices_folder, 0755, true );
	}

}

register_activation_hook( __FILE__, 'wqrmi_create_directory_invoices' );


function wqrmi_plugin_language() {
    
    load_plugin_textdomain( 'qr-invoice', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
    
}

add_action( 'plugins_loaded', 'wqrmi_plugin_language' );


include_once $wqrmi_plugin_dir . '/include/woo-functions.php';
include_once $wqrmi_plugin_dir . '/include/woo-payment-gateway.php';
include_once $wqrmi_plugin_dir . '/include/api.php';
include_once $wqrmi_plugin_dir . 'settings.php';
