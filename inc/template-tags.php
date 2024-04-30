<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package dadong2g
 */

//调试信息
function zb_dump() {
    $backtrace = debug_backtrace();
    $caller = array_shift($backtrace);

    echo PHP_EOL . '<pre class="site-zb-dump bg-dark text-warning">';
    echo '<div class="zb-dump-code">';
    foreach (func_get_args() as $arg) {
        var_dump($arg);
    }
    echo '</div>';
    printf( '<div class="zb-dump-info">%1$s from %2$s [line %3$s]</div>', wp_date('Y-m-d H:i:s'), $caller['file'], $caller['line'] );
    echo '</pre>' . PHP_EOL;
}

//全站弹窗报错
function zb_wp_die($title = '', $msg = '', $back_link = '') {
    ob_start();?>
    <!doctype html>
    <html <?php language_attributes();?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset');?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
        <link rel="profile" href="https://gmpg.org/xfn/11">
        <title><?php echo get_bloginfo('name') . ' - ' . $title; ?></title>
        <?php wp_head();?>
    </head>
    <body class="wpdie">
    <script type="text/javascript">
    window.onload = function(){
        var html = '<div class="text-center"><h4 class="text-danger"><i class="fas fa-info-circle"></i> <?php echo $title; ?></h4><hr><div class="text-muted py-3"><?php echo $msg; ?></div></div>';
        var back_url = '<?php echo $back_link; ?>';
        ri.popup(html,400,function(){
            if (back_url === 'close'){
                window.close();
            } else if (back_url !== ''){
                location.href = back_url;
            } else {
                location.href = document.referrer;
            }
        })
    };
    </script>
    <?php wp_footer();?>
    <div class="dimmer"></div>
    </body></html>
    <?php echo ob_get_clean();exit;
}

//获取用户客户端IP get_ip_address()
function get_ip_address($ignore_private_and_reserved = false) {
    $flags = $ignore_private_and_reserved ? (FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) : 0;
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false) {
                    return $ip;
                }
            }
        }
    }
    return 'unknown';
}



/**
 * 获取网站颜色风格模式 light dark
 * @Author Dadong2g
 * @date   2023-07-04
 * @param  integer    $key [description]
 * @return [type]
 */
function get_site_default_color_style() {

    $style = _cao('site_default_color_mod','light');
    //读取用户浏览器缓存模式
    $cookie_style = ZB_Cookie::get('current_site_color');

    if (!empty($cookie_style)) {
        $style = $cookie_style;
    }

    if ($style=='auto') {
        $current_hour = wp_date('H'); // 获取当前小时
        // 定义白天和黑夜的起止时间（您可以根据需要进行调整）
        $day_start = 6;   // 白天开始时间
        $night_start = 18;   // 黑夜开始时间
        // 根据当前时间判断风格
        if ($current_hour >= $day_start && $current_hour < $night_start) {
            $style = 'light';   // 白天风格
        } elseif ($current_hour >= $night_start || $current_hour < $day_start) {
            $style = 'dark';   // 黑夜风格
        }
    }

    return $style;
}


function zb_get_color_class($key = 0) {
    $colors  = ['danger', 'primary', 'success', 'warning', 'info', 'secondary'];
    $color   = (isset($colors[$key])) ? $colors[$key] : 'secondary';
    return $color;
}

//只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
function zb_substr_cut($user_name) {

    if (empty($user_name)) {
        return __( '游客', 'ripro' );
    }
    
    $strlen   = mb_strlen($user_name, 'utf-8');
    $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
    $lastStr  = mb_substr($user_name, -1, 1, 'utf-8');
    if ($strlen < 2) {
        return $user_name;
    }
    return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}



//User Agent 分析
function analyzeUserAgent($userAgent) {
    // 初始化结果数组
    $result = array(
        'browser' => '',
        'version' => '',
        'os' => ''
    );

    // 匹配常见浏览器
    $browserRegexes = array(
        '/msie/i'      => 'IE',
        '/firefox/i'   => 'Firefox',
        '/safari/i'    => 'Safari',
        '/chrome/i'    => 'Chrome',
        '/edge/i'      => 'Edge',
        '/opera/i'     => 'Opera',
        '/netscape/i'  => 'Netscape',
        '/maxthon/i'   => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i'    => 'Mobile Browser'
    );

    // 匹配常见操作系统
    $osRegexes = array(
        '/windows/i'             => 'Windows',
        '/macintosh|mac os x/i'  => 'Mac OS X',
        '/mac_powerpc/i'         => 'Mac OS 9',
        '/linux/i'               => 'Linux',
        '/ubuntu/i'              => 'Ubuntu',
        '/iphone/i'              => 'iPhone',
        '/ipod/i'                => 'iPod',
        '/ipad/i'                => 'iPad',
        '/android/i'             => 'Android',
        '/blackberry/i'          => 'BlackBerry',
        '/webos/i'               => 'Mobile'
    );

    // 分析浏览器
    foreach ($browserRegexes as $regex => $browser) {
        if (preg_match($regex, $userAgent)) {
            $result['browser'] = $browser;
            // break;
        }
    }

    // 分析操作系统
    foreach ($osRegexes as $regex => $os) {
        if (preg_match($regex, $userAgent)) {
            $result['os'] = $os;
            // break;
        }
    }

    // 提取浏览器版本号
    if ($result['browser']) {
        $versionRegex = '/' . preg_quote($result['browser'], '/') . '\/([\d\w\.]+)/i';
        preg_match($versionRegex, $userAgent, $matches);
        if (isset($matches[1])) {
            $result['version'] = $matches[1];
        }
    }

    return $result;
}



//获取响应参数
function get_param($key, $default = '', $method = 'post') {
    switch ($method) {
    case 'post':
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
        break;
    case 'get':
        return (isset($_GET[$key])) ? $_GET[$key] : $default;
        break;
    case 'request':
        return (isset($_REQUEST[$key])) ? $_REQUEST[$key] : $default;
        break;
    default:
        return null;
        break;
    }
}

/**
 * 获取今天开始结束时间戳
 * @Author Dadong2g
 * @date   2023-03-05
 * @return [type]
 */
function get_today_time_range() {
    $timezone_object = wp_timezone();
    // 获取今天开始和结束时间的DateTime对象
    $today_start = new DateTime('today', $timezone_object);
    $today_end = clone $today_start;
    $today_end->modify('+1 day');

    // 转换为时间戳
    $today_start_timestamp = $today_start->getTimestamp();
    $today_end_timestamp = $today_end->getTimestamp();


    return array(
        'start' => $today_start->getTimestamp(),
        'end'   => $today_end->getTimestamp(),
    );
}

//获取主题目录地址
function zb_theme_content_url($dir = '') {
    //是否多域名支持
    $content_url = get_template_directory_uri();
    $home_url    = home_url();
    if (strpos($content_url, $home_url) !== false) {
        return $content_url . $dir;
    }
    return $home_url . $content_url . $dir;
}

// 根据字符串搜索用户id 用于搜索
function get_user_id_from_str($string) {
    $string = trim($string);

    if (is_email($string) && $user = get_user_by('email', $string)) {
        return $user->ID;
    }
    if (is_numeric($string) && $user = get_user_by('id', absint($string))) {
        return $user->ID;
    }
    if (is_string($string) && $user = get_user_by('login', $string)) {
        return $user->ID; 
    }
    return 0;
}




/**
 * 时间戳
 * @Author Dadong2g
 * @date   2022-01-06
 * @return [type]
 */
function zb_meta_datetime() {
    
    $time = get_the_time('U');
    $time_string = sprintf(
        '<time class="pub-date" datetime="%1$s">%2$s</time>',
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(human_time_diff($time, current_time('timestamp')) . __('前', 'ripro'))
    );

    if (false) {
        // 显示最近修改时间
        $modified_time = get_the_modified_time('U');
        if ($time != $modified_time) {
            $time_string .= sprintf(
                '<time class="mod-date" datetime="%1$s">%2$s</time>',
                esc_attr(get_the_modified_date(DATE_W3C)),
                esc_html(human_time_diff($modified_time, current_time('timestamp')) . __('前', 'ripro'))
            );
        }
    }
    
    echo $time_string;
}

/**
 * 作者信息
 * @Author Dadong2g
 * @date   2023-04-19
 * @return [type]
 */
function zb_meta_by() {
    printf(
        '<a class=\'stretched-link text-reset btn-link\' href=\'%s\'>%s</a>',
        esc_url(get_author_posts_url(get_the_author_meta('ID'))),
        esc_html(get_the_author())
    );
}


/**
 * 分类信息
 * @Author Dadong2g
 * @date   2023-04-19
 * @param  integer    $num [description]
 * @return [type]
 */
function zb_meta_category($num = 2) {
    $categories = get_the_category();
    $separator = ' ';
    $output = '';
    if ($categories) {
        foreach ($categories as $key => $category) {
            if ($key == $num) {
                break;
            }
            $output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>' . $separator;
        }
        echo trim($output, $separator);
    }
}

/**
 * 摘要
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  string     $limit [description]
 * @return [type]
 */
add_filter('excerpt_more', function () {
    return '';
});

function zb_get_post_excerpt($limit = '46') {
    $excerpt = get_the_excerpt();
    if (empty($excerpt)) {
        $excerpt = get_the_content();
    }

    return wp_trim_words(strip_shortcodes($excerpt), $limit, '...');
}

/**
 * 默认头像
 * @Author Dadong2g
 * @date   2023-02-13
 * @return [type]
 */
function get_default_avatar_src() {
    return get_template_directory_uri() . '/assets/img/avatar.png';
}




/**
 * 默认缩略图
 * @Author Dadong2g
 * @date   2023-09-18
 * @return [type]
 */
function get_default_thumbnail_src() {
    global $post;
    $default = _cao('default_thumb') ? _cao('default_thumb') : get_template_directory_uri() . '/assets/img/thumb.jpg';
    $rand_gallery = _cao('rand_default_thumb', '');
    $gallery_ids  = explode(',', $rand_gallery);
    if (!empty($rand_gallery) && !empty($gallery_ids)) {
        $gallery_count = count($gallery_ids);
        $iv            = intval(substr($post->ID, -1));

        if ( !isset($gallery_ids[$iv]) ) {
            $iv = mt_rand(0, $gallery_count-1);
        }
        
        if ($_thum = wp_get_attachment_image_src($gallery_ids[$iv], 'thumbnail')) {
            $thum = $_thum;
        } else {
            $thum = wp_get_attachment_image_src($gallery_ids[$iv], 'full');
        }
        if (!empty($thum[0])) {
            return $thum[0];
        }
    }
    return $default;
}


function get_default_lazy_img_src() {
    return _cao('default_lazy_thumb') ? _cao('default_lazy_thumb') : 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
}



function zb_get_thumbnail_size_type(){
    $options = array(
        'bg-cover',
        'bg-auto',
        'bg-contain',
    );
    $opt = _cao('site_thumb_size_type','bg-cover');

    if (!in_array($opt, $options)) {
        $opt = $options[0];
    }
    return $opt;
}

function zb_get_thumbnail_fit_type(){
    $options = array(
        'bg-left-top',
        'bg-right-top',
        'bg-center-top',
        'bg-center',
        'bg-center-bottom',
        'bg-left-bottom',
        'bg-right-bottom',
    );
    $opt = _cao('site_thumb_fit_type','bg-center');

    if (!in_array($opt, $options)) {
        $opt = $options[0];
    }
    return $opt;
}

/**
 * 获取缩略图地址
 * @Author Dadong2g
 * @date   2023-04-19
 * @param  [type]     $post [description]
 * @param  string     $size [description]
 * @return [type]
 */
function zb_get_thumbnail_url($post = null, $size = 'thumbnail') {

    if (empty($post)) {
        global $post;
    } else {
        $post = get_post($post);
    }

    if (!$post instanceof WP_Post) {
        return get_default_thumbnail_src();
    }

    if (has_post_thumbnail($post)) {
        return get_the_post_thumbnail_url($post, $size);
    } elseif (_cao('is_post_one_thumbnail', true) && !empty($post->post_content)) {
        // Automatically get the first image in the post content
        ob_start();
        ob_end_clean();
        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        if (!empty($matches[1][0])) {
            return $matches[1][0];
        }
    }

    return get_default_thumbnail_src();
}


//获取媒体预览地址
function zb_get_media_preview_url($post = null){
    if (empty($post)) {
        global $post;
    } else {
        $post = get_post($post);
    }

    if (!$post instanceof WP_Post) {
        return '';
    }

    $url = get_post_meta($post->ID, 'thumb_video_src', true);

    if ( empty($url) ) {
        return '';
    }

    return $url;

}



/**
 * 获取缩略图
 * @Author Dadong2g
 * @date   2022-01-06
 * @param  string     $class [description]
 * @return [type]
 */
function zb_the_thumbnail($post = null, $class = 'thumb lazy', $size = 'thumbnail') {
    if (empty($post)) {
        global $post;
    }

    if (is_numeric($post)) {
        $post = get_post($post);
    }

    echo get_the_post_thumbnail($post->ID, $size, array('class' => $class, 'alt' => the_title_attribute(array('echo' => false))));

}

/**
 * 获取列表显示风格配置
 * @Author Dadong2g
 * @date   2022-12-08
 * @return [type]
 */
function zb_get_archive_item_config($cat_id = 0) {

    $item_col   = _cao('archive_item_col', '4');
    $item_style = _cao('archive_item_style', 'grid');
    $media_size = _cao('post_thumbnail_size', 'radio-3x2');

    $media_size_type = zb_get_thumbnail_size_type();
    $media_fit_type = zb_get_thumbnail_fit_type();

    $item_entry = _cao('archive_item_entry', array(
        'category_dot',
        'entry_desc',
        'entry_footer',
        'vip_icon',
    ));

    $term_item_style = _cao('site_term_item_style', array());

    

    if (!empty($cat_id) && !empty($term_item_style)) {
        foreach ($term_item_style as $key => $item) {
            if ($cat_id == $item['cat_id']) {
                $item_col   = $item['archive_item_col'];
                $item_style = $item['archive_item_style'];
                $media_size = $item['post_thumbnail_size'];
                $item_entry = $item['archive_item_entry'];
                continue;
            }
        }
    }


    $row_cols = [
        '1' => 'row-cols-1 g-2 g-md-3 g-lg-4',
        '2' => 'row-cols-2 g-2 g-md-3 g-lg-4',
        '3' => 'row-cols-2 row-cols-md-3 g-2 g-md-3 g-lg-4',
        '4' => 'row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3 g-lg-4',
        '5' => 'row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-2 g-lg-3',
        '6' => 'row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 row-cols-xxl-6 g-2 g-lg-3',
    ];

    if ($item_style=='list' && $item_col >= 2) {
        // 列表模式自适应...
        $row_cols_class = 'row-cols-1 row-cols-md-2 g-2 g-md-3 g-lg-4';
    }else{
        $row_cols_class = $row_cols[$item_col];
    }

    $defaults = [
        'type' => 'grid', // grid  grid-overlay list
        'media_class' => 'ratio-3x2', // ratio-1x1  3x2 3x4 4x3 16x9
        'media_size_type' => 'bg-cover',
        'media_fit_type' => 'bg-center',
        'is_vip_icon' => true,
        'is_entry_cat' => true,
        'is_entry_desc' => true,
        'is_entry_meta' => true,
    ];

    $config = array(
        'type'            => $item_style, //grid grid-overlay list list-icon
        'row_cols_class'  => $row_cols_class,
        'media_size_type' => $media_size_type,
        'media_fit_type'  => $media_fit_type,
        'media_class'     => $media_size, // media-3x2 media-3x3 media-2x3
        'is_vip_icon'     => @in_array('vip_icon', $item_entry),
        'is_entry_desc'   => @in_array('entry_desc', $item_entry),
        'is_entry_meta' => @in_array('entry_footer', $item_entry),
        'is_entry_cat' => @in_array('category_dot', $item_entry),
    );

    return wp_parse_args( $config, $defaults );
}



//添加文章阅读数量
function zb_add_post_views($post_id = null) {
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    $meta_key = 'views';
    $this_num = intval(get_post_meta($post_id, $meta_key, true));
    $new_num  = $this_num + 1;
    if ($new_num < 0) {
        $new_num = 1;
    }

    return update_post_meta($post_id, $meta_key, $new_num);
}

//获取文章查看数量
function zb_get_post_views($post_id = null) {
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    $meta_key = 'views';
    $this_num = absint(get_post_meta($post_id, $meta_key, true));
    if (1000 <= $this_num) {
        $this_num = sprintf('%0.1f', $this_num / 1000) . 'K';
    }
    return $this_num;
}

//添加点赞数
function zb_add_post_likes($post_id = null, $num = 1) {
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    $meta_key = 'likes';
    $this_num = intval(get_post_meta($post_id, $meta_key, true));
    $new_num  = $this_num + $num;
    if ($new_num < 0) {
        $new_num = 1;
    }

    return update_post_meta($post_id, $meta_key, $new_num);
}

//获取点赞数
function zb_get_post_likes($post_id = null) {
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    $meta_key = 'likes';
    $num      = absint(get_post_meta($post_id, $meta_key, true));
    if (1000 <= $num) {
        $num = sprintf('%0.1f', $num / 1000) . 'K';
    }
    return $num;
}

/**
 * 收藏或喜欢点赞 follow_post
 * @Author Dadong2g
 * @date   2022-11-28
 * @param  string     $user_id [description]
 * @param  string     $to_post [description]
 */
function zb_add_post_fav($user_id = null, $post_id = 0) {

    $post_id = absint($post_id);
    if (get_post_status($post_id) === false) {
        return false;
    }
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    $meta_key = 'follow_post';
    $post_key = 'follow_num';

    $old_data = get_user_meta($user_id, $meta_key, true); # 获取...

    if (empty($old_data) || !is_array($old_data)) {
        $new_data = [];
    } else {
        $new_data = $old_data;
    }

    if (!in_array($post_id, $new_data)) {
        // 新数据 开始处理
        array_push($new_data, $post_id);
    }
    if (true) {
        $favnum  = absint(get_post_meta($post_id, $post_key, true));
        $new_num = $favnum + 1;
        update_post_meta($post_id, $post_key, $new_num);
    }

    return update_user_meta($user_id, $meta_key, $new_data);
}

function zb_delete_post_fav($user_id = null, $post_id = 0) {

    $post_id = absint($post_id);
    if (get_post_status($post_id) === false) {
        return false;
    }
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }

    $meta_key = 'follow_post';
    $post_key = 'follow_num';

    $old_data = get_user_meta($user_id, $meta_key, true); # 获取...

    if (empty($old_data) || !is_array($old_data)) {
        $new_data = [];
    } else {
        $new_data = $old_data;
    }

    if (in_array($post_id, $new_data)) {
        $new_data = array_values(array_diff($new_data, [$post_id]));
    }

    if (true) {
        $favnum  = absint(get_post_meta($post_id, $post_key, true));
        $new_num = $favnum - 1;
        if ($new_num < 0) {
            $new_num = 0;
        }

        update_post_meta($post_id, $post_key, $new_num);
    }

    return update_user_meta($user_id, $meta_key, $new_data);
}

function zb_is_post_fav($user_id = null, $post_id = null) {
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    if (empty($user_id)) {
        $user_id = get_current_user_id();
    }
    $post_id = absint($post_id);

    if (get_post_status($post_id) === false) {
        return false;
    }

    $meta_key = 'follow_post';

    $data = get_user_meta($user_id, $meta_key, true); # 获取...

    if (empty($data) || !is_array($data)) {
        return false;
    }
    return in_array($post_id, $data);
}

function zb_get_post_fav($post_id = null) {
    if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }
    $meta_key = 'follow_num';
    $num      = absint(get_post_meta($post_id, $meta_key, true));
    if (1000 <= $num) {
        $num = sprintf('%0.1f', $num / 1000) . 'K';
    }
    return $num;
}

/**
 * 面包屑导航
 * @Author   Dadong2g
 * @DateTime 2021-05-26T11:11:24+0800
 * @param    string                   $class [description]
 * @return   [type]                          [description]
 */
function zb_the_breadcrumb($class = 'breadcrumb') {
    global $post, $wp_query;
    echo '<ol class="' . $class . '"><li class=""><a href="' . home_url() . '">' . __('首页', 'ripro') . '</a></li>';

    if (is_category()) {
        $cat_obj   = $wp_query->get_queried_object();
        $thisCat   = $cat_obj->term_id;
        $thisCat   = get_category($thisCat);
        $parentCat = get_category($thisCat->parent);

        if ($thisCat->parent != 0) {
            echo zb_get_category_parents_link($parentCat, 'category');
        }

        echo '<li class="active">';
        single_cat_title();
        echo '</li>';
    } elseif (is_day()) {
        echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> </li>';
        echo '<li><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a> </li>';
        echo '<li class="active">' . get_the_time('d') . '</li>';
    } elseif (is_month()) {
        echo '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> </li>';
        echo '<li class="active">' . get_the_time('F') . '</li>';
    } elseif (is_year()) {
        echo '<li class="active">' . get_the_time('Y') . '</li>';
    } elseif (is_attachment()) {
        echo '<li class="active">' . get_the_title() . '</li>';
    } elseif (is_single()) {
        $post_type = get_post_type();
        if ($post_type == 'post') {
            $cat = get_the_category();
            $cat = isset($cat[0]) ? $cat[0] : 0;
            echo zb_get_term_parents_link($cat, 'category');
            echo '<li class="active">' . __('正文', 'ripro') . '</li>';
        } else {
            $obj = get_post_type_object($post_type);
            echo '<li class="active">';
            echo $obj->labels->singular_name;
            echo '</li>';
        }
    } elseif (is_page() && !$post->post_parent) {
        echo '<li class="active">' . get_the_title() . '</li>';
    } elseif (is_page() && $post->post_parent) {
        $parent_id   = $post->post_parent;
        $breadcrumbs = array();
        while ($parent_id) {
            $page          = get_post($parent_id);
            $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
            $parent_id     = $page->post_parent;
        }
        $breadcrumbs = array_reverse($breadcrumbs);
        foreach ($breadcrumbs as $crumb) {
            echo $crumb;
        }

        echo '<li class="active">' . get_the_title() . '</li>';
    } elseif (is_search()) {
        $kw = get_search_query();
        $kw = !empty($kw) ? $kw : '无';
        echo '<li class="active">' . sprintf(__('搜索: %s', 'ripro'), $kw) . '</li>';
    } elseif (is_tag()) {
        echo '<li class="active">';
        single_tag_title();
        echo '</li>';
    } elseif (is_author()) {
        global $author;
        $userdata = get_userdata($author);
        echo '<li class="active">' . $userdata->display_name . '</li>';
    } elseif (is_404()) {
        echo '<li class="active">404</li>';
    }

    if (get_query_var('paged')) {
        echo '<li class="active">' . sprintf(__('第%s页', 'ripro'), get_query_var('paged')) . '</li>';
    }

    echo '</ol>';
}

// 自定义分类筛选

/**
 * 根据分类id获取分类父集返回链接
 * @Author Dadong2g
 * @date   2022-12-03
 * @param  [type]     $id       [description]
 * @param  [type]     $taxonomy [description]
 * @param  array      $visited  [description]
 * @return [type]
 */
function zb_get_term_parents_link($id, $taxonomy, $visited = array()) {
    if (!$id) {
        return '';
    }
    $chain  = '';
    $parent = get_term($id, $taxonomy);
    if (is_wp_error($parent)) {
        return '';
    }
    $name = $parent->name;
    if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
        $visited[] = $parent->parent;
        $chain .= zb_get_term_parents_link($parent->parent, $taxonomy, $visited);
    }
    $chain .= '<li><a href="' . esc_url(get_category_link($parent->term_id)) . '">' . $name . '</a></li>';
    return $chain;
}

/**
 * 获取分类ID的顶级分类id
 * @Author Dadong2g
 * @date   2023-04-19
 * @param  [type]     $term_id  [description]
 * @param  string     $taxonomy [description]
 * @return [type]
 */
function zb_get_term_top_id($term_id, $taxonomy='category') {
    $ancestors = get_ancestors($term_id, $taxonomy);
    if ($ancestors) {
        $top_level_id = end($ancestors);
    } else {
        $top_level_id = $term_id;
    }
    return $top_level_id;
}



//获取无限加载按钮 click auto
function zb_get_infinite_scroll_button($nav_type = 'click') {
    $spinner_html = '<span class="spinner-grow spinner-grow-sm"></span>';
    $button_html = '<div class="infinite-scroll-button infinite-' . $nav_type . ' btn btn-dark px-5 rounded-pill"><div class="infinite-scroll-status">' . $spinner_html . '</div>' . __('加载更多', 'ripro') . '</div>';
    $msg_html = '<p class="infinite-scroll-msg text-muted">' . __('已全部加载完毕', 'ripro') . '</p>';
    return '<div class="infinite-scroll-action">' . $button_html . $msg_html . '</div>';
}


/**
 * 翻页导航
 */
function zb_pagination($args = array()) {

    $defaults  = array(
        'range'           => 4,
        'custom_query'    => false,
        'previous_string' => __('<i class="fas fa-angle-left me-1"></i>上一页', 'ripro'),
        'next_string'     => __('下一页<i class="fas fa-angle-right ms-1"></i>', 'ripro'),
        'nav_type'  => _cao('site_page_nav_type', 'click'),
        'nav_class'   => 'page-nav mt-4',
    );

    $args = wp_parse_args($args, $defaults);

    $args['range'] = (int) $args['range'] - 1;
    if (!$args['custom_query']) {
        $args['custom_query'] = @$GLOBALS['wp_query'];
    }
    
    $count = (int) $args['custom_query']->max_num_pages;
    $page  = intval(get_query_var('paged'));
    $ceil  = ceil($args['range'] / 2);

    if ($count <= 1) {
        return false;
    }

    if (!$page) {
        $page = 1;
    }

    if ($count > $args['range']) {
        if ($page <= $args['range']) {
            $min = 1;
            $max = $args['range'] + 1;
        } elseif ($page >= ($count - $ceil)) {
            $min = $count - $args['range'];
            $max = $count;
        } elseif ($page >= $args['range'] && $page < ($count - $ceil)) {
            $min = $page - $ceil;
            $max = $page + $ceil;
        }
    } else {
        $min = 1;
        $max = $count;
    }

    $echo     = '<ul class="pagination">';
    
    //页码
    $echo .= '<li class="page-item disabled"><span class="page-link">' . $page . '/' . $count . '</span></li>';

    //最前一页
    $firstpage = esc_attr(get_pagenum_link(1));
    if ($firstpage && (1 != $page)) {
        $echo .= '<li class="page-item page-first"><a class="page-link" href="' . $firstpage . '"><span title="' . __('最新一页', 'ripro') . '" aria-hidden="true">&laquo;</span></a></li>';
    }

    //上一页
    $previous = intval($page) - 1;
    $previous = esc_attr(get_pagenum_link($previous));
    if ($previous && (1 != $page)) {
        $echo .= '<li class="page-item"><a class="page-link page-previous" href="' . $previous . '">' . $args['previous_string'] . '</a></li>';
    }


    //数字页
    if (!empty($min) && !empty($max)) {
        for ($i = $min; $i <= $max; $i++) {
            if ($page == $i) {
                $echo .= '<li class="page-item active"><span class="page-link">' . (int) $i . '</span></li>';
            } else {
                $echo .= sprintf('<li class="page-item"><a class="page-link" href="%s">%d</a></li>', esc_attr(get_pagenum_link($i)), $i);
            }
        }
    }

    //下一页
    $next = intval($page) + 1;
    $next = esc_attr(get_pagenum_link($next));
    if ($next && ($count != $page)) {
        $echo .= '<li class="page-item"><a class="page-link page-next" href="' . $next . '">' . $args['next_string'] . '</a></li>';
    }

    //最后一页
    $lastpage = esc_attr(get_pagenum_link($count));
    if ($lastpage) {
        $echo .= '<li class="page-item page-last"><a class="page-link" href="' . $lastpage . '"><span title="' . __('最后一页', 'ripro') . '" aria-hidden="true">&raquo;</span></a></li>';
    }

    $echo .= '</ul>';

    //无限加载
    if ($args['nav_type'] == 'click' || $args['nav_type'] == 'auto') {
        $args['nav_class'] = $args['nav_class'] . ' infinite-scroll';
        $echo .= zb_get_infinite_scroll_button($args['nav_type']);
    }

    if (isset($echo)) {
        echo '<nav class="'.$args['nav_class'].'">' . $echo . '</nav>';
    }

}

/**
 * 自定义导航
 * @Author Dadong2g
 * @date   2022-12-02
 * @param  integer    $pagenum       [当前页面]
 * @param  integer    $max_num_pages [MAX数量]
 * @return [type]
 */
function zb_custom_pagination($pagenum = 0, $max_num_pages = 0) {

    $page_links = paginate_links(array(
        'base'      => add_query_arg('page', '%#%'),
        'format'    => '?page=%#%',
        'prev_text' => __('<i class="fas fa-angle-left me-1"></i>上一页', 'ripro'),
        'next_text' => __('下一页<i class="fas fa-angle-right ms-1"></i>', 'ripro'),
        'total'     => intval($max_num_pages),
        'current'   => intval($pagenum),
        'show_all'   => false,
    ));

    // zb_dump($page_links);

    if ($page_links) {
        echo '<nav class="custom-nav mt-3 mt-md-4"><ul class="pagination d-flex justify-content-center flex-wrap"><span class="disabled">'.__('分页', 'ripro').'</span>' . $page_links . '</ul></nav>';
    }

}

function is_weixin_visit() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断邮件服务是否开启
 * @Author Dadong2g
 * @date   2022-11-27
 * @param  string     $role [description]
 * @param  [type]     $key  [description]
 * @return [type]
 */
function site_push_server($role, $key) {
    switch ($role) {
    case 'admin':
        $opt_key = 'site_admin_push_server';
        break;
    case 'user':
        $opt_key = 'site_user_push_server';
        break;
    default:
        $opt_key = 'null';
        break;
    }
    $data = _cao($opt_key);
    if (empty($data) || !is_array($data)) {
        return false;
    }
    if (!in_array($key, $data)) {
        return false;
    }
    return true;
}

/**
 * 链接新窗口打开
 * @Author   Dadong2g
 * @DateTime 2021-09-13T20:47:01+0800
 * @return   [type]                   [description]
 */
function get_target_blank() {
    return empty(_cao('site_main_target_blank', false)) ? '' : '_blank';
}

/**
 * 获取当前页面URL
 * @Author Dadong2g
 * @date   2022-11-27
 * @return [type]
 */
if (!function_exists('get_current_url')) {
    function get_current_url() {
        $current_url = home_url(add_query_arg(array()));
        return esc_url($current_url);
    }
}

/**
 * 获取二维码地址
 * @Author Dadong2g
 * @date   2022-11-28
 * @param  [type]     $text [description]
 * @return [type]
 */
function get_qrcode_url($text) {
    $api_url = get_template_directory_uri() . '/inc/plugins/qrcode/qrcode.php?data=';
    return $api_url . $text;
}

//是否开启图片验证码功能
function is_site_img_captcha() {
    return !empty(_cao('is_site_img_captcha', 0));
}

//是否开启邮件验证码功能
function is_site_mail_captcha() {
    return !empty(_cao('is_site_mail_captcha', 0));
}


/**
 * 发送邮箱验证码
 * @Author Dadong2g
 * @date   2023-12-10
 * @param  [type]     $emali [description]
 * @return [type]
 */
function send_mail_captcha_code($emali) {
    $originalcode = '0,1,2,3,4,5,6,7,8,9';
    $originalcode = explode(',', $originalcode);
    $countdistrub = 10;
    $_dscode      = "";
    $counts       = 6;
    for ($j = 0; $j < $counts; $j++) {
        $dscode = $originalcode[rand(0, $countdistrub - 1)];
        $_dscode .= $dscode;
    }

    ZB_Cookie::set('mail_captcha_code', ZB_Code::enstr($_dscode));

    return wp_mail($emali, __('验证码','ripro'), __('验证码：','ripro') . $_dscode);

}


/**
 * 验证码邮箱验证码
 * @Author Dadong2g
 * @date   2023-12-10
 * @param  string     $string [description]
 * @return [type]
 */
function mail_captcha_verify($string = '') {
    $cache = ZB_Cookie::get('mail_captcha_code');
    $cache = ZB_Code::destr($cache);
    // ZB_Cookie::set('mail_captcha_code', '');
    if (empty($cache) || empty($string) || $cache != $string) {
        return false;
    }
    return true;
}



/**
 * 获取图片验证码
 * @Author Dadong2g
 * @date   2022-04-12
 * @return [type]
 */
function get_img_captcha() {
    $builder = new Gregwar\Captcha\CaptchaBuilder(4);
    $builder->build();
    $cache = $builder->getPhrase();
    ZB_Cookie::set('img_captcha_code', ZB_Code::enstr($cache));
    return $builder->inline();
}

function is_img_captcha($string = '') {
    $cache = ZB_Cookie::get('img_captcha_code');
    $cache = ZB_Code::destr($cache);
    // Checking that the posted phrase match the phrase stored in the session
    $is_captcha = Gregwar\Captcha\PhraseBuilder::comparePhrases($cache, $string);

    ZB_Cookie::set('img_captcha_code', '');
    if (empty($cache) || empty($string)) {
        return false;
    }
    return (bool) $is_captcha;
}

/**
 * //识别视频格式
 * @Author Dadong2g
 * @date   2023-06-05
 * @param  [type]     $video_url [description]
 * @return [type]
 */

function zb_get_video_source_types($video_url){
    if (preg_match('/\.(mp4|webm|ogg|m3u8|mpd|mp3|weba|wav|aac)(?:\?.*)?$/i', $video_url, $matches)) {
        $extension = strtolower($matches[1]);
    } else {
        $extension = 'none';
    }

    $sourceTypes = [
        'mp4' =>'video/mp4',
        'webm' =>'video/webm',
        'ogg' =>'video/ogg',
        'm3u8' =>'application/x-mpegURL',
        'mpd' =>'application/dash+xml',
        'mp3' =>'audio/mpeg',
        'wav' =>'audio/wav',
        'aac' =>'audio/aac',
        'weba' =>'audio/webm',
        'none' =>'',
    ];

    return $sourceTypes[$extension];
}



/**
 * 移除 URL 中的分页路由
 * @Author Dadong2g
 * @date   2023-06-09
 * @param  [type]     $url [description]
 * @return [type]
 */
function remove_pagination_route($url) {

  // 解析 URL，获取路径和查询参数
  $parts = parse_url($url);
  
  if (!is_array($parts)) {
    return $url;
  }

  $path = isset($parts['path']) ? $parts['path'] : '';
  $queryString = isset($parts['query']) ? $parts['query'] : '';

  if (empty($path)) {
    return $url;
  }

  // 移除分页路由（例如 /page/2）
  $pathWithoutPage = preg_replace('/\/page\/\d+/', '', $path);

  // 重新构造 URL
  $newUrl = '';
  if (isset($parts['scheme'])) {
    $newUrl .= sprintf('%s://', $parts['scheme']);
  }
  if (isset($parts['host'])) {
    $newUrl .= $parts['host'];
  }
  $newUrl .= $pathWithoutPage;
  if (!empty($queryString)) {
    $newUrl .= sprintf('?%s', $queryString);
  }

  return $newUrl;
}



/**
 * 授权页面
 * @Author Dadong2g
 * @date   2022-04-18
 * @return [type]
 */
function riadmin_license_page() {

    $is_acv = !empty($GLOBALS['ripro_is_activ']);

    $disabled = ($is_acv) ? "" : "";
    $value    = ($is_acv) ? "****************" : "";
    $button   = ($is_acv) ? "恭喜，您已成功激活本站" : "点此激活授权";

    $html = '<div class="theme-license-warp">';
    if ($is_acv) {
        $html .= '<div class="license-icon"></div>';
    }
    $html .= sprintf('<p><a href="https://ritheme.com/" target="_blank">RiTheme.com-官网</a>->会员ID</p><input id="theme_lic_id" type="text" value="%s" %s />', $value, $disabled);
    $html .= sprintf('<p><a href="https://ritheme.com/" target="_blank">RiTheme.com-官网</a>->授权码</p><input id="theme_lic_key" type="text" value="%s" %s />', $value, $disabled);

    $html .= sprintf('<p>授权信息激活后自动删除，不会储存到本地，可有效防止被盗用复制，中途修改域名需重新激活</p><button id="theme_act_btn" class="button button-primary" type="button" data-text="%s" %s>%s</button>', $button, $disabled, $button);

    $html .= '</div>';

    echo $html;

}

################################################################
