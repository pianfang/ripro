<?php

if (empty($args)) {
    return;
}

$config = [
    'lazyLoad'   => false,
    'autoplay'   => false,
    'loop'       => false,
    'nav'        => false,
    'dots'       => false,
    'responsive' => [
        0   => ['items' => 2, 'nav' => false],
        768 => ['items' => 3, 'nav' => false],
        992 => ['items' => 5, 'nav' => true],
    ],
];

$cat_ids = (array) $args['category'];

$taxonomy_name = 'category';

$__args = array(
    'taxonomy'   => $taxonomy_name,
    'hide_empty' => false,
    'parent'     => 0,
    'include'    => $cat_ids,
    'orderby'    => 'include',
);

$terms = get_terms($__args);

if (is_wp_error($terms) || empty($terms)) {
    return;
}

$def_bg_img = get_template_directory_uri() . '/assets/img/bg.jpg'; //默认缩略图

?>


<section class="container">
	<div class="widget-catbox owl-carousel owl-theme" data-config='<?php echo json_encode($config);?>'>

		<?php foreach ($terms as $key => $item) : 

			$meta_bg = get_term_meta($item->term_id, 'bg-image', true);

			$bg_img = (!empty($meta_bg)) ? $meta_bg : $def_bg_img;

			$color = zb_get_color_class($key);
		?>

		<div class="item">
			<div class="widget-catbox-item lazy" data-bg="<?php echo $bg_img;?>">
				
				<a href="<?php echo get_term_link($item->term_id, $taxonomy_name);?>">
					<div class="catbox-content">
						<?php if (!empty($args['is_num'])) : ?>
							<span class="badge bg-<?php echo $color;?> text-white bg-opacity-75 mb-2"><?php echo $item->count;?>+</span>
						<?php endif; ?>
	                	<h3 class="catbox-title"><?php echo $item->name;?></h3>
	                </div>
				</a>
				
			</div>
		</div>

		<?php endforeach;?>
	  	
	</div>
</section>

