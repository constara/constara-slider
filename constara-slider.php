<?php

/*
Plugin Name: Constara Slider
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Constara Team
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define("CTS_PLUGIN_PATH", plugin_dir_path(__FILE__));
require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider.php';

function init_constara_slider(){
	$ctsp = new Constara_Slider();
	$ctsp->run();
}
init_constara_slider();

