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


	public function get_version(){
		return $this->version;
	}
}