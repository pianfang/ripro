<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package riblog
 */



/**
 * 自定义顶部css
 * @Author Dadong2g
 * @date   2023-03-14
 * @return [type]
 */
function custom_head_css() {
    $css = '';

    //背景颜色配置
    $container_width = (int) _cao('site_container_width', '');

    if ($container_width > 0) {
        $css .= "@media (min-width: 1200px){ .container-xl, .container-lg, .container-md, .container-sm, .container { max-width: {$container_width}px; } }";
    }

    //背景颜色配置
    $body_background = _cao('site_background', array());
    $__css           = '';
    foreach ($body_background as $property => $value) {
        if (!empty($value)) {
            if (is_array($value)) {
                $url = isset($value['url']) && !empty($value['url']) ? $value['url'] : null;
                if ($url !== null) {
                    $__css .= "$property: url('$url');";
                }
            } else {
                $__css .= "$property: $value;";
            }
        }
    }

    if (!empty($__css)) {
        $css .= "body{{$__css}}\n";
    }

    //顶部菜单配置
    $header_color = _cao('site_header_color', array());
    $__css        = '';

    if (!empty($header_color['bg-color'])) {
        $__css .= ".site-header{background-color:{$header_color['bg-color']};}\n";
        $__css .= ".navbar .nav-list .sub-menu:before{border-bottom-color:{$header_color['sub-bg-color']};}\n";
    }
    if (!empty($header_color['sub-bg-color'])) {
        $__css .= ".navbar .nav-list .sub-menu{background-color:{$header_color['sub-bg-color']};}\n";
    }
    if (!empty($header_color['color'])) {
        $__css .= ".site-header,.navbar .nav-list a,.navbar .actions .action-btn{color:{$header_color['color']};}\n";
    }
    if (!empty($header_color['hover-color'])) {
        $__css .= ".navbar .nav-list a:hover,.navbar .nav-list > .menu-item.current-menu-item > a {color:{$header_color['hover-color']};}\n";
    }

    if (!empty($__css)) {
        $css .= "$__css";
    }

    //自定义CSS
    $custom_web_css = _cao('site_web_css');
    if ($custom_web_css) {
        $css .= $custom_web_css;
    }
    //打包输出
    if (!empty($css)) {
        echo "<style type=\"text/css\">\n" . $css . "\n</style>";
    }

}
add_action('wp_head', 'custom_head_css');

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function custom_body_classes($classes) {

    if (get_query_var('uc-page')) {
        $classes[] = 'uc-page';
    }

    if (get_query_var('pay-vip-page')) {
        $classes[] = 'vip-prices-page';
    }

    //顶部透明菜单
    $is_home_header_transparent = (bool) _cao('is_site_home_header_transparent', false);
    if (is_home() && $is_home_header_transparent) {
        $classes[] = 'header-transparent';
    }

    return $classes;
}
add_filter('body_class', 'custom_body_classes');

//内页标题优化
function zb_archive_title($title, $original_title) {

    return $original_title;
}
add_filter('get_the_archive_title', 'zb_archive_title', 10, 2);

//内页描述优化
function zb_archive_description($description) {
    if (is_search()) {
        global $wp_query;
        $search_num  = $wp_query->found_posts;
        $description = sprintf(__('搜索到 %1$s 个与 "%2$s" 相关的结果', 'ripro'), $search_num, get_search_query());
    }
    return $description;
}
add_filter('get_the_archive_description', 'zb_archive_description');

// 在后台更新菜单时删除缓存
function zb_update_menu_callback() {
    delete_transient('main-menu-cache');
}
add_action('wp_update_nav_menu', 'zb_update_menu_callback', 10);

/**
 * 上一页翻页钩子替换
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  [type]     $attr [description]
 * @return [type]
 */
function _prev_posts_link_attr($attr) {
    return $attr . ' class="prev"';
}
add_filter('previous_posts_link_attributes', '_prev_posts_link_attr');

/**
 * 下一页翻页钩子替换
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  [type]     $attr [description]
 * @return [type]
 */
function _next_posts_link_attr($attr) {
    return $attr . ' class="next"';
}
add_filter('next_posts_link_attributes', '_next_posts_link_attr');

//隐藏评论者昵称
function substr_cut_comment_author($author) {
    if ($author || 　is_admin()) {
        return $author;
    } else {
        return zb_substr_cut($author);
    }
}
add_filter('get_comment_author', 'substr_cut_comment_author', 10, 1);

//关闭网站评论功能
function preprocess_close_comment($commentdata) {

    if (!is_site_comments()) {
        wp_die('本站评论系统暂未开启');exit;
    }

    $allowed_tags = array(
        'img' => array(
            'src' => array()
        ),
    );
    // 过滤不安全标签 
    $commentdata['comment_content'] = wp_kses($commentdata['comment_content'], $allowed_tags);
    
    return $commentdata;
}
add_filter('preprocess_comment', 'preprocess_close_comment');


function dynamic_message_comment($comment_id, $comment_approved){

    if( 1 === $comment_approved ){
        $comment = get_comment($comment_id);
        //添加网站动态
        ZB_Dynamic::add([
            'info' => sprintf( __('评论了%s', 'ripro-v2'),get_the_title($comment->comment_post_ID) ),
            'uid' => $comment->user_id, 
            'href' => get_the_permalink($comment->comment_post_ID),
        ]);
    }
    
}

add_action( 'comment_post', 'dynamic_message_comment', 10, 2 );




//过滤旧版本评论防止报错
function filter_question_comments($query) {
    // 只在后台评论页面进行过滤
    if (is_admin() && $query->query_vars['type'] == '') {
        // 排除文章类型为question的评论
        $query->query_vars['post_type'] = array('post', 'page');
    }
}
add_action('pre_get_comments', 'filter_question_comments');

/**
 * 图片懒加载 data属性
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  [type]     $attr       [description]
 * @param  [type]     $attachment [description]
 * @param  [type]     $size       [description]
 */
function add_custom_image_data_attributes($attr, $attachment, $size) {
    if (is_admin()) {
        return $attr;
    }
    if (empty($attr['class']) || strpos($attr['class'], 'lazy') === false) {
        return $attr;
    }
    if (!array_key_exists('data-src', $attr)) {
        $attr['data-src'] = $attr['src'];
        $attr['src']      = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    }
    return $attr;
}

// add_filter('wp_get_attachment_image_attributes', 'add_custom_image_data_attributes', 10, 3);

/**
 * 删除图片多余属性
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  [type]     $html [description]
 * @return [type]
 */
function remove_img_attr($html) {
    return preg_replace('/(width|height)="\d+"\s/', "", $html);
}
add_filter('post_thumbnail_html', 'remove_img_attr');

//替换默认头像
function _get_avatar_url($url, $id_or_email, $args) {
    $user_id = 0;
    if (is_numeric($id_or_email)) {
        $user_id = absint($id_or_email);
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        if (isset($user->ID) && $user->ID) {
            $user_id = $user->ID;
        }
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Post) {
        $user_id = $id_or_email->post_author;
    } elseif ($id_or_email instanceof WP_Comment) {
        $user_id = $id_or_email->user_id;
        if (!$user_id) {
            $user = get_user_by('email', $id_or_email->comment_author_email);
            if (isset($user->ID) && $user->ID) {
                $user_id = $user->ID;
            }

        }
    }

    $avatar_url = get_default_avatar_src(); //默认头像

    $avatar_type = get_user_meta($user_id, 'user_avatar_type', true); // null | custom | qq | weixin | weibo |

    if ($avatar_type == 'custom' || $avatar_type == 'gravatar') {

        $custom_avatar = get_user_meta($user_id, 'user_custom_avatar', true);
        if (!empty($custom_avatar)) {

            if (strpos($custom_avatar, '/') === 0) {
                // 相对路径，添加网站目录前缀
                //兼容老款相对地址
                $uploads = wp_upload_dir();
                if (file_exists(WP_CONTENT_DIR . '/uploads' . $custom_avatar)) {
                    $custom_avatar = WP_CONTENT_URL . '/uploads' . $custom_avatar;
                }
            } else {
                // 绝对路径，直接输出
                $avatar_url = $custom_avatar;
            }

            $avatar_url = set_url_scheme($custom_avatar); //头像存在
        }

    } elseif (in_array($avatar_type, ['qq', 'weixin', 'weibo'])) {
        $avatar_url = set_url_scheme(get_user_meta($user_id, 'open_' . $avatar_type . '_avatar', true)); //开发平台
    }

    return preg_replace('/^(http|https):/i', '', $avatar_url);

}

add_filter('get_avatar_url', '_get_avatar_url', 10, 3);

function _pre_get_avatar($avatar, $id_or_email, $args) {

    $url = get_avatar_url($id_or_email, $args);

    $class = array('avatar-img', 'avatar', 'avatar-' . (int) $args['size']);
    if ($args['class']) {
        if (is_array($args['class'])) {
            $class = array_merge($class, $args['class']);
        } else {
            $class[] = $args['class'];
        }
    }

    $avatar = sprintf(
        "<img alt='%s' src='%s' class='%s' height='%d' width='%d' %s/>",
        esc_attr($args['alt']),
        esc_url($url),
        esc_attr(join(' ', $class)),
        (int) $args['height'],
        (int) $args['width'],
        $args['extra_attr']
    );
    return $avatar;
}
add_filter('pre_get_avatar', '_pre_get_avatar', 10, 3);

function zb_login_url($url, $redirect) {
    $url = home_url('/login');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', urlencode($redirect), $url);
    }
    return esc_url($url);
}
add_filter('login_url', 'zb_login_url', 20, 2);

function zb_register_url($url) {
    $url = home_url('/register');
    return esc_url($url);
}
add_filter('register_url', 'zb_register_url', 20);

function zb_lostpassword_url($url, $redirect) {
    $url = home_url('/lostpwd');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', urlencode($redirect), $url);
    }
    return esc_url($url);
}
add_filter('lostpassword_url', 'zb_lostpassword_url', 20, 2);

// 禁用自动生成的图片尺寸
function zb_disable_image_sizes($sizes) {
    if (_cao('disable_wp_thumbnail_crop', false)) {
        $sizes = array();
    }

    return $sizes;
}
add_action('intermediate_image_sizes_advanced', 'zb_disable_image_sizes');

// 自定义筛选字段过滤器
function zb_archive_pre_posts_filter($query) {

    //后台不过滤
    if (is_admin()) {
        return $query;
    }

    //不是主查询排除过滤器
    if (!$query->is_main_query()) {
        return $query;
    }

    //搜索仅限搜索文章类型
    if(is_search()){
        $query->set('post_type', 'post');
    }
    
    //高级筛选过滤器
    if (is_archive()) {

        //价格筛选free
        $price      = sanitize_text_field(get_param('price', null, 'get'));
        $price_meta = ['free', 'vip_only', 'vip_free', 'vip_rate', 'boosvip_free'];
        if (in_array($price, $price_meta)) {

            $meta_query       = [];
            $price_key        = 'cao_price';
            $vip_rate_key     = 'cao_vip_rate';
            $boosvip_free_key = 'cao_is_boosvip';
            $vip_only_key     = 'cao_close_novip_pay';

            switch ($price) {
            case 'free':
                $meta_query[] = ['key' => $price_key, 'compare' => '=', 'value' => '0'];
                $meta_query[] = ['key' => $vip_only_key, 'compare' => '!=', 'value' => '1'];
                break;
            case 'vip_only':
                $meta_query[] = ['key' => $vip_only_key, 'compare' => '=', 'value' => '1'];
                break;
            case 'vip_free':
                $meta_query[] = ['key' => $price_key, 'compare' => '>', 'value' => '0'];
                $meta_query[] = ['key' => $vip_rate_key, 'compare' => '=', 'value' => '0'];
                break;
            case 'vip_rate':
                $meta_query[] = [
                    'relation' => 'AND',
                    ['key' => $price_key, 'compare' => '>', 'value' => '0'],
                    ['key' => $vip_rate_key, 'compare' => '>', 'value' => '0'],
                    ['key' => $vip_rate_key, 'compare' => '<', 'value' => '1'],
                ];
                break;
            case 'boosvip_free':
                $meta_query[] = [
                    'relation'            => 'OR',
                    'boosvip_free_clause' => ['key' => $boosvip_free_key, 'compare' => '=', 'value' => '1'],
                    'vip_rate_clause'     => ['key' => $vip_rate_key, 'compare' => '=', 'value' => '0'],
                ];
                break;
            }

            $query->set('meta_query', $meta_query);
        }

        // 排序：
        $orderby     = sanitize_text_field(get_param('orderby', null, 'get'));
        $orders_meta = ['views', 'likes', 'follow_num'];
        if (in_array($orderby, $orders_meta)) {
            $query->set('meta_key', $orderby);
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
        }

    }

    return $query;
}
add_filter('pre_get_posts', 'zb_archive_pre_posts_filter', 99);

/**
 * 搜素功能过滤器
 * @Author Dadong2g
 * @date   2023-02-13
 * @param  [type]     $search   [description]
 * @param  [type]     $wp_query [description]
 * @return [type]
 */
function site_search_by_title_only($search, $wp_query) {
    global $wpdb;

    if (empty($search) || empty(_cao('is_site_pro_search_title', false))) {
        return $search; // skip processing - no search term in query
    }

    // skip processing - no search term in query
    $q      = $wp_query->query_vars;
    $n      = !empty($q['exact']) ? '' : '%';
    $search = $searchand = '';
    foreach ((array) $q['search_terms'] as $term) {
        $term = esc_sql($wpdb->esc_like($term));
        $search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $searchand = ' AND ';
    }
    if (!empty($search)) {
        $search = " AND ({$search}) ";
        if (!is_user_logged_in()) {
            $search .= " AND ($wpdb->posts.post_password = '') ";
        }

    }
    return $search;

}
add_filter('posts_search', 'site_search_by_title_only', 99, 2);

/**
 * 广告代码
 * @Author Dadong2g
 * @date   2023-06-26
 * @param  [type]     $slug [description]
 * @return [type]
 */
function zb_ripro_ads_filter($slug) {

    global $current_user;

    if (defined('DOING_AJAX') && DOING_AJAX) {
        return false;
    }

    if (!empty(_cao('is_site_ads_vip_hide',0)) && get_user_vip_type($current_user->ID) != 'no') {
        return false;
    }

    $position   = (strpos($slug, 'bottum') !== false) ? ' bottum' : ' top';
    $is_ads     = _cao($slug);
    $ads_pc     = _cao($slug . '_pc');
    $ads_mobile = _cao($slug . '_mobile');

    $html = '';
    if (wp_is_mobile() && $is_ads && !empty($ads_mobile)) {
        $html = '<div class="site-addswarp mobile' . $position . '">';
        $html .= $ads_mobile;
        $html .= '</div>';
    } else if ($is_ads && isset($ads_pc)) {
        $html = '<div class="site-addswarp pc' . $position . '">';
        $html .= $ads_pc;
        $html .= '</div>';
    }
    echo $html;
}

add_action('ripro_ads', 'zb_ripro_ads_filter', 10, 1);







// 演示数据导入 需要安装 One Click Demo Import 插件
// src="/wp-content/uploads/.*?\.png"
// 

function zb_ocdi_import_files() {
  return [
    [
      'import_file_name'           => '演示风格1',
      'categories'                 => [],
      'import_file_url'            => 'https://ripro.rizhuti.com/ripro-demo-file/ripro-v5-content.xml',
      'import_widget_file_url'     => 'https://ripro.rizhuti.com/ripro-demo-file/ripro-v5-widgets.json',
      'import_customizer_file_url' => '',
      'import_redux'               => [],
      'import_preview_image_url'   => 'https://ripro.rizhuti.com/ripro-demo-file/demo1.jpg',
      'preview_url'                => 'https://ripro.rizhuti.com/',
    ]
  ];
}
add_filter( 'ocdi/import_files', 'zb_ocdi_import_files' );


function zb_ocdi_after_import_setup() {
    
    if (false) {
        $file_url = 'https://ripro.rizhuti.com/ripro-demo-file/ripro-v5-options.json';
        $json_data = file_get_contents( $file_url );
        $import_data  = json_decode( $json_data, true );
        $options = ( is_array( $import_data ) && ! empty( $import_data ) ) ? $import_data : array();
        update_option( _OPTIONS_PRE , $options); //更新主题设置
    }
    
    // Assign menus to their locations.
    $main_menu = get_term_by( 'name', 'main', 'nav_menu' );
 
    set_theme_mod( 'nav_menu_locations', [
            'main-menu' => $main_menu->term_id, // replace 'main-menu' here with the menu location identifier from register_nav_menu() function in your theme.
        ]
    );
    delete_transient('main-menu-cache');//删除菜单缓存
    //重写固定连接规则
    flush_rewrite_rules(false);
}
add_action( 'ocdi/after_import', 'zb_ocdi_after_import_setup' );
