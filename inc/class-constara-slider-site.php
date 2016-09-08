<?php 
/*
* Class for enqueues scripts, styles for front page.
*/
class Constara_Slider_Site {

	
	protected $version;
	static private $instance = null;

	private function __construct($version){
		$this->version = $version;
		$this->wp_hooks();
	}

	static function getInstance($version){

		if (is_null(self::$instance)){
			self::$instance = new Constara_Slider_Site($version);
		}
		return self::$instance;
	}

	private function wp_hooks(){
		add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode('cts_slider', array( $this, 'cts_slider_shortcode' ) );
	}

	public function enqueue_scripts(){
		wp_enqueue_script('slick-carousel', plugins_url('js/slick.min.js', dirname(__FIlE__)), array('jquery'), $this->get_version());
		wp_enqueue_script('cts-slider', plugins_url('js/slider.js', dirname(__FIlE__)), array('jquery'), $this->get_version());
		wp_enqueue_style('slick-carousel', plugins_url('css/slick.min.css', dirname(__FIlE__)), $this->get_version());
		wp_enqueue_style('cts-default-theme', CTS_PLUGIN_URL . 'css/themes/default.css', $this->get_version());
	}

	public function cts_slider_shortcode($attr){
		
	$slider = new Constara_Slider_Slider($attr);
		if ($slider->query->have_posts()){?>

		<div class="cts-slider" id="cts-slider" <?php echo $slider->get_opts(); ?> >
			<?php while ($slider->query->have_posts()){
				$slider->query->the_post();
				$slide = new Constara_Slider_Slide( get_post() );?>

				<div class="cts-slide" style="<?php $slide->the_slide_style(); ?>">
					<div class="cts-content-box" style="<?php $slide->the_content_style(); ?>">
						<?php $slide->the_title();
						$slide->the_desc();
						$slide->the_link_btn();
						?>
					</div>
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