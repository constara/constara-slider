<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 23:59
 */
class CTS_Slider{

    protected $attr;

    public $query;

    public function __construct($attr){
        $this->attr = $this->set_attr($attr);
        $this->query = $this->do_query($this->attr['name']);
    }

    protected function set_attr($attr){
        $slider_opts = array();
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
        $slider_opts['name'] = $slider;
        $slider_opts['autoplay'] = ('yes' == $autoplay) ? 'true' : 'false';
        $slider_opts['autoplayspeed'] = intval($autoplayspeed);
        $slider_opts['speed'] = intval($speed);
        $slider_opts['fade'] = ('yes' == $fade) ? 'true' : 'false';
        $slider_opts['adaptiveheight'] = ('yes' == $adaptiveheight) ? 'true' : 'false';
        $slider_opts['dots'] = ('yes' == $dots) ? 'true' : 'false';
        $slider_opts['arrows'] = ('yes' == $arrows) ? 'true' : 'false';

        return $slider_opts;
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
        $opts = "data-slick='{";
        foreach ($this->attr as $opt => $value){
            if ($opt == 'name'){
                continue;
            }
            $opts .= sprintf('"%s": %s, ',$opt, $value);
        }
        $opts = substr($opts,0,-2);

        $opts .= "}'";

        return $opts;
    }

}
