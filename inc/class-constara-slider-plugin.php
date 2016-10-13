<?php

/**
 * Class is responsible for loading the dependencies, setting the locale, and coordinating the hooks
 * Date: 11.04.2016
 * Time: 1:48
 */
class Constara_Slider_Plugin {

	protected $loader;

	public static $plugin_prefix;

	protected $version;

	static private $instance = null;

	static public function getInstance(){
		if (is_null(self::$instance)){
			self::$instance = new Constara_Slider_Plugin();
		}
		return self::$instance;
	}

	private function __construct() {
		static::$plugin_prefix = 'cts_slider_';
		$this->version       = 'beta-0.8.4';
		$this->load_dependencies();
	}

	private function load_dependencies(){
		require_once CTS_PLUGIN_ADMIN_PATH . 'class-constara-slider-admin.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider-slider.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider-slide.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider-site.php';
	}


	public function run(){
		Constara_Slider_Site::getInstance( $this->get_version() );
		Constara_Slider_Admin::getInstance( $this->get_version() );
	}

	public function get_version(){
		return $this->version;
	}

}