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
    
    public function get_style($selector){
        switch ($selector){
            case 'content':
                return 'style="top:' . $this->get_opt('title_position') . '%;"';
                break;
            default:
                return null;
                break;
        }
    }

    private function get_opts(WP_Post $post){
        $opts = array();
        $opts['title'] = $post->post_title;
        $opts['desc'] = $post->post_content;
        $opts['hide_title'] =sanitize_title(get_post_meta($post->ID, '_cts_slide_hide_title', true));
        $opts['title_position'] = get_post_meta($post->ID, '_cts_slide_title_position', true);
        $opts['link_url'] = esc_url(get_post_meta($post->ID, '_cts_slide_link_url', true));
        $opts['img_url'] = esc_url(get_post_meta($post->ID, '_cts_slide_img_url', true));

        return $opts;
    }
    public function get_content(){
        the_content();
    }

    public function has_link(){
        if (empty($this->opts['link_url'])){
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

    public function show_title(){
        if ($this->opts['hide_title']){
            return '';
        } else {
            return $this->opts['title'];
        }
    }

    private function get_id(){
        return $this->id;
    }
    
    

}