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
$OAuth->state = 'state';

$url = $OAuth->getAuthUrl($Config['callback'], null, null);

header('location:' . $url);

exit;