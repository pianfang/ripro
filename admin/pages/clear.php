<?php

defined('WPINC') || exit;

// 缓存/临时/垃圾数据清理


$page_action = get_param('action', '', 'get');

$message = '';

global $wpdb;

// clear_hook
if (isset($_GET['clear_hook'])) {

    switch (trim($_GET['clear_hook'])) {
    case 'order':
        $query   = $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->cao_order_tbl WHERE pay_status = %d AND create_time < %s", 0, strtotime("-1 day")));
        $message = sprintf('一天前所有未支付订单清理成功 (%s)个记录', number_format_i18n($query));
        break;
    case 'down':
        $query   = $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->cao_down_tbl WHERE create_time < %s", strtotime("-7 day")));
        $message = sprintf('成功清理7天前下载记录 (%s)个记录', number_format_i18n($query));
        break;
    case 'optimize':
        $query = $wpdb->get_col('SHOW TABLES');
        if ($query) {
            $tables = implode(',', $query);
            $wpdb->query("OPTIMIZE TABLE $tables");
            $message = sprintf('已成功优化数据表 %s ', number_format_i18n(count($query)));
        }
        break;
    case 'migrate_old_order_data':
        $ZB_SetupDb = new ZB_SetupDb;
        $count = $ZB_SetupDb->migrate_old_order_data();
        $message = sprintf('已成迁移数据记录 %s ', number_format_i18n($count));
        break;
    case 'migrate_old_post_meta':
        $ZB_SetupDb = new ZB_SetupDb;
        $count = $ZB_SetupDb->migrate_old_post_meta();
        $message = sprintf('已成迁移数据记录 %s ', number_format_i18n($count));
        break;
    case 'migrate_old_filter_meta':
        $ZB_SetupDb = new ZB_SetupDb;
        $count = $ZB_SetupDb->migrate_old_filter_meta();
        $message = sprintf('已成迁移数据记录 %s ', number_format_i18n($count));
    }

}

?>
<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <div class="health-check-header">
        <h1 class="m-0">数据清理优化</h1>
        <p>缓存/临时/历史数据清理，有效提升数据库负载和速度，孤立数据表示关了用户被删除，这些数据无意义</p>
        <p class="attention">清理数据记录会影响商城总览中统计数量减少和占比变化，不方便观察运营，请您谨慎操作</p>
    </div>

    <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>

    <hr class="wp-header-end">

    <div class="health-check-body">

<?php

$tables = [];



$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $wpdb->cao_order_tbl WHERE 1=%d", 1 ) );
$count2 = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $wpdb->cao_order_tbl WHERE pay_status=%d", 0 ) );

$sum = $count + $count2;

$ratio = ($sum>0) ? round(($count2 / $sum * 100)) : 0;
$d_link = ($ratio>0) ? 'admin.php?page=zb-admin-page-clear&clear_hook=order&day=1' : '';
$tables['order'] = [
    'title' => '订单清理',
    'desc' => '总计 <strong class="attention"><span>'.$count.'</span></strong> 个订单记录 <strong class="attention"><span>'.$count2.'</span> 个未支付订单</strong>。',
    'tr' => [
        [
            '未支付订单（全部）',
            $count2,
            $ratio.'%',
            $d_link,
        ]
    ],
];


$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $wpdb->cao_down_tbl WHERE 1=%d", 1 ) );
$count2 = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM $wpdb->cao_down_tbl WHERE create_time<%s",strtotime("-7 day")) );
$sum = $count + $count2;
$ratio = ($sum>0) ? round(($count2 / $sum * 100)) : 0;
$d_link = ($ratio>0) ? 'admin.php?page=zb-admin-page-clear&clear_hook=down&day=7' : '';
$tables['down'] = [
    'title' => '下载记录清理',
    'desc' => '总计 <strong class="attention"><span>'.$count.'</span></strong> 个下载记录 <strong class="attention"> 七天前下载记录<span>'.$count2.'</span> </strong>',
    'tr' => [
        [
            '七天前的下载记录',
            $count2,
            $ratio.'%',
            $d_link,
        ]
    ],
];

$count = count( $wpdb->get_col( 'SHOW TABLES' ) );
$tables['optimize'] = [
    'title' => '数据库优化',
    'desc' => '总计 <strong class="attention"><span>'.$count.'</span></strong> 个数据表，当数据很多时，每周一次或每月一次',
    'tr' => [
        [
            '数据表',
            $count,
            '100%',
            'admin.php?page=zb-admin-page-clear&clear_hook=optimize&day=1',
        ]
    ],
];


//老款数据迁移功能

$old_db = $wpdb->prefix . 'cao_order';
$is_old_db = $wpdb->get_var( "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '{$wpdb->dbname}' AND table_name = '{$old_db}'" );
$old_data_count = $wpdb->get_var( "SELECT COUNT(*) FROM $old_db WHERE status=1" );

if (!empty($is_old_db) && !empty($old_data_count)) {
    $tables['migrate_old_order_data'] = [
        'title' => '迁移旧版本订单数据',
        'desc' => '总计 <strong class="attention"><span>'.$old_data_count.'</span></strong> 只迁移支付成功的订单数据，未支付和无效订单不迁移',
        'tr' => [
            [
                '订单数据',
                $old_data_count,
                '100%',
                'admin.php?page=zb-admin-page-clear&clear_hook=migrate_old_order_data&day=1',
            ]
        ],
    ];

}


$meta_data_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = 'cao_downurl' OR meta_key = 'video_url'" );
if (!empty($meta_data_count)) {
    $tables['migrate_old_post_meta'] = [
        'title' => '迁移旧版本资源地址',
        'desc' => '总计 <strong class="attention"><span>'.$meta_data_count.'</span></strong> 每次点击处理最多1000条数据，如果还有剩余，请多点几次，只迁移旧版本中有下载地址和新版本中没有设置下载地址的文章的下载地址。以便于支持多地址下载功能和多集媒体功能',
        'tr' => [
            [
                '旧版下载地址',
                $meta_data_count,
                '100%',
                'admin.php?page=zb-admin-page-clear&clear_hook=migrate_old_post_meta&day=1',
            ]
        ],
    ];

}


$old_meta_opt = _cao_old('custom_post_meta_opt');
if (!empty($old_meta_opt) && is_array($old_meta_opt)) {
    $tables['migrate_old_filter_meta'] = [
        'title' => '迁移旧版本自定义筛选字段',
        'desc' => '总计 <strong class="attention"><span>'.count($old_meta_opt).'</span></strong> 迁移旧版本自定义筛选字段到新版本自定义分类法字段中',
        'tr' => [
            [
                '旧版筛选字段',
                count($old_meta_opt),
                '100%',
                'admin.php?page=zb-admin-page-clear&clear_hook=migrate_old_filter_meta&day=1',
            ]
        ],
    ];
}


foreach ($tables as $key => $item) {
    echo '<div class="clear-itme-warp">';
    echo sprintf('<h3>%s</h3>',$item['title']);
    echo sprintf('<p>%s</p>',$item['desc']);
    echo '<table class="widefat table-sweep">';
    echo '<thead><tr><th>详情</th><th>统计</th><th>占比</th><th>操作</th></tr></thead><tbody>';

    foreach ($item['tr'] as $iex => $tr) {
        $class = ($iex%2 != 0) ? 'class="alternate"' : 'class=""' ;
        echo '<tr '.$class.'>';
        foreach ($tr as $index => $td) {
            if ($index==0) {
                echo sprintf('<td><strong>%s</strong></td>',$td);
            }elseif ($index==3) {
                if (empty($td)) {
                    echo '<td>无需清理</td>';
                }else{
                    echo sprintf('<td><a class="button button-primary" href="%s">处理数据</a></td>',$td);
                }
            }else{
                echo sprintf('<td><span>%s</span></td>',$td);
            }
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}

?>



    </div>
    <br class="clear">
</div>
<script type="text/javascript">

jQuery(document).ready(function($){
    jQuery('a.button-primary').click(function(e) {
        return confirm('确实要对所选条目执行数据处理吗?');
    });

});
</script>
<!-- 主页面END -->
