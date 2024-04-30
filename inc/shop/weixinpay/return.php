<?php

/**
 * 微信官方同步通知
 */

header('Content-type:text/html; Charset=utf-8');

//商户本地订单号
$out_trade_no = $_GET['out_trade_no'];

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
