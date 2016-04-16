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
		$this->define_site_hooks();
	}

	private function load_dependencies(){
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-constara-slider-admin.php';
		require_once plugin_dir_path(__FILE__) . 'class-constara-slider-site.php';
		require_once plugin_dir_path(__FILE__) . 'class-constara-slider-loader.php';

		$this->loader = new Constara_Slider_Loader();
	}

	private function define_admin_hooks(){
	$admin = new CTS_Admin($this->get_version());
	$this->loader->add_action('init', $admin, 'register_post_type');
	$this->loader->add_action('init', $admin, 'register_taxonomy');
	$this->loader->add_action('manage_edit-cts_slides_category_columns', $admin, 'slider_column');
	$this->loader->add_action('after_switch_theme' ,$admin, 'flush_rewrite_rules');
    $this->loader->add_filter('manage_cts_slides_category_custom_column', $admin, 'manage_slider_columns', 10, 3);
	$this->loader->add_shortcode('cts_slider', $admin, 'cts_slider');

	}

	private function define_site_hooks(){
		$site = new CTS_Site($this->get_version());
		$this->loader->add_action('wp_enqueue_scripts', $site, 'enqueue_scripts');
	}

	public function run(){
		$this->loader->run();
	}

	public function get_version(){
		return $this->version;
	}

}