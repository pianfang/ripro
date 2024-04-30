<?php

defined('ABSPATH') || exit;

if (!current_user_can('manage_options') || !is_admin()) {
    return;
}

if (!class_exists('CSF')) {
    exit;
}

##################################################################################

$prefix = '_ripro_profile_options';

CSF::createProfileOptions($prefix, array(
    'data_type' => 'unserialize',
));

$__vip_options = _cao('site_vip_options');
if (empty($__vip_options)) {
    $__vip_options = array('no_name'=>'普通用户','vip_name'=>'包月VIP','boosvip_name'=>'永久VIP');
}

if (isset($_GET['user_id'])) {
    $user_id = absint($_GET['user_id']);
    $balance_log = get_user_meta($user_id, 'balance_log', true);
}


$balance_log_html = '<ul style="max-height: 150px;overflow: hidden;overflow-y: auto;background: #484848;color: #b1cd91;padding: 10px;border-radius: 5px;">';
if (!empty($balance_log) && is_array($balance_log)) {
    foreach ($balance_log as $item) {
        $balance_log_html .= sprintf('<li>%s ···· %s ····>[ %s%s ]····> %s</li>',$item['date'],$item['balance_before'],$item['event'],$item['amount'],$item['balance_after']);
    }
}else{
    $balance_log_html .= '<li>暂无</li>';
}
$balance_log_html .= '</ul>';


CSF::createSection($prefix, array(
    'title'  => '用户高级信息',
    'fields' => array(


        array(
            'id'      => 'cao_user_type',
            'type'    => 'select',
            'title'   => '用户VIP会员类型',
            'options' => array(
                'no'  => @$__vip_options['no_name'],
                'vip' => @$__vip_options['vip_name'],
            ),
            'default' => 'no',
        ),

        array(
            'id'       => 'cao_vip_end_time',
            'type'     => 'date',
            'title'    => '用户VIP会员到期时间',
            'desc'     => '如果要设置永久VIP会员，请把到期日期改为：9999-09-09',
            'settings' => array(
                'dateFormat' => 'yy-mm-dd', //date("Y-m-d");
            ),
        ),

        array(
            'id'         => 'cao_balance',
            'type'       => 'text',
            'title'      => '钱包余额',
            'attributes' => array(
                'readonly' => 'readonly',
            ),
            'default'    => '0',
        ),
        array(
          'title'      => '余额日志',
          'type'    => 'content',
          'content' => $balance_log_html,
        ),

        array(
            'id'    => 'cao_banned',
            'type'  => 'switcher',
            'title' => '封号该用户',
            'desc'  => '封号后无法登录操作',
        ),
        array(
            'id'         => 'cao_banned_reason',
            'type'       => 'textarea',
            'title'      => '封号原因',
            'default'    => '本站检测到您存在恶意下单，倒卖授权，传播破解行为，封号处理！',
            'sanitize'   => false,
            'dependency' => array('cao_banned', '==', 'true'),
        ),

        array(
            'id'      => 'cao_ref_from',
            'type'    => 'text',
            'title'   => '推荐人ID',
            'default' => '',
        ),

    ),

));

unset($prefix);

##################################################################################
