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
  'posts_per_page'      => 5,
  'orderby'             => $args['orderby'],
);

//字段排序
if ($args['orderby'] == 'views') {
  $query_args['meta_key'] = 'views';
  $query_args['orderby'] = 'meta_value_num';
  $query_args['order'] = 'DESC';
}

$PostData = new WP_Query($query_args);

$cms_style = $args['style'];
$cms_box_order = (!empty($args['is_box_right'])) ? 'order-first' : '' ;

// left 
$cms_box_item_conifg = [
  'type' => 'grid-overlay', // grid  grid-overlay list title
  'media_class' => 'ratio-3x2', // ratio-1x1  3x2 3x4 4x3 16x9
];

// right
$cms_list_item_conifg = [
  'type' => $cms_style,
  'media_class' => 'ratio-3x2',
  'is_entry_desc' => false,
];

$cms_list_item_rows = ($cms_style=='list') ? 'row-cols-1' : 'row-cols-2';


?>

<section class="container">

  <?php 
    $section_title = (!empty($args['title'])) ? $args['title'] : get_cat_name($cat_id);
    $section_desc = (!empty($args['desc'])) ? $args['desc'] : category_description($cat_id);
  ?>
  <?php if ($section_title): ?>
    <div class="section-title text-center mb-4">
      <h3><a href="<?php echo get_category_link($cat_id) ?>"><?php echo $section_title ?></a></h3>
      <?php if (!empty($section_desc)): ?>
        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>


  <div class="cms-post-warp <?php echo esc_attr($cms_style);?> row g-2 g-md-3">

    <?php if ( $PostData->have_posts() ) : $counter = 0;?>

    <div class="col-lg-6 col-sm-12 cms-left-itme">
      <?php while ( $PostData->have_posts() && $counter == 0 ) : $PostData->the_post(); ?>
        <div class="row row-cols-1 g-2 g-md-3">
          <?php get_template_part('template-parts/loop/item', '', $cms_box_item_conifg);?>
        </div>
        <?php $counter++; endwhile; ?>
    </div>

    <div class="col-lg-6 col-sm-12 cms-right-itme <?php echo esc_attr($cms_box_order);?>">
      <div class="row <?php echo esc_attr($cms_list_item_rows);?> g-2 g-md-3">
        <?php while ( $PostData->have_posts() && $counter < 5 ) : $PostData->the_post(); ?>
          <?php if ( $counter == 0 ) : continue; endif; ?>
          <?php get_template_part('template-parts/loop/item', '', $cms_list_item_conifg);?>
          <?php $counter++; endwhile; ?>
      </div>
    </div>

    <?php else: get_template_part('template-parts/loop/item', 'none'); endif;?>

  </div>

</section>

<?php wp_reset_postdata();?>
