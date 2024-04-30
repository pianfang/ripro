<?php

if (empty($args)) {
  return;
}


$cat_id = intval($args['category']);

// 查询
$query_args = array(
  'cat'                 => $cat_id,
  'ignore_sticky_posts' => false,
  'post_status'         => 'publish',
  'posts_per_page'      => (int) $args['count'],
  'orderby'             => $args['orderby'],
);
//字段排序
if ($args['orderby'] == 'views') {
  $query_args['meta_key'] = 'views';
  $query_args['orderby'] = 'meta_value_num';
  $query_args['order'] = 'DESC';
}

$PostData = new WP_Query($query_args);

$item_config = zb_get_archive_item_config($cat_id);


?>

<section class="container">
  <?php 
    $section_title = get_cat_name($cat_id);
    $section_desc  = category_description($cat_id);
  ?>
  <?php if ($section_title): ?>
    <div class="section-title text-center mb-4">
      <h3><a href="<?php echo get_category_link($cat_id) ?>"><?php echo $section_title ?></a></h3>
      <?php if (!empty($section_desc)): ?>
        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="row <?php echo esc_attr($item_config['row_cols_class']); ?>">
    <?php if ($PostData->have_posts()):
      while ($PostData->have_posts()): $PostData->the_post();
        get_template_part('template-parts/loop/item', '', $item_config);
      endwhile;
    else:
      get_template_part('template-parts/loop/item', 'none');
    endif;?>
  </div>

</section>

<?php wp_reset_postdata();?>
