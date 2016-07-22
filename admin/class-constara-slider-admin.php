<?php

/**
 * Class for enqueues scripts, styles, register meta boxes, shortcodes.
 * User: kutas
 * Date: 11.04.2016
 * Time: 1:13
 */

class Constara_Slider_Admin {

	static private $instance = null;

	protected $version;

	static public function getInstance($version){
		if (is_null(self::$instance)){
			self::$instance = new Constara_Slider_Admin($version);
		}
		return self::$instance;
	}

	private function __construct($version) {
		$this->version = $version;
		$this->wp_hooks();

	}

	private function wp_hooks(){
		add_action('plugins_loaded', array($this, 'load_lang_textdomain') );
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts') );
		add_action('init', array($this, 'register_post_type') );
		add_action('init', array($this, 'register_taxonomy') );
		add_action('save_post', array($this, 'save_metaboxes') );
		add_action('manage_edit-cts_slides_category_columns', array($this, 'slider_column') );
		add_action('after_switch_theme' ,array($this, 'flush_rewrite_rules') );
		add_filter('manage_cts_slides_category_custom_column', array($this, 'manage_slider_columns') , 10, 3);
		add_action('cts_slides_category_add_form_fields', array($this, 'slider_create_add_options') );
		add_action('cts_slides_category_edit_form_fields', array($this, 'slider_edit_add_options') );
		add_action('edited_cts_slides_category', array($this, 'slider_add_options_save') );
		add_action('create_cts_slides_category', array($this, 'slider_add_options_save') );
		add_filter('manage_cts_slide_posts_columns', array($this, 'set_slide_columns') );
		add_action('manage_cts_slide_posts_custom_column', array($this, 'slide_columns') , 10 ,2);
		if ( is_admin() ){
			add_action('add_meta_boxes', array($this, 'register_metaboxes') );
		}
	}

	public function load_lang_textdomain(){
		load_plugin_textdomain('cts-slider', false, CTS_PLUGIN_BASENAME . '/languages');
	}

	public function enqueue_scripts($hook){
		if ('edit-tags.php' == $hook || 'cts_slide' == get_post_type()){
			wp_enqueue_style('jquery-ui', CTS_PLUG_ADMIN_URL . 'css/jquery-ui.css');
			wp_enqueue_script('jquery-ui-slider', array('jquery'));
			wp_enqueue_style('cts-slider', CTS_PLUG_ADMIN_URL . 'css/slider.css', $this->get_version());
			wp_enqueue_script('cts-slider', CTS_PLUG_ADMIN_URL . 'js/slider.js', array('jquery'), $this->get_version());
		}

	}

	public function register_post_type(){
				
		register_post_type('cts_slide', array(
			'labels'		=> array(
				'name' 				=> __('Slider','cts-slider' ),
				'menu_name'			=> __('CT Slider','cts-slider' ),
				'all_items'			=> __('Slides','cts-slider' ),
				'add_new' 			=> __('Add New Slide','cts-slider'),
				'singular_name' 	=> __('Slide','cts-slider' ),
				'add_item'			=> __('New Slide','cts-slider'),
				'add_new_item' 		=> __('Add New Slide','cts-slider'),
				'edit_item' 		=> __('Edit Slide','cts-slider')
			),
			'public'		=>  false,
			'show_in_menu'	=>	true,
			'rewrite' 		=> 	array('slug' => 'slides'),
			'menu_position' => 	4,
			'show_ui'		=>	true,
			'has_archive'	=>	false,
			'hierarchical'	=>	false,
			'supports'		=>	array('title','editor'),
			'menu_icon'		=>  CTS_PLUGIN_URL . 'img/menu_icon.svg',
		));
	}

	public function register_taxonomy(){
		register_taxonomy('cts_slides_category', 'cts_slide', array(
			'labels' 			=> array(
				'name' 				=> __('Sliders', 'cts-slider'),
				'singular_name'		=> __('Slider', 'cts-slider'),
				'search_items' 		=> __('Search Sliders', 'cts-slider'),
				'all_items' 		=> __('All Sliders', 'cts-slider'),
				'parent_item' 		=> __('Parent Slider', 'cts-slider'),
				'parent_item_colon' => __('Parent Slider:', 'cts-slider'),
				'edit_item' 		=> __('Edit Slider', 'cts-slider'),
				'update_item' 		=> __('Update Slider', 'cts-slider'),
				'add_new_item' 		=> __('Add New Slider', 'cts-slider'),
				'new_item_name' 	=> __('New Slider Name', 'cts-slider'),
				'menu_name' 		=> __('Sliders', 'cts-slider'),
			),
			'hierarchical' 		=> true,
			'show_ui' 			=> true,
			'query_var' 		=> true,
			'show_admin_column' => true,
			'rewrite' 			=> array('slug' => 'slides-category'),
		));
	}

	public function register_metaboxes(){
		add_meta_box('slide-media', __('Slide media', 'cts-slider'), array($this,'cts_slide_media'), 'cts_slide', 'normal', 'default');
		add_meta_box('slide-option', __('Slide Options', 'cts-slider'), array($this,'cts_slide_option'), 'cts_slide', 'normal', 'default');

	}

	public function cts_slide_media($post){
		wp_create_nonce(__FILE__, 'cts_slide_media');
		$post_id = $post->ID;
		$slide_img_url = get_post_meta($post_id, '_cts_slide_img_url', true);
		?>
		<p class="cts-slide-media">
			<img class="img-preview" src="<?php echo esc_url($slide_img_url); ?>">
			<label for="cts_slide_img_url"><?php _e('Slide image url', 'cts-slider'); ?></label>
			<input type="text" class="widefat" name="cts_slide_img_url" id="cts_slide_img_url" value="<?php echo esc_url($slide_img_url); ?>">
			<span class="button" id="get-slide-img-url" ><?php _e('Get image', 'cts-slider');?></span>
			<span class="button" id="rm-slide-img-url" ><?php _e('Remove image', 'cts-slider'); ?></span>
		</p>

	<?php }

	public function cts_slide_option($post){
		wp_create_nonce(__FILE__, 'cts_slide_options');
		$post_id = $post->ID;
		$show_title = get_post_meta($post_id, '_cts_slide_hide_title', true);
		$title_position = get_post_meta($post_id, '_cts_slide_title_position', true);
		$slide_link = get_post_meta($post_id, '_cts_slide_link_url', true);
		?>
		<p> <label for="cts_slide_hide_title"><?php _e('Hide slide title', 'cts-slider'); ?></label>
			<input type="checkbox" name="cts_slide_hide_title" value="true" <?php checked($show_title, 'true'); ?>>
		</p>
		<p>
			<label for="cts_slide_title_position"><?php _e('Title position', 'cts-slider'); ?></label>
			<input type="text" size="5" name="cts_slide_title_position" id="cts_slide_title_position" value="<?php echo $title_position; ?>">
			<span id="set_default_title_position" class="button"><?php _e('set default', 'cts-slider'); ?></span>
		<div id="title-position"></div>
		<div class="title-position-desc"><?php _e('Choose title position for slide. Less value - higher title position', 'cts-slider'); ?></div>
		</p>
		<p>
			<label for="cts_slide_link_url"><?php _e('Slide link', 'cts-slider'); ?></label>
			<input type="text" name="cts_slide_link_url" id="cts_slide_link_url" size="70" value="<?php echo esc_attr($slide_link); ?>">
		</p>
	<?php }

	public function save_metaboxes($post_id){
		//slide options
		if (isset($_POST['cts_slide_title_position'])){
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
				return;
			}
			wp_verify_nonce(__FILE__, 'cts_slide_options');

			update_post_meta(
				$post_id,
				'_cts_slide_hide_title',
				$_POST['cts_slide_hide_title']
			);

			update_post_meta(
				$post_id,
				'_cts_slide_title_position',
				sanitize_text_field($_POST['cts_slide_title_position'])
			);

			update_post_meta(
				$post_id,
				'_cts_slide_link_url',
				esc_url_raw($_POST['cts_slide_link_url'])
			);
		}

		//slide media
		if (isset($_POST['cts_slide_img_url'])){
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
				return;
			}
			wp_verify_nonce(__FILE__, 'cts_slide_media');

			update_post_meta(
				$post_id,
				'_cts_slide_img_url',
				esc_url_raw($_POST['cts_slide_img_url'])
			);
		}
	}

	public function set_slide_columns($columns){
		array_splice($columns, 2, 0 , array('img-preview'=>__('Thumbnail', 'cts-slide')));
		return $columns;
	}

	public function slide_columns($column, $post_id){

		switch ($column){
			case 'image-preview':
				$img_url = get_post_meta($post_id, '_cts_slide_img_url', true);
				echo sprintf('<img src="%s" class="slide-preview">', $img_url);
		}
	}
	
	public function slider_column($slider_columns){
		$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Name', 'cts-slider'),
		'shortcode' => __('Shortcode', 'cts-slider'),
		//'description' => __('Description', 'cts-slider'),
		'slug' => __('Slug', 'cts-slider'),
		'posts' => __('Slides', 'cts-slider')
		);
		return $new_columns;
	}
	
	public function manage_slider_columns($column, $column_name, $theme_id){
		$slider = get_term($theme_id, 'cts_slides_category');
		switch ($column_name) {
			case 'shortcode':
				echo "[cts_slider slider='".$slider->slug."' ]";
				break;

			default:
				break;
		}
	}

	public function slider_create_add_options(){?>
		<div class="form-field cts-slider-create-options">
			<div class="settings">
				<p>
					<label for="trc_slider_opts[autoplay]"><?php _e('Autoplay', 'cts-slider'); ?></label>
					<select name="trc_slider_opts]">
						<option value="true"><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false"><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[autoplayspeed]"><?php _e('Autoplay speed', 'cts-slider'); ?></label>
					<input type="text" name="trc_slider_opts[autoplayspeed]" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="trc_slider_opts[speed]"><?php _e('Speed', 'cts-slider'); ?></label>
					<input type="text" name="trc_slider_opts[speed]" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="trc_slider_opts[fade]"><?php _e('Fade', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[fade]">
						<option value="true"><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false"><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[adaptiveheight]"><?php _e('Adaptive height', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[adaptiveheight]">
						<option value="false"><?php _e('No', 'cts-slider'); ?></option>
						<option value="true"><?php _e('Yes', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[dots]"><?php _e('Dots', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[dots]">
						<option value="true"><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false"><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[arrows]"><?php _e('Arrows', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[arrows]">
						<option value="true"><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false"><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
			</div>
			<div class="height">
				<?php _e('Slider height', 'cts-slider'); ?>
				<p>
					<label for="trc_slider_opts[height_type]"><?php _e('Auto', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[height_type]" value="auto" />
				</p>
				<p>
					<label for="trc_slider_opts[height_type]"><?php _e('Fixed', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[height_type]" value="fixed" />
					<input type="text" name="trc_slider_opts[heightValue]" />
					<span>px.</span>
				</p>
				<p>
					<label for="trc_slider_opts[height_type]"><?php _e('Ratio', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[height_type]" value="ratio" />
					<label for="trc_slider_opts[ratio_width]"></label>
					<input type="text" name="trc_slider_opts[ratio_width]" />:
					<input type="text" name="trc_slider_opts[ratio_height]" />
				</p>
				<p>
					<label for="trc_slider_opts[height_type]"><?php _e('Full', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[height_type]" value="full" />
				</p>
			</div>


		</div>

	<?php }

	public function slider_edit_add_options($term){
		$options = get_option($term->slug);
		$slick_opts 	= $options['slick'];
		$slider_opts 	= $options['slider'];
		print_r($options);?>
		<tr class="form-field cts-slider-edit-options">
			<th scope="row" valign="top"><?php _e('Slider settings', 'cts-slider'); ?></th>
			<td>
			<div class="settings">
				<p>
					<label for="trc_slider_opts[autoplay]"><?php _e('Autoplay', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[autoplay]">
						<option value="true" <?php selected('true', $slick_opts['autoplay']) ?>><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $slick_opts['autoplay']) ?>><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[autoplayspeed]"><?php _e('Autoplay speed', 'cts-slider'); ?></label>
					<input type="text" name="trc_slider_opts[autoplayspeed]" value="<?php echo $slick_opts['autoplayspeed']; ?>" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="trc_slider_opts[speed]"><?php _e('Speed', 'cts-slider'); ?></label>
					<input type="text" name="trc_slider_opts[speed]" value="<?php echo $slick_opts['speed']; ?>" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="trc_slider_opts[fade]"><?php _e('Fade', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[fade]">
						<option value="true" <?php selected('true', $slick_opts['fade']); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $slick_opts['fade']); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[adaptiveheight]"><?php _e('Adaptive height', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[adaptiveheight]">
						<option value="false" <?php selected('false', $slick_opts['adaptiveheight']); ?> ><?php _e('No', 'cts-slider'); ?></option>
						<option value="true" <?php selected('true', $slick_opts['adaptiveheight']); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[dots]"><?php _e('Dots', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[dots]">
						<option value="true" <?php selected('true', $slick_opts['dots']); ?>><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $slick_opts['dots']); ?>><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="trc_slider_opts[arrows]"><?php _e('Arrows', 'cts-slider'); ?></label>
					<select name="trc_slider_opts[arrows]">
						<option value="true" <?php selected('true', $slick_opts['arrows']); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $slick_opts['arrows']); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
			</div>
			<div>
				<label for="trc_slider_opts[height_type]"><?php _e('Auto', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[height_type]" value="auto" <?php checked('auto', $slider_opts['height_type']); ?> />
			</div>
			<div>
				<label for="trc_slider_opts[height_type]"><?php _e('Fixed', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[height_type]" value="fixed" <?php checked('fixed', $slider_opts['height_type']); ?> />
				<input type="text" name="trc_slider_opts[height_value]" value="<?php echo esc_attr($slider_opts['height_value']); ?>" />
				<span>px.</span>
			</div>
			<div>
				<label for="trc_slider_opts[height_type]"><?php _e('Ratio', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[height_type]" value="ratio" <?php checked('ratio', $slider_opts['height_type']); ?> />
				<label for="trc_slider_opts[ratio_width]"></label>
				<input type="text" name="trc_slider_opts[ratio_width]" value="<?php echo esc_attr($slider_opts['ratio_width']); ?>" />:
				<input type="text" name="trc_slider_opts[ratio_height]" value="<?php echo esc_attr($slider_opts['ratio_height']); ?>" />
			</div>
			<div>
				<label for="trc_slider_opts[height_type]"><?php _e('Full', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[height_type]" value="full" <?php checked('full', $slider_opts['height_type']); ?> />
			</div>
			</td>
		</tr>
	<?php }

	public function slider_add_options_save($term_id){
		$term = get_term($term_id, 'cts_slides_category');
		$option_name = $term->slug;
		if (isset($_POST['trc_slider_opts'])){
			$options = $_POST['trc_slider_opts'];
			$data_slider = array();
			$data_slider['slick']['autoplay'] 		= sanitize_text_field( $options['autoplay'] );
			$data_slider['slick']['autoplayspeed'] 	= sanitize_text_field( $options['autoplayspeed'] );
			$data_slider['slick']['speed'] 			= sanitize_text_field( $options['speed'] );
			$data_slider['slick']['fade'] 			= sanitize_text_field( $options['fade'] );
			$data_slider['slick']['adaptiveheight'] 	= sanitize_text_field( $options['adaptiveheight'] );
			$data_slider['slick']['dots'] 			= sanitize_text_field( $options['dots'] );
			$data_slider['slick']['arrows'] 			= sanitize_text_field( $options['arrows'] );
			$data_slider['slider']['height_type'] 		= sanitize_text_field( $options['height_type'] );
			switch ($options['height_type']){
				case 'fixed':
					$data_slider['slider']['height_value'] = sanitize_text_field($options['height_value']);
					break;
				case 'ratio':
					$data_slider['slider']['ratio_width'] 	= sanitize_text_field($options['ratio_width']);
					$data_slider['slider']['ratio_height'] = sanitize_text_field($options['ratio_height']);
					break;
				default:
					break;
			}


			update_option($option_name, $data_slider);
		}
	}
	

	public function flush_rewrite_rules(){
		flush_rewrite_rules();
	}

	public function get_version(){
		return $this->version;
	}
}?>