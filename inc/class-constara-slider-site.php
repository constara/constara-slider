<?php 
/*
* Class for enqueues scripts, styles for front page.
*/
class CTS_Site {
	
	protected $version;

	function __construct($version){
		$this->version = $version;
	}

	public function enqueue_scripts(){
		wp_enqueue_script('slick-carousel', plugins_url('js/slick.min.js', dirname(__FIlE__)), array('jquery'), $this->get_version());
		wp_enqueue_script('cts-slider', plugins_url('js/slider.js', dirname(__FIlE__)), array('jquery'), $this->get_version());		
		wp_enqueue_style('slick-carousel', plugins_url('css/slick.min.css', dirname(__FIlE__)), $this->get_version());		
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


	public function get_version(){
		return $this->version;
	}
}