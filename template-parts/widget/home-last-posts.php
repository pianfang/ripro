<?php

if (empty($args)) {
    return;
}

// 查询
$query_args = array(
    'paged'               => get_query_var('paged', 1),
    'ignore_sticky_posts' => false,
    'post_status'         => 'publish',
    'category__not_in'    => $args['no_cat'],
);

$PostData = new WP_Query($query_args);

$item_config = zb_get_archive_item_config(0);

?>

<section class="container">
	<?php 
	    $section_title = $args['title'];
	    $section_desc = $args['desc'];
	?>
	<?php if ($section_title): ?>
	    <div class="section-title text-center mb-4">
	      <h3><?php echo $section_title ?></h3>
	      <?php if (!empty($section_desc)): ?>
	        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
	      <?php endif; ?>
	    </div>
	<?php endif; ?>

	<?php if (!empty($args['cat_btn'])): ?>
	    <div class="section-cat-navbtn text-center mb-4">
	      <?php

	      printf('<a target="_slef" class="btn btn-sm m-1 px-3 active" href="">%s</a>',__('最新','ripro'));

	      foreach ($args['cat_btn'] as $cat_id) {
	      	$item = get_term($cat_id, 'category');
	      	if (!$item) { continue; }

	      	printf('<a target="_blank" class="btn btn-sm m-1 px-3" href="%s">%s</a>',get_term_link($item->term_id, 'category'),$item->name);

	      }


	      ?>


	    </div>
	<?php endif; ?>

	<div class="posts-warp row <?php echo esc_attr($item_config['row_cols_class']); ?>">
		<?php if ($PostData->have_posts()):
			while ($PostData->have_posts()): $PostData->the_post();
				get_template_part('template-parts/loop/item', '', $item_config);
			endwhile;
		else:
			get_template_part('template-parts/loop/item', 'none');
		endif;?>
	</div>

	<?php if (!empty($args['is_pagination'])) {
		zb_pagination(array(
			'custom_query' => $PostData
		));
	}?>

</section>

<?php wp_reset_postdata();?>
