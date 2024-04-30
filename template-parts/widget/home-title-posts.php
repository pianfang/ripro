<?php

if (empty($args)) {
  return;
}

$row_cols_class = 'row-cols-1 row-cols-md-2 row-cols-lg-'.$args['col'].' g-2 g-md-3 g-lg-4';
?>

<section class="container">
  <?php 
    $section_title = $args['title'];
    $section_desc = $args['desc'];
  ?>
  <?php if ($section_title): ?>
    <div class="section-title text-center mb-4">
      <h3><?php echo $section_title ?></h3>
      <?php if (!empty($section_desc)): ?>
        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>



  <div class="row <?php echo esc_attr($row_cols_class); ?>">

    <?php foreach ($args['category_data'] as $item) : 

    $cat_id = intval($item['category']);

    if (empty($cat_id)) {
        continue;
    }

    $category = get_term_by('ID', $cat_id,'category');
    
    // 查询
    $query_args = array(
      'cat'                 => $cat_id,
      'ignore_sticky_posts' => true,
      'post_status'         => 'publish',
      'posts_per_page'      => (int) $args['count'],
      'orderby'             => $item['orderby'],
    );
    //字段排序
    if ($item['orderby'] == 'views') {
      $query_args['meta_key'] = 'views';
      $query_args['orderby'] = 'meta_value_num';
      $query_args['order'] = 'DESC';
    }

    $PostData = new WP_Query($query_args);

    $meta_bg = get_term_meta($cat_id, 'bg-image', true);
    $bg_img = (!empty($meta_bg)) ? $meta_bg : zb_get_thumbnail_url();


    echo '<div class="col"><div class="list-posts">';

      echo '<div class="category-bg lazy" data-bg="'.$bg_img.'">';
        echo '<h3 class="category-title"><i class="fa fa-circle-o"></i>  <a target="'.get_target_blank().'" href="'.esc_url( get_term_link( $category->term_id ) ).'">'.$category->name.' &gt;</a></h3>';
        echo '<div class="category-desc">'.category_description($cat_id).'</div>';
      echo '</div>';

      echo '<ul>';
        $key_num = 1;
        while ($PostData->have_posts()): $PostData->the_post();
        echo '<li><div class="title"><span class="post-num num-'.$key_num.'">'.$key_num.'</span><a '.get_target_blank().' href="' . esc_url( get_permalink() ) . '" title="' . esc_attr(get_the_title()) . '" rel="bookmark">' . get_the_title() . '</a></div><span class="post-views">'.zb_get_post_views().'<i class="fab fa-hotjar ms-1"></i></span></li>';
        $key_num++;
        endwhile;
      echo '</ul>';
    echo '</div></div>';

    wp_reset_postdata();
    ?>


    <?php endforeach;?>

  </div>

</section>
