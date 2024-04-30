<?php

/**
 * 易支付异步通知
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

if ($epay->verifyNotify()) {
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
    echo 'success'; //当支付平台接收到此消息后，将不再重复回调当前接口
}

exit();
