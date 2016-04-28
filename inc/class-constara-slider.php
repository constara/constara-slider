<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 23:59
 */
class CTS_Slider{

    protected $name;

    protected $data_slick;

    protected $data_slider;

    public $query;

    public function __construct($attr){
        $this->name = $this->set_name($attr);
        $this->data_slick = $this->set_data_slick($attr);
        $this->data_slider = $this->set_data_slider($this->name);
        $this->query = $this->do_query($this->name);
    }

    protected function set_data_slick($attr){
        $data_slick = array();
        extract( shortcode_atts( array(
            'slider'            => '',
            'autoplay'          => 'yes',
            'autoplayspeed'     => '6000',
            'speed'             => '1000',
            'fade'              => 'yes',
            'adaptiveheight'    => 'false',
            'dots'              => 'yes',
            'arrows'            => 'yes',
        ), $attr) );
        $data_slick['name'] = $slider;
        $data_slick['autoplay'] = ('yes' == $autoplay) ? 'true' : 'false';
        $data_slick['autoplayspeed'] = intval($autoplayspeed);
        $data_slick['speed'] = intval($speed);
        $data_slick['fade'] = ('yes' == $fade) ? 'true' : 'false';
        $data_slick['adaptiveheight'] = ('yes' == $adaptiveheight) ? 'true' : 'false';
        $data_slick['dots'] = ('yes' == $dots) ? 'true' : 'false';
        $data_slick['arrows'] = ('yes' == $arrows) ? 'true' : 'false';

        return $data_slick;
    }

    protected function set_data_slider($slider_name){
        $options = get_option($slider_name);

       
        
        return $options;
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
            if ($opt == 'name') continue;
            $data_slick .= sprintf('"%s": %s, ',$opt, $value);
        }
        $data_slick = substr($data_slick,0,-2);
        $data_slick .= "}'";

        //options for slider from user
        $data_slider = "data-slider='{";
        foreach ($this->data_slider as $opt => $val){
            $data_slider .= sprintf('"%s": "%s", ', $opt, $val);
        }
        $data_slider = substr($data_slider, 0, -2);
        $data_slider .= "}'";


        $opts .= $data_slick . ' ' . $data_slider;
        return $opts;
    }


}
