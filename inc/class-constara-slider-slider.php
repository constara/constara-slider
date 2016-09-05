<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 23:59
 */
class Constara_Slider_Slider{

    protected $name;

    protected $slider_options;

    protected $data_slick;

    protected $data_slider;

    public $query;

    public function __construct($attr){
        $this->name = $this->set_name($attr);
        $this->slider_options = $this->get_slider_options($this->name);
        $this->query = $this->get_query($this->name);
    }


	protected function set_name($attr){
		$name = '' ;
		if ( !empty( $attr['slider'] ) ){
			$name = $attr['slider'];
		}
		return $name;
	}

	protected function get_slider_options($slider_name){
        $slider_options = get_option($slider_name);
	    if ( !$slider_options ){
            $slider_options = $this->get_default_slider_options();
	    }
        return $slider_options;
    }

    private function get_default_slider_options(){

    	$defaults = array(
    	    'slick'     => array(
    	    	'autoplay'      => 'true',
		        'autoplayspeed' => '2000',
		        'speed'         => '600',
		        'fade'          => 'true',
		        'dots'          => 'true',
		        'arrows'        => 'true',
	        ),
		    'slider'    => array(
		    	'height_type'   => 'auto',
		    ),
	    );

	    return $defaults;
    }


    public function get_query($name){
        //set attr for query
        $query_attr = array(
            'post_type'         => 'cts_slide',
            'posts_per_page'    => -1,
            'order'             => 'ASC',
            'tax_query'         => array(
                array(
                    'taxonomy'  => 'cts_slides_category',
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
        foreach ($this->slider_options['slick'] as $opt => $value){
            $data_slick .= sprintf('"%s": %s, ',$opt, $value);
        }
        $data_slick = substr($data_slick,0,-2);
        $data_slick .= "}'";

        //options for slider from user
        $data_slider = "data-slider='{";
        foreach ($this->slider_options['slider'] as $opt => $val){
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
