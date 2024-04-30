<?php

defined('ABSPATH') || exit;

$name_prefix = '';

/////////////////////////////////////// 下载小工具 ////////////////////////////////////////////
CSF::createWidget('ri_post_pay_widget', array(
    'title'       => $name_prefix . '【边栏】1.资源购买信息组件',
    'classname'   => 'post-buy-widget',
    'description' => '付费文章必备侧边栏下载按钮',
    'fields'      => array(

        array(
            'id'      => 'is_downurl_count',
            'type'    => 'switcher',
            'title'   => '显示资源数量',
            'default' => true,
        ),
        array(
            'id'      => 'is_modified_date',
            'type'    => 'switcher',
            'title'   => '显示最近更新日期',
            'default' => true,
        ),
        array(
            'id'      => 'is_sales_count',
            'type'    => 'switcher',
            'title'   => '显示销量',
            'default' => true,
        ),

        array(
            'id'          => 'resize_position',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '手机端显示位置',
            'desc'        => '',
            'placeholder' => '',
            'options'     => array(
                'bottom' => '文章内容底部',
                'top'    => '文章内容顶部',
            ),
            'default'     => 'bottom',
        ),

        array(
            'id'       => 'footer_text',
            'type'     => 'textarea',
            'sanitize' => false,
            'title'    => '底部自定义内容',
            'default'  => '下载遇到问题？可联系客服或反馈',
        ),

        array(
            'type'    => 'subheading',
            'content' => '此小工具为必选小工具',
        ),

    ),
));

function ri_post_pay_widget($args, $instance) {

    if ($args['id'] != 'single-sidebar') {
        return false; //非首页模块页面不显示
    }

    //非付费文章不显示
    if (!is_site_shop() || !is_single() || !post_is_down_pay(get_the_ID())) {
        return;
    }

    $instance = array_merge(array(
        'is_downurl_count' => true,
        'is_modified_date' => true,
        'is_sales_count'   => true,
        'resize_position'  => 'bottom',
        'footer_text'      => '下载遇到问题？可联系客服或反馈',
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/sidebar-post-pay', '', $instance);

    echo $args['after_widget'];

}

///////////////////////////////////////// 侧边栏文章展示 /////////////////////////////////////////
CSF::createWidget('ri_sidebar_posts_widget', array(
    'title'       => $name_prefix . '【边栏】2.文章展示',
    'classname'   => 'sidebar-posts-list',
    'description' => '文章展示',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '文章展示',
        ),

        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '要展示得分类文章',
            'placeholder' => '选择分类',
            'options'     => 'categories',
            'query_args'  => array(
                'hide_empty'  => 0,
            ),
        ),

        array(
            'id'      => 'orderby',
            'type'    => 'radio',
            'title'   => '排序方式',
            'inline'  => true,
            'options' => array(
                'date'     => '日期',
                'rand'     => '随机',
                'modified' => '最近编辑时间',
                'title'    => '标题',
                'ID'       => '文章ID',
            ),
            'default' => 'date',
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => '显示数量',
            'default' => 6,
        ),

    ),
));
function ri_sidebar_posts_widget($args, $instance) {

    if ($args['id'] != 'single-sidebar') {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'    => '文章展示',
        'category' => '',
        'orderby'  => 'date',
        'count'    => 6,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/sidebar-posts-list', '', $instance);

    echo $args['after_widget'];
}

///////////////////////////////////////// 侧边栏排行榜展示 /////////////////////////////////////////
CSF::createWidget('ri_sidebar_ranking_widget', array(
    'title'       => $name_prefix . '【边栏】3.排行榜展示',
    'classname'   => 'sidebar-ranking-list',
    'description' => '排行榜展示',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '排行榜展示',
        ),

        array(
            'id'      => 'orderby',
            'type'    => 'select',
            'title'   => '排行榜方式',
            'options' => array(
                'views_num' => '阅读量排行', //views
                'likes_num' => '点赞量排行', //likes
                'fav_num'   => '收藏量排行', //
                'down_num'  => '下载量排行', //
                'pay_num'   => '购买量排行', //
            ),
            'default' => 'views_num',
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => '显示数量',
            'default' => 6,
        ),

    ),
));
function ri_sidebar_ranking_widget($args, $instance) {

    if ($args['id'] != 'single-sidebar') {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'   => '排行榜展示',
        'orderby' => 'views_num',
        'count'   => 6,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/sidebar-ranking-list', '', $instance);

    echo $args['after_widget'];
}

///////////////////////////////////////// 侧边栏作者展示 /////////////////////////////////////////
CSF::createWidget('ri_sidebar_author_widget', array(
    'title'       => $name_prefix . '【边栏】4.作者信息展示',
    'classname'   => 'sidebar-author-info',
    'description' => '作者信息展示',
    'fields'      => array(
        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '作者信息',
        ),

    ),
));
function ri_sidebar_author_widget($args, $instance) {

    if ($args['id'] != 'single-sidebar') {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'      => '作者信息',
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/sidebar-author-info', '', $instance);

    echo $args['after_widget'];
}



///////////////////////////////////////// 高级搜索模块 /////////////////////////////////////////
CSF::createWidget('ri_home_search_widget', array(
    'title'       => $name_prefix . '【首页】1.高级搜索模块',
    'classname'   => 'home-search-box',
    'description' => '高级搜索模块，分类搜索',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '搜索介绍标题',
            'default' => '搜索本站精品资源',
        ),
        array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '搜索描述介绍',
            'default' => 'RiPro是Ritheme全新开发甄品VIP会员资源/素材虚拟商城主题',
        ),

        array(
            'id'          => 'bg_type',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '背景效果',
            'desc'        => '',
            'options'     => array(
                'img'     => '图片',
                'video'   => '视频',
                'waves'   => '动态方块',
                'clouds'   => '动态天空',
                'net'   => '动态线条',
                'halo'   => '动态流光',
            ),
            'default'     => 'img',
        ),

        array(
            'id'      => 'is_mobile_img_bg',
            'type'    => 'switcher',
            'title'   => '手机强制图片背景',
            'default' => true,
        ),

        array(
            'id'      => 'bg_img',
            'type'    => 'upload',
            'title'   => '背景图片',
            'default' => get_template_directory_uri() . '/assets/img/bg.jpg',
            'dependency' => array('bg_type', '==', 'img'),
        ),

        array(
            'id'      => 'bg_video',
            'type'    => 'upload',
            'title'   => 'mp4背景视频',
            'default' => '',
            'dependency' => array('bg_type', '==', 'video'),
        ),

        array(
            'id'      => 'color',
            'type'    => 'color_group',
            'title'   => '效果器颜色',
            'options' => array(
                'bgcolor' => '背景颜色',
                'color'     => '粒子颜色',
            ),
            'default' => array('bgcolor'=>'#228ed6','color'=>'#ededed'),
            'dependency' => array('bg_type', 'any', 'waves,clouds,net,halo'),
        ),


        array(
            'id'      => 'bg_overlay',
            'type'    => 'switcher',
            'title'   => '背景颜色遮罩',
            'default' => true,
        ),

        array(
            'id'      => 'search_hot',
            'type'    => 'textarea',
            'title'   => '搜索热词',
            'desc'    => '每个搜索词用英文逗号隔开',
            'default' => 'wordpress,测试,下载,素材,作品,主题,插件,你好',
        ),

    ),
));
function ri_home_search_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'      => '搜索本站精品资源',
        'desc'       => 'RiPro是Ritheme全新开发甄品纯VIP会员资源/素材虚拟商城主题',
        'bg_type'    => 'img',
        'is_mobile_img_bg' => true,
        'bg_img'     => get_template_directory_uri() . '/assets/img/bg.jpg',
        'bg_video'   => '',
        'color'   => array('bgcolor'=>'#228ed6','color'=>'#ededed'),
        'bg_overlay' => true,
        'search_hot' => '',
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-search-box', '', $instance);

    echo $args['after_widget'];
}

///////////////////////////////////////// 幻灯片模块 /////////////////////////////////////////
CSF::createWidget('ri_home_slider_widget', array(
    'title'       => $name_prefix . '【首页】4.幻灯片模块',
    'classname'   => 'home-owl-slider',
    'description' => '幻灯片模块',
    'fields'      => array(

        array(
            'id'      => 'container',
            'type'    => 'radio',
            'title'   => '布局宽度',
            'inline'  => true,
            'options' => array(
                'container-full' => '全宽',
                'container'      => '普通',
            ),
            'default' => 'container-full',
        ),

        array(
            'id'      => 'config',
            'type'    => 'checkbox',
            'title'   => '幻灯片配置',
            'options' => array(
                'autoplay' => '自动播放',
                'loop'     => '循环播放',
                'nav'      => '切换按钮',
                'dots'     => '导航圆点',
            ),
            'inline'  => true,
            'default' => array('autoplay'),
        ),

        array(
            'id'          => 'items',
            'type'        => 'number',
            'title'       => '幻灯片列数',
            'unit'        => '列',
            'output'      => '.heading',
            'output_mode' => 'width',
            'default'     => '1',
        ),

        array(
            'id'     => 'data',
            'type'   => 'group',
            'title'  => '幻灯片内容配置',
            'fields' => array(
                array(
                    'id'      => '_img',
                    'type'    => 'upload',
                    'title'   => '上传幻灯片',
                    'default' => get_template_directory_uri() . '/assets/img/slider.jpg',
                ),
                array(
                    'id'       => '_desc',
                    'type'     => 'textarea',
                    'title'    => '描述内容，支持html代码',
                    'sanitize' => false,
                    'default'  => '<h3 class="text-white">Hello, RiPro Theme</h3><p class="lead  text-white d-none d-lg-block">这是一个简单的内容展示，您可以随意插入HTML代码任意组合显示。',
                ),
                array(
                    'id'      => '_href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'default' => '',
                ),
                array(
                    'id'      => '_target',
                    'type'    => 'radio',
                    'title'   => '链接打开方式',
                    'inline'  => true,
                    'options' => array(
                        '_self'  => '默认',
                        '_blank' => '新窗口打开',
                    ),
                    'default' => '_self',
                ),

            ),

        ),

    ),
));
function ri_home_slider_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'container' => 'container-full',
        'items'     => 1,
        'config'    => array('autoplay'),
        'data'      => array(),
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-owl-slider', '', $instance);

    echo $args['after_widget'];
}

///////////////////////////////////////// 最新文章展示 /////////////////////////////////////////
CSF::createWidget('ri_home_lastpost_widget', array(
    'title'       => $name_prefix . '【首页】2.最新文章模块',
    'classname'   => 'home-last-post',
    'description' => '最新文章展示',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '最新推荐',
        ),

        array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '描述介绍',
            'default' => '当前最新发布更新的热门资源，我们将会持续保持更新',
        ),

        array(
            'id'          => 'no_cat',
            'type'        => 'checkbox',
            'inline'      => true,
            'title'       => '要排除的分类',
            'placeholder' => '选择要排除的分类',
            'options'     => 'categories',
            'query_args'  => array(
                'hide_empty'  => 0,
            ),
        ),

        array(
            'id'          => 'cat_btn',
            'type'        => 'select',
            'title'       => '要展示分类快速查看按钮',
            'desc'        => '按顺序选择可以排序',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'hide_empty'  => 0,
            ),
        ),

        array(
            'id'      => 'is_pagination',
            'type'    => 'switcher',
            'title'   => '显示翻页按钮',
            'default' => true,
        ),

        array(
            'type'    => 'subheading',
            'content' => '文章数请在 WP后台-设置-阅读-博客页面至多显示调整',
        ),

    ),
));

function ri_home_lastpost_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'         => '最新推荐',
        'desc'          => '当前最新发布更新的热门资源，我们将会持续保持更新',
        'no_cat'        => array(),
        'cat_btn'       => array(),
        'is_pagination' => true,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-last-posts', '', $instance);

    echo $args['after_widget'];
}

///////////////////////////////////////// 分类文章展示 /////////////////////////////////////////
CSF::createWidget('ri_home_catpost_widget', array(
    'title'       => $name_prefix . '【首页】3.分类文章模块',
    'classname'   => 'home-cat-post',
    'description' => '按照分类展示文章',
    'fields'      => array(

        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '要展示得分类文章',
            'placeholder' => '选择分类',
            'options'     => 'categories',
            'query_args'  => array(
                'hide_empty'  => 0,
            ),
        ),

        array(
            'id'      => 'orderby',
            'type'    => 'radio',
            'title'   => '排序方式',
            'inline'  => true,
            'options' => array(
                'date'          => '日期',
                'rand'          => '随机',
                'comment_count' => '评论数',
                'views'         => '阅读量',
                'modified'      => '最近编辑时间',
                'title'         => '标题',
                'ID'            => '文章ID',
            ),
            'default' => 'date',
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => '显示数量',
            'default' => 8,
        ),

    ),
));
function ri_home_catpost_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'category' => 0,
        'orderby'  => 'date',
        'count'    => 8,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-cat-posts', '', $instance);

    echo $args['after_widget'];
}


///////////////////////////////////////// CMS文章展示 /////////////////////////////////////////
CSF::createWidget('ri_home_cmspost_widget', array(
    'title'       => $name_prefix . '【首页】6.CMS文章模块',
    'classname'   => 'home-cms-post',
    'description' => '按照分类展示文章',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => 'CMS文章',
        ),

        array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '描述介绍',
            'default' => '当前热门分类文章展示',
        ),

        array(
            'id'      => 'style',
            'type'    => 'select',
            'title'   => 'CMS布局风格',
            'options' => array(
                'list' => '左大图-右列表',
                'grid-overlay' => '左大图-右网格',
            ),
            'default' => 'grid-overlay',
        ),
        array(
            'id'      => 'is_box_right',
            'type'    => 'switcher',
            'title'   => '大图右侧显示',
            'default' => false,
        ),

        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '要展示得分类文章',
            'placeholder' => '选择分类',
            'desc' => '不设置则展示最新文章',
            'options'     => 'categories',
            'query_args'  => array(
                'hide_empty'  => 0,
            ),
        ),

        array(
            'id'      => 'orderby',
            'type'    => 'radio',
            'title'   => '排序方式',
            'inline'  => true,
            'options' => array(
                'date'          => '日期',
                'rand'          => '随机',
                'comment_count' => '评论数',
                'views'         => '阅读量',
                'modified'      => '最近编辑时间',
                'title'         => '标题',
                'ID'            => '文章ID',
            ),
            'default' => 'date',
        ),

    ),
));
function ri_home_cmspost_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'    => 'CMS文章',
        'desc'     => '当前推荐文章展示',
        'category' => 0,
        'orderby'  => 'date',
        'style'    => 'grid-overlay',
        'is_box_right'  => false,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-cms-posts', '', $instance);

    echo $args['after_widget'];
}


///////////////////////////////////////// 分类BOX展示 /////////////////////////////////////////
CSF::createWidget('ri_home_catbox_widget', array(
    'title'       => $name_prefix . '【首页】5.分类BOX模块',
    'classname'   => 'home-cat-box',
    'description' => '展示网站分类信息',
    'fields'      => array(

        array(
            'id'          => 'category',
            'type'        => 'select',
            'title'       => '要展示的分类',
            'desc'        => '按顺序选择可以排序',
            'placeholder' => '选择分类',
            'inline'      => true,
            'chosen'      => true,
            'multiple'    => true,
            'options'     => 'categories',
            'query_args'  => array(
                'hide_empty'  => 0,
            ),
        ),

        array(
            'id'      => 'is_num',
            'type'    => 'checkbox',
            'title'   => '显示文章数量',
            'default' => true,
        ),

    ),
));
function ri_home_catbox_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'category' => array(),
        'is_num'   => true,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-cat-box', '', $instance);

    echo $args['after_widget'];
}


///////////////////////////////////////// 横条小块模块 /////////////////////////////////////////
CSF::createWidget('ri_home_division_widget', array(
    'title'       => $name_prefix . '【首页】7.横条图标模块',
    'classname'   => 'home-division',
    'description' => '添加小块图标介绍',
    'fields'      => array(

        array(
            'id'          => 'icon_style',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '图标风格',
            'placeholder' => '',
            'options'     => array(
                'rounded-2'    => '方形',
                'rounded-circle'   => '圆形',
            ),
            'default'     => 'rounded-2',
        ),

        array(
            'id'         => 'div_data',
            'type'       => 'group',
            'title'      => '新建',
            'fields'     => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题文字',
                    'default' => '标题文字',
                ),
                array(
                    'id'         => 'icon',
                    'type'       => 'icon',
                    'title'      => '图标',
                    'desc'       => '设置站内币图标，部分页面展示需要',
                    'default'    => 'fab fa-buffer',
                ),
                array(
                    'id'      => 'color',
                    'type'    => 'color',
                    'title'   => '图标颜色',
                    'default' => '#1e73be'
                ),
                array(
                    'id'      => 'desc',
                    'type'    => 'text',
                    'title'   => '描述内容',
                    'default' => '这里是描述内容介绍',
                ),
                array(
                    'id'      => 'link',
                    'type'    => 'text',
                    'title'   => '链接',
                    'desc'   => '不填写则不启用链接',
                    'default' => '',
                ),

            ),
            'default' => array(
                array(
                    'title' => '模块化首页',
                    'icon'  => 'fab fa-buffer',
                    'color'  => '#8399ff',
                    'desc'  => 'WP原生可视化模块定制',
                    'link'  => '',
                ),
                array(
                    'title' => '商城支持',
                    'icon'  => 'fab fa-shopify',
                    'color'  => '#FF9800',
                    'desc'  => '付费下载、查看、音视频播放',
                    'link'  => '',
                ),
                array(
                    'title' => '多级菜单',
                    'icon'  => 'fas fa-align-justify',
                    'color'  => '#4c4c4c',
                    'desc'  => '自定义菜单图标，三级菜单',
                    'link'  => '',
                ),
                array(
                    'title' => '会员系统',
                    'icon'  => 'far fa-gem',
                    'color'  => '#ff75a4',
                    'desc'  => '内置VIP和用户中心系统',
                    'link'  => '',
                ),
            ),
        ),

    ),
));
function ri_home_division_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'div_data' => array(),
        'icon_style' => 'cube',
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-division', '', $instance);

    echo $args['after_widget'];
}


///////////////////////////////////////// 图片背景按钮模块 /////////////////////////////////////////
CSF::createWidget('ri_home_background_widget', array(
    'title'       => $name_prefix . '【首页】8.图片背景按钮',
    'classname'   => 'home-background',
    'description' => '图片banner和按钮模块',
    'fields'      => array(


        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '模块主标题',
            'default' => '这是图片背景主标题',
        ),
        array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '模块介绍文字',
            'default' => '这里是模块介绍文字，不填写则不显示，并可以添加不同颜色按钮',
        ),

        array(
            'id'      => 'bg_img',
            'type'    => 'upload',
            'title'   => '背景图片',
            'default' => get_template_directory_uri() . '/assets/img/bg.jpg',
        ),

        array(
            'id'          => 'bg_style',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '背景图片风格',
            'placeholder' => '',
            'options'     => array(
                'fixed'    => '固定',
                'scroll-circle'   => '跟随',
            ),
            'default'     => 'fixed',
        ),

        array(
            'id'         => 'btn_data',
            'type'       => 'group',
            'title'      => '新建',
            'fields'     => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '按钮名称',
                    'default' => '标题文字',
                ),
                array(
                    'id'         => 'icon',
                    'type'       => 'icon',
                    'title'      => '按钮图标',
                    'desc'       => '设置站内币图标，部分页面展示需要',
                    'default'    => 'fab fa-buffer',
                ),
                array(
                    'id'      => 'link',
                    'type'    => 'text',
                    'title'   => '链接',
                    'desc'   => '不填写则不启用链接',
                    'default' => '',
                ),
                array(
                    'id'          => 'color',
                    'type'        => 'radio',
                    'inline'      => true,
                    'title'       => '按钮颜色',
                    'placeholder' => '',
                    'options'     => array(
                        'primary' => 'primary',
                        'secondary' => 'secondary',
                        'success' => 'success',
                        'danger' => 'danger',
                        'warning' => 'warning',
                        'info' => 'info',
                        'light' => 'light',
                        'dark' => 'dark',
                    ),
                    'default'     => 'primary',
                ),
                
            ),
            'default' => array(
                array(
                    'title' => '按钮名称1',
                    'icon'  => 'fab fa-buffer',
                    'color'  => 'info',
                    'link'  => '#',
                ),
                array(
                    'title' => '按钮名称2',
                    'icon'  => 'fab fa-buffer',
                    'color'  => 'success',
                    'link'  => '#',
                ),
            ),
        ),

    ),
));
function ri_home_background_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title' => 'RiPro是一个优秀的主题',
        'desc' => 'RiPro主题全新V5版本，是一个优秀且功能强大、易于管理、现代化的WordPress虚拟资源商城主题',
        'bg_img' => get_template_directory_uri() . '/assets/img/bg.jpg',
        'bg_style' => 'fixed',
        'btn_data' => array(),
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-background', '', $instance);

    echo $args['after_widget'];
}




///////////////////////////////////////// 网站动态模块 /////////////////////////////////////////
CSF::createWidget('ri_home_dynamic_widget', array(
    'title'       => $name_prefix . '【首页】9.网站动态展示',
    'classname'   => 'home-dynamic',
    'description' => '网站动态展示',
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'Text',
            'title' => '标题',
            'default' => '网站动态',
        ),

        array(
            'id'      => 'bg_color',
            'type'    => 'select',
            'title'   => '背景颜色',
            'options' => array(
                'primary'   => '蓝色',
                'success'   => '绿色',
                'danger'    => '红色',
                'warning'   => '黄色',
                'secondary' => '灰色',
                'dark'      => '黑色',
            ),
            'default' => 'primary',
        ),

        array(
            'id'      => 'is_autoplay',
            'type'    => 'switcher',
            'title'   => '自动播放',
            'default' => true,
        ),

    ),
));
function ri_home_dynamic_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title' => '网站动态',
        'bg_color' => 'primary',
        'is_autoplay' => true,
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-dynamic', '', $instance);

    echo $args['after_widget'];
}



///////////////////////////////////////// 优惠码刮刮卡 /////////////////////////////////////////
CSF::createWidget('ri_home_scratch_card', array(
    'title'       => $name_prefix . '【首页】10.优惠码发放展示',
    'classname'   => 'home-scratch-card',
    'description' => '优惠码发放展示',
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'Text',
            'title' => '标题',
            'default' => '优惠码领取',
        ),

        array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '模块介绍文字',
            'default' => '领取限量优惠码，错过在等下一波',
        ),

        array(
            'id'      => 'cdk_data',
            'type'    => 'textarea',
            'title'   => '卡密列表',
            'desc'   => '每行一个',
            'default' => 'xxxxxxx'.PHP_EOL.'xxxxxxx'.PHP_EOL.'xxxxxxx'.PHP_EOL.'xxxxxxx'.PHP_EOL.'xxxxxxx',
        ),

    ),
));
function ri_home_scratch_card($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title' => '优惠码领取',
        'desc' => '刮开刮刮卡领取优惠码，错过在等下一波',
        'cdk_data' => '',
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-scratch-card', '', $instance);

    echo $args['after_widget'];
}




///////////////////////////////////////// VIP介绍模块 /////////////////////////////////////////
CSF::createWidget('ri_home_vip_card', array(
    'title'       => $name_prefix . '【首页】11.VIP介绍展示',
    'classname'   => 'home-vip-card',
    'description' => 'VIP介绍展示',
    'fields'      => array(

        array(
            'id'    => 'title',
            'type'  => 'textarea',
            'sanitize'   => false,
            'title' => '标题',
            'default' => '<i class="fa fa-diamond me-1"></i> 加入本站会员，开启尊贵特权之体验',
        ),

        array(
            'id'      => 'desc',
            'type'    => 'textarea',
            'title'   => '模块介绍文字',
            'default' => '本站资源支持会员下载专享，普通注册会员只能原价购买资源或者限制免费下载次数，付费会员所有资源可无限下载。并可享受资源折扣或者免费下载。',
        ),

    ),
));
function ri_home_vip_card($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title' => '<i class="fa fa-diamond me-1"></i>加入本站会员，开启尊贵特权之体验',
        'desc' => '本站资源支持会员下载专享，普通注册会员只能原价购买资源或者限制免费下载次数，付费会员所有资源可无限下载。并可享受资源折扣或者免费下载。',
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-vip-card', '', $instance);

    echo $args['after_widget'];
}




///////////////////////////////////////// 纯标题文章展示 /////////////////////////////////////////
CSF::createWidget('ri_home_titlepost_widget', array(
    'title'       => $name_prefix . '【首页】12.纯标题文章展示',
    'classname'   => 'home-title-post',
    'description' => '按照分类展示纯标题文章',
    'fields'      => array(

        array(
            'id'      => 'title',
            'type'    => 'text',
            'title'   => '标题',
            'default' => '纯标题文章展示',
        ),

        array(
            'id'      => 'desc',
            'type'    => 'text',
            'title'   => '描述介绍',
            'default' => '文章标题列表展示',
        ),

        array(
            'id'      => 'col',
            'type'    => 'radio',
            'inline'  => true,
            'title'   => '显示列数',
            'options' => array(
                '1'          => '1列',
                '2'          => '2列',
                '3'          => '3列',
                '4'          => '4列',
            ),
            'default' => '3',
        ),

        array(
            'id'      => 'count',
            'type'    => 'text',
            'title'   => '每列显示数量',
            'default' => 4,
        ),


        array(
            'id'         => 'category_data',
            'type'       => 'group',
            'title'      => '要展示得分类',
            'fields'     => array(
                array(
                    'id'          => 'category',
                    'type'        => 'select',
                    'title'       => '要展示的分类文章',
                    'placeholder' => '选择分类',
                    'options'     => 'categories',
                    'query_args'  => array(
                        'hide_empty'  => 0,
                    ),
                ),

                array(
                    'id'      => 'orderby',
                    'type'    => 'radio',
                    'title'   => '排序方式',
                    'inline'  => true,
                    'options' => array(
                        'date'          => '日期',
                        'rand'          => '随机',
                        'comment_count' => '评论数',
                        'views'         => '阅读量',
                        'modified'      => '最近编辑时间',
                        'title'         => '标题',
                        'ID'            => '文章ID',
                    ),
                    'default' => 'date',
                ),
            ),
        ),

    ),
));
function ri_home_titlepost_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'title'    => '纯标题文章展示',
        'desc'     => '当前热门分类文章展示',
        'col' => 2,
        'count' => 4,
        'category_data' => array(),
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-title-posts', '', $instance);

    echo $args['after_widget'];
}




///////////////////////////////////////// 【首页】13.网站统计展示 /////////////////////////////////////////
CSF::createWidget('ri_home_overview_widget', array(
    'title'       => $name_prefix . '【首页】13.网站统计展示',
    'classname'   => 'home-overview',
    'description' => '展示网站文章，用户等统计',
    'fields'      => array(


        array(
            'id'      => 'bg_img',
            'type'    => 'upload',
            'title'   => '背景图片',
            'default' => get_template_directory_uri() . '/assets/img/bg2.png',
        ),

        array(
            'id'          => 'bg_style',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '背景图片风格',
            'placeholder' => '',
            'options'     => array(
                'fixed'    => '固定',
                'scroll-circle' => '跟随',
            ),
            'default'     => 'fixed',
        ),


        array(
            'id'      => 'datas',
            'type'    => 'checkbox',
            'title'   => '要展示的数据',
            'options' => array(
                'coutn_day' => '运营天数',
                'count_post' => '文章总数',
                'count_user' => '用户总数',
                'count_user_vip' => 'VIP会员数',
                'count_day_user' => '今日注册用户数',
                'conunt_up_post' => '近7天更新数', 
                'conunt_cats' => '分类总数', 
                'conunt_comment' => '评论总数', 
                'count_post_views' => '文章浏览量总数',
            ),
            'inline'  => true,
            'default' => array('coutn_day', 'count_post', 'count_user', 'count_day_user','count_user_vip','count_post_views'),
        ),
        array(
            'id'       => 'time',
            'type'     => 'date',
            'title'    => '网站创建时间',
            'desc'     => '设置后，自动累计运营天数',
            'settings' => array(
                'dateFormat' => 'yy-mm-dd', //date("Y-m-d");
            ),
            'dependency' => array( 'datas', 'any', 'coutn_day' ),
        ),



    ),
));
function ri_home_overview_widget($args, $instance) {

    if (strpos( $args['id'], 'home-center' ) === false) {
        return false; //非首页模块页面不显示
    }

    $instance = array_merge(array(
        'bg_img' => get_template_directory_uri() . '/assets/img/bg2.png',
        'bg_style' => 'fixed',
        'datas' => array('coutn_day', 'count_post', 'count_user', 'count_day_user','count_user_vip','count_post_views'),
        'time' => wp_date("Y-m-d"),
    ), $instance);

    echo $args['before_widget'];

    get_template_part('template-parts/widget/home-overview', '', $instance);

    echo $args['after_widget'];
}
