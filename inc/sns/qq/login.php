<?php

defined('WPINC') || exit;

if (!_cao('is_sns_qq')) {
    zb_wp_die('未开启QQ登录功能', '非法访问');exit;
}

$opt = _cao('sns_qq');

if (empty($opt)) {
    zb_wp_die('请配置QQ登录参数', '配置错误');exit;
}

$Config = array(
    'app_id'     => trim($opt['app_id']),
    'app_secret' => trim($opt['app_secret']),
    'scope'      => 'get_user_info',
    'callback'   => get_oauth_permalink('qq','callback'),
);

$OAuth = new \Yurun\OAuthLogin\QQ\OAuth2($Config['app_id'], $Config['app_secret'], $Config['callback']);
$OAuth->state = 'state';
$url   = $OAuth->getAuthUrl();


header('location:' . $url);
exit;