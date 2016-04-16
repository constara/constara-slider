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

	public function register_post_type(){
				
		register_post_type('cts_slide', array(
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

	
	public function cts_slider($atts){
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
	            $slides->the_post();?>
	            
	            <div class="slide" style="background: linear-gradient(to bottom, rgba(0,0,0,0.09) 0%,rgba(0,0,0,0.19) 100%), url('<?php echo get_the_post_thumbnail_url();?>') center center no-repeat;">
		            <div class="slide_content_block" >
		                <h1 class="title"><?php the_title();?></h1>
		                <div class="desc"><?php the_content(); ?></div>
		            </div>

	            </div> <!-- .slide -->	          
	        <?php }

	        echo '</div>';//.slider
	        wp_reset_postdata();
	    }
	}

	public function flush_rewrite_rules(){
		flush_rewrite_rules();
	}

}?>