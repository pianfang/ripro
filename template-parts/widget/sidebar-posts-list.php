<?php

if (empty($args)) {
  return;
}


$cat_id = intval($args['category']);

if (empty($cat_id)) {
  // 获取当前页面文章的主分类对象
  $categories = get_the_category();

  if (isset($categories[0])) {
      $cat_id = $categories[0]->term_id;
  }
}


// 查询
$query_args = array(
  'cat'                 => $cat_id,
  'ignore_sticky_posts' => true,
  'post_status'         => 'publish',
  'posts_per_page'      => (int) $args['count'],
  'orderby'             => $args['orderby'],
);


$cache_key = 'sidebar_posts_list_' . $cat_id; // 自定义缓存键，包含分类ID

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
  <?php if ($PostData->have_posts()) : 
    while ($PostData->have_posts()) : $PostData->the_post();?>

    <div class="col">
      <article class="post-item item-list">

        <div class="entry-media ratio ratio-3x2 col-auto">
          <a target="<?php echo get_target_blank();?>" class="media-img lazy" href="<?php the_permalink();?>" title="<?php the_title();?>" data-bg="<?php echo zb_get_thumbnail_url();?>"></a>
        </div>

        <div class="entry-wrapper">
          <div class="entry-body">
            <h2 class="entry-title">
              <a target="<?php echo get_target_blank();?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
            </h2>
          </div>
        </div>

      </article>
    </div>

  <?php endwhile;endif;?>
</div>

<?php wp_reset_postdata();?>
