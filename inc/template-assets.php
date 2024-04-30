<?php




function zb_assets_src($cdn_mod='theme'){
    //font-awesome图标/jquery加载模式
    $assets_mod = [
        'theme'      => [
            'jquery'       => get_template_directory_uri() . '/assets/js/jquery.min.js',
            'highlight-js' => get_template_directory_uri() . '/assets/js/highlight.min.js',
            'video-js'     => get_template_directory_uri() . '/assets/js/video-js/video.min.js',
            'video-css'    => get_template_directory_uri() . '/assets/css/video-js/video-js.min.css',
            'fw'           => get_template_directory_uri() . '/assets/css/font-awesome/css/all.min.css',
            'fw4'          => get_template_directory_uri() . '/assets/css/font-awesome/css/v4-shims.min.css',
        ],
        'jsdelivr'   => [
            'jquery'       => '//cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js',
            'highlight-js' => '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.7.0/build/highlight.min.js',
            'video-js'     => '//cdn.jsdelivr.net/npm/video.js@8.0.4/dist/video.min.js',
            'video-css'    => '//cdn.jsdelivr.net/npm/video.js@8.0.4/dist/video-js.min.css',
            'fw'           => '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css',
            'fw4'          => '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/v4-shims.min.css',
        ],
        'unpkg'      => [
            'jquery'       => '//unpkg.com/jquery@3.6.4/dist/jquery.min.js',
            'highlight-js' => '//unpkg.com/@highlightjs/cdn-assets@11.7.0/highlight.min.js',
            'video-js'     => '//unpkg.com/video.js/dist/video.min.js',
            'video-css'    => '//unpkg.com/video.js@8.0.4/dist/video-js.min.css',
            'fw'           => '//unpkg.com/@fortawesome/fontawesome-free@5.15.4/css/all.min.css',
            'fw4'          => '//unpkg.com/@fortawesome/fontawesome-free@5.15.4/css/v4-shims.min.css',
        ],

        'cloudflare' => [
            'jquery'       => '//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
            'highlight-js' => '//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js',
            'video-js'     => '//cdnjs.cloudflare.com/ajax/libs/video.js/8.0.4/video.min.js',
            'video-css'    => '//cdnjs.cloudflare.com/ajax/libs/video.js/8.0.4/video-js.min.css',
            'fw'           => '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
            'fw4'          => '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css',
        ],
        'bootcdn'    => [
            'jquery'       => '//cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js',
            'highlight-js' => '//cdn.bootcdn.net/ajax/libs/highlight.js/11.7.0/highlight.min.js',
            'video-js'     => '//cdn.bootcdn.net/ajax/libs/video.js/8.0.4/video.min.js',
            'video-css'    => '//cdn.bootcdn.net/ajax/libs/video.js/8.0.4/video-js.min.css',
            'fw'           => '//cdn.bootcdn.net/ajax/libs/font-awesome/5.15.4/css/all.min.css',
            'fw4'          => '//cdn.bootcdn.net/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css',
        ],
    ];

    // $cdn_mod = _cao('assets_cdn_mod', 'theme');
    return $assets_mod[$cdn_mod];
}




/**
 * 网站静态资源加载
 */
function zb_scripts() {

    // 移除无用
    wp_deregister_style('global-styles');
    wp_dequeue_style('global-styles');
    // wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style');

    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);

    //调试模式实时刷新css和js
    $assets_ver = (_THEME_DEBUG == true) ? _THEME_VERSION . time() : _THEME_VERSION;
    if (_THEME_DEBUG == true) {
        $assets_ver = _THEME_VERSION . time();
        $js_suffix  = '.js';
        $css_suffix = '.css';
    } else {
        $assets_ver = _THEME_VERSION;
        $js_suffix  = '.min.js';
        $css_suffix = '.min.css';
    }

    $cdn_mod = _cao('assets_cdn_mod', 'theme');
    $cdn_src = zb_assets_src($cdn_mod);

    wp_enqueue_style('csf-fa5', $cdn_src['fw'], array(), '5.15.4', 'all');
    wp_enqueue_style('csf-fa5-v4-shims', $cdn_src['fw4'], array(), '5.15.4', 'all');

    //主题样式
    wp_enqueue_style('main', get_template_directory_uri() . '/assets/css/main' . $css_suffix, array(), $assets_ver);

    //是否加载Jquery
    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', $cdn_src['jquery'], array(), '3.6.0', false);

    //代码高亮插件
    if (is_singular()) {
        wp_enqueue_script('highlight', $cdn_src['highlight-js'], array('jquery'), '11.7.0', true);


        if (in_array(get_post_format(get_the_ID()), array('video','audio')) && zb_get_media_preview_url(get_the_ID())){
            $is_media_preview = true;
        }else{
            $is_media_preview = false;
        }

        if ( has_shortcode( get_the_content(), 'ri-video' ) || get_post_meta(get_the_ID(), 'cao_video', true) || $is_media_preview) {
            // videojs按需调用
            wp_enqueue_style('video-css', $cdn_src['video-css'], array(), '8.0.4', 'all');
            wp_enqueue_script('video-js', $cdn_src['video-js'], array(), '8.0.4', true);
        }
    }

    // Vendors JS
    wp_enqueue_script('vendor', get_template_directory_uri() . '/assets/js/vendor' . $js_suffix, array('jquery'), $assets_ver, true);

    // Theme main functions JS
    wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main' . $js_suffix, array('vendor'), $assets_ver, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    $script_params = array(
        'home_url'        => esc_url(home_url()),
        'ajax_url'        => esc_url(admin_url('admin-ajax.php')),
        'theme_url'       => esc_url(get_template_directory_uri()),
        'singular_id'     => 0,
        'post_content_nav' => intval(_cao('site_post_content_nav',0)),
        'site_popup_login' => intval(_cao('is_site_popup_login',1) && !wp_is_mobile()),
        'site_notify_auto' => intval(is_site_notify_auto()),
        'current_user_id' => get_current_user_id(),
        'ajax_nonce'      => wp_create_nonce("zb_ajax"),
        'gettext'         => array(
            '__copypwd'=>__('密码已复制剪贴板', 'ripro'),
            '__copybtn'=>__('复制', 'ripro'),
            '__copy_succes'=>__('复制成功', 'ripro'),
            '__comment_be'=>__('提交中...', 'ripro'),
            '__comment_succes'=>__('评论成功', 'ripro'),
            '__comment_succes_n'=>__('评论成功，即将刷新页面', 'ripro'),
            '__buy_be_n'=>__('请求支付中···', 'ripro'),
            '__buy_no_n'=>__('支付已取消', 'ripro'),
            '__is_delete_n'=>__('确定删除此记录？', 'ripro'),
        ),
    );
    if (is_singular()) {
        $script_params['singular_id'] = get_the_ID();
    }

    wp_localize_script('main', 'zb', $script_params);
}
add_action('wp_enqueue_scripts', 'zb_scripts');

// admin
function zb_enqueue_admin_script($hook) {

    wp_enqueue_style('zb-admin-all', get_template_directory_uri() . '/admin/css/admin-all.css', array(), _THEME_VERSION);

    wp_enqueue_script('zb-admin-all', get_template_directory_uri() . '/admin/js/admin-all.js', array('jquery'), _THEME_VERSION, true);

    $script_params = array(
        'home_url'   => esc_url(home_url()),
        'ajax_url'   => esc_url(admin_url('admin-ajax.php')),
        'theme_url'  => esc_url(get_template_directory_uri()),
        'ajax_nonce' => wp_create_nonce("zb_ajax"),
    );

    wp_localize_script('zb-admin-all', 'zb', $script_params);

    //商城管理页面加载
    if (strpos($hook, 'zb-admin-page') !== false) {
        wp_enqueue_style('zb-admin-page', get_template_directory_uri() . '/admin/css/admin-page.css', array(), _THEME_VERSION);
        wp_enqueue_script('apexcharts', get_template_directory_uri() . '/admin/js/apexcharts.min.js', array(), '3.35.3', true);
        wp_enqueue_script('zb-admin-page', get_template_directory_uri() . '/admin/js/admin.js', array('jquery'), _THEME_VERSION, true);
    }

}
add_action('admin_enqueue_scripts', 'zb_enqueue_admin_script');
