<?php

/**
 * 虎皮椒异步通知
 */

header('Content-type:text/html; Charset=utf-8');

$_Config = _cao('hupijiao_weixin');
if (empty($_Config['app_id']) || empty($_Config['app_secret']) || empty($_POST)) {
    exit('error');
}

// 接收异步通知,无需关注验签动作,已自动处理
require_once get_template_directory() . '/inc/plugins/hupijiao/hupijiao.class.php';

$PayConfig = array_merge(array(
    'app_id'     => '',
    'app_secret' => '',
    'api_url'    => '',
), $_Config);

$Hupijiao = new Hupijiao($PayConfig);

if ($Hupijiao->checkResponse($_POST)) {
    //商户本地订单号
    $out_trade_no = $_POST['trade_order_id'];
    //交易号
    $trade_no = $_POST['transaction_id'];
    //发送支付成功回调
    ZB_Shop::pay_notfiy_callback($out_trade_no, $trade_no);

    echo 'success'; //当支付平台接收到此消息后，将不再重复回调当前接口
}

exit();
