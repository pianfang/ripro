<?php


/**
 * 获取自定义分类法筛选配置
 * @Author Dadong2g
 * @date   2022-12-04
 * @return [type]
 */
function get_site_custom_taxonomy() {

    $data   = _cao('site_custom_taxonomy', array());
    $config = array();
    if (empty($data) || !is_array($data)) {
        return array();
    }

    foreach ($data as $key => $value) {

        $custom_taxonomy = trim($value['taxonomy']);

        if (!preg_match("/^[a-zA-Z\s]+$/", $custom_taxonomy)) {
            continue; //不是英文标识
        }

        $config[$custom_taxonomy] = $value;
    }

    return $config;
}

/**
 * 根据分类id获取当前分类筛选配置
 * @Author Dadong2g
 * @date   2022-12-04
 * @param  [type]     $cat_id [description]
 * @return [type]
 */
function get_cat_filter_config($cat_id) {

    $data   = _cao('site_term_filter_config', array());

    if (empty($cat_id) || empty($data)) {
        return array();
    }

    $config = array();

    foreach ($data as $item) {
        if ($item['cat_id'] == $cat_id) {
            $config = $item;
        }
    }

    return $config;
}

// 自定义分类筛选
// prefix custom
//
add_action('init', function () {

    $custom_taxonomy = get_site_custom_taxonomy();

    if (empty($custom_taxonomy)) {
        return;
    }

    foreach ($custom_taxonomy as $tax_key => $tax_item) {

        if ($tax_key == 'type') {
            continue;
        }
        
        if (taxonomy_exists($tax_key)) {
            continue;
        }

        register_extended_taxonomy($tax_key, 'post', [
            'labels'       => [
                'name'          => $tax_item['name'],
                'singular_name' => $tax_item['name'],
                'menu_name'     => $tax_item['name'],
                'search_items'  => __('搜索', 'ripro'),
                'all_items'     => __('全部', 'ripro'),
                'view_item'     => __('查看', 'ripro'),
                'edit_item'     => __('编辑', 'ripro'),
                'update_item'   => __('更新', 'ripro'),
                'add_new_item'  => __('添加新', 'ripro') . $tax_item['name'],
                'new_item_name' => __('新增', 'ripro'),
            ],
            'hierarchical' => false,
            'meta_box'     => $tax_item['type'],
            'rewrite'      => [
                'slug' => $tax_key,
            ],
        ]);
    }

});


// 专题功能
add_action('init', function () {

    $tax_key  = 'series';
    $tax_name = '专题合集';

    register_extended_taxonomy($tax_key, 'post', [
        'labels'            => [
            'name'                       => $tax_name,
            'singular_name'              => $tax_name,
            'menu_name'                  => $tax_name,
            'search_items'               => '搜索',
            'all_items'                  => '全部',
            'view_item'                  => '查看',
            'edit_item'                  => '编辑',
            'update_item'                => '更新',
            'add_new_item'               => '添加新' . $tax_name,
            'new_item_name'              => '新增',
            'separate_items_with_commas' => '按逗号分开',
            'add_or_remove_items'        => '添加或删除',
            'choose_from_most_used'      => '从经常使用的类型中选择',
            'parent_item'                => '父级专题',
            'most_used'                  => '最多使用',
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'query_var'         => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        // 'meta_box'     => $tax_item['type'],
        'rewrite'           => [
            'slug' => $tax_key,
        ],
    ]);

});