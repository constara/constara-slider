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
		add_action('manage_edit-cts_slider_category_columns', array($this, 'slider_column') );
		add_filter('manage_cts_slider_category_custom_column', array($this, 'manage_slider_columns') , 10, 3);
		add_action('cts_slider_category_add_form_fields', array($this, 'slider_create_add_options') );
		add_action('cts_slider_category_edit_form_fields', array($this, 'slider_edit_add_options') );
		add_action('edited_cts_slider_category', array($this, 'slider_add_options_save') );
		add_action('create_cts_slider_category', array($this, 'slider_add_options_save') );
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
		if ( 'edit-tags.php' == $hook || 'cts_slide' == get_post_type() || 'term.php' == $hook ){
			wp_enqueue_media();
			wp_enqueue_style('jquery-ui');
			wp_enqueue_script('jquery-ui-slider', array('jquery'));
			wp_enqueue_style('cts-slider', CTS_PLUG_ADMIN_URL . 'css/slider-admin.css',array('wp-color-picker'), $this->get_version());
			wp_enqueue_script('cts-slider', CTS_PLUG_ADMIN_URL . 'js/slider-admin.js', array('jquery', 'wp-color-picker'), $this->get_version());
		}
	}

	public function register_post_type(){
				
		register_post_type('cts_slide', array(
			'labels'		=> array(
				'name' 			=> __('Slider','cts-slider' ),
				'menu_name'		=> __('CT Slider','cts-slider' ),
				'all_items'		=> __('Slides','cts-slider' ),
				'add_new' 		=> __('Add New Slide','cts-slider'),
				'singular_name' => __('Slide','cts-slider' ),
				'add_item'		=> __('New Slide','cts-slider'),
				'add_new_item' 	=> __('Add New Slide','cts-slider'),
				'edit_item' 	=> __('Edit Slide','cts-slider')
			),
			'public'		=>  false,
			'show_in_menu'	=>	true,
			'rewrite' 		=> 	array('slug' => 'slides'),
			'menu_position' => 	4,
			'show_ui'		=>	true,
			'has_archive'	=>	false,
			'hierarchical'	=>	false,
			'supports'		=>	array('title'),
			'menu_icon'		=>  CTS_PLUGIN_URL . 'img/menu_icon.svg',
		));
	}

	public function register_taxonomy(){
		register_taxonomy('cts_slider_category', 'cts_slide', array(
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
		));
	}

	public function register_metaboxes(){
		add_meta_box('slide-media', __('Slide media', 'cts-slider'), array($this,'cts_slide_media'), 'cts_slide', 'normal', 'default');
		add_meta_box('slide-option', __('Slide Options', 'cts-slider'), array($this,'cts_slide_option'), 'cts_slide', 'normal', 'default');

	}

	public function cts_slide_media($post){
		$post_id = $post->ID;
		$slide_media = get_post_meta( $post_id, '_cts_slide_media', true );
		$img_url     = isset( $slide_media['img_url'] ) ? (string) $slide_media['img_url'] : '';
		$img_id      = isset( $slide_media['img_id'] ) ? $slide_media['img_id'] : '';
		wp_nonce_field( __FILE__, 'cts_slide_media' );
		?>
		<p class="cts-slide-media">
			<img class="img-preview" src="<?php echo esc_url($img_url); ?>">
			<label for="cts_slide_img_url"><?php _e('Slide image url', 'cts-slider'); ?></label>
			<input type="text" class="widefat" name="slide_media[img_url]" id="cts_slide_img_url" value="<?php echo esc_url($img_url); ?>">
			<input type="hidden" class="img-id" name="slide_media[img_id]" value="<?php echo esc_attr( $img_id ); ?>" />
			<span class="button" id="get-slide-img-url" ><?php _e('Get image', 'cts-slider');?></span>
			<span class="button" id="rm-slide-img-url" ><?php _e('Remove image', 'cts-slider'); ?></span>
		</p>

	<?php }

	public function cts_slide_option($post){
		$post_id        = $post->ID;
		$slide_meta     = get_post_meta( $post_id, '_cts_slide_meta', true );
		$hide_title     = isset( $slide_meta['hide_title'] ) ? (bool) $slide_meta['hide_title'] : false;
		$title_color    = isset( $slide_meta['title_color'] ) ? $slide_meta['title_color'] : '#fff';
		$custom_title_position = isset( $slide_meta['custom_title_position'] ) ? $slide_meta['custom_title_position'] : false;
		$title_position = isset( $slide_meta['title_position'] ) ? (integer) $slide_meta['title_position'] : 40;
		$slide_desc     = isset( $slide_meta['slide_desc'] ) ? (string) $slide_meta['slide_desc'] : '';
		$desc_bold      = isset( $slide_meta['desc_bold'] ) ? (bool) $slide_meta['desc_bold'] : false;
		$desc_italic    = isset( $slide_meta['desc_italic'] ) ? (bool) $slide_meta['desc_italic'] : false;
		$desc_font_size = isset( $slide_meta['desc_font_size'] ) ? $slide_meta['desc_font_size'] : '18';
		$desc_align     = isset( $slide_meta['desc_align'] ) ? $slide_meta['desc_align'] : 'center';
		$desc_color     = isset( $slide_meta['desc_color'] ) ? $slide_meta['desc_color'] : '#fff';
		$link_url       = isset( $slide_meta['link_url'] ) ? (string) $slide_meta['link_url'] : '';
		$btn_link_text  = isset( $slide_meta['btn_link_text'] ) ? (string) $slide_meta['btn_link_text'] : '';
		$btn_bg_color   = isset( $slide_meta['btn_bg_color'] ) ? (string) $slide_meta['btn_bg_color'] : '';
		$btn_bg_color_hover = isset( $slide_meta['btn_bg_color_hover'] ) ? $slide_meta['btn_bg_color_hover'] : '';
		$btn_text_color = isset( $slide_meta['btn_text_color'] ) ? $slide_meta['btn_text_color'] : '';
		$btn_text_color_hover = isset( $slide_meta['btn_text_color_hover'] ) ? $slide_meta['btn_text_color_hover'] : '';
		$btn_ghost_style= isset( $slide_meta['btn_ghost_style'] ) ? (bool) $slide_meta['btn_ghost_style'] : false;

		wp_nonce_field( __FILE__, 'cts_slide_options' );
		?>
		<fieldset>
			<input type="checkbox" id="cts_slide_hide_title" name="slide[hide_title]" <?php checked( $hide_title ); ?>>
			<label for="cts_slide_hide_title"><?php _e('Hide slide title', 'cts-slider'); ?></label>
		</fieldset>
		<label><?php _e('Title color', 'cts-slider'); ?>
			<input type="text" id="cts-slide-title-color" name="slide[title_color]" value="<?php echo esc_attr( $title_color ); ?>" />
		</label>
		<fieldset>
			<input id="cts-slide-custom-title-position" type="checkbox" name="slide[custom_title_position]" <?php checked( $custom_title_position ); ?> />
			<label for="cts-slide-custom-title-position"><?php _e('Custom title position', 'cts-slider'); ?></label>
		</fieldset>
		<fieldset class="cts-title-position">
			<span for="cts_slide_title_position"><?php _e('Title position', 'cts-slider'); ?></span>
			<input type="text" size="5" name="slide[title_position]" id="cts_slide_title_position" value="<?php echo esc_attr( $title_position ) ; ?>">
			<span id="set_default_title_position" class="button"><?php _e('set default', 'cts-slider'); ?></span>
		<div id="title-position"></div>
		<div class="title-position-desc"><?php _e('Choose title position for slide. Less value - higher title position', 'cts-slider'); ?></div>
		</fieldset>
		<fieldset>
			<label >
				<span style="padding-bottom: 10px; display: inline-block;"><?php _e( 'Slide description', 'cts-slider' ); ?></span>
				<textarea class="widefat" rows="3" name="slide[slide_desc]"><?php echo esc_textarea( $slide_desc ); ?></textarea>
			</label>
			<label>
				<input type="checkbox" name="slide[desc_bold]" <?php checked( $desc_bold ); ?> />
				<b><?php _e( 'Bold', 'cts-slider' ); ?></b>
			</label>
			<label>
				<input type="checkbox" name="slide[desc_italic]" <?php checked( $desc_italic ); ?> />
				<i><?php _e( 'Italic', 'cts-slider' ); ?></i>
			</label>
			<label><?php _e( 'Font size', 'cts-slider' ); ?>
				<input type="number" min="6" max="50" step="1" name="slide[desc_font_size]" value="<?php echo esc_attr( $desc_font_size ); ?>" />
			</label>
			<label><?php _e( 'Text color', 'cts-slider' ); ?>
				<input type="text" id="cts-slide-desc-color" name="slide[desc_color]" value="<?php echo esc_attr( $desc_color )?>" />
			</label>
			<br/>
			<label>
				<span style="padding-top: 10px; display: inline-block;"><?php _e('Text align', 'cts-slider'); ?><span>
				<select name="slide[desc_align]">
					<option value="center" <?php selected( 'center', $desc_align ); ?>><?php _e('Center', 'cts-slider'); ?></option>
					<option value="left" <?php selected( 'left', $desc_align ); ?>><?php _e('Left', 'cts-slider'); ?></option>
					<option value="right" <?php selected( 'right', $desc_align ); ?>><?php _e('Right', 'cts-slider'); ?></option>
				</select>
			</label>
		</fieldset>
		<fieldset>
			<label for="cts_slide_link_url"><span style="padding-bottom: 10px; display: inline-block;"><?php _e('Slide link', 'cts-slider'); ?></span></label>
			<input type="text" class="widefat" name="slide[link_url]" id="cts_slide_link_url" size="70" value="<?php echo esc_url( $link_url ); ?>">
		</fieldset>
		<fieldset>
		<label for="cts_btn_link_text"><?php _e( 'Button text', 'cts-slider' ); ?></label>
		<input type="text" name="slide[btn_link_text]" id="cts_btn_link_text" size="30" value="<?php echo esc_attr( $btn_link_text ) ?>" />
			<br/><br/>
		<label>
			<input type="checkbox" class="cts-btn-ghost-style" name="slide[btn_ghost_style]" <?php checked( $btn_ghost_style ); ?> /><?php _e( 'Ghost button style', 'cts-slider' ); ?>
		</label>
		<div class="cts-btn-style">
			<p>
			<label><?php _e( 'Button color', 'cts-slider' ); ?>
				<input type="text" name="slide[btn_bg_color]" id="cts-slide-link-btn-color" value="<?php echo esc_attr( $btn_bg_color ); ?>" />
			</label>
			<label><?php _e('Button text color', 'cts-slider'); ?>
				<input type="text" name="slide[btn_text_color]" id="cts-slide-link-btn-text-color" value="<?php echo esc_attr( $btn_text_color ); ?>" />
			</label>
			</p>
			<p>
				<label><?php _e('Button color on hover', 'cts-slider'); ?>
					<input type="text" name="slide[btn_bg_color_hover]" id="cts-slide-link-btn-color-hover" value="<?php echo esc_attr( $btn_bg_color_hover ); ?>" />
				</label>
				<label><?php _e('Button text color on hover', 'cts-slider'); ?>
					<input type="text" name="slide[btn_text_color_hover]" id="cts-slide-link-btn-text-color-hover" value="<?php echo esc_attr( $btn_text_color_hover ); ?>" />
				</label>
			</p>

		</div>
		</fieldset>
	<?php }

	public function save_metaboxes($post_id){
		//slide options
		if (isset($_POST['cts_slide_options'])){
			if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
				return;
			}

			if ( ! wp_verify_nonce( $_POST['cts_slide_options'], __FILE__ ) ){
				return;
			}

			$slide_meta = array();
			$slide_meta['hide_title']       = isset( $_POST['slide']['hide_title'] ) ? true : false;
			$slide_meta['title_color']      = sanitize_text_field( $_POST['slide']['title_color'] );
			$slide_meta['custom_title_position'] = isset( $_POST['slide']['custom_title_position'] ) ? true : false;
			$slide_meta['title_position']   = (int) intval( $_POST['slide']['title_position'] );
			$slide_meta['slide_desc']       = wp_kses( $_POST['slide']['slide_desc'] , array('br'=> array())) ;
			$slide_meta['desc_bold']        = isset( $_POST['slide']['desc_bold'] ) ? true : false;
			$slide_meta['desc_italic']      = isset( $_POST['slide']['desc_italic'] ) ? true : false;
			$slide_meta['desc_font_size']   = sanitize_text_field( $_POST['slide']['desc_font_size'] );
			$slide_meta['desc_align']       = sanitize_text_field( $_POST['slide']['desc_align'] );
			$slide_meta['desc_color']       = sanitize_text_field( $_POST['slide']['desc_color'] );
			$slide_meta['link_url']         = esc_url_raw( $_POST['slide']['link_url'] );
			$slide_meta['btn_link_text']    = sanitize_text_field( $_POST['slide']['btn_link_text'] );
			$slide_meta['btn_bg_color']     = sanitize_text_field( $_POST['slide']['btn_bg_color'] );
			$slide_meta['btn_bg_color_hover'] = sanitize_text_field( $_POST['slide']['btn_bg_color_hover'] );
			$slide_meta['btn_text_color']   = sanitize_text_field( $_POST['slide']['btn_text_color'] );
			$slide_meta['btn_text_color_hover'] = sanitize_text_field( $_POST['slide']['btn_text_color_hover'] );
			$slide_meta['btn_ghost_style']  = isset( $_POST['slide']['btn_ghost_style'] ) ? true : false;

			update_post_meta(
				$post_id,
				'_cts_slide_meta',
				$slide_meta
			);
		}

		//slide media
		if ( isset( $_POST['cts_slide_media'] ) ){
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
				return;
			}

			if ( !wp_verify_nonce( $_POST['cts_slide_media'], __FILE__ ) ){
				return;
			}

			$slide_media = array();
			$slide_media['img_url'] = esc_url_raw( $_POST['slide_media']['img_url'] );
			$slide_media['img_id']  = sanitize_text_field( $_POST['slide_media']['img_id'] );

			update_post_meta(
				$post_id,
				'_cts_slide_media',
				$slide_media
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
				$slide_media = get_post_meta($post_id, '_cts_slide_media', true);

				$img_src = wp_get_attachment_image_src( $slide_media['img_id'], 'medium' );
				$img_url = $img_src[0];

				echo sprintf('<img src="%s" class="slide-preview">', $img_url );
				break;
			default:
				break;
		}
	}
	
	public function slider_column($slider_columns){
		$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Name', 'cts-slider'),
		'shortcode' => __('Shortcode', 'cts-slider'),
		'slug' => __('Slug', 'cts-slider'),
		'posts' => __('Slides', 'cts-slider')
		);
		return $new_columns;
	}
	
	public function manage_slider_columns($column, $column_name, $theme_id){
		$slider = get_term($theme_id, 'cts_slider_category');
		switch ($column_name) {
			case 'shortcode':
				echo "[cts_slider slider='".$slider->slug."' ]";
				break;
			default:
				break;
		}
	}

	public function slider_create_add_options(){
		$default_opt = Constara_Slider_Slider::$default_opt;
		?>

		<div class="form-field cts-slider-create-options">
			<div class="settings">
				<p>
					<label for="cts_slider_opts[autoplay]"><?php _e('Autoplay', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[autoplay]">
						<option value="true" <?php selected('true', $default_opt['autoplay']) ?>><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $default_opt['autoplay']) ?>><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="cts_slider_opts[autoplayspeed]"><?php _e('Autoplay speed', 'cts-slider'); ?></label>
					<input type="number" value="<?php echo esc_attr($default_opt['autoplayspeed']); ?>" min="0" size="6" name="cts_slider_opts[autoplayspeed]" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="cts_slider_opts[speed]"><?php _e('Speed', 'cts-slider'); ?></label>
					<input type="number" min="0" value="<?php echo esc_attr($default_opt['speed']); ?>" size="6" name="cts_slider_opts[speed]" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="cts_slider_opts[fade]"><?php _e('Fade', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[fade]">
						<option value="true" <?php selected('true', $default_opt['fade']); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $default_opt['fade']); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="cts_slider_opts[dots]"><?php _e('Dots', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[dots]">
						<option value="true" <?php selected('true', $default_opt['dots']); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $default_opt['dots']); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="cts_slider_opts[arrows]"><?php _e('Arrows', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[arrows]">
						<option value="true" <?php selected('true', $default_opt['arrows']); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $default_opt['arrows']); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
			</div>
			<div class="height">
				<?php _e('Slider height', 'cts-slider'); ?>
				<p>
					<label for="cts_slider_opts[height_type]"><?php _e('Auto', 'cts-slider'); ?></label>
					<input type="radio" name="cts_slider_opts[height_type]" <?php checked('auto', $default_opt['height_type']); ?> value="auto" />
				</p>
				<p>
					<label for="cts_slider_opts[height_type]"><?php _e('Fixed', 'cts-slider'); ?></label>
					<input type="radio" name="cts_slider_opts[height_type]" <?php checked('fixed', $default_opt['height_type']); ?> value="fixed" />
					<input type="number" size="6" value="<?php echo esc_attr($default_opt['height_value']); ?>" min="0" name="cts_slider_opts[height_value]" />
					<span><?php  _e('px.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="cts_slider_opts[height_type]"><?php _e('Full', 'cts-slider'); ?></label>
					<input type="radio" name="cts_slider_opts[height_type]" <?php checked('full', $default_opt['height_type']); ?> value="full" />
				</p>
			</div>
		</div>

	<?php }

	public function slider_edit_add_options($term){
		$options        = get_option($term->slug);
		$default_opt    = Constara_Slider_Slider::$default_opt;
		$autoplay       = isset( $options['autoplay'] ) ? $options['autoplay'] : $default_opt['autoplay'];
		$autoplay_speed = isset( $options['autoplayspeed'] ) ? $options['autoplayspeed'] : $default_opt['autoplayspeed'];
		$speed          = isset( $options['speed'] ) ? $options['speed'] : $default_opt['speed'];
		$fade           = isset( $options['fade'] ) ? $options['fade'] : $default_opt['fade'];
		$dots           = isset( $options['dots'] ) ? $options['dots'] : $default_opt['dots'];
		$arrows         = isset( $options['arrows'] ) ? $options['arrows'] : $default_opt['arrows'];
		$height_type    = isset( $options['height_type'] ) ? $options['height_type'] : $default_opt['height_type'];
		$height_value   = isset( $options['height_value'] ) ? $options['height_value'] : $default_opt['height_value'];
		?>

		<tr class="form-field cts-slider-edit-options">
			<th scope="row" valign="top"><?php _e('Slider settings', 'cts-slider'); ?></th>
			<td>
			<p class="settings">
				<p>
					<label for="cts_slider_opts[autoplay]"><?php _e('Autoplay', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[autoplay]">
						<option value="true" <?php selected('true', $autoplay ); ?>><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $autoplay ); ?>><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="cts_slider_opts[autoplayspeed]"><?php _e('Autoplay speed', 'cts-slider'); ?></label>
					<input type="number" min="0" size="6" name="cts_slider_opts[autoplayspeed]" value="<?php echo esc_attr( $autoplay_speed ); ?>" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="cts_slider_opts[speed]"><?php _e('Speed', 'cts-slider'); ?></label>
					<input type="number" min="0" size="6" name="cts_slider_opts[speed]" value="<?php echo esc_attr( $speed ); ?>" />
					<span><?php _e('ms.', 'cts-slider'); ?></span>
				</p>
				<p>
					<label for="cts_slider_opts[fade]"><?php _e('Fade', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[fade]">
						<option value="true" <?php selected('true', $fade); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $fade); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="cts_slider_opts[dots]"><?php _e('Dots', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[dots]">
						<option value="true" <?php selected('true', $dots); ?>><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $dots); ?>><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
				<p>
					<label for="cts_slider_opts[arrows]"><?php _e('Arrows', 'cts-slider'); ?></label>
					<select name="cts_slider_opts[arrows]">
						<option value="true" <?php selected('true', $arrows); ?> ><?php _e('Yes', 'cts-slider'); ?></option>
						<option value="false" <?php selected('false', $arrows); ?> ><?php _e('No', 'cts-slider'); ?></option>
					</select>
				</p>
			</p>

			<h3><?php _e('Slider height', 'cts-slider'); ?></h3>
			<p>
				<label for="cts-slider-height-type-auto"><?php _e('Auto', 'cts-slider'); ?></label>
				<input type="radio" id="cts-slider-height-type-auto" name="cts_slider_opts[height_type]" value="auto" <?php checked('auto', $height_type); ?> />
			</p>
			<p>
				<label for="cts-slider-height-type-fixed"><?php _e('Fixed', 'cts-slider'); ?></label>
				<input type="radio" id="cts-slider-height-type-fixed" name="cts_slider_opts[height_type]" value="fixed" <?php checked('fixed', $height_type); ?> />
				<input type="number" min="0" size="6" name="cts_slider_opts[height_value]" value="<?php echo esc_attr( $height_value ); ?>" />
				<span><?php  _e('px.', 'cts-slider'); ?></span>
			</p>
			<p>
				<label for="cts_slider_opts[height_type]"><?php _e('Full', 'cts-slider'); ?></label>
				<input type="radio" name="cts_slider_opts[height_type]" value="full" <?php checked('full', $height_type); ?> />
			</p>
			</td>
		</tr>
	<?php }

	public function slider_add_options_save($term_id){
		$term = get_term($term_id, 'cts_slider_category');
		$option_name = Constara_Slider_Plugin::$plugin_prefix . $term->slug;
		if (isset($_POST['cts_slider_opts'])){
			$options = $_POST['cts_slider_opts'];
			$data_slider = array();
			$data_slider['autoplay'] 	  = sanitize_text_field( $options['autoplay'] );
			$data_slider['autoplayspeed'] = sanitize_text_field( $options['autoplayspeed'] );
			$data_slider['speed'] 		  = sanitize_text_field( $options['speed'] );
			$data_slider['fade'] 		  = sanitize_text_field( $options['fade'] );
			$data_slider['dots'] 		  = sanitize_text_field( $options['dots'] );
			$data_slider['arrows'] 		  = sanitize_text_field( $options['arrows'] );
			$data_slider['height_type']   = sanitize_text_field( $options['height_type'] );
			$data_slider['height_value']  = sanitize_text_field( $options['height_value'] );

			update_option($option_name, $data_slider);
		}
	}

	public function get_version(){
		return $this->version;
	}
}?>