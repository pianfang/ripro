<?php

// 站内币余额 balance
// 折扣比例 discount_rate
// 推广奖励比例 aff_rate



//是否开启网站公告
function is_site_notify() {
    return !empty(_cao('is_site_notify', 1));
}

function is_site_notify_auto() {
    return is_site_notify() && !empty(_cao('is_site_notify_auto', 1));
}


//是否开启商城功能
function is_site_shop() {
    return _cao('site_shop_mod', 'all') !== 'close';
}

//是否开启卡密购买系统 cdk 
function is_site_cdk_shop() {
    return false;
    return !empty(_cao('is_site_cdk_shop', 1));
}

//是否开启签到功能
function is_site_qiandao() {
    return !empty(_cao('is_site_qiandao', 1));
}

//是否开启投稿
function is_site_tougao() {
    return !empty(_cao('is_site_tougao', 1));
}

//是否开启评论
function is_site_comments() {
    return !empty(_cao('is_site_comments', 1));
}

//是否开工单
function is_site_tickets() {
    return !empty(_cao('is_site_tickets', 1));
}


//是否开工单
function is_site_tags_page() {
    return !empty(_cao('is_site_tags_page', 1));
}

//是否开工单
function is_site_link_manager_page() {
    return !empty(_cao('is_site_link_manager_page', 1));
}

//独立VIP介绍页面
function is_site_vip_price_page() {
    return !empty(_cao('is_site_vip_price_page', 1));
}


//获取商城模式 [close , all , user_mod]
function get_site_shop_mod() {
    return _cao('site_shop_mod', 'all');
}


//是否开启免登录购买功能
function is_site_not_user_pay() {
    return get_site_shop_mod() == 'all';
}


//是否开启登录
function is_site_user_login() {
    return (bool) _cao('is_site_user_login', true);
}

//是否开启注册
function is_site_user_register() {
    return (bool) _cao('is_site_user_register', true);
}

//是否开启邀请码注册
function is_site_invitecode_register() {
    return (bool) _cao('is_site_invitecode_register', true);
}



//是否开启了分类筛选
function is_site_term_filter() {
    return (bool) _cao('is_site_term_filter', true);
}

//是否开启推广
function is_site_user_aff() {
    return (bool) _cao('is_site_aff', true);
}

//获取网站佣金比例
function get_site_user_aff_rate() {
    $ratio = (float) _cao('site_aff_ratio', 0);
    if ($ratio >= 1) {
        $ratio = 0;
    }
    if ($ratio <= 0) {
        $ratio = 0;
    }
    return $ratio;
}


//是否开启作者佣金
function is_site_author_aff() {
    return (bool) _cao('is_site_author_aff', true);
}

//获取网站作者佣金比例
function get_site_author_aff_rate() {
    $ratio = (float) _cao('site_author_aff_ratio', 0);
    if ($ratio >= 1) {
        $ratio = 0;
    }
    if ($ratio <= 0) {
        $ratio = 0;
    }
    return $ratio;
}

//获取网站商城会员组配置
function get_site_vip_options() {
    $options     = _cao('site_vip_options', array());
    $vip_options = array();

    if (empty($options)) {
        return $vip_options;
    }
    //定义天数
    $vip_group = ['no','vip','boosvip'];

    foreach ($vip_group as $key) {
        $name = (isset($options[$key . '_name'])) ? $options[$key . '_name'] : '';
        $downnum = (isset($options[$key . '_downnum'])) ? $options[$key . '_downnum'] : 0;
        $desc = (isset($options[$key . '_desc'])) ? $options[$key . '_desc'] : '';
        $desc = empty($desc) ? [] : explode("\n", $desc);
        $vip_options[$key] = [
            'key'     => $key, //标识
            'name'    => esc_html($name), //名称
            'desc'    => $desc, //介绍
            'downnum' => absint($downnum), //下载次数
        ];
    }
    return $vip_options;
}



//获取网站会员开通套餐
function get_site_vip_buy_options() {
    $options = _cao('site_vip_buy_options', array());
    $buy_options = array();

    if (empty($options) && !is_array($buy_options)) {
        return $buy_options;
    }
    
    $site_vip_options = get_site_vip_options();

    foreach ($options as $item) {

        $title = (!empty($item['title'])) ? esc_html($item['title']) : false;
        $day_num = (!empty($item['daynum'])) ? absint($item['daynum']) : false;
        $coin_price = (!empty($item['price'])) ? abs(floatval($item['price'])) : false;
        $type = $site_vip_options['vip']['key'];
        $name = $site_vip_options['vip']['name'];
        $desc = $site_vip_options['vip']['desc'];
        $downnum = $site_vip_options['vip']['downnum'];

        //永久套餐
        if ($day_num==9999) {
            $type = $site_vip_options['boosvip']['key'];
            $name = $site_vip_options['boosvip']['name'];
            $desc = $site_vip_options['boosvip']['desc'];
            $downnum = $site_vip_options['boosvip']['downnum'];
        }

        if ($title && $day_num && $coin_price) {
            $buy_options[$day_num] = [
                'type'           => $type,
                'name'           => $name,
                'desc'           => $desc,
                'downnum'        => $downnum,
                'buy_title'      => $title,
                'day_num'        => $day_num,
                'coin_price'     => $coin_price,
            ];
        }
    }

    return $buy_options;
}


//站内币名称
function get_site_coin_name(){
    return esc_html(_cao('site_coin_name','金币'));
}

//站内币兑RMB汇率
function get_site_coin_rate(){
    return absint( _cao('site_coin_rate','10') );
}

//站内币图标
function get_site_coin_icon(){
    return esc_html(_cao('site_coin_icon','fas fa-coins'));
}

//币种金额换算
function site_convert_amount($amount = 0, $type = 'coin') {
    // RMB汇率
    $coin_rate = get_site_coin_rate();
    switch ($type) {
    case 'coin':
        $amount = $amount * $coin_rate;
        break;
    case 'rmb':
        $amount = $amount / $coin_rate;
        break;
    default:
        $amount = $amount;
        break;
    }
    return (float) $amount;
}



//获取用户到期时间
function get_user_vip_end_date($user_id) {
    $vip_options  = get_site_vip_options();
    $user_type = get_user_meta($user_id, 'cao_user_type', true);
    $current_date = wp_date('Y-m-d');
    if (empty($user_type) || !isset($vip_options[$user_type])) {
        return $current_date;
    }
    $vip_end_date = get_user_meta($user_id, 'cao_vip_end_time', true);
    if (strtotime($vip_end_date)) {
        return $vip_end_date;
    }
    return $current_date;
}

//获取当前用户VIP类型
function get_user_vip_type($user_id) {
    $vip_options = get_site_vip_options();
    $user_type   = get_user_meta($user_id, 'cao_user_type', true);
    if (empty($user_type) || !isset($vip_options[$user_type])) {
        return $vip_options['no']['key'];
    }
    $current_date = wp_date('Y-m-d');
    $vip_end_date = get_user_vip_end_date($user_id);
    $end_time     = strtotime($vip_end_date);
    $current_time = strtotime($current_date);

    if (!$end_time) {
        $end_time = $current_time;
    }

    if ($user_type === 'vip' && $vip_end_date === '9999-09-09') {
        return $vip_options['boosvip']['key'];
    }

    if ($user_type === 'vip' && $end_time > $current_time) {
        return $vip_options['vip']['key'];
    }

    return $vip_options['no']['key'];
}

//获取用户VIP数据
function get_user_vip_data($user_id) {
    $vip_options = get_site_vip_options();
    $user_type   = get_user_vip_type($user_id);
    //今日可下载次数
    $downnum_total = $vip_options[$user_type]['downnum'];
    //今日已下载次数
    //更新下载次数缓存
    wp_cache_delete('user_today_down_num_' . $user_id);
    $downnum_used = ZB_Down::get_user_today_down_num($user_id);
    $downnum_not  = $downnum_total - $downnum_used;
    $downnum_not = ($downnum_not>=0) ? $downnum_not : 0 ;

    $data         = [
        'name'     => $vip_options[$user_type]['name'],
        'type'     => $vip_options[$user_type]['key'],
        'end_date' => get_user_vip_end_date($user_id),
        'downnums' => ['total' => $downnum_total, 'used' => $downnum_used, 'not' => $downnum_not],
    ];
    return $data;
}

//更新用户VIP数据信息
function update_user_vip_data($user_id, $new_day = '0') {

    $user_id = intval($user_id);
    $vip_options = get_site_vip_options();
    $vip_buy_options = get_site_vip_buy_options();
    $new_day = absint($new_day);

    if (empty($new_day)) {
        $new_type = 'no';
        $new_day = 0;
    }else{
        $new_type = $vip_buy_options[$new_day]['type'];
        $new_day =  $vip_buy_options[$new_day]['day_num'];
    }
    

    // 获取用户当前VIP信息
    $current_vip_data = get_user_vip_data($user_id);
    $current_vip_type = $current_vip_data['type']; //当前类型
    $current_end_date = $current_vip_data['end_date']; //

    //降级vip
    if ($current_end_date == '9999-09-09' && $new_type = 'vip') {
        $current_end_date = wp_date('Y-m-d');
    }

    if ($current_vip_type == 'no') {
        $current_end_date = wp_date('Y-m-d');
    }

    //计算时差秒数
    $diff_seconds     = time() - current_time('timestamp');
    $current_end_time = strtotime($current_end_date) + $diff_seconds;
    $new_end_time     = $current_end_time + ($new_day * 24 * 60 * 60);
    $new_vip_type     = $new_type;

    if ($new_type == 'boosvip') {
        $new_vip_type = 'vip';
        $new_end_date = "9999-09-09"; //永久
    }elseif ($new_type == 'no') {
        $new_vip_type = 'no';
        $new_end_date = wp_date('Y-m-d');
    }else{
        $new_vip_type = 'vip';
        $new_end_date = wp_date('Y-m-d', $new_end_time); //新到期时间
    }
    
    // 更新数据
    $update_type = update_user_meta($user_id, 'cao_user_type', $new_vip_type);
    $update_endtime = update_user_meta($user_id, 'cao_vip_end_time', $new_end_date);

    $status = ($update_type || $update_endtime) ? true : false;
    return $status;
}

//获取用户余额 balance
function get_user_coin_balance($user_id) {
    $current_balance = get_user_meta($user_id, 'cao_balance', true);
    
    // 如果当前余额未设置，默认为0
    if (empty($current_balance)) {
        $current_balance = 0;
    }

    return abs($current_balance);
}

//更新用户余额 balance [+ 充值 - 消费扣减]
function change_user_coin_balance($user_id, $amount, $change_type) {
    // 检查变更类型是否有效
    if (!in_array($change_type, array('+', '-'))) {
        return false;
    }
    $amount = abs($amount);
    // 获取当前余额和余额变更记录
    $current_balance = get_user_coin_balance($user_id);
    $balance_before = $current_balance;

    $balance_log = get_user_meta($user_id, 'balance_log', true);
    
    if (empty($balance_log) || !is_array($balance_log)) {
        $balance_log = array();
    }
    // 根据变更类型更新余额和余额变更记录
    if ($change_type == '+') {
        $current_balance += $amount;
        $event = '+';
    } else {
        if ($current_balance < $amount) { 
            // 余额不足
            return false;
        }
        
        $current_balance -= $amount;
        $event = '-';
    }
    $new_log_item = array(
        'date' => wp_date('Y-m-d H:i:s'),
        'event' => $event,
        'amount' => $amount,
        'balance_before' => $balance_before,
        'balance_after' => $current_balance
    );
    
    array_unshift($balance_log, $new_log_item);
    // 更新用户余额和余额变更记录
    update_user_meta($user_id, 'cao_balance', $current_balance);
    update_user_meta($user_id, 'balance_log', $balance_log);
    return true;
}



//获取用户的推广链接
function get_user_aff_permalink($link_url, $user_id = null) {
    if (empty($user_id)) {
        global $current_user;
        $user_id = $current_user->ID;
    }
    if (empty($user_id)) {
        return $link_url;
    }

    $url = esc_url_raw(
        add_query_arg(
            array(
                'aff'  => $user_id,
            ),
            $link_url
        )
    );
    return $url;
}







// 文章是否下载资源文章
function post_is_down_pay($post_id) {
    $price = get_post_meta($post_id, 'cao_price', true);
    $status = get_post_meta($post_id, 'cao_status', true);

    if (is_numeric($price) && !empty($status)) {
        return true;
    }
    return false;
}

//文章是否有付费查看内容
function post_is_hide_pay($post_id) {
    
    $price = get_post_meta($post_id, 'cao_price', true);

    $content = get_post_field('post_content', $post_id);

    if (is_numeric($price) && has_shortcode($content, 'rihide')) {
        return true;
    }
    return false;
}

//文章是否有付费播放视频内容
function post_is_video_pay($post_id) {
    
    $price = get_post_meta($post_id, 'cao_price', true);
    $status = get_post_meta($post_id, 'cao_video', true);

    if (is_numeric($price) && !empty($status)) {
        return true;
    }
    return false;

}

// 文章是否有付费资源
function post_is_pay($post_id) {
    if (post_is_down_pay($post_id) || post_is_hide_pay($post_id) || post_is_video_pay($post_id)) {
        return true;
    }
    return false;
}

//是否vip资源
function post_is_vip_pay($post_id) {

    if (!post_is_pay($post_id)) {
        return false;
    }

    $prices = get_post_price_data($post_id);

    if ($prices['default'] != $prices['vip'] || $prices['default'] != $prices['boosvip'] ) {
        return true;
    }

    return false;
}

//



//获取文章加密下载地址
function get_post_endown_url($post_id, $down_key) {
    $nonce     = wp_create_nonce('zb_down');
    $down_str  = $post_id . '-' . $down_key . '-' . $nonce;
    $down_stat = ZB_Code::enstr($down_str);
    return home_url('/goto?down=' . $down_stat);
}


//获取文章价格权限信息
function get_post_pay_data($post_id) {
    $price = get_post_meta($post_id, 'cao_price', true);
    $vip_rate = get_post_meta($post_id, 'cao_vip_rate', true);
    $boosvip_free = get_post_meta($post_id, 'cao_is_boosvip', true);
    $disable_no_buy = get_post_meta($post_id, 'cao_close_novip_pay', true);
    $sales_count = get_post_meta($post_id, 'cao_paynum', true);

    if (!is_numeric($price)) {
        $price = 0;
    }

    if (!is_numeric($vip_rate)) {
        $vip_rate = 1;
    }elseif ($vip_rate < 0) {
        $vip_rate = 0;
    } elseif ($vip_rate > 1) {
        $vip_rate = 1;
    } else {
        $vip_rate = floor($vip_rate * 100) / 100;
    }

    $data = [
        'coin_price' => abs(floatval($price)),
        'vip_rate' => $vip_rate,
        'boosvip_free' => empty($boosvip_free) ? 0 : 1,
        'disable_no_buy' => empty($disable_no_buy) ? 0 : 1,
        'sales_count' => absint($sales_count),
    ];
    return $data;
}


//获取文章价格信息 单位：站内币 0免费 false不可购买
function get_post_price_data($post_id) {
    $data = get_post_pay_data($post_id);
    $post_price = $data['coin_price'];

    $coin_price = floatval($data['coin_price']);

    $prices = [
        'default' => $coin_price, //原价
        'no' => $coin_price,
        'vip' => $coin_price,
        'boosvip' => $coin_price,
    ];

    if ($data['disable_no_buy']) {
        $prices['no'] = false;
    }

    if (isset($data['vip_rate'])) {
        $prices['vip'] = $coin_price * $data['vip_rate'];
        $prices['boosvip'] = $coin_price * $data['vip_rate'];
    }
    
    if ($data['boosvip_free']) {
        $prices['boosvip'] = 0;
    }

    return (array)$prices;
}


//根据用户id获取用户购买文章实际价格
function get_user_pay_post_price($user_id, $post_id) {
    $post_prices = get_post_price_data($post_id);
    $user_type   = get_user_vip_type($user_id);
    return $post_prices[$user_type];
}


//用户是否已购买或者可免费获取
function get_user_pay_post_status($user_id, $post_id) {
    

    if (empty($user_id)) {
        $pay_status = ZB_Shop::get_pay_post_status($user_id, $post_id);
    }else{
        $cache_key = 'pay_post_status_' . $user_id . '_' . $post_id;
        // wp_cache_set($cache_key, 0);
        $pay_status = wp_cache_get($cache_key);

        if (false === $pay_status) {
            //查询订单状态 免登录用户不走缓存
            $pay_status = ZB_Shop::get_pay_post_status($user_id, $post_id);
            wp_cache_set($cache_key, $pay_status);
        }
    }

    if ($pay_status > 0) {
        return 1;
    }

    //判断价格权限方式
    $price = get_user_pay_post_price($user_id, $post_id);
    if ($price === false) {
        return false;
    }elseif ($price == 0) {
        return true;
    }else{
        return false;
    }
    
}

//用户是否评论过文章
function get_user_has_commented($user_id = 0,$post_id = 0) {
    $has_commented = false;
    if ($user_id) {
        $user = get_userdata($user_id);
        $user_email = $user->user_email;
    } elseif (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
        $user_email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
    } else {
        $user_email = null;
    }

    if (!empty($user_email)) {
        global $wpdb;
        $comment_query = $wpdb->prepare("SELECT comment_ID FROM {$wpdb->comments} WHERE comment_post_ID = %d AND comment_author_email = %s LIMIT 1",$post_id, $user_email);
        $has_commented = (bool) $wpdb->get_var($comment_query);
    }

    return $has_commented;
}

//今日是否已签到
function is_user_today_qiandao($user_id) {

    // 会员当前签到时间
    $qiandao_time = get_user_meta($user_id, 'cao_qiandao_time', true);

    if (empty($qiandao_time)) {
        $qiandao_time = 0;
    }

    $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']

    if ($today_time['start'] < $qiandao_time && $today_time['end'] > $qiandao_time) {
        return true;
    }
    return false;
}


//获取购买按钮
function zb_get_pay_button($post_id = 0, $order_type = 0, $order_info = '', $button_name = '') {
    $post_id        = (int) $post_id;
    $order_type     = (int) $order_type;
    $button_class   = "btn btn-danger-soft js-pay-action";
    $button_icon    = ($order_type == 2) ? "far fa-gem" : "fab fa-shopify";
    $button_name = "<i class=\"$button_icon me-1\"></i>$button_name";
    $html           = "<button class=\"$button_class\" data-id=\"$post_id\" data-type=\"$order_type\" data-info=\"$order_info\">$button_name</button>";
    return $html;
}

//获取用户VIP类型标志
function zb_get_user_badge($user_id = null, $tag = 'a', $class = '') {
    //颜色配置
    $colors = [
        'no'        => 'secondary',
        'vip'     => 'success',
        'boosvip' => 'warning',
    ];
    $data  = get_user_vip_data($user_id);


    $color = $colors[$data['type']];
    $name  = $data['name'];
    $link  = get_uc_menu_link('vip');
    // 构建HTML代码
    
    if ($data['type'] != 'no') {
        $badge_title   = $data['end_date'] . __('到期', 'ripro');
    }else{
        $badge_title   = '';
    }

    $badge_class   = "badge bg-$color text-$color bg-opacity-10 $class";
    $badge_content = "<i class=\"far fa-gem me-1\"></i>$name";
    $badge_html    = "<$tag class=\"$badge_class\">$badge_content</$tag>";
    if ($tag == 'a') {
        return "<$tag title=\"$badge_title\" class=\"$badge_class\" href=\"$link\">$badge_content</$tag>";
    } else {
        return "<$tag title=\"$badge_title\" class=\"$badge_class\">$badge_content</$tag>";
    }
}

//根据会员类型获取角标
function zb_get_vip_badge($vip_type = 'no', $tag = 'span', $class = '') {
    //颜色配置
    $colors = [
        'no'        => 'secondary',
        'vip'     => 'success',
        'boosvip' => 'warning',
    ];
    $vip_options = get_site_vip_options();
    $color       = $colors[$vip_type];
    $name        = $vip_options[$vip_type]['name'];
    // 构建HTML代码
    $badge_class   = "badge bg-$color text-$color bg-opacity-10 $class";
    $badge_content = "<i class=\"far fa-gem me-1\"></i>$name";
    $badge_html    = "<$tag class=\"$badge_class\">$badge_content</$tag>";
    if ($tag == 'a') {
        $link  = get_uc_menu_link('vip');
        return "<$tag class=\"$badge_class\" href=\"$link\">$badge_content</$tag>";
    } else {
        return "<$tag class=\"$badge_class\">$badge_content</$tag>";
    }
}

/**
 * 获取VIP购买页面地址
 * @Author Dadong2g
 * @date   2022-11-29
 * @return [type]
 */
function get_vip_page_permalink() {
    return esc_url(home_url('/vip-prices'));
}


/**
 * 获取第三方登录地址
 * @Author Dadong2g
 * @date   2023-04-07
 * @param  string     $method   [description]
 * @param  boolean    $callback [description]
 * @return [type]
 */
function get_oauth_permalink($method = 'qq', $callback = false) {
    if (!in_array($method, array('qq', 'weixin'))) {
        $method = 'qq';
    }
    $callback = (!empty($callback)) ? '/callback' : '';
    return esc_url(home_url('/oauth/' . $method . $callback));
}

/**
 * 第三方登录回调事件处理
 * @Author Dadong2g
 * @date   2023-04-09
 * @param  [type]     $snsInfo [description]
 * @return [type]
 */
function zb_oauth_callback_event($data) {
    global $wpdb;

    $current_uid = get_current_user_id(); //当前用户
    // 查询meta关联的用户
    $meta_key   = 'open_' . $data['method'] . '_openid';
    $search_uid = $wpdb->get_var(
        $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value=%s", $meta_key, $data['openid'])
    );

    // 如果当前用户已登录，而$search_user存在，即该开放平台账号连接被其他用户占用了，不能再重复绑定了
    if (!empty($current_uid) && !empty($search_uid) && $current_uid != $search_uid) {
        zb_wp_die(
            __('绑定失败', 'ripro'),
            __('当前用户之前已有其他账号绑定，请先登录其他账户解绑，或者激活已经使用该方式登录的账号！', 'ripro')
        );
    }

    if (!empty($search_uid) && empty($current_uid)) {
        // 该开放平台账号已连接过WP系统，再次使用它直接登录
        $user = get_user_by('id', $search_uid);
        if ($user) {
            zb_updete_user_oauth_info($user->ID, $data);

            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID, true);
            do_action('wp_login', $user->user_login, $user);
            wp_safe_redirect(get_uc_menu_link());exit;
        }
    } elseif (!empty($current_uid) && empty($search_uid)) {
        //当前已登录了本地账号, 直接绑定该账号
        zb_updete_user_oauth_info($current_uid, $data);
        wp_safe_redirect(get_uc_menu_link());exit;

    } elseif (empty($search_uid) && empty($current_uid)) {
        //新用户注册
        $new_user_data = array(
            'user_login'   => $data['method'] . mt_rand(1000, 9999) . mt_rand(1000, 9999),
            'user_email'   => "",
            'display_name' => $data['name'],
            'nickname'     => $data['name'],
            'user_pass'    => md5($data['openid']),
            'role'         => get_option('default_role'),
        );

        $new_user = wp_insert_user($new_user_data);

        if (is_wp_error($new_user)) {
            zb_wp_die(__('新用户注册失败', 'ripro'), $new_user->get_error_message());
        } else {
            //登陆当前用户
            $user = get_user_by('id', $new_user);
            if ($user) {
                zb_updete_user_oauth_info($user->ID, $data);
                update_user_meta($user->ID, 'user_avatar_type', $data['method']); //更新默认头像

                wp_set_current_user($user->ID, $user->user_login);
                wp_set_auth_cookie($user->ID, true);
                do_action('wp_login', $user->user_login, $user);
                wp_safe_redirect(get_uc_menu_link());exit;
            }
        }
    }

}

/**
 * 用户是否第三方注册未设置密码
 * @Author Dadong2g
 * @date   2023-06-30
 * @param  [type]     $user_id [description]
 * @return [type]
 */
function user_is_oauth_password($user_id){
    $config = array('qq','weixin');
    $user = get_user_by('id', $user_id);
    
    foreach ($config as $type) {
        $meta_key   = 'open_' . $type. '_openid';
        $p2 = get_user_meta($user_id,$meta_key,true);
        if (!empty($p2) && wp_check_password(md5($p2), $user->data->user_pass, $user->ID )) {
            return true;
        }
    }
}


/**
 * 更新用户oauth信息
 * @Author Dadong2g
 * @date   2023-04-09
 * @param  [type]     $user_id [description]
 * @param  [type]     $data    [description]
 * @return [type]
 */
function zb_updete_user_oauth_info($user_id, $data) {
    $meta = 'open_' . $data['method'];
    unset($data['method']);
    foreach ($data as $key => $value) {
        if (!empty($value)) {
            $meta_key = $meta . '_' . $key;
            update_user_meta($user_id, $meta_key, $value);
        }
    }
    return true;
}

/**
 * 获取个人中心菜单
 * @Author Dadong2g
 * @date   2022-09-30
 * @param  [type]     $menu_part [description]
 * @return [type]
 */
function get_uc_menus($menu_part = null) {

    $default = 'profile';

    $part_tpl = array(
        'profile' => ['title' => __('基本信息', 'ripro'), 'desc' => '', 'icon' => 'far fa-user'],
        'coin'    => ['title' => __('我的余额', 'ripro'), 'desc' => '', 'icon' => get_site_coin_icon()],
        'vip'     => ['title' => __('我的会员', 'ripro'), 'desc' => '', 'icon' => 'fas fa-gem'],
        'order'   => ['title' => __('我的订单', 'ripro'), 'desc' => '', 'icon' => 'fab fa-shopify'],
        'down'    => ['title' => __('下载记录', 'ripro'), 'desc' => '', 'icon' => 'fas fa-cloud-download-alt'],
        'fav'     => ['title' => __('我的收藏', 'ripro'), 'desc' => '', 'icon' => 'fas fa-star'],
        'aff'     => ['title' => __('我的推广', 'ripro'), 'desc' => '', 'icon' => 'fas fa-hand-holding-usd'],
        'ticket'  => ['title' => __('我的工单', 'ripro'), 'desc' => '', 'icon' => 'fas fa-question-circle'],
        'tougao'  => ['title' => __('我的投稿', 'ripro'), 'desc' => '', 'icon' => 'fas fa-edit'],
        'logout'  => ['title' => __('退出登录', 'ripro'), 'desc' => '', 'icon' => 'fas fa-sign-out-alt'],
    );

    if (!is_site_shop()) {
        unset($part_tpl['coin']);
        unset($part_tpl['vip']);
        unset($part_tpl['order']);
        unset($part_tpl['down']);
        unset($part_tpl['aff']);
    }

    if (!is_site_user_aff()) {
        unset($part_tpl['aff']);
    }

    if (!is_site_tickets()) {
        unset($part_tpl['ticket']);
    }

    if (!is_site_tougao()) {
        unset($part_tpl['tougao']);
    }

    if ($menu_part !== null) {
        $menu_part = (array_key_exists($menu_part, $part_tpl)) ? $menu_part : $default;
        return $part_tpl[$menu_part];
    }

    return $part_tpl;
}

function get_uc_menu_link($menu_action = '') {
    $prefix = '/user/';
    if ($menu_action == 'logout') {
        return esc_url(wp_logout_url(get_current_url()));
    }
    return esc_url(home_url($prefix . $menu_action));
}

/**
 * 获取网站中当前推荐人信息
 * @Author Dadong2g
 * @date   2022-12-04
 * @param  integer    $user_id [description]
 * @return [ref_id]
 */
function zb_get_site_current_aff_id($user_id = 0) {
    $return_uid = 0;
    $aff_id     = (int) ZB_Cookie::get('aff'); //链接中的推荐人
    $ref_id     = (int) get_user_meta($user_id, 'cao_ref_from', true); //推荐人
    if (!empty($ref_id)) {
        $return_uid = $ref_id;
    } elseif (!empty($aff_id)) {
        $return_uid = $aff_id;
    }
    if ($return_uid == $user_id) {
        $return_uid = 0;
    }
    return absint($return_uid);
}



//获取工单状态
function zb_get_ticket_tbl_status($data) {
    $data = intval($data);
    //工单状态，0：未处理，1：处理中，2：已解决，3：已关闭
    switch ($data) {
    case '-1':
        return __('失效', 'ripro');
        break;
    case '0':
        return __('待回复', 'ripro');
        break;
    case '1':
        return __('处理中', 'ripro');
        break;
    case '2':
        return __('已回复', 'ripro');
        break;
    case '3':
        return __('已关闭', 'ripro');
        break;
    default:
        return __('其他', 'ripro');
        break;
    }
}

function zb_get_ticket_tbl_type($data) {
    $data = intval($data);
    //状态 0 无 1资源问题 2会员问题 3网站BUG 4其他问题
    switch ($data) {
    case '1':
        return __('资源问题', 'ripro');
        break;
    case '2':
        return __('会员问题', 'ripro');
        break;
    case '3':
        return __('网站BUG', 'ripro');
        break;
    case '4':
        return __('其他问题', 'ripro');
        break;
    default:
        return __('其他', 'ripro');
        break;
    }
}


function zb_get_cdk_tbl_status($data) {
    $data = intval($data);
    switch ($data) {
    case '-1':
        return __('失效', 'ripro');
        break;
    case '0':
        return __('未使用', 'ripro');
        break;
    case '1':
        return __('已使用', 'ripro');
        break;
    default:
        return __('异常', 'ripro');
        break;
    }
}


function zb_get_cdk_tbl_type($data) {
    $data = intval($data);
    switch ($data) {
    case '0':
        return __('无', 'ripro');
        break;
    case '1':
        return __('余额充值卡', 'ripro');
    case '2':
        return __('会员兑换卡', 'ripro');
        break;
    case '3':
        return __('注册邀请码', 'ripro');
        break;
    default:
        return __('无', 'ripro');
        break;
    }
}


function zb_get_aff_tbl_status($data) {
    $data = intval($data);
    switch ($data) {
    case '-1':
        return __('失效', 'ripro');
        break;
    case '0':
        return __('未提现', 'ripro');
        break;
    case '1':
        return __('提现中', 'ripro');
        break;
    case '2':
        return __('已提现', 'ripro');
        break;
    default:
        return __('异常', 'ripro');
        break;
    }
}


//获取订单支付状态
function zb_get_order_tbl_status($data) {
    $data = intval($data);
    switch ($data) {
    case '-1':
        return __('失效', 'ripro');
        break;
    case '0':
        return __('未支付', 'ripro');
        break;
    case '1':
        return __('支付成功', 'ripro');
        break;
    default:
        return __('异常', 'ripro');
        break;
    }
}

function zb_get_order_tbl_type($data) {
    $data = intval($data);
    switch ($data) {
    case '0':
        return __('其他订单', 'ripro');
        break;
    case '1':
        return __('文章订单', 'ripro');
        break;
    case '2':
        return __('充值订单', 'ripro');
        break;
    case '3':
        return __('VIP订单', 'ripro');
        break;
    case '4':
        return __('其他订单', 'ripro');
        break;
    default:
        return __('无', 'ripro');
        break;
    }
}


//获取支付配置数据config
function zb_get_pay_optons($id = null) {
    $config = [
        1  => ['id' => 'alipay', 'name' => '官方-支付宝', 'is' => (bool) _cao('is_alipay')],
        2  => ['id' => 'weixinpay', 'name' => '官方-微信', 'is' => (bool) _cao('is_weixinpay')],

        11 => ['id' => 'hupijiao_alipay', 'name' => '虎皮椒-支付宝', 'is' => (bool) _cao('is_hupijiao_alipay')],
        12 => ['id' => 'hupijiao_weixin', 'name' => '虎皮椒-微信', 'is' => (bool) _cao('is_hupijiao_weixin')],

        21  => ['id' => 'xunhu_alipay', 'name' => '讯虎-支付宝', 'is' => (bool) _cao('is_xunhupay_alipay')],
        22  => ['id' => 'xunhu_weixin', 'name' => '讯虎-微信', 'is' => (bool) _cao('is_xunhupay_weixin')],

        31  => ['id' => 'payjs_alipay', 'name' => 'PAYJS-支付宝', 'is' => (bool) _cao('is_payjs_alipay')],
        32  => ['id' => 'payjs_weixin', 'name' => 'PAYJS-微信', 'is' => (bool) _cao('is_payjs_weixin')],

        41  => ['id' => 'epay_alipay', 'name' => '易支付-支付宝', 'is' => (bool) _cao('is_epay_alipay')],
        42 =>  ['id' => 'epay_weixin', 'name' => '易支付-微信', 'is' => (bool) _cao('is_epay_weixin')],

        55  => ['id' => 'paypal', 'name' => 'PayPal', 'is' => (bool) _cao('is_paypal')],

        66  => ['id' => 'manualpay', 'name' => '手工支付', 'is' => (bool) _cao('is_manualpay')],
        77  => ['id' => 'site_admin_charge', 'name' => __('后台充值', 'ripro'), 'is' => false],
        88  => ['id' => 'site_cdk_pay', 'name' => __('卡密支付', 'ripro'), 'is' => false],
        99  => ['id' => 'site_coin_pay', 'name' => __('余额支付', 'ripro'), 'is' => (bool) _cao('is_site_coin_pay')],

    ];
    $options = apply_filters('ri_pay_optons', $config);
    if ($id !== null && isset($options[$id])) {
        return $options[$id];
    }
    return $options;
}

//获取支付方式选项模板
function zb_get_pay_select_html($order_type=0) {
    $data = zb_get_pay_optons();
    $html = '<div class="pay-select-box">';
    $str  = array('虎皮椒-', '讯虎-', 'PayJS-', '易支付-', '码支付-', '官方-');
    // $str  = array(''); //去掉注释开启显示支付前缀

    if ($order_type ==2 || !is_user_logged_in()) {
        // 2充值订单 去掉余额支付
        unset($data[99]);
    }

    if ($order_type ==3 && !empty(_cao('is_pay_vip_allow_oline',false))) {
        // VIP订单关闭余额支付...
        unset($data[99]);
    }


    foreach ($data as $id => $item) {
        if (empty($item['is'])) {
            continue;
        }
        $name = str_replace($str, "", $item['name']);
        $html .= sprintf('<div class="pay-item" id="%s" data-id="%s"><i class="%s"></i><span>%s</span></div>', $item['id'], $id, $item['id'], $name);
    }
    $html .= '</div>';
    return apply_filters('ri_pay_select_html', $html);
}

//获取支付弹窗内容
function zb_get_pay_body_html($id, $price, $qrimg) {
    //分组
    $alipay_group    = [1, 11, 21, 31, 41];
    $weixinpay_group = [2, 12, 22, 32, 42];


    if (in_array($id, $alipay_group)) {
        # alipay
        $icon_url = get_template_directory_uri() . '/assets/img/alipay.png';
        $title    = sprintf(__('支付宝扫码支付 %s 元', 'ripro'), $price);
    } elseif (in_array($id, $weixinpay_group)) {
        # weixinpay
        $icon_url = get_template_directory_uri() . '/assets/img/weixinpay.png';
        $title    = sprintf(__('微信扫码支付 %s 元', 'ripro'), $price);
    } else {
        $icon_url = '';
        $title    = sprintf(__('扫码支付 %s 元', 'ripro'), $price);
    }

    $desc = __('支付后请等待 5 秒左右，切勿关闭扫码窗口', 'ripro');
    $html = sprintf('<div class="pay-body-html"><img class="pay-icon" src="%s"><div class="title">%s</div><div class="qrcode"><img src="%s"></div><div class="desc">%s</div></div>', $icon_url, $title, $qrimg, $desc);
    return apply_filters('ri_pay_body_html', $html);
}

/**
 * 发起支付请求接口 add_filter( 'ri_get_request_pay','test_func',10,3);
 * 如需二开或自行修改接入其他支付方式，可直接在此函数内拦截或者修改源码返回对应json格式数据即可
 * @Author Dadong2g
 * @date   2023-05-17
 * @param  [type]     $order_data  [description]
 * @return [type]
 */
function zb_get_request_pay($order_data) {


    $result = [
        'status' => 0, //状态
        'method' => 'popup', // popup|弹窗  url|跳转 jsapi|js方法
        'num'    => $order_data['order_trade_no'], //订单号
        'msg'    => __('支付接口未配置', 'ripro'), //
    ];

    $pay_optons = zb_get_pay_optons($order_data['pay_type']);

    if (empty($pay_optons['is']) || ($order_data['order_type']==2 && $pay_optons['id']=='site_coin_pay')) {
        $result['msg'] = __('支付接口暂未开启', 'ripro');
        return $result;
    }

    if ($order_data['order_type'] ==3 && $pay_optons['id']=='site_coin_pay' && !empty(_cao('is_pay_vip_allow_oline',false))) {
        // VIP订单关闭余额支付...
        $result['msg'] = __('支付接口暂未开启', 'ripro');
        return $result;
    }

    //订单IP地址
    $order_info       = maybe_unserialize($order_data['order_info']);
    $order_data['ip'] = $order_info['ip'];

    // 在线支付类 alipay weixinpay xunhu_alipay xunhu_weixin paypal
    $ZB_Pay = new ZB_Pay();

    switch ($pay_optons['id']) {

    case 'site_coin_pay':
        # 余额支付类
        $user_id = get_current_user_id();
        $coin_amount = site_convert_amount($order_data['pay_price'], 'coin');
        $user_coin_balance = get_user_coin_balance($user_id);

        usleep(500000);
        
        if ($coin_amount > $user_coin_balance) {
            $result['msg'] = get_site_coin_name() . __('余额不足', 'ripro');
            return $result;
        }

        if (!change_user_coin_balance($user_id, $coin_amount, '-')) {
            $result['msg'] = __('余额支付失败', 'ripro');
            return $result;
        }

        //处理回调
        $trade_no = '99-' . time(); // 时间戳
        $update_order = ZB_Shop::pay_notfiy_callback($order_data['order_trade_no'], $trade_no);

        if (!$update_order) {
            $result['msg'] = __('订单状态处理异常', 'ripro');
            return $result;
        }else{
            return [
                'status' => 1, //状态
                'method' => 'reload', // popup|弹窗  url|跳转 reload|刷新 jsapi|js方法
                'num'    => $order_data['order_trade_no'], //订单号
                'msg'    => __('支付成功', 'ripro'), //
            ];
        }

        break;
    case 'alipay':
        # 支付宝官方...
        $config   = _cao('alipay');
        $api_type = (isset($config['api_type'])) ? $config['api_type'] : '';

        if ($api_type == 'web') {
            $result['method'] = 'url';
            $pay_url          = wp_is_mobile() && !empty($config['is_mobile']) ? $ZB_Pay->alipay_app_wap_pay($order_data) : $ZB_Pay->alipay_app_web_pay($order_data);

        } elseif ($api_type == 'qr') {
            $pay_url = $ZB_Pay->alipay_app_qr_pay($order_data);
            $pay_url = zb_get_pay_body_html($order_data['pay_type'], $order_data['pay_price'], get_qrcode_url($pay_url));
        }

        break;

    case 'weixinpay':
        # 微信官方...
        $config = _cao('weixinpay');

        if (wp_is_mobile() && !empty($config['is_mobile']) && !is_weixin_visit()) {
            $result['method'] = 'url';
            $pay_url = $ZB_Pay->weixin_h5_pay($order_data);
        } else {
            $pay_url = $ZB_Pay->weixin_qr_pay($order_data);
            $pay_url = zb_get_pay_body_html($order_data['pay_type'], $order_data['pay_price'], get_qrcode_url($pay_url));
        }

        break;

    case 'hupijiao_alipay':
    case 'hupijiao_weixin':
        #虎皮椒
        $the_pay_type = ($pay_optons['id'] == 'hupijiao_weixin') ? 'wechat' : 'alipay';
        $date         = $ZB_Pay->hupijiao_pay($order_data, $the_pay_type);

        if (wp_is_mobile()) {
            $result['method'] = 'url';
            $pay_url          = $date['url'];
        } else {
            $pay_url = zb_get_pay_body_html($order_data['pay_type'], $order_data['pay_price'], $date['url_qrcode']);
        }

        break;

    case 'xunhu_alipay':
    case 'xunhu_weixin':
        #讯虎支付
        $the_pay_type = ($pay_optons['id'] == 'xunhu_weixin') ? 'wechat' : 'alipay';
        $date         = $ZB_Pay->new_xunhu_pay($order_data, $the_pay_type);

        if (!empty($date['h5'])) {
            $result['method'] = 'url';
            $pay_url          = $date['h5'];
        } elseif (!empty($date['qrcode'])) {
            $pay_url = $date['qrcode'];
            $pay_url = zb_get_pay_body_html($order_data['pay_type'], $order_data['pay_price'], get_qrcode_url($pay_url));
        }

        break;

    case 'epay_alipay':
    case 'epay_weixin':
        #彩虹易支付
        $the_pay_type = ($pay_optons['id'] == 'epay_weixin') ? 'wxpay' : 'alipay';
        $date         = $ZB_Pay->epay_pay($order_data, $the_pay_type);
        if (!empty($date)) {
            #payurl
            $result['method'] = 'url';
            $pay_url = $date;
        }
        break;

    case 'paypal':
        # paypal...
        $result['method'] = 'url';
        $pay_url          = $ZB_Pay->paypal_pay($order_data);
        break;

    default:
        break;
    }

    //设置当前订单号缓存
    if (!empty($pay_url)) {
        ZB_Cookie::set('current_order_num', $order_data['order_trade_no'], 300);
        $result['status'] = 1;
        $result['msg']    = $pay_url;
    }

    return apply_filters('ri_get_request_pay', $result, $order_data);
}

/**
 * 支付成功后处理订单
 * @Author Dadong2g
 * @date   2023-04-22
 * @param  [type]     $order [description]
 * @return [type]
 */
function zb_pay_request_success($order) {

    if (empty($order) || empty($order->pay_status)) {
        return false;
    }

    // 处理订单业务逻辑 1 => 'Post',2 => 'VIP',3 => 'Other'
    
    // 订单其他信息
    $order_info = maybe_unserialize($order->order_info);

    if ($order->order_type == 1) {
        // 文章订单
        $sales_count = absint(get_post_meta($order->post_id, 'cao_paynum', true));
        $sales_count++;
        $update = update_post_meta( $order->post_id, 'cao_paynum', $sales_count);

        // 更新购买状态缓存
        wp_cache_delete('pay_post_status_' . $order->user_id . '_' . $order->post_id);

        
        //作者佣金 非本人购买发放
        $post = get_post($order->post_id);
        $author_id = (int) $post->post_author;
        if (is_site_author_aff() && $author_id && $order->user_id != $post->post_author) {
            $aff_rate = get_site_author_aff_rate(); //网站佣金比例
            $aff_money = sprintf('%0.2f', $order->pay_price * $aff_rate);//收益
            $param = [
                'order_id'    => $order->id, //关联订单表id
                'aff_uid'     => $author_id,
                'aff_rate'    => $aff_rate, //佣金比例
                'note'        => 'author',//佣金备注说明 aff推广佣金 invite  作者收入 author
                'status'      => 0,
            ];
            //添加佣金记录
            $add = ZB_Aff::add_aff_log($param);

        }

        //添加网站动态
        ZB_Dynamic::add([
            'info' => sprintf( __('成功购买了%s', 'ripro'),get_the_title( $order->post_id )),
            'uid' => $order->user_id,
            'href' => get_the_permalink( $order->post_id ),
        ]);


    }elseif ($order->order_type == 2) {
        // 充值订单...
        if (in_array($order->pay_type, array(77,99))) {
            // 后台 卡密充值 余额支付.
            return false;
        }

        if ($order->pay_price > 0) {
            $recharge_num = site_convert_amount($order->pay_price,'coin');
            $recharge = change_user_coin_balance($order->user_id, $recharge_num, '+');
        }

        //添加网站动态
        ZB_Dynamic::add([
            'info' => sprintf( __('成功充值%s%s', 'ripro'),$recharge_num,get_site_coin_name()),
            'uid' => $order->user_id,
            'href' => get_uc_menu_link('coin'),
        ]);



    }elseif ($order->order_type == 3 && isset($order_info['vip_type'])) {
        // VIP订单...
        $uc_vip_info = get_user_vip_data($order->user_id);

        
        if ($uc_vip_info['type'] != 'boosvip') {

            //更新用户会员状态
            $update = update_user_vip_data($order->user_id, $order_info['vip_day']);
        }

        $site_vip_options = get_site_vip_options();

        //添加网站动态
        ZB_Dynamic::add([
            'info' => sprintf( __('成功开通了本站%s', 'ripro'),$site_vip_options[$order_info['vip_type']]['name']),
            'uid' => $order->user_id,
            'href' => home_url('/vip-prices'),
        ]);
        
    }

    // 根据AFFID处理推荐佣金 前置条件 本人购买本人订单无佣金 
    if (is_site_user_aff() && !empty($order_info['aff_id']) && $order_info['aff_id'] != $order->user_id) {

        // 后台 卡密充值 余额支付不计算佣金
        if (!in_array($order->pay_type, array(77,88,99))) {
            $aff_rate = get_site_user_aff_rate(); //网站佣金比例
            $aff_money = sprintf('%0.2f', $order->pay_price * $aff_rate);//收益
            $param = [
                'order_id'    => $order->id, //关联订单表id
                'aff_uid'     => absint($order_info['aff_id']), //推荐人ID
                'aff_rate'    => $aff_rate, //佣金比例
                'note'        => 'invite',//佣金备注说明 aff推广佣金 invite  作者收入 author
                'status'      => 0,
            ];
            //添加佣金记录
            $add = ZB_Aff::add_aff_log($param);

            //添加网站动态
            ZB_Dynamic::add([
                'info' => sprintf( __('推广成功，获得佣金奖励%s', 'ripro'),$aff_money),
                'uid' => $param['aff_uid'],
                'href' => get_the_permalink( $order->post_id ),
            ]);
        }
    }

    

    //发送消息推送"\n"
    if (site_push_server('admin', 'vip_pay')) {
        do_action('zb_send_mail_msg', [
            'email' => get_bloginfo('admin_email'),
            'title' => __('新订单付款成功提醒', 'ripro'),
            'msg'   => sprintf(__('订单金额：%s，</br>详细信息请在网站后台查看订单信息', 'ripro'), $order->pay_price),
        ]);
    }

}
add_action('site_pay_order_success', 'zb_pay_request_success', 10, 1);

################################################################
