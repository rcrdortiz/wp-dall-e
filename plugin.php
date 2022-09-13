<?php
/*
  Plugin Name: WP Dall-e
  Plugin URI: https://github.com/rcrdortiz/wp-dall-e
  Description: Ads Dall-e functionalities to WordPress
  Author: rcrdortiz
  Author URI: https://github.com/rcrdortiz
  Version: 0.0.1
  Update URI: https://github.com/rcrdortiz/wp-dall-e
*/

/**
 * Build dependencies
 */

use DallE\DallEAdmin;
use DallE\DallEAjax;
use DallE\DallEService;
use DallE\DallEStore;

require_once 'includes/base/OptionStore.php';
require_once 'includes/DallEAdmin.php';
require_once 'includes/DallEAjax.php';
require_once 'includes/DallEStore.php';
require_once 'includes/DallEService.php';

const DALL_E_PATH    = __DIR__;
const DALL_E_VERSION = '0.0.1';

$store       = new DallEStore();
$curl_client = new WP_Http_Curl();
$service     = new DallEService( $curl_client, $store );
$admin       = new DallEAdmin( $service );
$ajax        = new DallEAjax( $service );

if ( is_admin() ) {
	add_action( 'admin_menu', fn() => add_menu_page(
		'WP Dall-e',
		'WP Dall-e',
		'manage_options',
		'wp-dall-e',
		fn() => $admin->dalle_when_is_admin()
	) );

	// Admin Routing
	if ( isset( $_REQUEST['dall-e-admin'] ) ) {
		if ( isset( $_REQUEST['api_key'] ) ) {
			$admin->set_api_key( $_REQUEST['api_key'] );
		}
		if ( isset( $_REQUEST['query'] ) ) {
			$admin->generate_image_from_admin( $_REQUEST['query'] );
		}
	}

	// Hook into media library upload
	add_action( 'pre-upload-ui', fn() => include DALL_E_PATH . '/views/dall-e-prompt.php' );

	// Ajax action for uploading
	add_action( 'wp_ajax_get_dall_e_images', fn() => $ajax->get_images_on_wp_ajax_get_dall_e_images() );
}
