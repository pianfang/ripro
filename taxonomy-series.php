<?php

get_header();

$cat_id = get_queried_object_id();

$meta_bg = get_term_meta($cat_id, 'bg-image', true);
$bg_img = (!empty($meta_bg)) ? $meta_bg : zb_get_thumbnail_url();


$archive_item_config = zb_get_archive_item_config($cat_id);

// zb_dump($cat_id);

?>


<section class="container">

	<div class="archive-series-top p-3 mb-4 bg-white rounded-2">


		<div class="row">
			<div class="col-12 col-md-4">
				<div class="img-box mb-3 mb-md-0">
	                <img class="lazy" src="<?php echo esc_attr($bg_img);?>" alt="<?php the_archive_title( '', '' );?>" />
	            </div>
			</div>
			<div class="col-12 col-md-8">
				<div class="desc-box">
	                <?php
		            the_archive_title( '<h3 class="archive-title mb-3">', '</h3>' );

		            the_archive_description();
		            ?>
	            </div>
			</div>

			<div class="col-12 position-relative"> <a class="more-a" href="<?php echo esc_url(home_url('/series'));?>">更多专题></a> </div>

		</div>

	</div>



	<div class="posts-warp row <?php echo esc_attr($archive_item_config['row_cols_class']); ?>">
		<?php if (have_posts()):
			while (have_posts()): the_post();
				get_template_part('template-parts/loop/item', get_post_format(), $archive_item_config);
			endwhile;
		else:
			get_template_part('template-parts/loop/item', 'none');
		endif;
		?>
	</div>


	<?php zb_pagination(array(
		'range'     => 4,
		'nav_class' => 'page-nav mt-4',
		'nav_type'  => _cao('site_page_nav_type', 'click'),
	));?>

</section>

<?php
get_footer();
