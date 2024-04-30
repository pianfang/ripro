<?php

get_header();

$archive_item_config = zb_get_archive_item_config();

?>


<?php get_template_part( 'template-parts/archive-hero');?>


<section class="container">
	<?php do_action('ripro_ads', 'ad_archive_top'); ?>

	<div class="posts-warp row <?php echo esc_attr($archive_item_config['row_cols_class']);?>">
	<?php if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/loop/item', get_post_format() , $archive_item_config);
		endwhile;
	else :
		get_template_part( 'template-parts/loop/item','none');
	endif; 
	?>
	</div>

	<?php do_action('ripro_ads', 'ad_archive_bottum'); ?>


	<?php zb_pagination(array(
    	'range'  => 4,
    	'nav_class' => 'page-nav mt-4',
    	'nav_type'  => _cao('site_page_nav_type', 'click'),
    )); ?>

</section>

<?php
get_footer();
