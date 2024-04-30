<?php

defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
    exit;
}

$prefix = '_ripro_taxonomy_options';

CSF::createTaxonomyOptions($prefix, array(
    'taxonomy'  => array('category', 'post_tag', 'series'),
    'data_type' => 'unserialize',
));

CSF::createSection($prefix, array(
    'fields' => array(

        array(
            'id'      => 'bg-image',
            'type'    => 'upload',
            'title'   => '特色图片',
            'desc'    => '用于展示背景图，缩略图',
            'default' => '',
        ),

        array(
            'id'       => 'seo-title',
            'type'     => 'text',
            'title'    => 'SEO标题',
            'subtitle' => '不填写为自动规则',
        ),
        array(
            'id'       => 'seo-keywords',
            'type'     => 'textarea',
            'title'    => 'SEO关键词',
            'subtitle' => '关键词用英文逗号,隔开',
        ),
        array(
            'id'       => 'seo-description',
            'type'     => 'textarea',
            'title'    => 'SEO描述内容',
            'subtitle' => '字数控制到80-180最佳',
        ),

    ),
));
