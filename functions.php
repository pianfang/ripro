<?php
defined('ABSPATH') || exit;
###############################################################################

#主题版本信息
define('_THEME_VERSION', '7.8'); //主题版本
define('_THEME_DEBUG', 0); //调试模式控制切勿开启
define('_THEME_TOKEN', 'JFAkQlNXZ1lrQXpDUHU4TVVnRExwUjFIc1VVUHJOQ1lvMA=='); //主题授权TOKEN 切勿修改

###############################################################################

//调试模式显示错误日志信息
if ((defined('WP_DEBUG') && WP_DEBUG == true) || _THEME_DEBUG == true) {
    error_reporting(E_ALL);
} else {
    error_reporting(0); //关闭报错止乱码
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function zb_setup() {

    //扩展支持安装 
    if (!extension_loaded('swoole_loader')) {
        wp_redirect(get_template_directory_uri() . '/install/swoole-compiler-loader.php');exit;
    }
    

    load_theme_textdomain('ripro', get_template_directory() . '/languages');

    
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('post-formats', array('image', 'video', 'audio'));

    // add link manager // 开启友情链接功能
    add_filter('pre_option_link_manager_enabled', '__return_true');
    
    // This theme uses wp_nav_menu() in one location.
    register_nav_menus(
        array(
            'main-menu' => esc_html__('全站顶部菜单', 'ripro'),
        )
    );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    

    // Add theme support for selective refresh for widgets.
    add_theme_support('customize-selective-refresh-widgets');

    // 删除render_block 过滤器
    remove_filter('render_block', 'wp_render_duotone_support');
    remove_filter('render_block', 'wp_restore_group_inner_container');
    remove_filter('render_block', 'wp_render_layout_support_flag');


    // 第一启用主题时候插入订单表
    $theme_status_key = 'ripro_theme_setup_v50';
    $the_theme_status = get_option($theme_status_key);

    //初始化安装

    require get_template_directory() . '/inc/setup-db.php';
    $ZB_SetupDb = new ZB_SetupDb();

    if (empty($the_theme_status)) {
        //安装数据库操作
        if ($ZB_SetupDb->install_db()) {
            update_option($theme_status_key, '1');
        }

        //重写固定连接规则
        flush_rewrite_rules(false);
    }

}
add_action('after_setup_theme', 'zb_setup');

/**
 * Register widget area.
 */
function zb_widgets_init() {

    register_sidebar(array(
        'name'          => esc_html__('首页模块', 'ripro'),
        'id'            => 'home-center',
        'description'   => esc_html__('首页模块主内容区域', 'ripro'),
        'before_widget' => '<div id="%1$s" class="home-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '',
        'after_title'   => '',
    ));


    $cache_key = 'site_mod_page_template';

    // 尝试从缓存中获取侧边栏文章展示查询结果
    $widget_pages = wp_cache_get($cache_key);

    if ($widget_pages === false) {
        // 如果缓存中不存在查询结果，则执行查询并将结果存入缓存
        $widget_pages = get_pages( array(
          'meta_key' => '_wp_page_template',
          'meta_value' => 'page-templates/page-widget.php'
        ) );
        wp_cache_set($cache_key, $widget_pages);
    }


    // Loop through the pages and output their IDs
    foreach ( $widget_pages as $page ) {
    
       register_sidebar(array(
            'name'          => $page->post_title,
            'id'            => 'home-center-'.$page->ID,
            'description'   => esc_html__('自定义模块主内容区域', 'ripro'),
            'before_widget' => '<div id="%1$s" class="home-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '',
            'after_title'   => '',
        ));
    }

    register_sidebar(array(
        'name'          => esc_html__('文章侧边栏', 'ripro'),
        'id'            => 'single-sidebar',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>',
    ));


    unregister_widget('WP_Widget_Calendar');
    unregister_widget('WP_Widget_RSS');
    unregister_widget('WP_Widget_Meta');
    unregister_widget('WP_Widget_Search');
    unregister_widget('WP_Widget_Archives');
    unregister_widget('WP_Widget_Pages');
    unregister_widget('WP_Widget_Media_Gallery');
    unregister_widget('WP_Widget_Recent_Comments');
    unregister_widget('WP_Nav_Menu_Widget');

}
add_action('widgets_init', 'zb_widgets_init');

// Require Composer's autoloading file
if (is_file($composer = get_template_directory() . '/vendor/autoload.php')) {
    require_once $composer;
}

// //设置框架
require_once get_template_directory() . '/inc/template-csf.php';

// //主题基本优化
require_once get_template_directory() . '/inc/template-clean.php';

// //主题钩子
require_once get_template_directory() . '/inc/template-filter.php';

// //js CSS
require_once get_template_directory() . '/inc/template-assets.php';

// //后台定制和后台安全优化
require_once get_template_directory() . '/inc/template-admin.php';

// // 自定义类型
require_once get_template_directory() . '/inc/template-post-type.php';

// //伪静态路由
require_once get_template_directory() . '/inc/template-rewrite.php';

// //消息推送webhook
require_once get_template_directory() . '/inc/template-mail.php';

// //AJAX接口集成
require_once get_template_directory() . '/inc/template-ajax.php';

// //bootstrap菜单类
require_once get_template_directory() . '/inc/template-walker.php';

// //内置SEO
require_once get_template_directory() . '/inc/template-seo.php';

// //主题功能标签
require_once get_template_directory() . '/inc/template-tags.php';

//主题shop
require_once get_template_directory() . '/inc/template-shop.php';

//商城用户权限等核心类
if (extension_loaded('swoole_loader')) {
    $php_version = substr(PHP_VERSION, 0, 3);
    // $php_version = '0.0';
    require_once get_template_directory() . '/inc/core/template-core-' . $php_version . '.php';
}

###################################### RITHEME.COM END #########################################