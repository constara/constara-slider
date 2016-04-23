<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 21:41
 */
class CTS_Slide{

    private $id;

    private $post_obj;

    protected $opts;

    public function __construct($slide_id){
        $this->id = $slide_id;
        $this->post_obj =  get_post($this->id);
        $this->opts =  $this->get_opts($this->post_obj);
    }

    protected function get_post_obj($id){
        $this->post_obj = get_post($id);
    }
    
    public function get_style(){
            return 'style=""';
    }

    private function get_opts(WP_Post $post){
        $opts = array();
        $opts['title'] = $post->post_title;
        $opts['content'] = $post->post_content;
        $opts['show_title'] = get_post_meta($post->ID, '_cts_slide_show_title', true);
        $opts['title_position'] = get_post_meta($post->ID, '_cts_slide_title_position', true);
        $opts['slide_link'] = get_post_meta($post->ID, '_cts_slide_link', true);
        $opts['img_url'] = get_post_meta($post->ID, '_cts_slide_img_url', true);

        return $opts;
    }
    public function get_content(){
        the_content();
    }

    public function has_link(){
        if (empty($this->opts['slide_link'])){
            return false;
        } else {
            return true;
        }
    }

    public function get_opt($key){
        $option = $this->opts[$key];
        return $option;
    }

    public function the_img(){
    }

    private function get_id(){
        return $this->id;
    }
    
    

}