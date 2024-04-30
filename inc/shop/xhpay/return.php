<?php

/**
 * 讯虎支付同步通知 同步查询模式
 */

header('Content-type:text/html; Charset=utf-8');

if (empty($_GET['pay_type']) || empty($_GET['out_trade_no'])) {
    wp_redirect(home_url());exit;
}

if ($_GET['pay_type'] == 'alipay') {
    $_Config = _cao('xunhupay_alipay');
} elseif ($_GET['pay_type'] == 'wechat') {
    $_Config = _cao('xunhupay_weixin');
}

if (empty($_Config['mchid']) || empty($_Config['private_key'])) {
    exit('error');
}

require_once get_template_directory() . '/inc/plugins/xunhupay/xhpay.class.php';

$xhpay = new Xhpay($_Config);

$data = $xhpay->sign(array('out_trade_no' => $_GET['out_trade_no']));

try {
    $result = $xhpay->query($data);
} catch (Exception $e) {
    // exit;
}

if (isset($result['return_code']) && isset($result['status']) && isset($result['sign'])) {

    if ($xhpay->checkSign($result) === true && $result['return_code'] == 'SUCCESS' && $result['status'] == 'complete') {

        //商户本地订单号
        $out_trade_no = $result['out_trade_no'];
        //讯虎平台交易号
        $trade_no = $result['order_id'];
        //发送支付成功回调
        ZB_Shop::pay_notfiy_callback($out_trade_no, $trade_no);

    }

}

//商户本地订单号
$order = ZB_Shop::get_order(wp_unslash($result['out_trade_no']));

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