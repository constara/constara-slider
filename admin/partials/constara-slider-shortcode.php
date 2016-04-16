<?php
/**
 * User: kutas
 * Date: 11.04.2016
 * Time: 5:06
 * Add slider shotcode
 */?>

<div class="slide" 
    style="background: linear-gradient(to bottom, rgba(0,0,0,0.09) 0%,rgba(0,0,0,0.19) 100%), url('<?php echo get_the_post_thumbnail_url();?>') center center no-repeat;">
    <div class="slide_content_block" >
        <h1 class="title"><?php the_title();?></h1>
        <div class="desc"><?php the_content(); ?></div>
    </div>

</div> <!-- .slide -->