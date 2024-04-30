<?php

if (empty($args)) {
    return;
}

$author_id = get_the_author_meta( 'ID' );
$author_name = get_the_author_meta( 'display_name', $author_id );
$author_link = esc_url( get_author_posts_url($author_id,$author_name) );
$author_img = get_avatar_url($author_id);

// 构建用于存储缓存的唯一键
$cache_key = 'user_post_count_' . $author_id;

// 尝试从缓存中获取作者的文章数量
$post_count = wp_cache_get($cache_key);

if ($post_count === false) {
    // 如果缓存中不存在，执行查询获取文章数量
    $post_count = count_user_posts($author_id, 'post');

    // 将结果存入缓存
    wp_cache_set($cache_key, $post_count);
}


$fav_ids = get_user_meta($author_id,'follow_post',true);
if (empty($fav_ids) || !is_array($fav_ids)) {
  $fav_ids = array();
}

$fav_count = count($fav_ids);
$comment_count = get_comments(array(
    'user_id' => $author_id,
    'count'   => true,
));
?>


<?php if (!empty($args['title'])):?>
<h5 class="widget-title"><?php echo $args['title']; ?></h5>
<?php endif;?>



<div class="author-header">
  <div class="row align-items-center">
    <div class="col-auto">
      <!-- Avatar -->
      <div class="avatar">
        <img class="lazy avatar-img rounded-circle border border-white border-3 shadow" data-src="<?php echo $author_img;?>">
      </div>

    </div>
    <div class="col">
      <span class="d-block h6 fw-bold mb-1"><?php echo $author_name;?></span>
      <small class="d-block text-muted"><?php _e('等级', 'ripro');?><?php echo zb_get_user_badge($author_id,'span','ms-1 mb-0'); ?></small>
    </div>
  </div>
</div>

<div class="author-body">
  <div class="row">
    <div class="col-4 text-center">
      <span class="h5 mb-0"><?php echo $post_count;?></span>
      <span class="d-block text-sm"><?php _e('文章', 'ripro');?></span>
    </div>
    <div class="col-4 text-center">
      <span class="h5 mb-0"><?php echo $comment_count;?></span>
      <span class="d-block text-sm"></i><?php _e('评论', 'ripro');?></span>
    </div>
    <div class="col-4 text-center">
      <span class="h5 mb-0"><?php echo $fav_count;?></span>
      <span class="d-block text-sm"><?php _e('收藏', 'ripro');?></span>
    </div>
  </div>
</div>

<div class="author-footer">
  <div class="text-center">
    <a href="<?php echo $author_link;?>" class="btn btn-sm px-0 btn-link"><?php _e('查看作者其他文章', 'ripro');?></a>
  </div>
</div>