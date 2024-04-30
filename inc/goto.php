<?php

defined('ABSPATH') || exit;

############################ 验证数据是否合法 ############################
if (empty($_GET) || !(isset($_GET['down']) || isset($_GET['url']))) {
    wp_safe_redirect(home_url());exit;
}



############################ 内链跳转URL模式 ############################
if (isset($_GET['url']) && !isset($_GET['down'])) {

    $url = urldecode($_GET['url']);

    $parse_url = parse_url($url);
    $url_host = (!empty($parse_url['host'])) ? $parse_url['host'] : false;

    if (!empty($url_host) && $url_host != $_SERVER['HTTP_HOST']) {
        $url_html = '<p>'.__('安全性未知，点击Url跳转', 'ripro').'<p/><a href="'.$url.'">'.$url.'</a>';
        zb_wp_die(__('即将跳转到外部网站', 'ripro'),$url_html,'close');exit;
    }

    wp_redirect($url);exit;
}



############################ 下载文件模式 ############################
if (isset($_GET['down']) && !isset($_GET['url'])) {

    $down_str = trim(wp_unslash(sanitize_text_field($_GET['down'])));

    $down_str = ZB_Code::destr($down_str);

    $down_info = (array) explode('-', $down_str);


    if (!isset($down_info) || !isset($down_info[0]) || !isset($down_info[1]) || !wp_verify_nonce($down_info[2], 'zb_down')) {
        zb_wp_die(
            __('非法请求', 'ripro'),
            __('非法访问', 'ripro')
        );exit;
    }


    $post_id = intval($down_info[0]);
    $down_key = intval($down_info[1]);

    //文章不存在或未发布
    if (get_post_status($post_id) !== 'publish') {
        zb_wp_die(
            __('资源不存在或维护中', 'ripro'),
            __('请返回其他页面重试', 'ripro'),
            get_permalink($post_id)
        );exit;
    }

    $user_id = get_current_user_id(); ####

    // 用户是否已购买或者可免费获取
    $down_status = get_user_pay_post_status($user_id, $post_id);


    if (!$down_status) {
        zb_wp_die(
            __('暂无下载权限', 'ripro'),
            __('请购买后下载本资源', 'ripro'),
            get_permalink($post_id)
        );exit;
    }

    
    // 未登录用户跳转登录页面 // 免费资源下载无需登录
    if (empty($user_id) && !is_numeric($down_status) && empty(_cao('is_free_nologin_down', 1))) {
        wp_safe_redirect(wp_login_url(get_permalink($post_id)));exit;
    }



    if ($down_status===true) {
        //通过权限下载的资源 判断次数
        $user_vip_data = get_user_vip_data($user_id);
        $not_down_num = $user_vip_data['downnums']['not']; //今日剩余下载次数
        // 更新下载次数缓存
        wp_cache_delete('user_today_down_num_' . $user_id);
        $today_downed = ZB_Down::get_today_post_downnum($user_id, $post_id); //今日是否下载过本资源次数

        //免费资源下载无需计次
        if ( empty($user_id) && !empty(_cao('is_free_nologin_down', 1)) ) {
            $today_downed = $not_down_num = 1;
        }

        if (!$today_downed && !$not_down_num) {
            //今日没有下载过本资源并且剩余下载次数为0
            zb_wp_die(
                __('下载次数超限', 'ripro'),
                __('请明日再来或升级套餐', 'ripro'),
                get_permalink($post_id)
            );exit;
        }
    }



    //权限判断完毕 处理下载文件逻辑
    $down_info = get_post_meta( $post_id, 'cao_downurl_new', true);

    if (!isset($down_info[$down_key]['url']) || empty($down_info[$down_key]['url'])) {
        zb_wp_die(
            __('下载链接丢失', 'ripro'),
            __('请联系管理员检查下载地址', 'ripro'),
            get_permalink($post_id)
        );exit;
    }

    $down_url = urldecode( $down_info[$down_key]['url'] ); //赋值下载地址
    //添加下载记录
    $down_log = [
        'user_id' => $user_id,
        'post_id' => $post_id,
        'ip'      => get_ip_address(),
        'note'    => $down_info[$down_key]['name'],
    ];

    if (!ZB_Down::add_down_log($down_log)) {
        zb_wp_die(
            __('下载记录添加失败', 'ripro'),
            __('请返回刷新重试', 'ripro'),
            get_permalink($post_id)
        );exit;
    }

    // 更新下载次数缓存
    wp_cache_delete('user_today_down_num_' . $user_id);


    //添加网站动态
    ZB_Dynamic::add([
        'info' => sprintf( __('下载了%s', 'ripro'),get_the_title( $post_id ) ),
        'uid' => $user_id,
        'href' => get_the_permalink( $post_id ),
    ]);
    
    // 处理下载地址
    $parse_url = wp_parse_url( $down_url );

    // 相对地址处理
    if (!isset($parse_url['scheme']) && !isset($parse_url['host'])) {
        $down_url = esc_url_raw( home_url( $down_url ) );
    }

    if ( isset($parse_url['scheme']) && !in_array($parse_url['scheme'],array('http','https')) ) {
        //其他协议地址bt等非http协议
        echo "<script>var win = window.open('{$down_url}', '_self'); setTimeout(function() { win.close(); }, 5000); </script>";exit;

    }elseif (wp_parse_url( $down_url, PHP_URL_HOST ) === wp_parse_url( home_url(), PHP_URL_HOST )) {

        // 获取链接URL的文件扩展名
        $url_extension = pathinfo($down_url, PATHINFO_EXTENSION);

        if ($url_extension==='') {
            wp_redirect($down_url);exit; //非文件类url站内链接跳转
        }
        
        // 站内文件地址...
        $file_name = str_replace( home_url(), ABSPATH, $down_url ); // 将URL转换为文件路径

        if (!is_file($file_name)) {
            zb_wp_die(
                __('下载文件丢失', 'ripro'),
                __('请联系管理员检查文件是否存在', 'ripro'),
                get_permalink($post_id)
            );exit;
        }
        //成功输出下载
        ZB_Down::local_download_file($file_name);
    }else{
        //外链跳转
        wp_redirect(html_entity_decode($down_url));exit;
    }

}

exit;

############################ END ############################