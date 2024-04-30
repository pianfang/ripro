<?php

get_header();

//调用模块化小工具
if (is_active_sidebar('home-center')) {
    dynamic_sidebar('home-center');
} else {

    //默认显示搜索模块
    ri_home_search_widget(array(
        'id'            => 'home-center',
        'before_widget' => '<div class="home-widget home-search-box">',
        'after_widget'  => '</div>',
    ), array());

    //默认最新文章模块
    ri_home_lastpost_widget(array(
        'id'            => 'home-center',
        'before_widget' => '<div class="home-widget home-last-post">',
        'after_widget'  => '</div>',
    ), array());

    echo '<div class="container text-white bg-dark bg-opacity-75 text-center rounded-2 p-4"> <p>重要提示：请在 <a class="text-warning" href="' . admin_url('widgets.php') . '">后台-外观-小工具</a> 设置拖拽【首页】小工具到首页模块框配置首页布局。</p><p>WP自带小工具高性能实现首页模块可以无限复制拖拽排序，单独设置各自参数，当前首页为默认显示输出，设置首页模块后则自动被替换，<p class="text-center"><a class="btn btn-sm btn-outline-light" target="_blank" href="https://ritheme.com/document/1028.html">首页模块配置教程及截图</a></p></p> </div>';

}

get_footer();
