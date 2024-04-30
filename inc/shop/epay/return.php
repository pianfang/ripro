<?php

/**
 * 易支付同步通知
 */

header('Content-type:text/html; Charset=utf-8');

$pay_type = $_GET['type'];
switch ($pay_type) {
case 'wxpay':
    $_Config = _cao('epay_weixin', array());
    break;
case 'alipay':
    $_Config = _cao('epay_alipay', array());
    break;
default:
    $_Config = array();
    break;
}

if (empty($_Config['pid']) || empty($_Config['key'])) {
    exit('error');
}

// 接收异步通知,无需关注验签动作,已自动处理
require_once get_template_directory() . '/inc/plugins/epay/EpayCore.class.php';

$PayConfig = array_merge(array(
    'pid'    => '',
    'key'    => '',
    'apiurl' => '',
), $_Config);

$epay = new EpayCore($PayConfig);

if ($epay->verifyReturn()) {
    //商户本地订单号
    $out_trade_no = $_GET['out_trade_no'];
    //交易号
    $trade_no = $_GET['trade_no'];

    if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
        // 获取订单信息
        $order_info = ZB_Shop::get_order(wp_unslash($out_trade_no));

        if ($order_info && $order_info->pay_price == $_GET['money']) {
            #验证金额 发送支付成功回调
            ZB_Shop::pay_notfiy_callback($out_trade_no, $trade_no);
        }

    }


    $order = ZB_Shop::get_order(wp_unslash($out_trade_no));
    if ($order) {
        if ($order->order_type == 1) {
            $back_url = get_permalink($order->post_id);
        } elseif ($order->order_type == 2) {
            $back_url = get_uc_menu_link('coin');
        } elseif ($order->order_type == 3) {
            $back_url = get_uc_menu_link('vip');
        } else {
            $back_url = home_url();
        }
    }
    wp_redirect($back_url);exit;
    
}

wp_redirect(home_url());exit;
