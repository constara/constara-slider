<?php

/**
 * Class for enqueues scripts, styles, register meta boxes, shortcodes.
 * User: kutas
 * Date: 11.04.2016
 * Time: 1:13
 */

class CTS_Admin {

	static private $instance = null;

	protected $version;

	static public function getInstance($version){
		if (is_null(self::$instance)){
			self::$instance = new CTS_Admin($version);
		}
		return self::$instance;
	}

	private function __construct($version) {
		$this->version = $version;
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
			'public'		=> false,
			'show_in_menu'	=>	true,
			'rewrite' 		=> 	array('slug' => 'slides'),
			'menu_position' => 	4,
			'show_ui'		=>	true,
			'has_archive'	=>	false,
			'hierarchical'	=>	false,
			'supports'		=>	array('title','editor'),
			'menu_icon'		=> 'dashicons-images-alt2',
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
		<p><label for="cts_slide_img_url"><?php _e('Slide image url', 'cts-slider'); ?></label>
			<input type="text" class="widefat" name="cts_slide_img_url" id="cts_slide_img_url" value="<?php echo esc_url($slide_img_url); ?>">
			<span class="button" id="get-slide-img-url"  class="get-slide-img-url" >Get image</span>
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
				echo "[cts_slider slider='".$slider->slug."' 
				autoplay='yes'  
				autoplaySpeed='6000' 
				speed='1000' 
				fade='yes' 
				dots='yes' 
				arrows='yes' 
				adaptiveHeight='no']";
				break;

			default:
				break;
		}
	}

	public function slider_create_add_options(){?>
		<div class="form-field cts-slider-create-options">
			<div class="height">
				<?php _e('Slider height', 'cts-slider'); ?>
				<div>
					<label for="trc_slider_opts[heightType]"><?php _e('Auto', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[heightType]" value="auto">
				</div>
				<div>
					<label for="trc_slider_opts[heightType]"><?php _e('Fixed', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[heightType]" value="fixed">
					<input type="text" name="trc_slider_opts[heightValue]" >
					<span>px.</span>
				</div>
				<div>
					<label for="trc_slider_opts[heightType]"><?php _e('Ratio', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[heightType]" value="ratio">
					<label for="trc_slider_opts[ratio][width]"></label>
					<input type="text" name="trc_slider_opts[ratio][width]" >:
					<input type="text" name="trc_slider_opts[ratio][height]" >
				</div>
				<div>
					<label for="trc_slider_opts[heightType]"><?php _e('Full', 'cts-slider'); ?></label>
					<input type="radio" name="trc_slider_opts[heightType]" value="full">
				</div>
			</div>


		</div>

	<?php }

	public function slider_edit_add_options($term){
		$options = get_option($term->slug);
		print_r($options);?>
		<tr class="form-field cts-slider-edit-options">
			<th scope="row" valign="top"><?php _e('Slider height', 'cts-slider'); ?></th>
			<td>
			<div>
				<label for="trc_slider_opts[heightType]"><?php _e('Auto', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[heightType]" value="auto" <?php checked('auto', $options['heightType']); ?> >
			</div>
			<div>
				<label for="trc_slider_opts[heightType]"><?php _e('Fixed', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[heightType]" value="fixed" <?php checked('fixed', $options['heightType']); ?> >
				<input type="text" name="trc_slider_opts[heightValue]" value="<?php echo esc_attr($options['heightValue']); ?>" >
				<span>px.</span>
			</div>
			<div>
				<label for="trc_slider_opts[heightType]"><?php _e('Ratio', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[heightType]" value="ratio" <?php checked('ratio', $options['heightType']); ?> >
				<label for="trc_slider_opts[ratio][width]"></label>
				<input type="text" name="trc_slider_opts[ratioWidth]" value="<?php echo esc_attr($options['ratioWidth']); ?>" >:
				<input type="text" name="trc_slider_opts[ratioHeight]" value="<?php echo esc_attr($options['ratioHeight']); ?>" >
			</div>
			<div>
				<label for="trc_slider_opts[heightType]"><?php _e('Full', 'cts-slider'); ?></label>
				<input type="radio" name="trc_slider_opts[heightType]" value="full" <?php checked('full', $options['heightType']); ?> >
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
			$data_slider['heightType'] = $options['heightType'];
			switch ($options['heightType']){
				case 'fixed':
					$data_slider['heightValue'] = sanitize_text_field($options['heightValue']);
					break;
				case 'ratio':
					$data_slider['ratioWidth'] = sanitize_text_field($options['ratioWidth']);
					$data_slider['ratioHeight'] = sanitize_text_field($options['ratioHeight']);
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