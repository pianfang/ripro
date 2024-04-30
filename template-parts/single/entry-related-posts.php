<?php

// 获取相关文章配置
$post_id    = get_the_ID();
$limit      = (int) _cao('single_bottom_related_post_num', 4); //显示数量

if (empty($limit)) {
    return;
}

// 使用WordPress内置函数获取当前文章的标签和分类 关联最新的6个标签 和其他全部分类 并且随机展示增强灵活性
$tags       = wp_get_post_tags($post_id, array('fields' => 'ids', 'number' => 6));
$categories = wp_get_post_categories($post_id, array('fields' => 'ids'));
// 构建查询参数
$query_args = array(
    'post_type'      => 'post',
    'ignore_sticky_posts' => false,
    'orderby'        => 'rand',
    'posts_per_page' => $limit,
    'post__not_in'   => array($post_id),
    'tax_query'      => array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'category',
            'field'    => 'term_id',
            'terms'    => $categories,
        ),
        array(
            'taxonomy' => 'post_tag',
            'field'    => 'term_id',
            'terms'    => $tags,
        ),
    ),
);


// 查询相关文章
$PostData = new WP_Query($query_args);

if (!$PostData->have_posts()) {
    return;
}

$item_config         = zb_get_archive_item_config();
$item_config['type'] = 'grid';

?>

<div class="related-posts">
   <h2 class="related-posts-title"><i class="fab fa-hive me-1"></i><?php _e('相关文章', 'ripro'); ?></h2>
   <div class="row g-2 g-md-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
    <?php 
    while ($PostData->have_posts()): $PostData->the_post();
        get_template_part('template-parts/loop/item', '', $item_config);
    endwhile;
    wp_reset_postdata();
    ?>
    </div>
</div>
