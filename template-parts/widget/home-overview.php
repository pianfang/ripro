<?php

if (empty($args)) {
    return;
}

$cols = ( count($args['datas']) >= 6 ) ? 6 : count($args['datas']) ;
$row_cols_class = 'row-cols-3 row-cols-md-4 row-cols-lg-'.$cols.' g-2';

?>

<div class="bg-warp <?php echo esc_attr($args['bg_style']);?> lazy" data-bg="<?php echo esc_url($args['bg_img']); ?>">
	<div class="container py-3 py-md-4">
		

		<div class="row <?php echo esc_attr($row_cols_class); ?>">
			<?php 
			global $wpdb;

			if (in_array('coutn_day', $args['datas'])) {
				$_count = floor((time()-strtotime($args['time']))/86400);
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="fas fa-chart-line me-1"></i>%s</strong></div></div>',$_count,__('运营天数','ripro' ));
			}

			if (in_array('count_post', $args['datas'])) {
				$_count = wp_count_posts()->publish;
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="fas fa-database me-1"></i>%s</strong></div></div>',$_count,__('文章总数','ripro' ));
			}

			if (in_array('count_user', $args['datas'])) {
				$_count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="fas fa-users me-1"></i>%s</strong></div></div>',$_count,__('用户总数','ripro' ));
			}

			if (in_array('count_user_vip', $args['datas'])) {
				$_count = $wpdb->get_var("SELECT count(a.ID) FROM $wpdb->users a INNER JOIN $wpdb->usermeta b ON ( a.ID = b.user_id ) WHERE (  ( b.meta_key = 'cao_user_type' AND b.meta_value = 'vip' )  )");
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="far fa-gem me-1"></i>%s</strong></div></div>',$_count,__('VIP会员','ripro' ));
			}

			if (in_array('count_day_user', $args['datas'])) {
				$_count = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE DATE_FORMAT( user_registered,'%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="fas fa-user-plus me-1"></i>%s</strong></div></div>',$_count,__('今日注册','ripro' ));
			}

			if (in_array('conunt_up_post', $args['datas'])) {
				// 获取当前日期时间，并计算7天前的日期时间
				$current_date = current_time('mysql');
				$seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days', strtotime($current_date)));

				// 构建 SQL 查询语句
				$query = $wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' AND post_date > %s", $seven_days_ago);
				$_count = $wpdb->get_var($query);
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="far fa-calendar-plus me-1"></i>%s</strong></div></div>',$_count,__('近7天更新','ripro' ));
			}

			if (in_array('conunt_cats', $args['datas'])) {
				$_count = wp_count_terms('category');
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="far fa-folder me-1"></i>%s</strong></div></div>',$_count,__('分类总数','ripro' ));
			}

			if (in_array('conunt_comment', $args['datas'])) {
				$_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments");
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="far fa-comments me-1"></i>%s</strong></div></div>',$_count,__('评论总数','ripro' ));
			}

			if (in_array('count_post_views', $args['datas'])) {
				$views_data = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='views'");
				$_count = 0;
				if ($views_data) {
				    foreach ($views_data as $view) {
				        $_count += (int) $view->meta_value;
				    }
				}
				printf('<div class="col"><div class="count-item"><span class="count-num">%s</span><strong><i class="fab fa-hotjar me-1"></i>%s</strong></div></div>',$_count,__('浏览量总数','ripro' ));
			}


			?>
		</div>
	</div>
</div>

