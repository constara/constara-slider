<?php

/**
 * Class is responsible for loading the dependencies, setting the locale, and coordinating the hooks
 * Date: 11.04.2016
 * Time: 1:48
 */
class Constara_Slider {

	protected $loader;

	protected $plugin_slug;

	protected $version;

	public function __construct() {
		$this->plugin_slug = 'constara-slider';
		$this->version = '0.1';
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	private function load_dependencies(){
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-constara-slider-admin.php';

		require_once plugin_dir_path(__FILE__) . 'class-constara-slider-loader.php';
		$this->loader = new Constara_Slider_Loader();

	}

	private function define_admin_hooks(){
		
	}

	public function run(){

	}

	public function get_version(){
		return $this->version;
	}

}