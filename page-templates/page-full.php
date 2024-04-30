<?php
/**
 * Template Name: 全宽页面
 * 
 * Description: 
 */


get_header();


while (have_posts()): the_post();

    get_template_part('template-parts/page','',array('full'=>true));

endwhile; // End of the loop.

get_footer();


