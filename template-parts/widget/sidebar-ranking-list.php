<?php

if (empty($args)) {
    return;
}

// 查询
$query_args = array(
    'ignore_sticky_posts' => true,
    'post_status'         => 'publish',
    'posts_per_page'      => (int) $args['count'],
);
//字段排序
if (in_array($args['orderby'], array('views_num', 'likes_num', 'fav_num'))) {
    $meta_ranks = [
        'views_num' => 'views',
        'likes_num' => 'likes',
        'fav_num'   => 'follow_num',
    ];
    $query_args['meta_key'] = $meta_ranks[$args['orderby']];
    $query_args['order']    = 'DESC';
    $query_args['orderby']  = 'meta_value_num';
}elseif ($args['orderby']=='down_num') {
  // 下载量排行...
  global $wpdb;
  
  $cache_key = 'sidebar_ranking_down_num_ids'; // 自定义缓存键，包含分类ID

  // 尝试从缓存中获取侧边栏文章展示查询结果
  $post_ids = wp_cache_get($cache_key);

  if ($post_ids === false) {
      // 如果缓存中不存在查询结果，则执行查询并将结果存入缓存
      $post_ids = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM {$wpdb->cao_down_tbl} GROUP BY post_id ORDER BY COUNT(*) DESC LIMIT %d", $args['count'])
      );
      wp_cache_set($cache_key, $post_ids);
  }

  if (!empty($post_ids)) {
      $query_args['post__in'] = $post_ids;
      $query_args['orderby']  = 'post__in';
  }

}elseif ($args['orderby']=='pay_num') {
  // 购买量排行...
  global $wpdb;

  $cache_key = 'sidebar_ranking_pay_num_ids'; // 自定义缓存键

  // 尝试从缓存中获取侧边栏文章展示查询结果
  $post_ids = wp_cache_get($cache_key);

  if ($post_ids === false) {
      // 如果缓存中不存在查询结果，则执行查询并将结果存入缓存
      $post_ids = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM {$wpdb->cao_order_tbl} WHERE status = 1 GROUP BY post_id ORDER BY COUNT(*) DESC LIMIT %d", $args['count'])
      );
      wp_cache_set($cache_key, $post_ids);
  }


  if (!empty($post_ids)) {
      $query_args['post__in'] = $post_ids;
      $query_args['orderby']  = 'post__in';
  }

}

//查询排序


$cache_key = 'sidebar_ranking_list'; // 自定义缓存键

// 尝试从缓存中获取侧边栏文章展示查询结果
$PostData = wp_cache_get($cache_key);

if ($PostData === false) {
    // 如果缓存中不存在查询结果，则执行查询并将结果存入缓存
    $PostData = new WP_Query($query_args);
    wp_cache_set($cache_key, $PostData);
}



?>

<h5 class="widget-title"><?php echo $args['title']; ?></h5>

<div class="row g-3 row-cols-1">
  <?php if ($PostData->have_posts()): $rank_key = 0; while ($PostData->have_posts()): $PostData->the_post(); $rank_key++;?>
      <div class="col">
        <article class="ranking-item">
          <span class="ranking-num badge bg-<?php echo zb_get_color_class($rank_key);?> bg-opacity-50"><?php echo $rank_key; ?></span>
          <h3 class="ranking-title">
            <a target="<?php echo get_target_blank(); ?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
          </h3>
        </article>
      </div>
    <?php endwhile;else:?>
    <p class="col mb-0"><?php _e('暂无排行', 'ripro');?></p>
    <?php endif;?>
</div>

<?php wp_reset_postdata();?>
