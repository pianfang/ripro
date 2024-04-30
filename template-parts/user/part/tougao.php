<?php

global $current_user;

$user_id = $current_user->ID;
$curr_page_action = sanitize_text_field(get_param('action','publish','get'));
$curr_post_id = intval(get_param('post_id','','get'));
$site_vip_options = _cao('site_vip_options');

if ($curr_page_action=='trash_post' && $curr_post_id) {
	// 获取文章的作者ID
    $post_author_id = get_post_field('post_author', $curr_post_id);
	
	// 判断文章的作者ID是否与当前用户ID相同
    if ($post_author_id == $user_id) {
        // 将文章移至回收站
        wp_trash_post($curr_post_id);
        $curr_page_action = 'trash';
    }
}elseif ($curr_page_action=='delete_post' && $curr_post_id) {
	// 获取文章的作者ID
    $post_author_id = get_post_field('post_author', $curr_post_id);
	
	// 判断文章的作者ID是否与当前用户ID相同
    if ($post_author_id == $user_id || current_user_can('delete_others_posts')) {
        // 删除文章...
        wp_delete_post($curr_post_id, true);
        $curr_page_action = 'trash';
    }
}

$page_link = get_uc_menu_link('tougao');


?>

<!-- 投稿列表 -->
<div class="card user-tougao-warp">
	<div class="card-header mb-4">
		<h5 class="fw-bold mb-0 d-flex align-items-center justify-content-between"><?php _e('投稿管理', 'ripro');?>
		<a class="btn btn-sm btn-success" href="<?php echo esc_url(home_url('/tougao' ));?>"><i class="fas fa-edit me-1"></i><?php _e('新建投稿', 'ripro');?></a>
		</h5>
		<div class="mt-2">
			<?php
			$menus = [
				['title'=>__('已发布', 'ripro'),'icon'=>'far fa-check-circle','key'=>'publish'],
				['title'=>__('草稿', 'ripro'),'icon'=>'fas fa-circle-notch','key'=>'draft'],
				['title'=>__('待审核', 'ripro'),'icon'=>'far fa-circle','key'=>'pending'],
				['title'=>__('回收站', 'ripro'),'icon'=>'far fa-trash-alt','key'=>'trash'],
			];
			foreach ($menus as $menu) {
				$_link = esc_url(add_query_arg(array('action' => $menu['key']),$page_link));
				$classes = ($curr_page_action==$menu['key']) ? ' text-dark' : ' text-muted';
				printf('<a class="me-2%s" href="%s"><i class="%s me-1"></i>%s</a>',$classes,$_link,$menu['icon'],$menu['title']);
			}

			?>
		</div>
	</div>
	<div class="card-body">

	<?php

	$page = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1; //第几页
	$limit = 10; //每页显示数量

	$post_filter = (in_array($curr_page_action, array('publish','draft','pending','trash'))) ? $curr_page_action : 'publish';
	$args = array(
	    'post_type' => 'post',
	    'post_status' => $post_filter,
	    'posts_per_page' => $limit,
	    'paged' => $page,
	    'ignore_sticky_posts' => 1,
	    'author' => $current_user->ID,
	    'orderby' => 'date',
	    'order' => 'DESC'
	);
	$PostData = new WP_Query($args);

	?>

	<?php if ($PostData->have_posts() ) : ?>
		<div class="row g-2 g-md-2 row-cols-1">
		<?php while ( $PostData->have_posts() ) : $PostData->the_post(); ?>

			<?php 

			$post_id = get_the_id();

			$post_prices = get_post_price_data($post_id);
			$post_price = $post_prices['default'];

			?>

			<div class="col">
				<article class="post-item item-list">

					<div class="tips-badge position-absolute top-0 start-0 z-1 m-3 m-md-3">
						<?php if (is_sticky()) :?>
						<div class="badge bg-dark bg-opacity-25"><?php _e('置顶', 'ripro'); ?></div>
						<?php endif;?>
					</div>

					<div class="entry-media ratio ratio-3x2 col-auto">
						<a target="<?php echo get_target_blank();?>" class="media-img lazy bg-cover bg-center" href="<?php the_permalink();?>" title="<?php the_title();?>" data-bg="<?php echo zb_get_thumbnail_url();?>"></a>
					</div>
					<div class="entry-wrapper">
						<div class="entry-body">
							<div class="entry-cat-dot"><?php zb_meta_category(2);?></div>
							<h2 class="entry-title">
								<a target="<?php echo get_target_blank();?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
							</h2>
						</div>
						<div class="entry-footer">
							<div class="entry-meta">
								<span class="meta-date"><i class="far fa-clock me-1"></i><?php zb_meta_datetime();?></span>
								<span class="meta-likes d-none d-md-inline-block"><i class="far fa-heart me-1"></i><?php echo zb_get_post_likes();?></span>
								<span class="meta-fav d-none d-md-inline-block"><i class="far fa-star me-1"></i><?php echo zb_get_post_fav();?></span>
								<span class="meta-views"><i class="far fa-eye me-1"></i><?php echo zb_get_post_views();?></span>
								<?php if (is_site_shop() && post_is_pay($post_id)) :?>
								<span class="meta-price"><i class="<?php echo get_site_coin_icon();?> me-1"></i><?php echo $post_price;?></span>
								<?php endif;?>
								<span class="meta-action">

									<a target="_blank" href="<?php echo esc_url(add_query_arg(array('post_id' => $post_id),home_url('/tougao')));?>" class="text-info"><i class="bi bi-pencil-square fa-fw me-1"></i><?php _e('编辑', 'ripro');?></a>

									<?php $retVal = ($curr_page_action=='trash') ? 'delete_post' : 'trash_post';?>
									<a href="<?php echo esc_url(add_query_arg(array('action' => $retVal,'post_id' => $post_id),$page_link));?>" class="text-danger"><i class="bi bi-trash3 fa-fw me-1"></i><?php _e('删除', 'ripro');?></a>
								</span>

							</div>
						</div>

					</div>
				</article>
			</div>

		<?php endwhile;?>
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


