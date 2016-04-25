<?php

/**
 * Class is responsible for loading the dependencies, setting the locale, and coordinating the hooks
 * Date: 11.04.2016
 * Time: 1:48
 */
class Constara_Slider_Plugin {

	protected $loader;

	protected $plugin_slug;

	protected $version;

	static private $instance = null;

	static public function getInstance(){
		if (is_null(self::$instance)){
			self::$instance = new Constara_Slider_Plugin();
		}
		return self::$instance;
	}

	private function __construct() {
		$this->plugin_slug = 'constara-slider';
		$this->version = '0.1';
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_site_hooks();
	}

	private function load_dependencies(){
		require_once CTS_PLUGIN_ADMIN_PATH . 'class-constara-slider-admin.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slide.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider-loader.php';
		require_once CTS_PLUGIN_PATH . 'inc/class-constara-slider-site.php';

		$this->loader =  Constara_Slider_Loader::getInstance();
	}

	private function define_admin_hooks(){
		$admin = CTS_Admin::getInstance($this->version);
		$this->loader->add_action('admin_enqueue_scripts', $admin, 'enqueue_scripts');
		$this->loader->add_action('init', $admin, 'register_post_type');
		$this->loader->add_action('init', $admin, 'register_taxonomy');
		$this->loader->add_action('add_meta_boxes', $admin, 'register_metaboxes');
		$this->loader->add_action('save_post', $admin, 'save_metaboxes');
		$this->loader->add_action('manage_edit-cts_slides_category_columns', $admin, 'slider_column');
		$this->loader->add_action('after_switch_theme' ,$admin, 'flush_rewrite_rules');
		$this->loader->add_filter('manage_cts_slides_category_custom_column', $admin, 'manage_slider_columns', 10, 3);
		$this->loader->add_action('cts_slides_category_add_form_fields', $admin, 'slider_create_add_options');
		$this->loader->add_action('cts_slides_category_edit_form_fields', $admin, 'slider_edit_add_options');
		$this->loader->add_action('edited_cts_slides_category', $admin, 'slider_add_options_save');
		$this->loader->add_action('create_cts_slides_category', $admin, 'slider_add_options_save');

	}

	private function define_site_hooks(){
		$site = CTS_Site::getInstance($this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $site, 'enqueue_scripts');
		$this->loader->add_shortcode('cts_slider', $site, 'cts_slider_shortcode');

	}

	public function run(){
		$this->loader->run();
	}

	public function get_version(){
		return $this->version;
	}

	public function get_plugin_slug(){
		return $this->plugin_slug;
	}

}