<?php

global $current_user;

$ids = get_user_meta($current_user->ID,'follow_post',true);
if (empty($ids)) {
    $ids = array(0);
}

$page = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1; //第几页
$limit = 12; //每页显示数量

$args = array(
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'paged' => $page,
    'post__in' => $ids,
    'has_password' => false,
    'ignore_sticky_posts' => 1,
    'orderby' => 'date',
    'order' => 'DESC'
);

$PostData = new WP_Query($args);

$item_config = [
	'type' => 'grid-overlay', // grid  grid-overlay list
	'media_class' => 'ratio-3x2', // ratio-1x1  3x2 3x4 4x3 16x9
	'media_size_type' => 'bg-cover',
	'media_fit_type' => 'bg-center',
	'is_vip_icon' => true,
	'is_entry_cat' => true,
	'is_entry_desc' => false,
	'is_entry_meta' => true,
];


?>


<div class="card">
	<div class="card-header mb-4">
      <h5 class="fw-bold mb-0"><?php _e('收藏列表','ripro');?></h5>
    </div>
	<div class="card-body">

		<?php if ($PostData->have_posts() ) : ?>
			<div class="row g-2 g-md-2 row-cols-2 row-cols-md-3">
			<?php while ( $PostData->have_posts() ) : $PostData->the_post();

				get_template_part( 'template-parts/loop/item', get_post_format() ,$item_config);

			endwhile;?>
			</div>
			<?php 
			zb_custom_pagination($page,$PostData->max_num_pages);
			wp_reset_postdata();
		else :

			echo '<p class="w-100 p-4 text-center">'.__('暂无内容','ripro').'</p>';

		endif;
		?>

		
	</div>

</div>