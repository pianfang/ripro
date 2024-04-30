<?php

defined('WPINC') || exit;

if (!_cao('is_sns_weixin')) {
    zb_wp_die('未开启微信登录功能', '非法访问');exit;
}

$opt = _cao('sns_weixin');

if (empty($opt)) {
    zb_wp_die('请配置微信登录参数', '配置错误');exit;
}

$Config = array(
    'app_id'     => trim($opt['app_id']),
    'app_secret' => trim($opt['app_secret']),
    'scope'      => null,
    'callback'   => get_oauth_permalink('weixin','callback'),
);

$OAuth = new \Yurun\OAuthLogin\Weixin\OAuth2($Config['app_id'], $Config['app_secret']);
$OAuth->openidMode = Yurun\OAuthLogin\Weixin\OpenidMode::UNION_ID_FIRST;

try {
    //验证AccessToken是否有效
    $AccessToken = $OAuth->getAccessToken('state', null, 'state');
} catch (Exception $e) {
    zb_wp_die('获取接口信息失败' ,$e->getMessage());die;
}


//获取格式化后的第三方用户信息
$data = $OAuth->getUserInfo();

if (empty($OAuth->openid)) {
    zb_wp_die('登录失败', '未获取到第三方平台返回的用户信息');
}

//构参数 固定格式 切勿修改
$snsInfo = [
    'openid'   => $OAuth->openid,
    'name'     => $data['nickname'],
    'avatar'   => $data['headimgurl'],
    'method' => 'weixin', //qq weixin
];

//处理本地业务逻辑
zb_oauth_callback_event($snsInfo);

exit;
