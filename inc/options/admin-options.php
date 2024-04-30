<?php

defined('ABSPATH') || exit;

if (!current_user_can('manage_options') || !is_admin()) {
    return;
}

if (!class_exists('CSF')) {
    return;
}

$prefix       = _OPTIONS_PRE;
$template_dir = get_template_directory_uri();


function _get_custom_taxonomy_option() {
    $data = _cao('site_custom_taxonomy', array());
    $option = array();
    if (empty($data) || !is_array($data)) {
        return array();
    }
    foreach ($data as $key => $value) {
        $custom_taxonomy          = trim($value['taxonomy']);
        $option[$custom_taxonomy] = $value['name'];
    }
    return $option;
}


CSF::createOptions($prefix, array(
    'menu_title' => '主题设置',
    'menu_slug'  => 'ripro',
));

CSF::createSection($prefix, array(
    'title'  => '基本设置',
    'fields' => array(

        array(
            'id'          => 'assets_cdn_mod',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '网站静态资源加载模式',
            'desc'        => '推荐使用CDN加载图标文件，速度较快，不占用网站请求压力，优化效果明显，如果某个CDN节点不稳定或者慢，更换即可',
            'placeholder' => '',
            'options'     => array(
                'theme'      => '本地加载',
                'jsdelivr'   => 'jsdelivr源',
                'unpkg'      => 'unpkg源',
                'cloudflare' => 'cloudflare源',
                'bootcdn'    => 'bootcdn源',
            ),
            'default'     => 'theme',
        ),

        array(
            'id'      => 'site_logo',
            'type'    => 'upload',
            'title'   => '网站LOGO',
            'default' => _cao_old('site_logo', get_template_directory_uri() . '/assets/img/logo.png'),
        ),

        array(
            'id'      => 'site_logo_dark',
            'type'    => 'upload',
            'title'   => '网站暗色模式LOGO',
            'default' => get_template_directory_uri() . '/assets/img/logo-dark.png',
        ),

        array(
            'id'      => 'site_favicon',
            'type'    => 'upload',
            'title'   => '网站favicon图标',
            'default' => _cao_old('site_favicon', get_template_directory_uri() . '/assets/img/favicon.png'),
        ),


        array(
            'id'      => 'is_site_notify',
            'type'    => 'switcher',
            'title'   => '全站弹窗公告',
            'desc'   => '开启网站顶部显示公告图标，点击弹出公告',
            'default' => true,
        ),

        array(
            'id'      => 'is_site_notify_auto',
            'type'    => 'checkbox',
            'title'   => '自动弹出公告',
            'label'   => '用户首次打开网站自动弹出公告',
            'default' => true,
            'dependency' => array('is_site_notify', '==', 'true'),
        ),

        array(
            'id'         => 'site_notify_title',
            'type'       => 'text',
            'title'      => '全站弹窗公告-标题',
            'desc'       => '纯文本',
            'attributes' => array(
                'style' => 'width: 100%;',
            ),
            'default'    => '本站最新公告通知',
            'dependency' => array('is_site_notify', '==', 'true'),
        ),
        array(
            'id'         => 'site_notify_desc',
            'type'       => 'textarea',
            'title'      => '全站弹窗公告-内容',
            'desc'       => '支持纯文本和自定义html代码内容',
            'attributes' => array(
                'style' => 'width: 100%;',
            ),
            'sanitize' => false,
            'default'    => '感谢您使用体验RiPro主题，此公告内容支持首次自动弹出通知，支持纯文本通知弹窗，支持html代码等自定义内容。主题介绍 <a href="https://ritheme.com/theme/ripro-v5.html" target="_blank" style="text-align:center;color:#ff6499;"><i class="far fa-hand-point-right me-1"></i>RiPro主题官网</a><a href="https://ritheme.com/" target="_blank" rel="nofollow noopener noreferrer" title="广告：多款wordpress正版主题打包仅需599"><img src="/wp-content/themes/ripro-v5/assets/img/adds-2.jpg" style="width:100%;margin-top:.5rem;border-radius:.35rem;"></a>',
            'dependency' => array('is_site_notify', '==', 'true'),
        ),


        array(
            'id'      => 'is_site_tougao',
            'type'    => 'switcher',
            'title'   => '网站投稿功能',
            'label'   => '控制网站投稿功能开关，启用后前端用户可以投稿管理编辑自己的文章,发布权限跟随WP自身，订阅者、贡献者需要审核、作者权限不需要审核，使用此功能请自行对自己的用户恶意上传投稿附件图片等审核负责。嫌麻烦就关闭',
            'default' => true,
        ),

        array(
            'id'      => 'is_site_comments',
            'type'    => 'switcher',
            'title'   => '网站评论功能',
            'label'   => '控制网站评论功能开关，如果用不到，推荐关闭',
            'default' => true,
        ),
        array(
            'id'      => 'is_site_tickets',
            'type'    => 'switcher',
            'title'   => '网站工单功能',
            'label'   => '控制网站工单功能开关，启用后用户可在前端个人中心提交问题反馈咨询等，管理员可在后台回复管理',
            'default' => true,
        ),

        array(
            'id'      => 'is_site_tags_page',
            'type'    => 'switcher',
            'title'   => '网站标签云功能',
            'label'   => '开启后可打开访问，功能页面地址：'.esc_url(home_url('/tags')),
            'default' => true,
        ),
        array(
            'id'      => 'is_site_link_manager_page',
            'type'    => 'switcher',
            'title'   => '网站网址导航功能',
            'label'   => '开启后可打开访问，功能页面地址：'.esc_url(home_url('/links')),
            'default' => true,
        ),
        array(
            'id'      => 'is_site_vip_price_page',
            'type'    => 'switcher',
            'title'   => '网站VIP介绍页面',
            'label'   => '开启后可在前台独立页面展示网站VIP套餐和介绍，功能页面地址：'.esc_url(home_url('/vip-prices')),
            'default' => true,
        ),


        array(
            'id'      => 'site_main_target_blank',
            'type'    => 'switcher',
            'title'   => '网站主链接新窗口打开文章',
            'desc'    => '主要链接包括列表展示盒网格展示一些文章都新窗口打开',
            'default' => false,
        ),
        

        array(
            'id'       => 'site_web_css',
            'type'     => 'textarea',
            'title'    => '自定义CSS样式代码',
            'before'   => '<p class="csf-text-muted"><strong>位于顶部，自定义修改CSS</strong>不用添加<strong>&lt;style></strong>标签</p>',
            'sanitize' => false,
            'default'  => '',
        ),

        array(
            'id'       => 'site_web_js',
            'type'     => 'textarea',
            'title'    => '网站底部自定义JS代码',
            'desc'     => '位于底部，用于添加第三方流量数据统计代码，如：Google analytics、百度统计、CNZZ,例如：' . esc_attr('<script>统计代码</script>'),
            'sanitize' => false,
            'default'  => '',
        ),

    ),
));

CSF::createSection($prefix, array(
    'title'  => '安全设置',
    'fields' => array(

        array(
            'id'      => 'site_security_key',
            'type'    => 'text',
            'title'   => '网站安全秘钥',
            'desc'    => '用于网站免登录购买游客识别等敏感信息加密等重要参数，可定期更换，中途更换后，如果刚好有游客购买资源，则购买权限缓存将失效，切勿在高峰期频繁更换或泄露给他人',
            'default' => md5(home_url() . time()),
        ),

        array(
            'id'      => 'is_site_down_basename',
            'type'    => 'switcher',
            'title'   => '下载时显示真实文件名',
            'label'   => '用户下载站内文件时，文件名显示真实文件名，如无特殊需求，不建议开启，容易被人恶意抓取嗅探文件地址',
            'default' => false,
        ),

        array(
            'id'      => 'site_login_security_param',
            'type'    => 'text',
            'title'   => 'WP自带登录地址访问密码',
            'desc'    => '留空则不开启后台登录地址（' . esc_url(home_url('/wp-login.php')) . '）保护，<br>开启后您的真实登录地址是<b style="color:red;">' . esc_url(home_url('/wp-login.php')) . '?security=你设置得密码</b><br>设置后请牢记自己的真实登录地址，此功能不影响网站前台登录界面，只是为了保护wp本身自带得登录地址，防止他人恶意扫描攻击爆破，可有效保护',
            'default' => '',
        ),

        

    ),
));

CSF::createSection($prefix, array(
    'title'  => 'SEO设置',
    'icon'   => '',
    'fields' => array(

        array(
            'type'    => 'content',
            'content' => '主题自带SEO优化功能说明：
            <br>1，自带SEO功能包含了自定义文章，首页，页面的TDK功能，自动抓取网站摘要，关键词，自动添加OG协议描述信息等
            <br>2，支持自定义替换wordpress默认的标题链接符号',
        ),

        array(
            'id'      => 'site_no_categoty',
            'type'    => 'switcher',
            'title'   => '分类别名category精简',
            'desc'   => '网站文章分类链接去除category/前缀，非特殊需求不必开启，尽量保持WP原有规则',
            'default' => _cao_old('no_categoty',false),
        ),

        array(
            'id'      => 'is_theme_seo',
            'type'    => 'switcher',
            'title'   => '主题内置的SEO功能',
            'label'   => '有部分用户在用插件做SEO，可以在此关闭主题自带SEO功能',
            'default' => true,
        ),

        array(
            'id'         => 'site_seo',
            'type'       => 'fieldset',
            'title'      => '内置SEO设置',
            'fields'     => array(
                array(
                    'id'      => 'separator',
                    'type'    => 'text',
                    'title'   => '全站链接符',
                    'desc'    => '一经选择，切勿中途更改，对SEO不友好，一般为“-”或“_”',
                    'default' => _cao_old('site_seo:separator', '-'),
                ),
                array(
                    'id'         => 'keywords',
                    'type'       => 'text',
                    'title'      => '网站关键词',
                    'desc'       => '3-5个关键词，用英文逗号隔开',
                    'attributes' => array(
                        'style' => 'width: 100%;',
                    ),
                    'default'    => _cao_old('site_seo:keywords', ''),
                ),
                array(
                    'id'       => 'description',
                    'type'     => 'textarea',
                    'sanitize' => false,
                    'title'    => '网站描述',
                    'default'  => _cao_old('site_seo:description', ''),
                ),

            ),
            'dependency' => array('is_theme_seo', '==', 'true'),
        ),

    ),
));




CSF::createSection($prefix, array(
    'id'    => 'style_fields',
    'title' => '布局设置',
));

CSF::createSection($prefix, array(
    'parent' => 'style_fields',
    'title'  => '全局颜色风格',
    'fields' => array(


        array(
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => '根据你的个人喜好合理设置配色，设置随时保存前台刷新页面观察效果',
        ),

        array(
            'id'      => 'is_site_dark_toggle',
            'type'    => 'switcher',
            'title'   => '网站深色暗黑模式切换',
            'desc'    => '开启后，网站可以切换白天黑夜模式',
            'default' => true,
        ),

        array(
            'id'          => 'site_default_color_mod',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '网站默认颜色模式',
            'desc'        => '自动模式为当地时间早6点开始到晚18点为白天模式，其后为黑夜模式，系统自动时候别时区。',
            'placeholder' => '',
            'options'     => array(
                'light' => '亮色（白天）',
                'dark'  => '深色（黑夜）',
                'auto'  => '自动（早晚）',
            ),
            'default'     => 'light',
        ),
        
        array(
            'id'          => 'site_container_width',
            'type'        => 'number',
            'title'       => '网站全局内容区域宽度',
            'desc'        => '（在浏览器宽度1200px以上时的container宽度，其他小屏幕尺寸自适应）留空则默认1200px',
            'unit'        => 'PX',
            'output'      => '.heading',
            'output_mode' => 'width',
            'default'     => '',
        ),

        array(
            'id'    => 'site_background',
            'type'  => 'background',
            'title' => '网站背景配置',
        ),

        array(
            'id'      => 'site_header_color',
            'type'    => 'color_group',
            'title'   => '网站顶部菜单颜色配置',
            'options' => array(
                'bg-color'     => '菜单背景颜色',
                'sub-bg-color' => '子菜单背景颜色',
                'color'        => '文字颜色',
                'hover-color'  => '文字滑中颜色',
            ),
        ),

        array(
            'id'      => 'is_site_home_header_transparent',
            'type'    => 'switcher',
            'title'   => '首页顶部菜单透明',
            'desc'    => '开启后，可以搭配首页模块顶部的全宽搜索模块或者幻灯片展示',
            'default' => false,
        ),
        
    ),
));


CSF::createSection($prefix, array(
    'parent' => 'style_fields',
    'title'  => '默认文章列表布局',
    'fields' => array(


        array(
            'type'    => 'submessage',
            'style'   => 'info',
            'content' => '根据你网站文章类型前往WP后台设置-媒体-缩略图大小设置缩略图尺寸，常见尺寸300x200 ，400x300，不勾选强制裁剪，或者600x600，适配各类型比例。如果你网站大部分文章是采集的，建议点选本页面下方关闭WP自带图片裁剪功能，这样后台上传图片时，不默认裁剪多个缩略图，减少占用空间、因本主题功能支持不同宽度高度列表布局，所以没有完美固定的缩略图尺寸推荐，请根据自己网站整体内容风格尝试不同比例尺寸。如需完美尺寸请固定全站风格为一种布局。',
        ),

        array(
            'id'      => 'default_thumb',
            'type'    => 'upload',
            'title'   => '文章默认缩略图',
            'desc'    => '设置文章默认缩略图（建议和自定义文章缩略图宽高保持一致）',
            'default' => get_template_directory_uri() . '/assets/img/thumb.jpg',
        ),

        array(
            'id'        => 'rand_default_thumb',
            'type'      => 'gallery',
            'title'     => '文章随机缩略图',
            'add_title' => '上传图片',
            'desc'      => '不设置则不启用，建议添加10张，设置随机缩略图集则自动启用，对没有缩略图的文章随机缩略图，随机缩略图会带有文章id的关联算法，优化了搜索引擎抓图，防止完全随机导致收录后地址变更问题',
        ),


        array(
            'id'          => 'site_thumb_size_type',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '缩略图显示模式',
            'desc'        => '',
            'placeholder' => '',
            'options'     => array(
                'bg-cover'   => 'cover：自适应铺满',
                'bg-auto'    => 'auto：原图大小',
                'bg-contain' => 'contain：缩放全图',
            ),
            'default'     => 'bg-cover',
        ),

        array(
            'id'          => 'site_thumb_fit_type',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '缩略图对齐模式',
            'desc'        => '因网站采用自适应设计，尽可能的在自适应缩略图展示完整的情况根据对齐模式优先对齐展示缩略图',
            'placeholder' => '',
            'options'     => array(
                'bg-left-top'      => '左上',
                'bg-right-top'     => '右上',
                'bg-center-top'    => '中上',
                'bg-center'        => '居中',
                'bg-center-bottom' => '中下',
                'bg-left-bottom'   => '左下',
                'bg-right-bottom'  => '右下',
            ),
            'default'     => 'bg-center',
        ),

        array(
            'id'      => 'is_post_one_thumbnail',
            'type'    => 'switcher',
            'title'   => '自动抓取第一张图片',
            'desc'    => '没设置特色图自动获取文章中第一张图片作为特色图，如果出现抓取的是最后一张的情况，请检查文章内容中的图片是否有回车或者空格隔开，必须隔开才能识别',
            'default' => true,
        ),

        array(
            'id'      => 'disable_wp_thumbnail_crop',
            'type'    => 'switcher',
            'title'   => '关闭WP自带图片裁剪',
            'desc'    => '防止每次上传图片都生成多余缩略图占用空间问题，如果您网站缩略图尺寸单一，建议开启此项',
            'default' => false,
        ),

        array(
            'id'      => 'post_thumbnail_size',
            'type'    => 'image_select',
            'title'   => '全站默认缩略图尺寸比例',
            'desc'    => '常见宽高3:2比例，3:3正方形，2:3比例，',
            'options' => array(
                'ratio-2x3'  => $template_dir . '/assets/img/options/img-2x3.png',
                'ratio-3x4'  => $template_dir . '/assets/img/options/img-3x4.png',
                'ratio-1x1'  => $template_dir . '/assets/img/options/img-1x1.png',
                'ratio-4x3'  => $template_dir . '/assets/img/options/img-4x3.png',
                'ratio-3x2'  => $template_dir . '/assets/img/options/img-3x2.png',
                'ratio-16x9' => $template_dir . '/assets/img/options/img-16x9.png',
                'ratio-21x9' => $template_dir . '/assets/img/options/img-21x9.png',
            ),
            'default' => 'ratio-3x2',
        ),

        array(
            'id'      => 'archive_item_style',
            'type'    => 'image_select',
            'title'   => '全站默认文章列表展示风格',
            'desc'    => '网格，列表，图片，图标风格',
            'options' => array(
                'grid'         => $template_dir . '/assets/img/options/item-grid.png',
                'grid-overlay' => $template_dir . '/assets/img/options/item-grid-overlay.png',
                'list'         => $template_dir . '/assets/img/options/item-list.png',
                'title'        => $template_dir . '/assets/img/options/item-title.png',
            ),
            'default' => 'grid',
        ),

        array(
            'id'      => 'archive_item_col',
            'type'    => 'image_select',
            'title'   => '全站默认文章列表展示列数',
            'desc'    => '在最大尺寸1080px宽度时显示列数，其他设备自适应展示',
            'options' => array(
                '1' => $template_dir . '/assets/img/options/col-1.png',
                '2' => $template_dir . '/assets/img/options/col-2.png',
                '3' => $template_dir . '/assets/img/options/col-3.png',
                '4' => $template_dir . '/assets/img/options/col-4.png',
                '5' => $template_dir . '/assets/img/options/col-5.png',
                '6' => $template_dir . '/assets/img/options/col-6.png',
            ),
            'default' => '4',
        ),

        array(
            'id'      => 'archive_item_entry',
            'type'    => 'checkbox',
            'title'   => '全站文章列表辅助信息显示',
            'options' => array(
                'category_dot' => '显示分类',
                'entry_desc'   => '显示摘要',
                'vip_icon'     => 'VIP图标',
                'entry_footer' => '显示时间，阅读数点赞数等',
            ),
            'inline'  => true,
            'default' => array('category_dot', 'entry_desc', 'entry_footer', 'vip_icon'),
        ),

        array(
            'id'      => 'archive_item_entry_footer',
            'type'    => 'checkbox',
            'title'   => '列表辅助信息开关',
            'options' => array(
                'date'  => '显示日期时间',
                'likes' => '显示点赞数',
                'views' => '显示阅读量',
                'fav'   => '显示收藏数',
                'price' => '价格',
            ),
            'inline'  => true,
            'default' => array('date', 'likes', 'views', 'likes', 'fav','price'),
        ),



        array(
            'id'      => 'site_page_nav_type',
            'type'    => 'radio',
            'inline'  => true,
            'title'   => '网站翻页风格模式',
            'desc'    => '',
            'options' => array(
                'click'  => '点击按钮加载更多',
                'auto'   => '下拉自动加载更多',
                'number' => '上/下页翻页按钮',
            ),
            'default' => 'click',
        ),

    ),
));

CSF::createSection($prefix, array(
    'parent' => 'style_fields',
    'title'  => '自定义分类布局',
    'fields' => array(
        array(
            'id'                     => 'site_term_item_style',
            'type'                   => 'group',
            'title'                  => '自定义分类页面布局风格',
            'subtitle'               => '单独设置某个分类的布局风格',
            'accordion_title_number' => true,
            'fields'                 => array(
                array(
                    'id'         => 'cat_id',
                    'type'       => 'select',
                    'title'      => '关联分类',
                    'desc'       => '配置此分类页面下的布局风格',
                    'options'    => 'categories',
                    'query_args' => array(
                        'orderby' => 'count',
                        'order'   => 'DESC',
                    ),
                ),

                array(
                    'id'      => 'post_thumbnail_size',
                    'type'    => 'image_select',
                    'title'   => '默认缩略图尺寸比例',
                    'desc'    => '常见宽高3:2比例，3:3正方形，2:3比例，',
                    'options' => array(
                        'ratio-2x3'  => $template_dir . '/assets/img/options/img-2x3.png',
                        'ratio-3x4'  => $template_dir . '/assets/img/options/img-3x4.png',
                        'ratio-1x1'  => $template_dir . '/assets/img/options/img-1x1.png',
                        'ratio-4x3'  => $template_dir . '/assets/img/options/img-4x3.png',
                        'ratio-3x2'  => $template_dir . '/assets/img/options/img-3x2.png',
                        'ratio-16x9' => $template_dir . '/assets/img/options/img-16x9.png',
                        'ratio-21x9' => $template_dir . '/assets/img/options/img-21x9.png',
                    ),
                    'default' => 'ratio-3x2',
                ),

                array(
                    'id'      => 'archive_item_style',
                    'type'    => 'image_select',
                    'title'   => '分类页默认文章列表展示风格',
                    'desc'    => '网格，列表，图片，图标风格',
                    'options' => array(
                        'grid'         => $template_dir . '/assets/img/options/item-grid.png',
                        'grid-overlay' => $template_dir . '/assets/img/options/item-grid-overlay.png',
                        'list'         => $template_dir . '/assets/img/options/item-list.png',
                        'title'        => $template_dir . '/assets/img/options/item-title.png',
                    ),
                    'default' => 'grid',
                ),
                array(
                    'id'      => 'archive_item_col',
                    'type'    => 'image_select',
                    'title'   => '分类页默认文章列表展示列数',
                    'desc'    => '在最大尺寸1080px宽度时显示列数，其他设备自适应展示',
                    'options' => array(
                        '1' => $template_dir . '/assets/img/options/col-1.png',
                        '2' => $template_dir . '/assets/img/options/col-2.png',
                        '3' => $template_dir . '/assets/img/options/col-3.png',
                        '4' => $template_dir . '/assets/img/options/col-4.png',
                        '5' => $template_dir . '/assets/img/options/col-5.png',
                        '6' => $template_dir . '/assets/img/options/col-6.png',
                    ),
                    'default' => '4',
                ),
                array(
                    'id'      => 'archive_item_entry',
                    'type'    => 'checkbox',
                    'title'   => '列表其他信息显示',
                    'options' => array(
                        'category_dot' => '显示分类',
                        'entry_desc'   => '显示摘要',
                        'vip_icon'     => 'VIP图标',
                        'entry_footer' => '显示时间，阅读数点赞数等',
                    ),
                    'inline'  => true,
                    'default' => array('category_dot', 'entry_desc', 'entry_footer', 'vip_icon'),
                ),
            ),
        ),
        

    ),
));


CSF::createSection($prefix, array(
    'parent' => 'style_fields',
    'title'  => '文章内容页布局',
    'fields' => array(

        array(
            'id'      => 'single_style',
            'type'    => 'image_select',
            'title'   => '内容页展示风格',
            'desc'    => '图三下载商品顶部布局风格启用后，带有下载类文章自动展示顶部购买区域，普通文章不显示。',
            'options' => array(
                'general' => $template_dir . '/assets/img/options/single-style-general.png',
                'hero' => $template_dir . '/assets/img/options/single-style-hero.png',
                // 'shoptop' => $template_dir . '/assets/img/options/single-style-shoptop.png',
                'shop' => $template_dir . '/assets/img/options/single-style-shoptop.png',
            ),
            'default' => 'hero',
        ),


        array(
            'id'      => 'is_single_new_style',
            'type'    => 'switcher',
            'title'   => '文章内容新布局',
            'desc'    => '开启后，新布局可以设置TAB切换按钮，常见问题，评论等，无需线下滑动界面，直观方便，建议开启',
            'default' => true,
        ),


        array(
            'id'         => 'single_new_style_tab_helps',
            'type'       => 'repeater',
            'title'      => '文章内页常见问题配置',
            'fields'     => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题',
                    'default' => '问题标题',
                ),
                array(
                    'id'       => 'desc',
                    'type'     => 'textarea',
                    'title'    => '描述内容',
                    'sanitize' => false,
                    'default'  => '这里是问题描述内容',
                ),
            ),
            'default'    => [
                ['title' => '免费下载或者VIP会员资源能否直接商用？', 'desc' => '本站所有资源版权均属于原作者所有，这里所提供资源均只能用于参考学习用，请勿直接商用。若由于商用引起版权纠纷，一切责任均由使用者承担。更多说明请参考 VIP介绍。'],
                ['title' => '提示下载完但解压或打开不了？', 'desc' => '最常见的情况是下载不完整: 可对比下载完压缩包的与网盘上的容量，若小于网盘提示的容量则是这个原因。这是浏览器下载的bug，建议用百度网盘软件或迅雷下载。 若排除这种情况，可在对应资源底部留言，或联络我们。'],
                ['title' => '找不到素材资源介绍文章里的示例图片？', 'desc' => '对于会员专享、整站源码、程序插件、网站模板、网页模版等类型的素材，文章内用于介绍的图片通常并不包含在对应可供下载素材包内。这些相关商业图片需另外购买，且本站不负责(也没有办法)找到出处。 同样地一些字体文件也是这种情况，但部分素材会在素材包内有一份字体下载链接清单。'],
                ['title' => '付款后无法显示下载地址或者无法查看内容？', 'desc' => '如果您已经成功付款但是网站没有弹出成功提示，请联系站长提供付款信息为您处理'],
                ['title' => '购买该资源后，可以退款吗？', 'desc' => '源码素材属于虚拟商品，具有可复制性，可传播性，一旦授予，不接受任何形式的退款、换货要求。请您在购买获取之前确认好 是您所需要的资源'],
            ],
            'dependency' => array('is_single_new_style', '==', 'true'),
        ),


        array(
            'id'      => 'single_top_breadcrumb',
            'type'    => 'switcher',
            'title'   => '文章页面包屑导航',
            'desc'    => '',
            'default' => false,
        ),

        array(
            'id'      => 'single_top_title_meta',
            'type'    => 'checkbox',
            'title'   => '文章内容顶部显示小组件',
            'options' => array(
                'date'  => '显示日期时间',
                'cat'   => '显示分类',
                'views' => '显示阅读量',
                'likes' => '显示点赞数',
                'fav'   => '显示收藏数',
                'comment'   => '显示评论数',
            ),
            'inline'  => true,
            'default' => array('date', 'cat', 'views', 'likes', 'fav','comment'),
        ),


        array(
            'id'      => 'single_media_preview',
            'type'    => 'switcher',
            'title'   => '文章内容顶部显示媒体预览播放器',
            'desc'    => '默认开启，当文章有视频或音频缩略图时，点开文章内页自动加载播放器',
            'default' => true,
        ),


        array(
            'id'      => 'site_post_content_nav',
            'type'    => 'switcher',
            'title'   => '文章内容侧边栏H目录导航',
            'desc'    => '开启后，自动根据文章内容中的H1、2、3标题生成文章目录，点击可以快速滑动到内容',
            'default' => false,
        ),

        array(
            'id'      => 'single_bottom_copyright',
            'type'    => 'textarea',
            'title'   => '文章内容底部版权信息',
            'sanitize'   => false,
            'desc'    => '不填写则不显示',
            'default' => '声明：本站所有文章，如无特殊说明或标注，均为本站原创发布。任何个人或组织，在未征得本站同意时，禁止复制、盗用、采集、发布本站内容到任何网站、书籍等各类媒体平台。如若本站内容侵犯了原著者的合法权益，可联系我们进行处理。',
        ),

        array(
            'id'      => 'single_bottom_tags',
            'type'    => 'switcher',
            'title'   => '文章内容底部本文标签',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'single_bottom_author',
            'type'    => 'switcher',
            'title'   => '文章内容底部显示本文作者信息',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'single_bottom_action_btn',
            'type'    => 'checkbox',
            'title'   => '文章内容底部功能按钮',
            'options' => array(
                'share' => '分享按钮',
                'fav'   => '收藏按钮',
                'like'  => '点赞按钮',
            ),
            'inline'  => true,
            'default' => array('share', 'fav', 'like'),
        ),

        array(
            'id'      => 'is_single_bottom_navigation',
            'type'    => 'switcher',
            'title'   => '文章页底部上下篇翻页导航',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'single_bottom_related_post_num',
            'type'    => 'number',
            'title'   => '文章底部展示相关文章数量',
            'desc'    => '填写0则关闭，启用后根据当前文章的标签，分类，获取相关文章，如果没有相关文章则不显示',
            'default' => '4',
        ),

    ),
));


CSF::createSection($prefix, array(
    'parent' => 'style_fields',
    'title'  => '网站底部设置',
    'fields' => array(

        //手机端底部菜单
        array(
            'id'      => 'site_moblie_footer_menu',
            'type'    => 'group',
            'title'   => '手机端底部菜单栏',
            'max'     => '5',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '菜单名称',
                    'default' => '首页',
                ),
                array(
                    'id'    => 'icon',
                    'type'  => 'icon',
                    'title' => '图标',
                ),
                array(
                    'id'      => 'is_blank',
                    'type'    => 'switcher',
                    'title'   => '新窗口打开',
                    'default' => false,
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'desc'    => '比如用户中心，填写' . home_url('/user'),
                    'default' => home_url(),
                ),

            ),
            'default' => array(
                array(
                    'title' => '首页',
                    'icon'  => 'fas fa-home',
                    'href'  => home_url(),
                    'is_blank'  => false,
                ),
                array(
                    'title' => '分类',
                    'icon'  => 'fas fa-layer-group',
                    'href'  => home_url('/tags'),
                    'is_blank'  => false,
                ),
                array(
                    'title' => '会员',
                    'icon'  => 'far fa-gem',
                    'href'  => home_url('/vip-prices'),
                    'is_blank'  => false,
                ),
                array(
                    'title' => '我的',
                    'icon'  => 'fas fa-user',
                    'href'  => home_url('/user'),
                    'is_blank'  => false,
                ),
            ),
        ),
        // rollbar
        array(
            'id'      => 'site_footer_rollbar',
            'type'    => 'group',
            'title'   => 'PC端全站右下角菜单（返回顶部+）',
            'max'     => '10',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '菜单名称',
                    'default' => '首页',
                ),
                array(
                    'id'    => 'icon',
                    'type'  => 'icon',
                    'title' => '图标',
                    'default' => 'fas fa-bars',
                ),
                array(
                    'id'      => 'is_blank',
                    'type'    => 'switcher',
                    'title'   => '新窗口打开',
                    'default' => true,
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'desc'    => '比如用户中心，填写' . home_url('/user'),
                    'default' => home_url(),
                ),

            ),
            'default' => array(
                array(
                    'title' => '首页',
                    'icon'  => 'fas fa-home',
                    'href'  => home_url('/'),
                ),
                array(
                    'title' => '用户中心',
                    'icon'  => 'far fa-user',
                    'href'  => home_url('/user'),
                ),
                array(
                    'title' => '会员介绍',
                    'icon'  => 'fa fa-diamond',
                    'href'  => home_url('/vip-prices'),
                ),
                array(
                    'title' => 'QQ客服',
                    'icon'  => 'fab fa-qq',
                    'href'  => 'http://wpa.qq.com/msgrd?v=3&uin=6666666&site=qq&menu=yes',
                ),
                array(
                    'title' => '购买主题',
                    'icon'  => 'fab fa-shopware',
                    'href'  => 'https://ritheme.com/',
                ),
            ),
        ),

        array(
            'id'      => 'is_site_footer_widget',
            'type'    => 'switcher',
            'title'   => '是否启用网站高级底部',
            'desc'    => '',
            'default' => true,
        ),


        array(
            'id'         => 'site_footer_desc',
            'type'       => 'textarea',
            'sanitize'   => false,
            'title'      => '底部LOGO下文字介绍',
            'subtitle'   => '自定义文字介绍',
            'default'    => _cao_old('site_footer_desc','RiPro-V5是一款强大的Wordpress资源商城主题，支持付费下载、付费播放音视频、付费查看等众多功能。'),
            'dependency' => array('is_site_footer_widget', '==', 'true'),
        ),

        array(
            'id'      => 'site_footer_widget_link1',
            'type'    => 'group',
            'title'   => '底部快速导航链接',
            'max'     => '5',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '链接名称',
                    'default' => '链接',
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'desc'    => '',
                    'default' => '#',
                ),
            ),
            'default' => array(
                array(
                    'title' => '个人中心',
                    'href'  => home_url('/user'),
                ),
                array(
                    'title' => '标签云',
                    'href'  => home_url('/tags'),
                ),
                array(
                    'title' => '网址导航',
                    'href'  => home_url('/links'),
                ),
            ),
            'dependency' => array('is_site_footer_widget', '==', 'true'),
        ),
        array(
            'id'      => 'site_footer_widget_link2',
            'type'    => 'group',
            'title'   => '底部关于本站链接',
            'max'     => '5',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '链接名称',
                    'default' => '链接',
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'desc'    => '',
                    'default' => '#',
                ),
            ),
            'default' => array(
                array(
                    'title' => 'VIP介绍',
                    'href'  => home_url('/vip-prices'),
                ),
                array(
                    'title' => '客服咨询',
                    'href'  => home_url('/user/ticket'),
                ),
                array(
                    'title' => '推广计划',
                    'href'  => home_url('/user/aff'),
                ),
            ),
            'dependency' => array('is_site_footer_widget', '==', 'true'),
        ),

        array(
            'id'       => 'site_contact_desc',
            'type'     => 'textarea',
            'title'    => '底部联系我们介绍',
            'sanitize' => false,
            'default'  => '<img width="80" height="80" src="'.get_template_directory_uri() . '/assets/img/ritheme-qr.png'.'" style="float: left;" title="二维码"><img width="80" height="80" src="'.get_template_directory_uri() . '/assets/img/ritheme-qr.png'.'" style="float: left;" title="二维码">如有BUG或建议可与我们在线联系或登录本站账号进入个人中心提交工单。',
            'dependency' => array('is_site_footer_widget', '==', 'true'),
        ),

        
        array(
            'id'       => 'site_copyright_text',
            'type'     => 'textarea',
            'title'    => '全站底部版权信息',
            'sanitize' => false,
            'subtitle' => '自定义版权信息',
            'default'  => _cao_old('site_copyright_text', 'Copyright © 2023 <a target="_blank" href="http://ritheme.com/">RiPro-V5 Theme</a> - All rights reserved'),
        ),

        array(
            'id'       => 'site_ipc_text',
            'type'     => 'textarea',
            'sanitize' => false,
            'title'    => '网站备案链接',
            'subtitle' => '',
            'default'  => _cao_old('site_ipc_text', '<a href="https://beian.miit.gov.cn" target="_blank" rel="noreferrer nofollow">京ICP备0000000号-1</a>'),
        ),

        array(
            'id'       => 'site_ipc2_text',
            'type'     => 'textarea',
            'sanitize' => false,
            'title'    => '网站公安备案链接',
            'subtitle' => '',
            'default'  => _cao_old('site_ipc2_text', '<a href="#" target="_blank" rel="noreferrer nofollow">京公网安备 00000000</a>'),
        ),

        array(
            'id'      => 'site_footer_links',
            'type'    => 'group',
            'title'   => '底部友情链接(仅首页显示)',
            'max'     => '20',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '链接名称',
                    'default' => '链接',
                ),
                array(
                    'id'      => 'href',
                    'type'    => 'text',
                    'title'   => '链接地址',
                    'desc'    => '',
                    'default' => '#',
                ),

            ),
            'default' => array(
                array(
                    'title' => 'RiTheme主题官网',
                    'href'  => 'https://ritheme.com/',
                ),
                array(
                    'title' => '日主题官网',
                    'href'  => 'https://ritheme.com/',
                ),
                array(
                    'title' => 'RiPro-V5主题官方',
                    'href'  => 'https://ritheme.com/',
                ),
                array(
                    'title' => '服务器推荐',
                    'href'  => '/goto?url=https://www.aliyun.com/minisite/goods?userCode=u4kxbrjo',
                ),
                array(
                    'title' => '免备案服务器',
                    'href'  => '/goto?url=https://www.yisu.com/reg/?partner=2OPSd',
                ),
            ),
        ),
        
        
    ),
));

CSF::createSection($prefix, array(
    'title'  => '高级筛选',
    'fields' => array(

        array(
            'id'      => 'is_site_term_filter',
            'type'    => 'switcher',
            'title'   => '分类页高级筛选功能',
            'desc'    => '总开关，开启后在文章分类内页顶部显示高级筛选组件，关闭则不显示',
            'default' => true,
        ),

        array(
            'id'                     => 'site_term_filter_config',
            'type'                   => 'group',
            'title'                  => '高级筛选显示设置',
            'subtitle'               => '控制筛选功能在某个分类中显示哪些按钮，不配置则不显示',
            'accordion_title_number' => true,
            'fields'                 => array(
                array(
                    'id'         => 'cat_id',
                    'type'       => 'select',
                    'title'      => '关联分类',
                    'desc'       => '配置此分类页面下显示的筛选条目',
                    'options'    => 'categories',
                    'inline'     => true,
                    'query_args' => array(
                        'orderby' => 'count',
                        'order'   => 'DESC',
                        'parent'  => 0,
                    ),
                ),

                array(
                    'id'          => 'top_cats',
                    'type'        => 'checkbox',
                    'title'       => '主分类筛选显示',
                    'desc'        => '排序规则以设置的顺序为准',
                    'placeholder' => '选择分类',
                    'inline'      => true,
                    'multiple'    => true,
                    'options'     => 'categories',
                    'query_args'  => array(
                        'orderby' => 'count',
                        'order'   => 'DESC',
                        'parent'  => 0,
                    ),
                ),
                array(
                    'id'      => 'is_child_cat',
                    'type'    => 'checkbox',
                    'title'   => '子分类筛选',
                    'label'   => '是否显示子分类筛选条目（支持无限子分类）',
                    'default' => true,
                ),

                array(
                    'id'         => 'child_cat_orderby',
                    'type'       => 'radio',
                    'title'      => '子分类筛选排序规则',
                    'inline'     => true,
                    'options'    => array(
                        'none' => '不排序',
                        'id'    => '分类ID',
                        'name'  => '分类名称',
                        'slug'  => '分类别名',
                        'count' => '文章数量',
                        // 'include' => '自定义ID排序',
                    ),
                    'default'    => 'none',
                ),

                // array(
                //     'id'      => 'orderby_include',
                //     'type'    => 'text',
                //     'title'   => '子分类自定义排序ID',
                //     'desc' => '填写要排序的分类ID，用英文逗号隔开，例如： 1,3,55,25',
                //     'default' => '',
                //     'dependency' => array('child_cat_orderby', '==', 'include'),
                // ),

                array(
                    'id'         => 'child_cat_order',
                    'type'       => 'radio',
                    'title'      => '子分类筛选排序方式',
                    'inline'     => true,
                    'options'    => array(
                        'ASC'    => '升序排序',
                        'DESC'  => '降序排序',
                    ),
                    'default'    => 'ASC',
                ),

                

                array(
                    'id'      => 'is_price',
                    'type'    => 'checkbox',
                    'title'   => '价格筛选',
                    'label'   => '是否显示价格筛选条目，可筛选全部文章，免费资源，VIP资源',
                    'default' => true,
                ),
                array(
                    'id'      => 'custom_taxonomy',
                    'type'    => 'checkbox',
                    'title'   => '自定义筛选字段',
                    'inline'  => true,
                    'options' => _get_custom_taxonomy_option(),
                    'desc'    => '显示哪些自定义字段筛选条目',
                    'default' => '',
                ),
            ),
        ),

        array(
            'id'                     => 'site_custom_taxonomy',
            'type'                   => 'group',
            'title'                  => '高级自定义筛选字段配置',
            'subtitle'               => '（按需选用）采用WP原生高效率的高级定义分类法开发，在编辑发布文章得时候按需选择，一般用于复杂的网站内容，大部分网站不需要配置此功能',
            'accordion_title_number' => true,
            'fields'                 => array(
                array(
                    'id'      => 'name',
                    'type'    => 'text',
                    'title'   => '筛选名称',
                    'desc'    => '例如：格式，颜色，大小，尺寸，设置后中途可修改名称',
                    'default' => '颜色',
                ),
                array(
                    'id'      => 'taxonomy',
                    'type'    => 'text',
                    'title'   => '分类字段唯一标识',
                    'desc'    => '必须是纯英文，例如geshhi，yanse，szie，color，<b style="color:red;">设置后如果已经有文章关联选中，则不可以中途修改此标识</b>',
                    'default' => 'yanse',
                ),
                // array(
                //     'id'    => 'slug',
                //     'type'  => 'text',
                //     'title' => '链接别名',
                //     'desc'   => 'URL链接中显示的别名',
                // ),
                array(
                    'id'      => 'type',
                    'type'    => 'radio',
                    'title'   => '选项类型',
                    'inline'  => true,
                    'options' => array(
                        'simple'   => '多选',
                        'dropdown' => '下拉单选',
                        'radio'    => '单选',
                    ),
                    'desc'    => '后台发布文章时选项类型',
                    'default' => 'simple',
                ),
            ),
        ),

    ),
));

CSF::createSection($prefix, array(
    'title'  => '高级搜索',
    'fields' => array(

        array(
            'id'      => 'remove_site_search',
            'type'    => 'switcher',
            'title'   => '关闭网站全站搜索功能（注意开关）',
            'desc'    => '禁用后全站前台无法搜索文章，可以有效防止爆破，数据库堵塞',
            'default' => false,
        ),

        array(
            'id'      => 'is_site_pro_search_title',
            'type'    => 'switcher',
            'title'   => '优化全站搜索只搜文章标题',
            'desc'    => '开启后，网站搜索关键词时，只根据文章标题进行搜索查询，在文章数量较多得时候，搜索性能提升巨大，按需开启',
            'default' => false,
        ),


        array(
            'id'      => 'is_site_pro_search',
            'type'    => 'switcher',
            'title'   => '搜索框支持按分类选择搜索',
            'desc'    => '默认开启，关闭后不显示分类下拉选择选项',
            'default' => true,
        ),

        array(
            'id'      => 'pro_search_select_depth',
            'type'    => 'radio',
            'inline'  => true,
            'title'   => '搜索框中分类展示层级深度',
            'options' => array(
                '1' => '只显示1级主分类',
                '2' => '扩展到2级子分类',
                '3' => '扩展到3级子分类',
            ),
            'default' => '1',
        ),

        array(
            'id'      => 'pro_search_select_order',
            'type'    => 'radio',
            'inline'  => true,
            'title'   => '搜索框中分类选择排序方式',
            'options' => array(
                'id'    => '分类ID',
                'name'  => '分类名称',
                'slug'  => '分类别名',
                'count' => '分类文章总数',
            ),
            'default' => 'id',
        ),

    ),
));



CSF::createSection($prefix, array(
    'title'  => '登录注册',
    'fields' => array(


        array(
            'id'      => 'is_site_popup_login',
            'type'    => 'switcher',
            'title'   => '是否弹窗登录注册（新）',
            'desc'    => '开启后，使用弹窗快速登录注册',
            'default' => true,
        ),


        array(
            'id'          => 'site_loginpage_bg_type',
            'type'        => 'radio',
            'inline'      => true,
            'title'       => '登录注册页面-背景效果',
            'desc'        => '',
            'options'     => array(
                'img'      => '图片',
                'waves'   => '动态方块',
                'clouds'   => '动态天空',
                'net'   => '动态线条',
                'halo'   => '动态流光',
            ),
            'default'     => 'img',
        ),

        array(
            'id'      => 'site_loginpage_bg_img',
            'type'    => 'upload',
            'title'   => '登录注册页面-背景图片',
            'default' => get_template_directory_uri() . '/assets/img/bg.jpg',
            'dependency' => array('site_loginpage_bg_type', '==', 'img'),
        ),

        array(
            'id'      => 'site_loginpage_color',
            'type'    => 'color_group',
            'title'   => '登录注册页面-效果器颜色',
            'options' => array(
                'bgcolor' => '背景颜色',
                'color'     => '粒子颜色',
            ),
            'default' => array('bgcolor'=>'#005588','color'=>'#ededed'),
            'dependency' => array('site_loginpage_bg_type', '!=', 'img'),
        ),

        
        array(
            'id'      => 'site_user_agreement_href',
            'type'    => 'text',
            'title'   => '网站用户协议页面地址',
            'default' => '#',
        ),
        array(
            'id'      => 'site_privacy_href',
            'type'    => 'text',
            'title'   => '网站隐私政策页面地址',
            'default' => '#',
        ),


        array(
            'id'      => 'is_site_img_captcha',
            'type'    => 'switcher',
            'title'   => '注册登录图片验证码',
            'label'   => '开启后，注册登录需要填写图片验证码，防止恶意注册和扫描',
            'default' => false,
        ),

        array(
            'id'      => 'is_site_mail_captcha',
            'type'    => 'switcher',
            'title'   => '注册需要邮箱验证码',
            'label'   => '开启后，注册需要填写邮箱验证码，需配置好网站smtp发信服务，防止恶意注册和扫描',
            'default' => false,
        ),


        array(
            'id'      => 'is_site_user_login',
            'type'    => 'switcher',
            'title'   => '登录模块',
            'desc'    => '网站登录功能总开关',
            'default' => true,
        ),

        array(
            'id'      => 'is_site_user_register',
            'type'    => 'switcher',
            'desc'    => '网站注册功能总开关，为了确保注册只走前台通道，请您在wp后台设置-常规-成员资格中，取消勾选任何人都可以注册选项，关闭wp自带的注册功能防止用户而已注册。',
            'title'   => '注册模块',
            'default' => true,
        ),

        array(
            'id'         => 'is_site_invitecode_register',
            'type'       => 'switcher',
            'title'      => '仅允许邀请码注册',
            'desc'       => '开启此功能后，新注册用户必须使用邀请码注册，否则无法注册，邀请码可在后台运营管理-卡卷管理中生成注册邀请码，支持批量生成，并且每个邀请码支持设置过期时间到期后自动失效',
            'default'    => false,
            'dependency' => array('is_site_user_register', '==', 'true'),
        ),

        array(
            'id'         => 'site_invitecode_get_url',
            'type'       => 'text',
            'title'      => '邀请码获取地址',
            'desc'       => '获取注册邀请码获取地址，例如发卡地址或者某个页面或者文章发布了一些注册邀请码，用户打开后自行复制或者购买',
            'default'    => '#',
            'dependency' => array('is_site_invitecode_register', '==', 'true'),
        ),

        array(
            'id'      => 'is_sns_qq',
            'type'    => 'switcher',
            'title'   => 'QQ登录',
            'label'   => '申请地址： QQ互联官网 https://connect.qq.com/',
            'default' => _cao_old('is_sns_qq', false),
        ),
        array(
            'id'         => 'sns_qq',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => 'Appid',
                    'default' => _cao_old('sns_qq:app_id', ''),
                ),
                array(
                    'id'      => 'app_secret',
                    'type'    => 'text',
                    'title'   => 'Appkey',
                    'default' => _cao_old('sns_qq:app_secret', ''),
                ),
                array(
                    'type'    => 'subheading',
                    'content' => '回调地址填写：' . esc_url(home_url('/oauth/qq/callback')),
                ),
            ),
            'dependency' => array('is_sns_qq', '==', 'true'),
        ),
        array(
            'id'      => 'is_sns_weixin',
            'type'    => 'switcher',
            'title'   => '微信登录',
            'label'   => '申请地址： 微信开放平台官网 https://open.weixin.qq.com/，2021年12月27日之后，微信公众号模式官方不再输出头像、昵称信息，所以公众号登录模式意义不大，所以暂时砍掉，特别说明，建议不要使用微信登录，要掏认证费。网站只需要一个QQ登录一般足够',
            'default' => _cao_old('is_sns_weixin', false),
        ),
        array(
            'id'         => 'sns_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(

                // 微信登陆模式
                // array(
                //     'id'          => 'sns_weixin_mod',
                //     'type'        => 'select',
                //     'title'       => '微信登陆模式',
                //     'placeholder' => '',
                //     'options'     => array(
                //         'open' => '微信开放平台',
                //         'mp'   => '微信公众号（认证服务号）',
                //     ),
                //     'default'     => 'mp',
                //     'desc'        => '推荐使用公众号模式，因微信官方openid和unionid模式错综复杂，建议不要中途更换模式',
                // ),

                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => '开放平台 Appid',
                    'default' => _cao_old('sns_weixin:app_id', ''),
                    // 'dependency' => array('sns_weixin_mod', '==', 'open'),
                ),
                array(
                    'id'      => 'app_secret',
                    'type'    => 'text',
                    'title'   => '开放平台 AppSecret',
                    'default' => _cao_old('sns_weixin:app_secret', ''),
                    // 'dependency' => array('sns_weixin_mod', '==', 'open'),
                ),

                array(
                    'type'    => 'subheading',
                    'content' => '配置说明：微信开放平台-授权回调域填写：' . parse_url(home_url(), PHP_URL_HOST) . '</br> 本接口为微信开放平台接口模式，不支持公众号接入，仅适合PC端',
                    // 'dependency' => array('sns_weixin_mod', '==', 'open'),
                ),

                // array(
                //     'id'         => 'mp_app_id',
                //     'type'       => 'text',
                //     'title'      => '公众号 Appid',
                //     'default' => _cao_old('sns_weixin:mp_app_id',''),
                //     // 'dependency' => array('sns_weixin_mod', '==', 'mp'),
                // ),
                // array(
                //     'id'         => 'mp_app_secret',
                //     'type'       => 'text',
                //     'title'      => '公众号 AppSecret',
                //     'default' => _cao_old('sns_weixin:mp_app_secret',''),
                //     // 'dependency' => array('sns_weixin_mod', '==', 'mp'),
                // ),
                // array(
                //     'id'         => 'mp_app_token',
                //     'type'       => 'text',
                //     'title'      => '公众号配置 token',
                //     'default' => _cao_old('sns_weixin:mp_app_token',''),
                //     'desc'       => '自定义一个随机字符串作为token通信密码，与-公众号后台->基本配置->服务器配置->令牌(Token)，保持一致即可',
                //     // 'dependency' => array('sns_weixin_mod', '==', 'mp'),
                // ),



            ),
            'dependency' => array('is_sns_weixin', '==', 'true'),
        ),

    ),
));

CSF::createSection($prefix, array(
    'id'    => 'shop_fields',
    'title' => '商城设置',
));

// 商城-基本设置
CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '商城基本设置',
    'fields' => array(

        array(
            'id'          => 'site_shop_mod',
            'type'        => 'radio',
            'title'       => '商城模式配置',
            'desc'        => '',
            'placeholder' => '',
            'options'     => array(
                'close'    => '不启用商城功能（网站仅作为博客展示）',
                'all'      => '全能商城（支持游客购买、登录用户购买）',
                'user_mod' => '用户模式（不支持游客购买）',
            ),
            'default'     => 'all',
        ),

        array(
            'id'      => 'is_free_nologin_down',
            'type'    => 'switcher',
            'title'   => '免费资源下载无需登录',
            'desc'   => '开启后，免费资源无需登录可直接下载查看',
            'default' => true,
        ),


        array(
            'id'         => 'site_coin_name',
            'type'       => 'text',
            'title'      => '站内币名称',
            'desc'       => '设置站内币名称，例如：金币、下载币、积分、资源币、BB币、USDT等',
            'default'    => _cao_old('site_mycoin_name', '金币'),
            'attributes' => array(
                'style' => 'width: 100px;',
            ),
        ),

        array(
            'id'         => 'site_coin_rate',
            'type'       => 'text',
            'title'      => '站内币充值比例',
            'default'    => _cao_old('site_mycoin_rate', '10'),
            'desc'       => '默认：1元等于10个站内币(必须是正整数1~10000，建议一次设置好，后续谨慎更改，会影响后台订单的汇率)',
            'attributes' => array(
                'style' => 'width: 100px;',
            ),
        ),

        array(
            'id'      => 'site_coin_icon',
            'type'    => 'icon',
            'title'   => '站内币图标',
            'desc'    => '设置站内币图标，部分页面展示需要',
            'default' => _cao_old('site_mycoin_icon', 'fas fa-coins'),
        ),

        array(
            'id'         => 'site_coin_pay_minnum',
            'type'       => 'text',
            'title'      => '站内币最小充值数量限制',
            'default'    => _cao_old('site_mycoin_pay_minnum', '1'),
            'desc'       => '',
            'attributes' => array(
                'style' => 'width: 100px;',
            ),
        ),
        array(
            'id'         => 'site_coin_pay_maxnum',
            'type'       => 'text',
            'title'      => '站内币最大充值数量限制',
            'default'    => _cao_old('site_mycoin_pay_maxnum', '9999'),
            'desc'       => '',
            'attributes' => array(
                'style' => 'width: 100px;',
            ),
        ),
        array(
            'id'      => 'site_mycoin_pay_arr',
            'type'    => 'text',
            'title'   => '站内币充值套餐设置',
            'desc'    => '设置充值套餐，用英文逗号隔开，“,”',
            'default' => '1,10,50,100,300,500,1000,5000',
        ),
        array(
            'id'      => 'site_mycoin_pay_desc',
            'type'    => 'textarea',
            'title'   => '站内币充值说明',
            'desc'   => '每行一个，用于前台展示',
            'default' => '充值最低额度为1金币'.PHP_EOL.'充值汇率为1元=10金币'.PHP_EOL.'人民币和金币不能互相转换'.PHP_EOL.'余额永久有效，无时间限制',
        ),

        array(
            'id'      => 'is_site_qiandao',
            'type'    => 'switcher',
            'title'   => '每日签到功能',
            'desc'   => '启用后在前台个人中心我的余额界面右上角可以点击签到领取奖励',
            'default' => false,
        ),

        array(
            'id'         => 'site_qiandao_coin_num',
            'type'       => 'text',
            'title'      => '每日签到赠送'._cao('site_coin_name').'数量',
            'desc'       => '填写单个数字0.5表示固定赠送0.5，签到赠送的站内币直接到账用户的钱包余额',
            'default'    => _cao_old('site_qiandao_coin_num','0.5'),
            'dependency' => array('is_site_qiandao', '==', 'true'),
        ),

        array(
            'id'      => 'site_shop_name_txt',
            'type'    => 'text',
            'title'   => '自定义全站订单名称',
            'desc'    => '购买资源时在支付平台显示的商品名称，例如自助购买，自助充值，防止，敏感词汇风控,字数不要超过8个，防止微信支付报错',
            'default' => '商城自助购买',
        ),

    ),
));

// 会员组配置

CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'id'     => 'vip_fields',
    'title'  => 'VIP会员配置',
    'fields' => array(

        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<b>请注意：</b><p>1，本会员组涉及逻辑为。只有普通用户，vip用户，永久用户三种权限，对应资源权限有普通原价购买，vip用户折扣或者免费，永久vip免费，其中可以自定义会员开通套餐时长</p><p>2，注意开通套餐仅仅是购买会员开通，不涉及权限控制，ripro会员组一直都是会员非会员两种，如果要多会员组而不是多套餐开通，请用riplus或者rimini能弄几十万个会员组，根据自己网站运营思路设计网站VIP会员售价</p><p>3，会员组主要控制你网站用户不同级别的叫法名称白标识和权限次数</p><p>4，会员开通套餐配置用于前台个人中心和vip开通页面购买套餐选择，不涉及上面的会员组配置任何权限控制。请不要混淆，更不要当成自定义权限，这只是套餐，套餐，套餐，用于前台开通购买显示。</p>',
        ),

        array(
            'id'      => 'is_pay_vip_allow_oline',
            'type'    => 'switcher',
            'title'   => '仅限在线支付购买开通VIP',
            'desc'   => '开启后，站内币不允许支付购买vip，必须在线支付',
            'default' => false,
        ),

        array(
            'id'    => 'site_vip_options',
            'type'  => 'tabbed',
            'title' => '会员组设置',
            'desc'   => '会员组主要控制你网站用户不同级别的叫法名称白标识和权限次数',
            'tabs'  => array(
                array(
                    'id'     => 'no',
                    'title'  => '默认普通用户',
                    'icon'   => 'fa fa-circle',
                    'fields' => array(

                        array(
                            'id'      => 'no_name',
                            'type'    => 'text',
                            'title'   => '名称',
                            'default' => _cao_old('site_vip_options:nov_name', '普通用户'),
                        ),

                        array(
                            'id'      => 'no_downnum',
                            'type'    => 'text',
                            'title'   => '每日可下载次数',
                            'default' => _cao_old('site_vip_options:nov_downnum', '5'),
                        ),

                        array(
                            'id'      => 'no_desc',
                            'type'    => 'textarea',
                            'title'   => '特权介绍',
                            'desc'   => '每行一个，用于前台展示',
                            'default' => '下载本站免费资源'.PHP_EOL.'每日可下载5个免费资源'.PHP_EOL.'5×8小时在线人工客服'.PHP_EOL.'全站无限制收藏次数',
                        ),

                    ),
                ),
                array(
                    'id'     => 'vip',
                    'title'  => '会员用户',
                    'icon'   => 'fa fa-circle',
                    'fields' => array(
                        array(
                            'id'      => 'vip_name',
                            'type'    => 'text',
                            'title'   => '名称',
                            'default' => _cao_old('site_vip_options:vip_name', 'VIP会员'),
                        ),
                        array(
                            'id'      => 'vip_downnum',
                            'type'    => 'text',
                            'title'   => '每日可下载次数',
                            'default' => _cao_old('site_vip_options:vip_downnum', '10'),
                        ),
                        array(
                            'id'      => 'vip_desc',
                            'type'    => 'textarea',
                            'title'   => '特权介绍',
                            'desc'   => '每行一个，用于前台展示',
                            'default' => '可获取专属免费资源'.PHP_EOL.'每日可下载10个免费资源'.PHP_EOL.'5×8小时在线人工客服'.PHP_EOL.'全站无限制收藏次数',
                        ),
                    ),
                ),
                array(
                    'id'     => 'boosvip',
                    'title'  => '永久会员用户',
                    'icon'   => 'fa fa-circle',
                    'fields' => array(
                        array(
                            'id'      => 'boosvip_name',
                            'type'    => 'text',
                            'title'   => '名称',
                            'default' => _cao_old('site_vip_options:boosvip_name', '永久会员'),
                        ),
                        array(
                            'id'      => 'boosvip_downnum',
                            'type'    => 'text',
                            'title'   => '每日可下载次数',
                            'default' => _cao_old('site_vip_options:boosvip_downnum', '99'),
                        ),
                        array(
                            'id'      => 'boosvip_desc',
                            'type'    => 'textarea',
                            'title'   => '特权介绍',
                            'desc'   => '每行一个，用于前台展示',
                            'default' => '可获取专属免费资源'.PHP_EOL.'每日可下载99个免费资源'.PHP_EOL.'5×8小时在线人工客服'.PHP_EOL.'全站无限制收藏次数',
                        ),

                    ),
                ),
            ),
        ),

        

        array(
            'id'      => 'site_vip_buy_options',
            'type'    => 'group',
            'title'   => '会员开通套餐配置',
            'desc'   => '会员开通套餐配置用于前台个人中心和vip开通页面购买套餐选择，不涉及上面的会员组配置任何权限控制。请不要混淆，更不要当成自定义权限，这只是套餐，套餐，套餐，用于前台开通购买显示。',
            'fields'  => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'default' => '会员',
                    'desc'    => '比如包月会员',
                    'title'   => '套餐名称',
                ),
                array(
                    'id'      => 'daynum',
                    'type'    => 'text',
                    'default' => '30',
                    'desc'    => '比如你想设置一个套餐是月费，则填写30，如果要设置终身会员套餐，填写：9999',
                    'title'   => '开通天数',
                ),
                array(
                    'id'      => 'price',
                    'type'    => 'text',
                    'default' => '20',
                    'desc'    => '此套餐所需的站内币价格',
                    'title'   => '套餐价格/'._cao('site_coin_name','金币'),
                ),
            ),
            'default' => array(
                array(
                    'title'  => '体验会员',
                    'daynum' => '1',
                    'price'  => '10',
                ),
                array(
                    'title'  => '包月会员',
                    'daynum' => '30',
                    'price'  => '300',
                ),
                array(
                    'title'  => '永久会员',
                    'daynum' => '9999',
                    'price'  => '3000',
                ),
            ),
        ),

        array(
            'id'      => 'site_buyvip_desc',
            'type'    => 'repeater',
            'title'   => '会员开通协议说明',
            'fields'  => array(
                array(
                    'id'       => 'content',
                    'type'     => 'text',
                    'default'  => '',
                    'sanitize' => false,
                ),
            ),
            'default' => array(
                array('content' => '指会员所享有根据选择购买的会员选项所享有的特殊服务，具体以本站公布的服务内容为准。'),
                array('content' => '在遵守VIP会员协议前提下，VIP会员在会员有效期内可以享受免费或折扣权限购买获取资源。'),
                array('content' => 'VIP会员属于虚拟服务，购买后不能够申请退款。如付款前有任何疑问，联系站长处理'),
                array('content' => '本站所有资源，针对不同等级VIP会员可直接下载，特殊资源商品会注明是否免费'),
            ),
        ),

    ),
));

CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '网站推广设置',
    'fields' => array(


        array(
            'id'      => 'is_site_author_aff',
            'type'    => 'switcher',
            'title'   => '投稿作者佣金奖励系统',
            'desc'    => '关闭后网站不给投稿者卖出资源分佣',
            'default' => true,
        ),

        array(
            'id'      => 'site_author_aff_ratio',
            'type'    => 'text',
            'title'   => '投稿作者佣金比例',
            'desc'    => '投稿作者的文章被其他用户购买时，给作者的分佣比例，计算后直接发放个人佣金，0为关闭，0.8为百分之80',
            'default' => '0.8',
        ),

        array(
            'id'      => 'is_site_aff',
            'type'    => 'switcher',
            'title'   => '会员推广奖励系统',
            'desc'    => '关闭后网站不涉及推广奖励功能',
            'default' => true,
        ),

        array(
            'id'      => 'site_aff_ratio',
            'type'    => 'text',
            'title'   => '网站推广佣金比例',
            'desc'    => '通过该会员推广链接购买奖励比例，0为关闭，0.05为百分之5',
            'default' => '0.05',
        ),

        array(
            'id'      => 'site_min_tixin_price',
            'type'    => 'text',
            'title'   => '用户最低提现金额限制',
            'desc'    => '',
            'default' => '10',
        ),

        array(
            'id'      => 'site_tixian_desc',
            'type'    => 'repeater',
            'title'   => '提现说明',
            'fields'  => array(
                array(
                    'id'       => 'content',
                    'type'     => 'text',
                    'default'  => '',
                    'sanitize' => false,
                ),
            ),
            'default' => array(
                array('content' => '申请提现后请联系网站客服，发送您的账号信息和收款码进行人工提现'),
                array('content' => '推广奖励只针对在线支付方式支付成功的订单记录有效'),
                array('content' => '如果用户是通过您的推广链接注册并购买，则成为您得下级用户'),
                array('content' => '如果用户是您推荐的下级，则用户每次购买都会给你发放佣金奖励'),
                array('content' => '如果用户是你的下级，用户使用其他推荐人链接购买，以上下级关系为准，优先给注册推荐人而不是推荐链接'),
                array('content' => '推广奖励金额精确到两位小数点。可提现佣金未达标无法申请提现'),
                array('content' => '前台无法查看推广订单详情，如需查看详情可联系管理员截图查看详细记录和时间'),
            ),
        ),

    ),
));


//卡密购买系统
// CSF::createSection($prefix, array(
//     'parent' => 'shop_fields',
//     'title'  => '卡密发货系统',
//     'fields' => array(
//         array(
//             'id'      => 'is_site_cdk_shop',
//             'type'    => 'switcher',
//             'title'   => '卡密发货系统',
//             'desc'    => '开启后可以销售卡密，自动发货发卡',
//             'default' => false,
//         ),

//         array(
//             'id'      => 'is_pay_cdk_allow_oline',
//             'type'    => 'switcher',
//             'title'   => '仅限在线支付购买卡密',
//             'desc'   => '开启后，站内币不允许支付购买，必须在线支付',
//             'default' => false,
//         ),
//     ),
// ));


// 商城-支付接口配置
CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '支付接口配置',
    'fields' => array(

        array(
            'id'      => 'is_site_coin_pay',
            'type'    => 'switcher',
            'title'   => '站内币-余额支付购买',
            'label'   => '开启后网站支持站内币购买文章和会员',
            'default' => true,
        ),
        array(
            'id'      => 'is_site_cdk_pay',
            'type'    => 'switcher',
            'title'   => '卡密CDK-支付兑换',
            'label'   => '开启后网站支持卡密CDK充值余额和会员',
            'default' => true,
        ),
        array(
            'id'         => 'site_cdk_pay_link',
            'type'       => 'text',
            'title'      => '卡密购买地址',
            'desc'       => '不想用站自己支付的可以用卡密规避风险，自己生产充值卡密去第三方平台发卡，用户购买卡密后回来充值消费。',
            'dependency' => array('is_site_cdk_pay', '==', 'true'),
        ),

        // 支付宝配置
        array(
            'id'      => 'is_alipay',
            'type'    => 'switcher',
            'title'   => '支付宝（官方企业支付-新应用模式）',
            'label'   => '支付宝商户后台推荐签约电脑网站支付，当面付，手机网站支付，配置教程（https://www.kancloud.cn/rizhuti/ritheme/1961638）',
            'default' => _cao_old('is_alipay', true),
        ),
        array(
            'id'         => 'alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(

                array(
                    'id'         => 'appid',
                    'type'       => 'text',
                    'title'      => '开放平台-应用appid',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                    'default'    => _cao_old('alipay:appid', ''),
                ),
                array(
                    'id'      => 'privateKey',
                    'type'    => 'textarea',
                    'title'   => '开放平台-应用私钥',
                    'desc'    => '请注意这里是应用的私钥，就是你用工具生成的应用私钥',
                    'default' => _cao_old('alipay:privateKey', ''),
                ),
                array(
                    'id'      => 'publicKey',
                    'type'    => 'textarea',
                    'title'   => '开放平台-支付宝公钥',
                    'desc'    => '请注意这里是支付宝后台中的公钥，不是你生成的那个应用私钥，如果支付成功后，网站支付状态不刷新或者后台的订单显示未支付，请检查公钥是否支付宝公钥和https证书是否正常，一般更换https证书即可，各大支付平台对ssl证书都有一定的安全性验证，个别有时候无法通知，换一个ssl证书即可',
                    'default' => _cao_old('alipay:publicKey', ''),
                ),

                array(
                    'id'      => 'api_type',
                    'type'    => 'radio',
                    'title'   => '应用接口模式',
                    'inline'  => true,
                    'options' => array(
                        'qr'  => '当面付(需签约当面付产品)',
                        'web' => '电脑网站支付(需签约电脑网站支付产品)',
                    ),
                    'desc'    => '自2021年初开始，支付宝官方风控系统对异地跨地区进行当面付扫码支付或者信用卡以及分期付款的异常用户，容易被风控商户，建议非必要情况下不要使用当面付模式，关闭此项，如果是个人的商户，没有电脑网站支付产品，只能硬刚当面付，没有其他办法。',
                    'default' => 'web',
                ),

                array(
                    'id'      => 'is_mobile',
                    'type'    => 'switcher',
                    'title'   => '手机端自动跳转H5支付',
                    'label'   => '(需签约手机网站支付产品，只支持手机浏览器打开唤醒APP支付，并不能在应用内，如QQ/微信/支付宝内部浏览器无效)',
                    'default' => false,
                ),

            ),
            'dependency' => array('is_alipay', '==', 'true'),
        ),

        // 微信支付配置
        array(
            'id'      => 'is_weixinpay',
            'type'    => 'switcher',
            'title'   => '微信支付（官方企业支付）',
            'label'   => '微信官方商户后台推荐签约native产品，JSAPI产品，h5支付产品',
            'default' => _cao_old('is_weixinpay', false),
        ),
        array(
            'id'         => 'weixinpay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'mch_id',
                    'type'    => 'text',
                    'title'   => '微信支付商户号',
                    'desc'    => '微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送',
                    'default' => _cao_old('weixinpay:mch_id', ''),
                ),
                array(
                    'id'      => 'appid',
                    'type'    => 'text',
                    'title'   => '公众号或小程序APPID',
                    'desc'    => '公众号APPID 通过微信支付商户资料审核后邮件发送,开通jsapi支付和配置公众号手机内直接登录的用户注意,如果是小程序的appid,请到支付商户绑定公众号appid授权,这里填写为公众号即可',
                    'default' => _cao_old('weixinpay:appid', ''),
                ),
                array(
                    'id'         => 'key',
                    'type'       => 'text',
                    'title'      => '微信支付API密钥',
                    'desc'       => '帐户设置-安全设置-API安全-API密钥-设置API密钥',
                    'default'    => _cao_old('weixinpay:key', ''),
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                // array(
                //     'id'      => 'is_jsapi',
                //     'type'    => 'switcher',
                //     'title'   => 'JSAPI支付',
                //     'label'   => '微信端内打开可以直接发起支付，开启此项需要登录注册里开启公众号登录，开启后网站用户在微信内登录后可以直接支付',
                //     'default' => false,
                // ),
                array(
                    'id'      => 'is_mobile',
                    'type'    => 'switcher',
                    'title'   => '手机跳转H5支付',
                    'label'   => '移动端自动自动切换为跳转支付（需开通H5支付，只支持手机浏览器打开唤醒APP支付，并不能在应用内，如QQ/微信/支付宝内部浏览器无效）',
                    'default' => _cao_old('weixinpay:is_mobile', false),
                ),
            ),
            'dependency' => array('is_weixinpay', '==', 'true'),
        ),

        //虎皮椒 weixin
        array(
            'id'      => 'is_hupijiao_weixin',
            'type'    => 'switcher',
            'title'   => '虎皮椒(微信)',
            'label'   => '无需企业资质，个人用户推荐，微信完美收款，无资质可以用此方法完美替代*_*',
            'default' => false,
        ),
        array(
            'id'         => 'hupijiao_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '虎皮椒V3  <a target="_blank" href="https://admin.xunhupay.com/sign-up/4123.html">注册地址</a>',
                ),
                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => 'APPID',
                    'desc'    => 'APPID',
                    'default' => '',
                ),
                array(
                    'id'         => 'app_secret',
                    'type'       => 'text',
                    'title'      => 'APPSECRET',
                    'desc'       => '密钥',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'api_url',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '必填',
                    'default' => '',
                ),

            ),
            'dependency' => array('is_hupijiao_weixin', '==', 'true'),
        ),

        //虎皮椒 alpay
        array(
            'id'      => 'is_hupijiao_alipay',
            'type'    => 'switcher',
            'title'   => '虎皮椒(支付宝)',
            'label'   => '稳定第三方服务商渠道',
            'default' => false,
        ),
        array(
            'id'         => 'hupijiao_alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '虎皮椒（讯虎支付）V3  <a target="_blank" href="https://admin.xunhupay.com/sign-up/4123.html">注册地址</a>',
                ),
                array(
                    'id'      => 'app_id',
                    'type'    => 'text',
                    'title'   => 'APPID',
                    'desc'    => 'APPID',
                    'default' => '',
                ),
                array(
                    'id'         => 'app_secret',
                    'type'       => 'text',
                    'title'      => 'APPSECRET',
                    'desc'       => '密钥',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'api_url',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '必填',
                    'default' => '',
                ),

            ),
            'dependency' => array('is_hupijiao_alipay', '==', 'true'),
        ),

        //讯虎新支付 微信
        array(
            'id'      => 'is_xunhupay_weixin',
            'type'    => 'switcher',
            'title'   => '迅虎(微信H5支付)',
            'label'   => '支持电PC端扫码，移动端H5唤醒支付，微信内JSAPI支付，无资质可以用此方法完美替代*_*',
            'default' => _cao_old('is_xunhupay_weixin', false),
        ),
        array(
            'id'         => 'xunhupay_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '讯虎支付 <a target="_blank" href="https://admin.xunhuweb.com/register/15235553019447ebb7e54725220a7cb9">-->>注册地址</a>',
                ),

                array(
                    'id'      => 'mchid',
                    'type'    => 'text',
                    'title'   => 'MCHID',
                    'desc'    => 'MCHID',
                    'default' => _cao_old('xunhupay_weixin:mchid', ''),
                ),
                array(
                    'id'         => 'private_key',
                    'type'       => 'text',
                    'title'      => 'Private Key',
                    'desc'       => '密钥',
                    'default'    => _cao_old('xunhupay_weixin:private_key', ''),
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'url_do',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
                    'default' => _cao_old('xunhupay_weixin:url_do', ''),
                ),

            ),
            'dependency' => array('is_xunhupay_weixin', '==', 'true'),
        ),

        //讯虎新支付 支付宝
        array(
            'id'      => 'is_xunhupay_alipay',
            'type'    => 'switcher',
            'title'   => '迅虎(支付宝H5支付)',
            'label'   => '稳定第三方服务商渠道*_*',
            'default' => _cao_old('is_xunhupay_alipay', false),
        ),
        array(
            'id'         => 'xunhupay_alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '讯虎支付 <a target="_blank" href="https://admin.xunhuweb.com/register/15235553019447ebb7e54725220a7cb9">-->>注册地址</a>',
                ),
                array(
                    'id'      => 'mchid',
                    'type'    => 'text',
                    'title'   => 'MCHID',
                    'desc'    => 'MCHID',
                    'default' => _cao_old('xunhupay_alipay:mchid', ''),
                ),
                array(
                    'id'         => 'private_key',
                    'type'       => 'text',
                    'title'      => 'Private Key',
                    'desc'       => '密钥',
                    'default'    => _cao_old('xunhupay_alipay:private_key', ''),
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'url_do',
                    'type'    => 'text',
                    'title'   => '支付网关',
                    'desc'    => '一般不用动，如虎皮椒官方有调整手动更新即可',
                    'default' => _cao_old('xunhupay_alipay:url_do', ''),
                ),

            ),
            'dependency' => array('is_xunhupay_alipay', '==', 'true'),
        ),

        // 易支付-支付宝
        array(
            'id'      => 'is_epay_alipay',
            'type'    => 'switcher',
            'title'   => '易支付(支付宝通道)',
            'label'   => '易支付(支付宝通道)，本API接口为彩虹易支付版本SDK接口',
            'default' => false,
        ),

        array(
            'id'         => 'epay_alipay',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'pid',
                    'type'    => 'text',
                    'title'   => '商户ID',
                    'desc'    => '',
                    'default' => '',
                ),
                array(
                    'id'         => 'key',
                    'type'       => 'text',
                    'title'      => '商户KEY',
                    'desc'       => '',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'apiurl',
                    'type'    => 'text',
                    'title'   => '支付API地址',
                    'desc'    => '请填写你的易支付-接口地址,格式为:http[s]://www.xxxxx.xx/记得协议和最后的/别少',
                    'default' => '',
                ),

            ),
            'dependency' => array('is_epay_alipay', '==', 'true'),
        ),
        // 易支付-微信
        array(
            'id'      => 'is_epay_weixin',
            'type'    => 'switcher',
            'title'   => '易支付(微信通道)',
            'label'   => '易支付(微信通道)，本API接口为彩虹易支付版本SDK接口',
            'default' => false,
        ),

        array(
            'id'         => 'epay_weixin',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'id'      => 'pid',
                    'type'    => 'text',
                    'title'   => '商户ID',
                    'desc'    => '',
                    'default' => '',
                ),
                array(
                    'id'         => 'key',
                    'type'       => 'text',
                    'title'      => '商户KEY',
                    'desc'       => '',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'apiurl',
                    'type'    => 'text',
                    'title'   => '支付API地址',
                    'desc'    => '请填写你的易支付-接口地址,格式为:http[s]://www.xxxxx.xx/记得协议和最后的/别少',
                    'default' => '',
                ),

            ),
            'dependency' => array('is_epay_weixin', '==', 'true'),
        ),

        //paypal
        array(
            'id'      => 'is_paypal',
            'type'    => 'switcher',
            'title'   => 'PayPal（贝宝）',
            'label'   => '贝宝国际支付，需要企业版',
            'default' => false,
        ),
        array(
            'id'         => 'paypal',
            'type'       => 'fieldset',
            'title'      => '配置详情',
            'fields'     => array(
                array(
                    'type'    => 'notice',
                    'style'   => 'success',
                    'content' => '查看你的paypal秘钥信息：https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature',
                ),
                array(
                    'id'      => 'username',
                    'type'    => 'text',
                    'title'   => 'API用户名',
                    'desc'    => '',
                    'default' => '',
                ),
                array(
                    'id'         => 'password',
                    'type'       => 'text',
                    'title'      => 'API密码',
                    'desc'       => '',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'         => 'signature',
                    'type'       => 'text',
                    'title'      => '签名',
                    'desc'       => '',
                    'default'    => '',
                    'attributes' => array(
                        'type' => 'password',
                    ),
                ),
                array(
                    'id'      => 'currency',
                    'type'    => 'text',
                    'title'   => '结算货币',
                    'desc'    => '列如(USD：美元、EUR：欧元、GBP：英镑、JPY：日元、CAD：加拿大元、AUD：澳大利亚元、CHF：瑞士法郎、CNY：人民币、SEK：瑞典克朗、NZD：新西兰元)',
                    'default' => 'USD',
                ),
                array(
                    'id'      => 'rates',
                    'type'    => 'text',
                    'title'   => '货币汇率',
                    'desc'    => '1元等于多少结算货币,例如你设置结算货币为USD，则1元=0.7美元',
                    'default' => '0.14',
                ),
                array(
                    'id'      => 'debug',
                    'type'    => 'switcher',
                    'title'   => '沙盒调试模式',
                    'label'   => '不是测试账户调试时切勿开启！',
                    'default' => false,
                ),

            ),
            'dependency' => array('is_paypal', '==', 'true'),
        ),

        // array(
        //     'id'      => 'is_manualpay',
        //     'type'    => 'switcher',
        //     'title'   => '手动静态支付（人工支付）',
        //     'label'   => '采用微信或支付宝静态收款码提示用户付款对应金额后，引导用户联系网站客服发送订单号核对收取金额由网站管理员后台确认收款',
        //     'default' => false,
        // ),

    ),
));

CSF::createSection($prefix, array(
    'parent' => 'shop_fields',
    'title'  => '默认发布字段',
    'fields' => array(

        array(
            'type'    => 'heading',
            'content' => '自定义发布文章时的价格等默认字段，可以配置好默认字段，比如你不想每次都填写价格，可以配置默认为多少',
        ),
        
        array(
            'id'          => 'cao_price',
            'type'        => 'number',
            'title'       => '价格：*',
            'desc'        => '免费请填写：0',
            'output'      => '.heading',
            'output_mode' => 'width',
            'default'     => 0.1,
        ),

        array(
            'id'          => 'cao_vip_rate',
            'type'        => 'number',
            'title'       => '会员折扣：*',
            'desc'        => '0.N 等于N折；1 等于不打折；0 等于会员免费',
            'unit'        => '.N折',
            'output'      => '.heading',
            'output_mode' => 'width',
            'default'     => 1,
        ),

        array(
            'id'      => 'cao_close_novip_pay',
            'type'    => 'checkbox',
            'title'   => '普通用户禁止购买',
            'default' => false,
            'label'   => '勾选后普通用户不能下单支付，只允许会员可以购买',
        ),
        array(
            'id'      => 'cao_is_boosvip',
            'type'    => 'checkbox',
            'title'   => '永久会员免费',
            'label'   => '勾选后永久会员免费，其他会员按折扣或者原价购买',
            'default' => false,
        ),
        array(
            'id'          => 'cao_expire_day',
            'type'        => 'number',
            'title'       => '购买有效期天数',
            'desc'        => '0 无限期；N天后失效需要重新购买',
            'unit'        => '天',
            'output'      => '.heading',
            'output_mode' => 'width',
            'default'     => 0,
        ),

        array(
            'id'      => 'cao_status',
            'type'    => 'switcher',
            'title'   => '启用付费下载模块',
            'label'   => '开启后可设置付费下载专有内容',
            'default' => false,
        ),
        // 下载地址 新
        array(
            'id'                     => 'cao_downurl_new',
            'type'                   => 'group',
            'title'                  => '下载资源',
            'subtitle'               => '支持多个下载地址，支持https:,thunder:,magnet:,ed2k 开头地址',
            'accordion_title_number' => true,
            'fields'                 => array(
                array(
                    'id'      => 'name',
                    'type'    => 'text',
                    'title'   => '资源名称',
                    'default' => '资源名称',
                ),
                array(
                    'id'       => 'url',
                    'type'     => 'upload',
                    'title'    => '下载地址',
                    'sanitize' => false,
                    'default'  => '#',
                ),
                array(
                    'id'    => 'pwd',
                    'type'  => 'text',
                    'title' => '下载密码',
                ),
            ),
            'default'                => '',
            'dependency'             => array('cao_status', '==', 'true'),
        ),

        array(
            'id'         => 'cao_demourl',
            'type'       => 'text',
            'title'      => '演示地址',
            'label'      => '为空则不显示',
            'default'    => '',
            'dependency' => array('cao_status', '==', 'true'),
        ),

        array(
            'id'         => 'cao_diy_btn',
            'type'       => 'text',
            'title'      => '自定义按钮',
            'subtitle'   => '为空则不显示，用 | 隔开',
            'desc'       => '格式： 下载免费版|https://www.baidu.com/',
            'default'    => '',
            'dependency' => array('cao_status', '==', 'true'),
        ),

        array(
            'id'         => 'cao_info',
            'type'       => 'repeater',
            'title'      => '下载资源其他信息',
            'fields'     => array(
                array(
                    'id'      => 'title',
                    'type'    => 'text',
                    'title'   => '标题',
                    'default' => '标题',
                ),
                array(
                    'id'       => 'desc',
                    'type'     => 'text',
                    'title'    => '描述内容',
                    'sanitize' => false,
                    'default'  => '这里是描述内容',
                ),
            ),
            'dependency' => array('cao_status', '==', 'true'),
        ),

        array(
            'id'          => 'cao_paynum',
            'type'        => 'number',
            'title'       => '已售数量',
            'desc'        => '可自定义修改数字',
            'unit'        => '个',
            'output'      => '.heading',
            'output_mode' => 'width',
            'default'     => 0,
        ),
    ),
));

//
// 广告设置
//

$ripro_ads_opt = array(
    'ad_archive_top'       => '分类页-顶部',
    'ad_archive_bottum'    => '分类页-底部',
    'ad_single_top'        => '文章内页-顶部',
    'ad_single_bottum'     => '文章内页-底部',
);
// $ripro_ads_opt_c = [];
$ripro_ads_opt_c = [
    [
        'id'      => 'is_site_ads_vip_hide',
        'type'    => 'switcher',
        'title'   => '网站VIP用户不显示广告',
        'default' => false,
    ]
];
foreach ($ripro_ads_opt as $slug => $name) {

    // 是否开启
    $ripro_ads_opt_c[] = array(
        'id'      => $slug,
        'type'    => 'switcher',
        'title'   => $name,
        'default' => false,
    );
    $ripro_ads_opt_c[] = array(
        'id'         => $slug . '_pc',
        'type'       => 'textarea',
        'title'      => '电脑端广告代码',
        'default'    => '<a href="https://ritheme.com/" target="_blank" rel="nofollow noopener noreferrer" title="广告：多款wordpress正版主题打包仅需599"><img src="' . get_template_directory_uri() . '/assets/img/adds-2.jpg" style=" width: 100%;margin-bottom: 1rem;"></a>',
        'dependency' => array($slug, '==', 'true'),
        'sanitize'   => false,
    );
    $ripro_ads_opt_c[] = array(
        'id'         => $slug . '_mobile',
        'type'       => 'textarea',
        'title'      => '手机端广告代码',
        'default'    => '<a href="https://ritheme.com/" target="_blank" rel="nofollow noopener noreferrer" title="广告：多款wordpress正版主题打包仅需599"><img src="' . get_template_directory_uri() . '/assets/img/adds.jpg" style=" width: 100%;margin-bottom: 1rem;"></a>',
        'dependency' => array($slug, '==', 'true'),
        'sanitize'   => false,
    );

}



CSF::createSection($prefix, array(
    'title'  => '广告设置',
    'fields' => $ripro_ads_opt_c,
));

CSF::createSection($prefix, array(
    'title'       => '邮件设置',
    'description' => 'SMTP设置可以解决wordpress无法发送邮件问题，建议用QQ邮箱，<br>简单说一下如何开启邮箱IMAP/SMTP服务和获得第三方授权码。<br>登录你的QQ邮箱，依次点击，设置 → 账户，找到“POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV服务”设置选项，开启邮箱“IMAP/SMTP服务”。<br>点击下面的“生成授权码 ”，按要求发送短信：配置邮件客户端，到指定的号码，之后点击“我已发送”，会自动生一个授权码，要记好这个授权码，因为只显示一次，没记住只能再次发送短信了，将这个授权码填写到配置信息中即可。<br>注：貌似目前所有邮箱端口都可以设置为465，都支持ssl加密',
    'fields'      => array(

        array(
            'id'      => 'site_admin_push_server',
            'type'    => 'checkbox',
            'title'   => '管理员邮件提醒服务',
            'options' => array(
                'login'    => '用户登录时',
                'register' => '新用户注册时',
                'vip_pay'  => '新VIP开通/续费时',
            ),
            'inline'  => true,
            'default' => array(),
        ),
        // 邮件模板配置
        array(
            'id'      => 'is_site_mail_tpl',
            'type'    => 'switcher',
            'title'   => '自带邮件美化模板',
            'default' => true,
        ),

        array(
            'id'         => 'mail_more_content',
            'type'       => 'text',
            'title'      => '邮件美化模板底部自定义内容',
            'subtitle'   => '',
            'default'    => '此邮件为系统通知邮件，切勿直接回复',
            'dependency' => array('is_site_mail_tpl', '==', 'true'),
        ),

        array(
            'id'      => 'is_site_smtp',
            'type'    => 'switcher',
            'title'   => 'SMTP服务',
            'default' => '该设置主题自带，不能与插件重复开启,如果自带smtp无法使用，请使用smtp插件',
            'default' => _cao_old('is_site_smtp', false),
        ),

        array(
            'id'         => 'smtp_mail_name',
            'type'       => 'text',
            'title'      => '发信邮箱',
            'subtitle'   => '请填写发件人邮箱帐号',
            'default'    => _cao_old('smtp_mail_name', ''),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),

        array(
            'id'         => 'smtp_mail_nicname',
            'type'       => 'text',
            'title'      => '发信人昵称',
            'subtitle'   => '昵称',
            'default'    => _cao_old('smtp_mail_nicname', get_bloginfo('name')),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),
        array(
            'id'         => 'smtp_mail_host',
            'type'       => 'text',
            'title'      => '邮件服务器',
            'subtitle'   => '请填写SMTP服务器地址',
            'default'    => _cao_old('smtp_mail_host', 'smtp.qq.com'),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),
        array(
            'id'         => 'smtp_mail_port',
            'type'       => 'text',
            'title'      => '服务器端口',
            'subtitle'   => '请填写SMTP服务器端口',
            'default'    => _cao_old('smtp_mail_port', '465'),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),
        array(
            'id'         => 'smtp_mail_passwd',
            'type'       => 'text',
            'title'      => '邮箱密码',
            'subtitle'   => '请填写SMTP服务器邮箱密码，特别注意：QQ邮箱的密码在账户设置，最底下，是独立生成的授权码，而不是qq密码和邮箱密码',
            'default'    => _cao_old('smtp_mail_passwd', ''),
            'attributes' => array(
                'type'         => 'password',
                'autocomplete' => 'off',
            ),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),
        array(
            'id'         => 'smtp_mail_smtpauth',
            'type'       => 'switcher',
            'title'      => '启用SMTPAuth服务',
            'label'      => '启用SMTPAuth服务',
            'default'    => _cao_old('smtp_mail_smtpauth', true),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),
        array(
            'id'         => 'smtp_mail_smtpsecure',
            'type'       => 'text',
            'title'      => 'SMTPSecure设置',
            'subtitle'   => '若启用SMTPAuth服务则填写ssl，若不启用则留空',
            'default'    => _cao_old('smtp_mail_smtpsecure', 'ssl'),
            'dependency' => array('is_site_smtp', '==', 'true'),
        ),

    ),
));

CSF::createSection($prefix, array(
    'title'  => '网站优化',
    'fields' => array(
        array(
            'id'      => 'gutenberg_edit',
            'type'    => 'switcher',
            'title'   => '使用古滕堡编辑器',
            'desc'    => '',
            'default' => false,
        ),

        array(
            'id'      => 'gutenberg_widgets',
            'type'    => 'switcher',
            'title'   => '使用古滕堡小工具',
            'desc'    => '',
            'default' => false,
        ),

        array(
            'id'      => 'is_site_menu_cache',
            'type'    => 'switcher',
            'title'   => '使用网站菜单缓存',
            'desc'    => '开启后网站顶部菜单支持缓存，减少SQL查询，如果是多域名菜单不一样，建议关闭此功能',
            'default' => true,
        ),


        array(
            'id'      => 'site_update_file_md5_rename',
            'type'    => 'switcher',
            'title'   => '上传文件MD5加密重命名',
            'desc'    => '建议开启，可以有效解决中文字符无法上传图片问题，防止付费图片被抓包等',
            'default' => false,
        ),

        array(
            'id'      => 'remove_wptexturize',
            'type'    => 'switcher',
            'title'   => '禁用wordpress文章内容输出转码转义功能',
            'desc'    => '禁用后在编辑器中输入代码乱码将原格式输出，不进行转义，适合有写代码内容的开启。',
            'default' => false,
        ),

        array(
            'id'      => 'show_admin_bar',
            'type'    => 'switcher',
            'title'   => '移除前端顶部管理栏',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'remove_admin_bar_menu',
            'type'    => 'switcher',
            'title'   => '移除WP后台顶部LOGO菜单链接',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'remove_admin_foote_wp',
            'type'    => 'switcher',
            'title'   => '移除wp后台底部版本信息',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'remove_admin_menu',
            'type'    => 'switcher',
            'title'   => '移除WP后台仪表盘菜单',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'remove_emoji',
            'type'    => 'switcher',
            'title'   => '移除WP自带emoji表情插件',
            'desc'    => '可以大幅度精简JS和CSS',
            'default' => true,
        ),

        array(
            'id'      => 'remove_wp_head_more',
            'type'    => 'switcher',
            'title'   => '精简优化网站前台head标签代码',
            'desc'    => '',
            'default' => true,
        ),

        array(
            'id'      => 'remove_wp_img_attributes',
            'type'    => 'switcher',
            'title'   => '精简优化网站图片代码',
            'desc'    => '移除wp自带编辑器插入图片时一堆不必要的html属性和元素',
            'default' => false,
        ),

        array(
            'id'      => 'remove_wp_rest_api',
            'type'    => 'switcher',
            'title'   => '关闭网站REST API接口',
            'desc'    => '如果你有使用小程序等功能，请不要优化此项',
            'default' => false,
        ),
        array(
            'id'      => 'remove_wp_xmlrpc',
            'type'    => 'switcher',
            'title'   => '关闭XML-RPC (pingback) 功能',
            'desc'    => 'XML-RPC 是 WordPress 用于第三方客户端，关闭后可以防止爆破攻击',
            'default' => false,
        ),

    ),
));

CSF::createSection($prefix, array(
    'title'       => '备份设置',
    'description' => '仅备份该页面主题设置所有选项设置数据，并不备份wp自有的文章等数据，提示说明：此处备份中保存的数据格式是字符串类型，有长度验证，切勿修改字符串导致乱码，如需修改请原封不动导入进去在设置页码输入框修改',
    'fields'      => array(

        array(
            'type' => 'backup',
        ),

    ),
));

CSF::createSection($prefix, array(
    'title'  => '主题授权',
    'fields' => array(
        array(
            'type'    => 'submessage',
            'style'   => 'danger',
            'content' => '<i class="fa fa-heart" style=" color: red; "></i> 感谢您使用本主题进行创作，请先填写ritheme.com个人中心您的授权ID和授权码点击保存验证。<i class="fa fa-heart" style=" color: red; "></i>
  <br/>------授权错误会提示错误消息，成功则显示授权正常后刷新页面即可。</br></br><i class="fa fa-heart" style=" color: red; "></i> PS：承蒙您对本主题的喜爱，我们愿向小三一样，做大哥的女人，做大哥网站中最想日的一个。开发者不易，我们真诚的希望和感谢每一个真心建站运营的朋友能支持日系列的正版主题，更好的更用心的等你来调教。如此强大好用的日系列主题，包含支付等重要安全功能，我们希望用户一定要重视自己的数据安全，不要相信所谓破解等盗版，盗版无利不起早，一切以安全为己任。共同维护创造国内健康的WordPress使用环境。开发者油条敬上。',
        ),
        array(
            'type'     => 'callback',
            'function' => 'riadmin_license_page',
        ),
    ),
));

unset($prefix);