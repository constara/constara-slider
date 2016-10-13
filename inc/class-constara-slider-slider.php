<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 23:59
 */
class Constara_Slider_Slider{

    protected $name;

	protected $slider_slug;

    protected $slider_options;

    private $data_slick;

    private $data_slider;

	public static $default_opt = array(
		'autoplay'      => 'true',
		'autoplayspeed' => 2000,
		'speed'         => 600,
		'fade'          => 'true',
		'dots'          => 'true',
		'arrows'        => 'true',
		'height_type'   => 'auto',
		'height_value'  => '400',
	);

    public $query;

    public function __construct($attr){
    	$this->slider_slug = $attr['slider'];
        $this->set_name($this->slider_slug);
        $this->slider_options = $this->get_slider_options($this->name);
		$this->set_data();
        $this->query = $this->get_query($this->slider_slug);
    }



	protected function set_name($slider_slug){
		$this->name =  Constara_Slider_Plugin::$plugin_prefix . $slider_slug;


	}

	private function set_data(){
		$opts = $this->slider_options;
		$this->data_slick = array(
			'autoplay'      => $opts['autoplay'],
			'autoplayspeed' => $opts['autoplayspeed'],
			'speed'         => $opts['speed'],
			'fade'          => $opts['fade'],
			'dots'          => $opts['dots'],
			'arrows'        => $opts['arrows'],
		);

		$this->data_slider = array(
			'height_type' => $opts['height_type'],
			'height_value' => $opts['height_value'],
		);
	}

	protected function get_slider_options($slider_name){
		error_log($slider_name);
		$slider_options = get_option($slider_name);
		if ( !$slider_options ){
			$slider_options = self::$default_opt;
		}
		return $slider_options;
    }

    public function get_query($name){
        //set attr for query
        $query_attr = array(
            'post_type'         => 'cts_slide',
            'posts_per_page'    => -1,
            'order'             => 'ASC',
            'tax_query'         => array(
                array(
                    'taxonomy'  => 'cts_slider_category',
                    'field'     => 'slug',
                    'terms'     => $name,
                )
            )    );

        $query = new WP_Query($query_attr);
        return $query;
    }

    public function get_opts(){
        $opts = '';
        //options for slick.js script
        $data_slick = "data-slick='{";
        foreach ($this->data_slick as $opt => $value){
            $data_slick .= sprintf('"%s": %s, ',$opt, $value);
        }
        $data_slick = substr($data_slick,0,-2);
        $data_slick .= "}'";

        //options for slider from user
        $data_slider = "data-slider='{";
        foreach ($this->data_slider as $opt => $val){
        	if (!empty($val)){
		        $data_slider .= sprintf('"%s": "%s", ', $opt, $val);
	        }
        }
        $data_slider = substr($data_slider, 0, -2);
        $data_slider .= "}'";

        $opts .= $data_slick . ' ' . $data_slider;
        return $opts;
    }


}
