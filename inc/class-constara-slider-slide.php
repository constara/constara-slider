<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 21:41
 */
class Constara_Slider_Slide{



    private $slide_obj;

    protected $opts;

    public function __construct( WP_Post $slide ){
        $this->slide_obj =  $slide;
        $this->opts =  $this->retrieve_opts($this->slide_obj);
    }

	private function retrieve_opts( WP_Post $post ){
		$opts = array();
		$opts['title'] = $post->post_title;
		$opts['desc'] = $post->post_content;
		$opts['hide_title'] =sanitize_title(get_post_meta($post->ID, '_cts_slide_hide_title', true));
		$opts['title_position'] = get_post_meta($post->ID, '_cts_slide_title_position', true);
		$opts['link_url'] = esc_url(get_post_meta($post->ID, '_cts_slide_link_url', true));
		$opts['img_url'] = esc_url(get_post_meta($post->ID, '_cts_slide_img_url', true));

		return $opts;
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


    public function the_slide_style(){

    	echo sprintf('background-image: url(%s);', $this->get_opt('img_url'));

    }

    public function the_content_style(){
    	echo 'top: ' . $this->get_opt('title_position') . '%;';
    }

    public function the_title(){
    	$title =  sprintf('<span class="cts-slide-title">%s</span>', $this->get_opt('title'));

	    if ( $this->get_opt('link_url') ){
	    	$title = sprintf('<a href="%s">%s</a>', esc_url($this->get_opt('link_url')), $title);
	    }

	    echo $title;
    }

    public function show_title(){
        if ($this->opts['hide_title']){
            return '';
        } else {
            return $this->opts['title'];
        }
    }

    public function the_desc(){
    	$desc = sprintf('<div class="cts-slide-description">%s</div>', $this->get_opt('desc'));
	    echo $desc;
    }

    private function get_id(){
        return $this->id;
    }
    
    

}