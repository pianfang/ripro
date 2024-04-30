<?php
/**
 * Template Name: 模块化页面
 *
 * Description:
 */

get_header();

$page_id   = get_the_ID();
$widget_id = 'home-center-' . $page_id;

//调用模块化小工具
if (is_active_sidebar($widget_id)) {
    dynamic_sidebar($widget_id);
} else {

    echo '<div class="container text-white bg-dark bg-opacity-75 text-center rounded-2 p-4 my-6"> <p>重要提示：请在 <a class="text-warning" href="' . admin_url('widgets.php') . '">后台-外观-小工具</a> 设置拖拽【首页】小工具到首页模块框配置自定义模块化页面布局。</p><p>WP自带小工具高性能实现首页模块可以无限复制拖拽排序，单独设置各自参数，当前首页为默认显示输出，设置首页模块后则自动被替换</p><p class="text-center"><a class="btn btn-sm btn-outline-light" target="_blank" href="https://ritheme.com/document/1028.html">首页模块配置教程及截图</a></p> </div>';

}

get_footer();
