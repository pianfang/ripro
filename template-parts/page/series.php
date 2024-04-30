<?php

get_header();


$orderby = trim(urldecode(get_param('orderby', 'count', 'get'))); //count name
$order = ($orderby=='count') ? 'desc' : 'asc';
$number = 20; // 每页显示的数目
$page = get_query_var('page') ? get_query_var('page') : 1; // 获取当前页码

$args = array(
	// 'taxonomy' => array('post_tag','category'),
	'taxonomy' => array('series'),
	'orderby'  => $orderby,
	'order'    => $order,
	'number'   => $number,
	'offset'   => ($page - 1) * $number, // 偏移量等于 (页码 - 1) * 每页数目
	'hide_empty' => false // for development
);

$tags = get_terms($args);
$total_pages = ceil(wp_count_terms($args['taxonomy']) / $number); // 计算总页数

$bg_image = get_template_directory_uri() . '/assets/img/bg.jpg';

?>

<div class="archive-hero text-center">
    <div class="archive-hero-bg lazy" data-bg="<?php echo $bg_image; ?>"></div>
    <div class="container py-3 py-md-4">
    	<h1 class="archive-title mb-0"><i class="fas fa-tags me-1"></i><?php _e('专题合集', 'ripro');?></h1>

    	<?php 

    	if (0) {
    		//修改这里括号里得 0 和 1 开启显示
    		get_search_form();
    	}

    	?>
    </div>
</div>

<section class="container">

	<div class="section-title text-center mb-4">
		<?php
		$orderbyOptions = [
	        'count' => __('按数量排序', 'ripro'),
	        'name' => __('按名称排序', 'ripro'),
	    ];

	    foreach ($orderbyOptions as $key => $name) {
	    	if (!in_array($key, ['count', 'name'])) {
		        continue; // 排除非 count 和 name 的选项
		    }
	    	$active = ($key==$orderby) ? 'active' : '';
	    	printf('<a class="btn btn-sm btn-outline-info px-4 mx-1 rounded-pill %s" href="%s"><i class="fas fa-sort me-1"></i>%s</a>',$active,esc_url_raw( add_query_arg('orderby', $key) ),$name);
	    }
	    ?>
	</div>

	<div class="series-page-warp row g-2 g-md-3 g-lg-4 row-cols-2 row-cols-md-3 row-cols-lg-4">
		<?php if ($tags): foreach ( $tags as $tag ) : $color = zb_get_color_class(mt_rand(1, 6));?>

			<?php

			$meta_bg = get_term_meta($tag->term_id, 'bg-image', true);
			$bg_img = (!empty($meta_bg)) ? $meta_bg : get_template_directory_uri() . '/assets/img/bg2.png';

			?>

			<div class="col">
				<a class="tag-item" href="<?php echo get_tag_link( $tag->term_id );?>" rel="tag" title="<?php echo $tag->name;?>">

				<div class="img-box">
	                <img class="lazy" src="<?php echo esc_attr($bg_img);?>" alt="<?php echo $tag->name;?>" />
	            </div>

				<div class="mt-2 text-center">
					<b class="text-dark"><?php echo $tag->name;?></b>
					<p class="mb-0 small text-muted"><span class="mr-2"><b class="b mr-1">+<?php echo $tag->count;?></b>个文章</span></p>
				</div>
		      	</a>
			</div>

		<?php endforeach;
		else:
			get_template_part('template-parts/loop/item', 'none');
		endif;?>
	</div>

	<?php zb_custom_pagination($page,$total_pages);?>
	

</section>

<?php get_footer();?>