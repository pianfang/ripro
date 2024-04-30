<?php
/**
 * Template Name: Html页面
 * 
 * Description: 
 */


get_header();

while (have_posts()): the_post();

    the_content();

endwhile; // End of the loop.

get_footer();


