<?php

/**
 * Class for enqueues scripts, styles, register meta boxes, shortcodes.
 * User: kutas
 * Date: 11.04.2016
 * Time: 1:13
 */

class CTS_Admin {

	protected $version;

	public function __construct($version) {
		$this->version = $version;
	}

	public function enqueue_scripts($hook){
		if ('post-new.php' == $hook || 'post.php' == $hook && 'cts_slide' == get_post_type()){
			wp_enqueue_style('jquery-ui', CTS_PLUG_ADMIN_URL . 'css/jquery-ui.css');
			wp_enqueue_script('jquery-ui-slider', array('jquery'));
			wp_enqueue_style('cts-slider', CTS_PLUG_ADMIN_URL . 'css/slider.css', $this->get_version());
			wp_enqueue_script('cts-slider', CTS_PLUG_ADMIN_URL . 'js/slider.js', array('jquery'), $this->get_version());
		}
	}

	public function register_post_type(){
				
		register_post_type('cts_slide', array(
			'labels'		=> array(
				'name' 				=> __('Slider','tracker' ),
				'menu_name'			=> __('CT Slider','tracker' ),
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
			'menu_icon'		=> 'dashicons-images-alt2',
		));
	}

	public function register_taxonomy(){
		register_taxonomy('cts_slides_category', 'cts_slide', array(
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

	public function register_metaboxes(){
		add_meta_box('slide-option', __('Slide Options', 'tracker'), array($this,'cts_slide_option'), 'cts_slide', 'normal', 'default');

	}
	
	public function cts_slide_option($post){
		wp_create_nonce(__FILE__, 'cts_slide_options');
		$post_id = $post->ID;
		$show_title = get_post_meta($post_id, '_cts_slide_show_title', true);
		$title_position = get_post_meta($post_id, '_cts_slide_title_position', true);
		$slide_link = get_post_meta($post_id, '_cts_slide_link', true);
		?>
		<p> <label for="cts_slide_show_title"><?php _e('Show slide title', 'tracker'); ?></label>
			<input type="checkbox" name="cts_slide_show_title" value="true" <?php checked($show_title, 'true'); ?>>
		</p>
		<p>
			<label for="cts_slide_title_position"><?php _e('Title position', 'tracker'); ?></label>
			<input type="text" size="5" name="cts_slide_title_position" id="cts_slide_title_position" value="<?php echo $title_position; ?>">
			<span id="set_default_title_position" class="button"><?php _e('set default', 'tracker'); ?></span>
		<div id="title-position"></div>
		<div class="title-position-desc"><?php _e('Choose title position for slide. Less value - higher title position', 'tracker'); ?></div>
		</p>
		<p>
			<label for="cts_slide_link"><?php _e('Slide link', 'tracker'); ?></label>
			<input type="text" name="cts_slide_link" id="cts_slide_link" size="70" value="<?php echo esc_attr($slide_link); ?>">
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
				'_cts_slide_show_title',
				$_POST['cts_slide_show_title']
			);

			update_post_meta(
				$post_id,
				'_cts_slide_title_position',
				sanitize_text_field($_POST['cts_slide_title_position'])
			);

			update_post_meta(
				$post_id,
				'_cts_slide_link',
				esc_url_raw($_POST['cts_slide_link'])
			);


		}
	}
	
	public function slider_column($slider_columns){
		$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __('Name', 'tracker'),
		'shortcode' => __('Shortcode', 'tracker'),
		//'description' => __('Description', 'tracker'),
		'slug' => __('Slug', 'tracker'),
		'posts' => __('Posts', 'tracker')
		);
		return $new_columns;
	}
	
	public function manage_slider_columns($column, $column_name, $theme_id){
		error_log($theme_id);
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
	
	public function cts_slider_shortcode($atts){
	    extract( shortcode_atts( array(
	        'slider'            => '',
	        'autoplay'          => 'yes',
	        'autoplayspeed'     => '6000',
	        'speed'             => '1000',
	        'fade'              => 'yes',
	        'adaptiveheight'    => 'false',
	        'dots'              => 'yes',
	        'arrows'            => 'yes',
	    ), $atts) );
	    $autoplay = ('yes' == $autoplay) ? 'true' : 'false';
	    $autoplayspeed = intval($autoplayspeed);
	    $speed = intval($speed);
	    $fade = ('yes' == $fade) ? 'true' : 'false';
	    $adaptiveheight = ('yes' == $adaptiveheight) ? 'true' : 'false';	    
	    $dots = ('yes' == $dots) ? 'true' : 'false';
	    $arrows = ('yes' == $arrows) ? 'true' : 'false';
	    

	    //set arrt for query
	    $query_attr = array(
	        'post_type'         => 'cts_slide',
	        'posts_per_page'    => -1,
	        'order'             => 'ASC',
	        'tax_query'         => array(
	            array(
	                'taxonomy'  => 'cts_slides_category',
	                'field'     => 'slug',
	                'terms'     => $slider,
	            )
	        )    );

	    $slides = new WP_Query($query_attr);
	    if ($slides->have_posts()){?>
	        <div class='slider'  data-slick='{
	            "autoplay": <?php echo $autoplay; ?>,
	            "autoplaySpeed": <?php echo $autoplayspeed; ?>,
	            "speed": <?php echo $speed; ?>,
	            "fade": <?php echo $fade; ?>,
	            "adaptiveHeight" : <?php echo $adaptiveheight; ?>,
	            "dots": <?php echo $dots; ?>,
	            "arrows": <?php echo $arrows; ?>
	        }'>

	        <?php while ($slides->have_posts()){
	            $slides->the_post();
			    $show_title = get_post_meta(get_the_ID(), '_cts_slide_show_title', true);
			    $title_position = get_post_meta(get_the_ID(), '_cts_slide_title_position', true);
			    $slide_link = get_post_meta(get_the_ID(), '_cts_slide_link', true);
			    echo $show_title . ' ' . $title_position . ' ' . $slide_link;

			    echo '<div class="slide" style="background: linear-gradient(to bottom, rgba(0,0,0,0.09) 0%,rgba(0,0,0,0.19) 100%), url(' . get_the_post_thumbnail_url() . ') center center no-repeat;">';
			    ?>
			    <div class="slide-content-block" style="<?php if (isset($title_position)){echo 'top:' . $title_position . '%';} ?>">
				    <?php
				    if ($show_title == 'true') {

					    if (!empty($slide_link)) {?>
						    <a href="<?php echo esc_url($slide_link); ?>">
							    <h1 class="title">
								    <?php echo get_the_title(); ?>
							    </h1>
						    </a>
					    <?php } else {
						    echo sprintf('<h1 class="title"></h1>', get_the_title());
					    }

				    }?>
				    <div class="desc"><?php echo get_the_content(); ?></div>
			    </div>

			    <?php
			    if (!empty($slide_link)) {
				    echo sprintf('<a class="slide-link" href="%s"></a>',$slide_link);
			    }
			    echo '</div>';// .slide

		    }

		    echo '</div>';
		    
	        wp_reset_postdata();
	    }
	}

	public function flush_rewrite_rules(){
		flush_rewrite_rules();
	}

	public function get_version(){
		return $this->version;
	}
}?>