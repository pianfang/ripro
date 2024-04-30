<?php

// 自定义筛选
// return;

if (!is_category() || !is_site_term_filter()) {
    return;
}

$taxonomy_name = 'category';
$curr_term = get_queried_object();
if (!$curr_term || !isset($curr_term->term_id)) {
    return;
}
$curr_term_id = $curr_term->term_id; //获取当前分类ID
$top_term_id = zb_get_term_top_id($curr_term_id, $taxonomy_name); //获取当前分类顶级分类ID


$filter_config = get_cat_filter_config($top_term_id); //获取当前顶级分类筛选配置

if (empty($filter_config)) {
    return;
}


$parent_ids = array($curr_term_id); //子分类ID集
if (!empty($filter_config['is_child_cat'])) {
    $parent_id = isset($curr_term->parent) ? $curr_term->parent : 0; //是否有父ID
    while ($parent_id) {
        $parent_term = get_term($parent_id, $taxonomy_name);
        if (!$parent_term) {
            break;
        }
        $parent_ids[] = $parent_id;
        $parent_id = $parent_term->parent;
    }
}


?>



<div class="filter-warp">
<div class="container">

<?php


//一级分类
$top_cats  = (!empty($filter_config['top_cats'])) ? $filter_config['top_cats'] : array();
$top_terms = array();
if (!empty($filter_config['top_cats'])) {
    $top_terms = get_terms(array(
        'taxonomy'   => $taxonomy_name,
        'hide_empty' => false,
        'include'    => $top_cats,
        'orderby'    => 'include',
    ));
}

if (!empty($top_terms) && !is_wp_error($top_terms)) :?>
<ul class="filter-item">
    <li class="filter-name"><i class="fas fa-layer-group me-1"></i><?php _e('分类', 'ripro');?></li>
    <?php foreach ($top_terms as $item) { ?>
        <li class="filter-link <?php echo in_array($item->term_id, $parent_ids) ? 'active' : ''; ?>">
            <a href="<?php echo get_term_link($item->term_id, $taxonomy_name); ?>"><?php echo $item->name; ?></a>
        </li>
    <?php } ?>
</ul>
<?php
endif;




// 子分类筛选 自动显示
$children_filter = array_reverse($parent_ids);

foreach ($children_filter as $parent_id) {
    $term = get_term($parent_id, $taxonomy_name);
    if (is_wp_error($term)) {
        continue;
    }

    $__args = array(
        'taxonomy'   => $taxonomy_name,
        'hide_empty' => false,
        'parent'     => $parent_id,
        'orderby'    => $filter_config['child_cat_orderby'],
        'order'      => $filter_config['child_cat_order'],
    );

    if ($filter_config['child_cat_orderby'] == 'include') {
        $__args['include'] = $filter_config['orderby_include'];
    }

    $children_terms = get_terms($__args);

    if (is_wp_error($children_terms) || empty($children_terms)) {
        continue;
    }
    ?>
    <ul class="filter-item">
        <li class="filter-name"><i class="fas fa-layer-group me-1"></i><?php echo $term->name; ?></li>
        <li class="filter-link <?php echo $curr_term_id === $term->term_id ? 'active' : ''; ?>">
            <a href="<?php echo get_term_link($term->term_id, $taxonomy_name); ?>"><?php echo esc_html__('全部', 'ripro'); ?></a>
        </li>
        <?php foreach ($children_terms as $item) { ?>
            <li class="filter-link <?php echo in_array($item->term_id, $children_filter) ? 'active' : ''; ?>">
                <a href="<?php echo get_term_link($item->term_id, $taxonomy_name); ?>"><?php echo $item->name; ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php
}


//自定义字段分类法筛选 custom_taxonomy
if (!empty($filter_config['custom_taxonomy']) && is_array($filter_config['custom_taxonomy'])) {
    $custom_taxonomy = get_site_custom_taxonomy(); //获取自定义字段配置
    foreach ($filter_config['custom_taxonomy'] as $taxonomy_name) {
        $term_name = $custom_taxonomy[$taxonomy_name]['name'];
        $terms = get_terms(array(
            'taxonomy'   => $taxonomy_name,
            'hide_empty' => false,
            'orderby'    => 'none',
        ));
        if (is_wp_error($terms) || empty($terms)) {
            continue;
        }
        ?>
        <ul class="filter-item">
            <li class="filter-name"><i class="fas fa-filter me-1"></i><?php echo $term_name; ?></li>
            <li class="filter-link <?php echo get_param($taxonomy_name, null, 'get') === null ? 'active' : ''; ?>">
                <a href="<?php echo remove_query_arg($taxonomy_name); ?>"><?php echo __('全部', 'ripro'); ?></a>
            </li>
            <?php foreach ($terms as $item) { ?>
                <li class="filter-link <?php echo urldecode(get_query_var($taxonomy_name)) == urldecode($item->slug) ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(add_query_arg($taxonomy_name, $item->slug)); ?>"><?php echo $item->name; ?></a>
                </li>
            <?php } ?>
        </ul>
        <?php
    }
}

// 价格权限
if (is_site_shop() && !empty($filter_config['is_price'])) {


    $site_vip_options = get_site_vip_options();

    $priceOptions = [
        'all' => __('全部', 'ripro'),
        'free' => __('免费', 'ripro'),
        'vip_free' => sprintf(__('%s免费', 'ripro'), $site_vip_options['vip']['name']),
        'vip_rate' => sprintf(__('%s折扣', 'ripro'), $site_vip_options['vip']['name']),
        'vip_only' => sprintf(__('%s专属', 'ripro'), $site_vip_options['vip']['name']),
        'boosvip_free' => sprintf(__('%s免费', 'ripro'), $site_vip_options['boosvip']['name']),
    ];

    $priceKey = 'price';
    ?>
    <ul class="filter-item">
        <li class="filter-name"><i class="<?php echo get_site_coin_icon();?> me-1"></i><?php echo __('价格', 'ripro'); ?></li>
        <?php foreach ($priceOptions as $key => $name) { ?>
            <li class="filter-link <?php echo urldecode(get_param($priceKey, 'all', 'get')) == urldecode($key) ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(add_query_arg($priceKey, $key)); ?>"><?php echo $name; ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php
}

// 排序
if (true) {
    $orderOptions = [
        'date' => __('最新', 'ripro'),
        'views' => __('热度', 'ripro'),
        'likes' => __('点赞', 'ripro'),
        'follow_num' => __('收藏', 'ripro'),
        'modified' => __('更新', 'ripro'),
        'rand' => __('随机', 'ripro')
    ];
    $queryKey = 'orderby';
    ?>
    <ul class="filter-item">
        <li class="filter-name"><i class="fas fa-sort-amount-down-alt me-1"></i><?php echo __('排序', 'ripro'); ?></li>
        <?php foreach ($orderOptions as $key => $name) { ?>
            <li class="filter-link <?php echo urldecode(get_param($queryKey, 'date', 'get')) == urldecode($key) ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(add_query_arg($queryKey, $key)); ?>"><?php echo $name; ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php
}

?>


</div>
</div>