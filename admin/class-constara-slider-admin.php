<?php

/**
 * Class for enqueues scripts, styles, register meta boxes, shortcodes.
 * User: kutas
 * Date: 11.04.2016
 * Time: 1:13
 */
class Constara_Slider_Admin {

	protected $version;

	public function __construct($version) {
		$this->version = $version;
	}



	public function register_post_type(){
				
		register_post_type('ct_slide', array(
			'labels'		=> array(
				'name' 				=> __('Slider','tracker' ),
				'menu_name'			=> __('Slider','tracker' ),
				'all_items'			=> __('Slides','tracker' ),
				'add_new' 			=> __('Add New Slide','tracker'),
				'singular_name' 	=> __('Slide','tracker' ),
				'add_item'			=> __('New Slide','tracker'),
				'add_new_item' 		=> __('Add New Slide','tracker'),
				'edit_item' 		=> __('Edit Slide','tracker')
			),
			'public'		=> false,
			'show_in_menu'	=>	true,
			'rewrite' 		=> 	array('slug' => 'slides'),
			'menu_position' => 	4,
			'show_ui'		=>	true,
			'has_archive'	=>	false,
			'hierarchical'	=>	false,
			'supports'		=>	array('title','editor','thumbnail'),
			'menu_icon'			=> 'dashicons-images-alt2',
		));
	}

	public function register_taxonomy(){

		register_taxonomy('slides_category', array('ct_slides'), array(
			'labels' 			=> array(
				'name' 				=> __('Sliders', 'tracker'),
				'singular_name'		=> __('Slider', 'tracker'),
				'search_items' 		=> __('Search Sliders', 'tracker'),
				'all_items' 		=> __('All Sliders', 'tracker'),
				'parent_item' 		=> __('Parent Slider', 'tracker'),
				'parent_item_colon' => __('Parent Slider:', 'tracker'),
				'edit_item' 		=> __('Edit Slider', 'tracker'),
				'update_item' 		=> __('Update Slider', 'tracker'),
				'add_new_item' 		=> __('Add New Slider', 'tracker'),
				'new_item_name' 	=> __('New Slider Name', 'tracker'),
				'menu_name' 		=> __('Sliders', 'tracker'),
			),
			'hierarchical' 		=> true,
			'show_ui' 			=> true,
			'query_var' 		=> true,
			'show_admin_column' => true,
			'rewrite' 			=> array('slug' => 'slides-category'),
		));
	}
	
	public function slider_column($slider_columns){
		
	}
	
	public function manage_theme_columns($column, $column_name, $theme_id){
		
	}

	public function add_shortcode(){
		add_shortcode('constara_slider',array($this, 'render_slider_shortcode'));
	}

	public function render_slider_shortcode(){
		require_once plugin_dir_path(__FILE__) . 'partials/constara-slider-shortcode.php';
	}


}