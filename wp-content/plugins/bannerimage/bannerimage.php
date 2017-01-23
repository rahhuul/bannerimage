<?php
/**
 * @package Banner Image
 */
/*
Plugin Name: Banner Image
Plugin URI: https://Wedding Extra.com/
Description: Banner Image.
Version: 3.1.11
Author: Automattic
Author URI: http://siddhiinfosoft.com
License: GPLv2 or later
Text Domain: banner_image
*/



// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'BANNERIMAGE_VERSION', '3.1.11' );
define( 'BANNERIMAGE__MINIMUM_WP_VERSION', '3.2' );
define( 'BANNERIMAGE__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BANNERIMAGE__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BANNERIMAGE_DELETE_LIMIT', 100000 );

register_activation_hook( __FILE__, array( 'Bannerimage', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Bannerimage', 'plugin_deactivation' ) );

require_once( BANNERIMAGE__PLUGIN_DIR . 'class.bannerimage.php' );
//require_once( BANNERIMAGE__PLUGIN_DIR . 'class.weddingextra-widget.php' );

add_action( 'init', array( 'Bannerimage', 'init' ) );