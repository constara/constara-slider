<?php

/**
 * Created by PhpStorm.
 * User: kutas
 * Date: 21.04.2016
 * Time: 21:41
 */
class Constara_Slider_Slide{



    private $post_obj;

    protected $opts;

    public function __construct( WP_Post $post ){
        $this->post_obj = $post;
        $this->opts     = $this->retrieve_opts( $this->post_obj );
    }

	private function retrieve_opts( WP_Post $post ){
		$slide_meta             = get_post_meta( $post->ID, '_cts_slide_meta', true );
		$slide_media            = get_post_meta( $post->ID, '_cts_slide_media', true);
		$opts                   = array();
		//meta
		$opts['title']          = $post->post_title;
		$opts['desc']           = $post->post_content;
		$opts['hide_title']     = (bool) $slide_meta['hide_title'];
		$opts['title_position'] = (integer) $slide_meta['title_position'];
		$opts['slide_desc']     = (string) $slide_meta['slide_desc'];
		$opts['desc_bold']      = (bool) $slide_meta['desc_bold'];
		$opts['desc_italic']    = (bool) $slide_meta['desc_italic'];
		$opts['desc_font_size'] = (string) $slide_meta['desc_font_size'];
		$opts['link_url']       = (string) $slide_meta['link_url'];
		$opts['btn_link_text']  = (string) $slide_meta['btn_link_text'];
		$opts['btn_background_color']  = (string) $slide_meta['btn_background_color'];
		$opts['btn_ghost_style']= (bool) $slide_meta['btn_ghost_style'];
		//media
		$opts['img_url']        = (string) $slide_media['img_url'];

		return $opts;
	}

    public function get_style($selector){
        switch ($selector){
            case 'content':
                return 'style="top:' . esc_attr( $this->get_opt('title_position') ) . '%;"';
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
    	$style = '';
	    $style .= sprintf( 'background-image: url(%s);', esc_url( $this->get_opt('img_url') ) );

		echo esc_attr( $style );
    }

    public function the_content_style(){
    	$style = 'top: ' . $this->get_opt('title_position') . '%;';
    	echo esc_attr( $style );
    }

    public function the_title(){
    	//do not show title if hide_title is true
    	if ( $this->get_opt( 'hide_title' ) ){
    		return;
	    }
    	$title =  sprintf('<span class="cts-slide-title">%s</span>', sanitize_title( $this->get_opt('title') ) );

	    if ( $this->get_opt('link_url') ){
	    	$title = sprintf('<a href="%s">%s</a>', esc_url($this->get_opt('link_url')), $title);
	    }

	    echo $title;
    }


    public function the_desc(){
    	$style = '';
	    $style .= sprintf( 'font-weight: %s;', ( $this->get_opt('desc_bold') ) ? 'bold' : '200' );
	    $style .= sprintf( 'font-style: %s;', ( $this->get_opt('desc_italic') ) ? 'italic' : 'normal' );
	    $style .= sprintf( 'font-size: %s;', $this->get_opt('desc_font_size') . 'px' );

    	$desc = sprintf('<div class="cts-slide-description" style="%s">%s</div>', esc_attr($style) , $this->get_opt('slide_desc'));
	    echo $desc;
    }

	public function the_link_btn(){
		if ( $this->get_opt('btn_link_text') ){
			$class =  ( $this->get_opt('btn_ghost_style') ) ? 'cts-ghost-btn' : '';
			$style =  empty( $this->get_opt('btn_background_color') ) ? '' : sprintf( 'background-color: %s;', $this->get_opt('btn_background_color') );

			$btn_html = sprintf( '<a href="%s" title="%s"><button class="cts-slide-button %s" style="%s" >%s</button></a>',
				esc_url($this->get_opt('link_url')),
				esc_attr($this->get_opt('title')),
				esc_attr( $class ),
				esc_attr( $style ),
				sanitize_title($this->get_opt('btn_link_text')) );

			echo $btn_html;
		}
	}
    private function get_id(){
        return $this->id;
    }
    
    

}