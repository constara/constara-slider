<?php

/*
Plugin Name: Constara Slider
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: beta-0.8.2
Author: Constara Team
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

if ( ! defined( 'WPINC' ) ) {
	wp_die();
}
define("CTS_PLUGIN_BASENAME", plugin_basename(dirname(__FILE__)));
define("CTS_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("CTS_PLUGIN_URL", plugin_dir_url(__FILE__));
define('CTS_PLUG_ADMIN_URL', plugin_dir_url(__FILE__) . 'admin/');
define('CTS_PLUGIN_ADMIN_PATH', plugin_dir_path(__FILE__) . 'admin/');

require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider-plugin.php';

function init_constara_slider(){
	$ctsp = Constara_Slider_Plugin::getInstance();
	$ctsp->run();
}
init_constara_slider();

