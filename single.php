<?php

get_header();

while (have_posts()): the_post();

    get_template_part('template-parts/single', get_post_type());

endwhile; // End of the loop.

get_footer();
