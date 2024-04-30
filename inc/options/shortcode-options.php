<?php

defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
    exit;
}

//
// Set a unique slug-like ID
//
$prefix = '_ripro_shortcodes_hide';

CSF::createShortcoder($prefix, array(
    'button_title'   => '添加付费隐藏内容',
    'select_title'   => '选择一个简码组件',
    'insert_title'   => '插入到文章',
    'show_in_editor' => true,
    'gutenberg'      => array(
        'title'       => '添加隐藏内容',
        'description' => '添加隐藏内容',
        'icon'        => 'screenoptions',
        'category'    => 'widgets',
        'keywords'    => array('shortcode', 'ripro', 'insert', 'hide'),
        'placeholder' => '在此处编写简码...',
    ),
));

// A shortcode
CSF::createSection($prefix, array(
    'title'     => '付费可见内容[rihide]',
    'view'      => 'normal',
    'shortcode' => 'rihide',
    'fields'    => array(
        array(
            'id'    => 'content',
            'type'  => 'wp_editor',
            'title' => '',
            'desc'  => '[rihide]隐藏部分付费内容[/rihide]查看价格和权限设置等和付费下载相同',
        ),

    ),
));

// [rihide] 付费查看内容
function _ripro_hide_shortcode($atts, $content = '') {
    if (!is_site_shop()) {
        return false;
    }
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/rihide', '', $content);
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('rihide', '_ripro_hide_shortcode');


$prefix = '_ripro_shortcodes_other';
CSF::createShortcoder($prefix, array(
    'button_title'   => '添加内容组件',
    'select_title'   => '选择一个简码组件',
    'insert_title'   => '插入到文章',
    'show_in_editor' => true,
    'gutenberg'      => array(
        'title'       => '其他组件内容',
        'description' => '其他组件内容',
        'icon'        => 'screenoptions',
        'category'    => 'widgets',
        'keywords'    => array('shortcode', 'ripro', 'insert', 'hide'),
        'placeholder' => '在此处编写简码...',
    ),
));


// A shortcode
CSF::createSection($prefix, array(
    'title'     => '评论可见内容[ri-reply-hide]',
    'view'      => 'normal',
    'shortcode' => 'ri-reply-hide',
    'fields'    => array(
        array(
            'id'    => 'content',
            'type'  => 'wp_editor',
            'title' => '',
            'desc'  => '[ri-reply-hide]隐藏部分评论后可见内容[/ri-reply-hide]',
        ),

    ),
));

function _ripro_reply_hide_shortcode($atts, $content = '') {
    if (!is_site_shop()) {
        return false;
    }
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/ri-reply-hide', '', $content);
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('ri-reply-hide', '_ripro_reply_hide_shortcode');

// A shortcode
CSF::createSection($prefix, array(
    'title'     => '登录可见内容[ri-login-hide]',
    'view'      => 'normal',
    'shortcode' => 'ri-login-hide',
    'fields'    => array(
        array(
            'id'    => 'content',
            'type'  => 'wp_editor',
            'title' => '',
            'desc'  => '[ri-login-hide]隐藏部分登录后可见内容[/ri-login-hide]',
        ),

    ),
));

function _ripro_login_hide_shortcode($atts, $content = '') {
    if (!is_site_shop()) {
        return false;
    }
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/ri-login-hide', '', $content);
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('ri-login-hide', '_ripro_login_hide_shortcode');


//视频播放器
CSF::createSection($prefix, array(
    'title'     => '视频播放器',
    'view'      => 'normal',
    'shortcode' => 'ri-video',
    'fields'    => array(

        array(
            'id'      => 'url',
            'type'    => 'upload',
            'title'   => '视频地址',
            'desc'    => '内置videojs播放器，只支持视频真实播放地址，支付mp4,m3u8等格式',
            'default' => '',
        ),

        array(
            'id'      => 'pic',
            'type'    => 'upload',
            'title'   => '视频封面',
            'desc'    => '视频封面,不上传不显示,推荐16:9的封面图，740x420',
            'default' => '',
        ),

    ),
));

function _ripro_video_shortcode($atts, $content = '') {
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/ri-video', '',array('atts'=>$atts, 'content'=>$content));
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('ri-video', '_ripro_video_shortcode');


//自定义按钮
CSF::createSection($prefix, array(
    'title'     => '按钮',
    'view'      => 'normal',
    'shortcode' => 'ri-buttons',
    'fields'    => array(
        array(
            'id'      => 'size',
            'type'    => 'radio',
            'title'   => '大小',
            'options' => array(
                'btn-sm' => '小',
                ''       => '常规',
                'btn-lg' => '大',
            ),
            'inline'  => true,
            'default' => '',
        ),
        array(
            'id'      => 'color',
            'type'    => 'radio',
            'title'   => '颜色',
            'inline'  => true,
            'options' => array(
                'primary'   => '蓝',
                'info'      => '浅蓝',
                'success'   => '绿',
                'danger'    => '红',
                'warning'   => '黄',
                'secondary' => '灰',
                'light'     => '浅灰',
                'dark'      => '黑',
            ),
            'default' => 'primary',
        ),
        array(
            'id'      => 'outline',
            'type'    => 'checkbox',
            'title'   => '边框',
            'label'   => '按钮显示风格为边框模式',
            'default' => false,
        ),
        array(
            'id'      => 'rounded',
            'type'    => 'checkbox',
            'title'   => '圆角',
            'label'   => '',
            'default' => false,
        ),
        array(
            'id'      => 'href',
            'type'    => 'text',
            'title'   => '链接',
            'default' => '#',
        ),
        array(
            'id'      => 'blank',
            'type'    => 'checkbox',
            'title'   => '新窗口打开',
            'default' => false,
        ),
        array(
            'id'      => 'content',
            'type'    => 'text',
            'title'   => '名称',
            'default' => '这是按钮',
        ),

    ),
));

function _ripro_buttons_shortcode($atts, $content = '') {
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/ri-buttons', '',array('atts'=>$atts, 'content'=>$content));
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('ri-buttons', '_ripro_buttons_shortcode');



//  警告框（Alerts）
CSF::createSection($prefix, array(
    'title'     => '提示框',
    'view'      => 'normal',
    'shortcode' => 'ri-alerts',
    'fields'    => array(

        array(
            'id'      => 'color',
            'type'    => 'radio',
            'title'   => '颜色',
            'inline'  => true,
            'options' => array(
                'primary'   => '蓝',
                'info'      => '浅蓝',
                'success'   => '绿',
                'danger'    => '红',
                'warning'   => '黄',
                'secondary' => '灰',
                'light'     => '浅灰',
                'dark'      => '黑',
            ),
            'default' => 'primary',
        ),
        array(
            'id'       => 'content',
            'type'     => 'textarea',
            'title'    => '内容',
            'sanitize' => false,
            'default'  => '这是一条醒目的提示消息',
            'desc'     => '可以插入html代码，例如：' . esc_html('<h4 class="alert-heading">hello ripro-v2!</h4>这是一条醒目的提示消息'),
        ),
    ),
));

function _ripro_alerts_shortcode($atts, $content = '') {
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/ri-alerts', '',array('atts'=>$atts, 'content'=>$content));
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('ri-alerts', '_ripro_alerts_shortcode');


//列表内容 accordion
CSF::createSection($prefix, array(
    'title'           => '列表内容',
    'view'            => 'repeater',
    'shortcode'       => 'ri-accordions',
    'fields'    => array(
        array(
            'id'       => 'title',
            'type'     => 'text',
            'title'    => '标题',
            'default'  => '自定义标题',
            'desc'     => '',
        ),
        array(
            'id'       => 'content',
            'type'     => 'textarea',
            'title'    => '内容',
            'sanitize' => false,
            'default'  => '这里显示的是一条醒目内容...',
            'desc'     => '可以插入html代码',
        ),

    ),

));

function _ripro_accordions_shortcode($atts, $content = '') {
    // 加载并缓存模板内容
    ob_start();
    get_template_part('template-parts/shortcode/ri-accordions', '',array('atts'=>$atts, 'content'=>$content));
    $html = ob_get_clean();
    return do_shortcode($html);
}
add_shortcode('ri-accordions', '_ripro_accordions_shortcode');
