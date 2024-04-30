<?php

/**
 * 讯虎支付异步通知 weixinpay
 */

header('Content-type:text/html; Charset=utf-8');

$_Config = _cao('xunhupay_weixin');
if (empty($_Config['mchid']) || empty($_Config['private_key']) || empty(file_get_contents('php://input'))) {
    exit('error');
}

// 接收异步通知,无需关注验签动作,已自动处理
require_once get_template_directory() . '/inc/plugins/xunhupay/xhpay.class.php';

$xhpay = new Xhpay($_Config);
$data  = $xhpay->getNotify();

if ($data['return_code'] == 'SUCCESS') {
    //商户本地订单号
    $out_trade_no = $data['out_trade_no'];
    //交易号
    $trade_no = $data['transaction_id'];
    //发送支付成功回调
    ZB_Shop::pay_notfiy_callback($out_trade_no, $trade_no);

    echo 'success'; //当支付平台接收到此消息后，将不再重复回调当前接口
} else {
    echo $data['msg'];
}

exit();
