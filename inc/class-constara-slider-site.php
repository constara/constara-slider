<?php 
/*
* Class for enqueues scripts, styles for front page.
*/
class CTS_Site {

	
	protected $version;
	static private $instance = null;

	private function __construct($version){
		$this->version = $version;
	}

	static function getInstance($version){

		if (is_null(self::$instance)){
			self::$instance = new CTS_Site($version);
		}
		return self::$instance;
	}

	public function enqueue_scripts(){
		wp_enqueue_script('slick-carousel', plugins_url('js/slick.min.js', dirname(__FIlE__)), array('jquery'), $this->get_version());
		wp_enqueue_script('cts-slider', plugins_url('js/slider.js', dirname(__FIlE__)), array('jquery'), $this->get_version());		
		wp_enqueue_style('slick-carousel', plugins_url('css/slick.min.css', dirname(__FIlE__)), $this->get_version());		
	}

	public function cts_slider_shortcode($atts){
		
		$slider = new CTS_Slider($atts);
		if ($slider->query->have_posts()){?>
			<div class="cts-slider" <?php echo $slider->get_opts(); ?> >
				<?php while ($slider->query->have_posts()){
					$slider->query->the_post();
					$slide = new CTS_Slide(get_the_ID());?>


					<div class="cts-slide" <?php $slide->get_style() ?>  >
						<?php if ($slide->has_link()){?>
							<a href="<?php echo $slide->get_opt('link_url');?>">
						<?php } ?>

							<img src="<?php echo $slide->get_opt('img_url');?>" class="cts-slide-img" >
							<h1>
								<?php echo $slide->show_title(); ?>
							</h1>
							<div class="desc">
								<?php echo  $slide->get_opt('content'); ?>
							</div>

						<?php if ($slide->has_link()){?>
							</a>
						<?php } ?>
					</div>

			<?php }?>

			</div>
		<?php }

	        wp_reset_postdata();
	    }

	public function get_slide_settings($post_id){

	}
	public function get_version(){
		return $this->version;
	}
}