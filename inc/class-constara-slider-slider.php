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
        $this->slider_options = $this->set_slider_options($this->name);
        $this->data_slick = $this->set_data_slick($this->slider_options);
        $this->data_slider = $this->set_data_slider($this->slider_options);
        $this->query = $this->do_query($this->name);
    }

    protected function set_slider_options($slider_name){
        $slider_options = get_option($slider_name);
        return $slider_options;
    }


    protected function set_data_slick($slider_options){
        $slick_options = $slider_options['slick'];
        $data_slick = array();
        $data_slick['autoplay']         = $slick_options['autoplay'];
        $data_slick['autoplayspeed']    = intval($slick_options['autoplayspeed']);
        $data_slick['speed']            = intval($slick_options['speed']);
        $data_slick['fade']             = $slick_options['fade'];
        $data_slick['adaptiveheight']   = $slick_options['adaptiveheight'];
        $data_slick['dots']             = $slick_options['dots'];
        $data_slick['arrows']           = $slick_options['arrows'];

        return $data_slick;
    }

    protected function set_data_slider($slider_options){
        $data_slider = $slider_options['slider'];
        return $data_slider;
    }


    protected function set_name($attr){
        $name = '' ;
        if(!empty($attr['slider']) && is_string($attr['slider'])){
            $name = $attr['slider'];
        }
        return $name;
    }


    public function do_query($name){
        //set arrt for query
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
