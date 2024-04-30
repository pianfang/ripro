<?php
defined('WPINC') || exit;
###########################################

$ZB_UI  = new ZB_UI();
$screen = $ZB_UI->screen;

$QueryPayType = urldecode(get_param('query-pay-type', 'all', 'get')); // all online
if ($QueryPayType=='all') {
    // code...
}

global $pay_type_not_in;
$pay_type_not_in = ($QueryPayType=='all') ? '0' : '99,88,77';



?>


<div class="wrap zb-admin-page">
	<h1 class="wp-heading-inline">商城管理总览/仪表盘</h1>

    <?php if ($QueryPayType=='all') {
        echo '<a class="add-new-h2" href="'.add_query_arg('query-pay-type','online').'">仅统计在线支付方式收入</a>';
    }else{
        echo '<a class="add-new-h2" href="'.add_query_arg('query-pay-type','all').'">统计全部支付方式收入</a>';
    }?>
    
    <hr class="wp-header-end">

	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);?>
			<div class="postbox-container">
				<?php do_meta_boxes($screen->id, 'column1', null);?>
			</div>
			<div class="postbox-container">
				<?php do_meta_boxes($screen->id, 'column2', null);?>
			</div>
			<div class='postbox-container'>
				<?php do_meta_boxes($screen->id, 'column3', null);?>
			</div>
			<div class='postbox-container'>
				<?php do_meta_boxes($screen->id, 'column4', null);?>
			</div>
            <div style="float: left;width: 100%;"></div>
			<div class='postbox-container zb-postbox-container'>
				<?php do_meta_boxes($screen->id, 'normal', null);?>
			</div>
			<div class='postbox-container zb-postbox-container'>
				<?php do_meta_boxes($screen->id, 'side', null);?>
			</div>

		</div>
		<div class="clear"></div>
	</div>
</div><!-- wrap -->



<?php

class ZB_UI {

    public $screen;
    public function __construct() {
        wp_enqueue_script('dashboard');
        if (wp_is_mobile()) {
            wp_enqueue_script('jquery-touch-punch');
        }
        $this->screen = get_current_screen();
        $this->reg_box();
    }

    //添加小工具
    public function reg_box() {

        // column1 column2 column3 normal  side

        add_meta_box(
            'widget-1', sprintf('<span>今日订单统计（%s）</span>', wp_date('Y-m-d')),
            array(&$this, 'top_widget_1'),
            $this->screen->id,
            'column1'
        );

        add_meta_box(
            'widget-6', '<span>全站销量排行</span>',
            array(&$this, 'top_widget_6'),
            $this->screen->id,
            'column1'
        );

        add_meta_box(
            'widget-2', sprintf('<span>今日下载统计（%s）</span>', wp_date('Y-m-d')),
            array(&$this, 'top_widget_2'),
            $this->screen->id,
            'column2'
        );

        add_meta_box(
            'widget-7', '<span>全站下载排行</span>',
            array(&$this, 'top_widget_7'),
            $this->screen->id,
            'column2'
        );

        add_meta_box(
            'widget-3', sprintf('<span>今日推广统计（%s）</span>', wp_date('Y-m-d')),
            array(&$this, 'top_widget_3'),
            $this->screen->id,
            'column3'
        );

         add_meta_box(
            'widget-8', '<span>全站推广排行</span>',
            array(&$this, 'top_widget_8'),
            $this->screen->id,
            'column3'
        );

        add_meta_box(
            'widget-4', sprintf('<span>今日用户统计（%s）</span>', wp_date('Y-m-d')),
            array(&$this, 'top_widget_4'),
            $this->screen->id,
            'column4'
        );

        add_meta_box(
            'widget-5', '<span>全站用户余额排行</span>',
            array(&$this, 'top_widget_5'),
            $this->screen->id,
            'column4'
        );

        add_meta_box(
            'side-1', sprintf('<span>年度销售统计图表（%s）总览</span>', wp_date('Y年1月~m月')),
            array(&$this, 'widget_1'),
            $this->screen->id,
            'side'
        );

        add_meta_box(
            'normal-1', sprintf('<span>本月销售统计图表（%s）总览</span>', wp_date('Y年m月')),
            array(&$this, 'widget_2'),
            $this->screen->id,
            'normal'
        );

        

    }

    
    //今日订单统计
    public function top_widget_1() {
        $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']
        $res = $this->query_pay_info_for_time($today_time['start'],$today_time['end']);
        $price1 = $price2 = $count1 = $count2 = 0;
        foreach ($res as $key => $v) {
            if ($v->pay_status == 0) {
                $price1 = $v->pay_price;
                $count1 = $v->count;
            } elseif ($v->pay_status == 1) {
                $price2 = $v->pay_price;
                $count2 = $v->count;
            }
        }
        $sumcount = $count1 + $count2;
        $ratio = ($sumcount > 0) ? round(($count2 / $sumcount * 100)) : 0;
        $data = [
            ['name' => '总订单数', 'value' => sprintf('%s条', ($count1 + $count2))],
            ['name' => '订单总额', 'value' => sprintf('￥%s', ($price2 + $price1))],
            ['name' => '付款率', 'value' => sprintf('%0.1f%%', $ratio)],
        ];
        ob_start(); ?>
        <div class="card-body text-center">
            <h4 class="m-0"><span class="text-primary"></span>今日已付款 (<?php echo $count2;?>/条)</h4>
            <h2 class="text-dark number-font" style="color:#FF9800;">￥<?php echo $price2;?></h2>
            <div class="progress mt-4">
                <div class="progress-bar progress-bar-striped bg-primary" style="width:<?php printf('%0.1f%%',$ratio );?>;background-color:#6c5ffc;" role="progressbar"></div>
            </div>
            <div class="row mt-4">
                <?php foreach ($data as $item) { 
                    echo '<div class="col text-center">';
                    echo '<span class="text-muted">'.$item['name'].'</span><h4 class="number-font">'.$item['value'].'</h4>';
                    echo '</div>';
                }?>
            </div>
        </div>
        <?php echo ob_get_clean();

    }

    

    // 今日下载统计
    public function top_widget_2() {
        global $wpdb;
        $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']
        $res = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->cao_down_tbl} WHERE create_time BETWEEN %s AND %s ", $today_time['start'],$today_time['end'])
        );

        $res2 = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(DISTINCT post_id) FROM {$wpdb->cao_down_tbl} WHERE create_time BETWEEN %s AND %s ", $today_time['start'],$today_time['end'])
        );
        $res3 = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(DISTINCT user_id) FROM {$wpdb->cao_down_tbl} WHERE create_time BETWEEN %s AND %s ", $today_time['start'],$today_time['end'])
        );

        $ratio = ($res > 0) ? round(((1-$res2 / $res) * 100)) : 0;

        $data = [
            ['name' => '下载总次数', 'value' => sprintf('%d次', $res)],
            ['name' => '下载用户数', 'value' => sprintf('%d位', $res3)],
            ['name' => '重复下载率', 'value' => sprintf('%0.1f%%', $ratio)],
        ];
        ob_start(); ?>
        <div class="card-body text-center">
            <h4 class="m-0"><span class="text-primary"></span>今日被下载产品数</h4>
            <h2 class="text-dark number-font" style="color:#CDDC39;"><?php echo $res2;?> 个</h2>
            <div class="progress mt-4">
                <div class="progress-bar progress-bar-striped bg-primary" style="width:<?php printf('%0.1f%%',$ratio );?>;background-color:#4caf50;" role="progressbar"></div>
            </div>
            <div class="row mt-4">
                <?php foreach ($data as $item) { 
                    echo '<div class="col text-center">';
                    echo '<span class="text-muted">'.$item['name'].'</span><h4 class="number-font">'.$item['value'].'</h4>';
                    echo '</div>';
                }?>
            </div>
        </div>
        <?php echo ob_get_clean();

    }

    // 今日推广中心
    public function top_widget_3() {

        global $wpdb;
        $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']
        $res = $wpdb->get_results(
            $wpdb->prepare("SELECT status,COUNT(id) as count FROM {$wpdb->cao_aff_tbl} WHERE create_time BETWEEN %s AND %s GROUP BY status ORDER BY status ASC", $today_time['start'],$today_time['end'])
        );

        $count1 = $count2 = $count3 =  0;
        foreach ($res as $key => $v) {
            if ($v->status == 0) {
                $count1 = $v->count;//未提现
            } elseif ($v->status == 1) {
                $count2 = $v->count;//提现中
            } elseif ($v->status == 2) {
                $count3 = $v->count;//已提现
            }
        }

        $sumcount = $count1 + $count2 + $count3;
        $ratio = ($sumcount > 0) ? round(($count2 / $sumcount * 100)) : 0;
        $data = [
            ['name' => '未提现', 'value' => sprintf('%d单', $count1)],
            ['name' => '申请提现', 'value' => sprintf('%d单', $count2)],
            ['name' => '提现率', 'value' => sprintf('%0.1f%%', $ratio)],
        ];
        ob_start(); ?>
        <div class="card-body text-center">
            <h4 class="m-0"><span class="text-primary"></span>今日推广单数</h4>
            <h2 class="text-dark number-font" style="color:#f16c99;"><?php echo $sumcount;?> 单</h2>
            <div class="progress mt-4">
                <div class="progress-bar progress-bar-striped bg-primary" style="width:<?php printf('%0.1f%%',$ratio );?>;background-color:#ff9800;" role="progressbar"></div>
            </div>
            <div class="row mt-4">
                <?php foreach ($data as $item) { 
                    echo '<div class="col text-center">';
                    echo '<span class="text-muted">'.$item['name'].'</span><h4 class="number-font">'.$item['value'].'</h4>';
                    echo '</div>';
                }?>
            </div>
        </div>
        <?php echo ob_get_clean();
    }

    // 今日用户统计
    public function top_widget_4() {

        global $wpdb;
        $count1 = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
        $count2 = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE DATE_FORMAT( user_registered,'%Y-%m-%d') = DATE_FORMAT(NOW(), '%Y-%m-%d')");
        $count3 = $wpdb->get_var("SELECT count(a.ID) FROM $wpdb->users a INNER JOIN $wpdb->usermeta b ON ( a.ID = b.user_id ) WHERE (  ( b.meta_key = 'cao_banned' AND b.meta_value = 1 )  )");

        $ratio = ($count1 > 0) ? round(($count3 / $count1 * 100)) : 0;
        $data = [
            ['name' => '全站总用户', 'value' => sprintf('%d位', $count1)],
            ['name' => '已封号用户', 'value' => sprintf('%d位', $count3)],
            ['name' => '封号率', 'value' => sprintf('%0.1f%%', $ratio)],
        ];
        ob_start(); ?>
        <div class="card-body text-center">
            <h4 class="m-0"><span class="text-primary"></span>今日注册用户数</h4>
            <h2 class="text-dark number-font" style="color:#03A9F4;"><?php echo $count2;?> 位</h2>
            <div class="progress mt-4">
                <div class="progress-bar progress-bar-striped bg-primary" style="width:<?php printf('%0.1f%%',$ratio );?>;background-color:#f44336;" role="progressbar"></div>
            </div>
            <div class="row mt-4">
                <?php foreach ($data as $item) { 
                    echo '<div class="col text-center">';
                    echo '<span class="text-muted">'.$item['name'].'</span><h4 class="number-font">'.$item['value'].'</h4>';
                    echo '</div>';
                }?>
            </div>
        </div>
        <?php echo ob_get_clean();
    }

    //用户余额排行榜
    public function top_widget_5() {

        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key='cao_balance' ORDER BY CAST(meta_value AS UNSIGNED) DESC LIMIT 7"
        );

        echo '<div class="card-body">';
        if (!empty($results)) {
            echo '<ul class="card-body-ul">';
            $rank_num = 0;
            foreach ( $results as $result ) {
                $rank_num++;
                $user_info = get_userdata( $result->user_id );
                $vip_info = get_user_vip_data($result->user_id);
                $avatar = get_avatar($result->user_id, 50);
                echo sprintf('<li class="text-muted"><span class="badge bg-secondary" style="margin-right:10px;">%s</span> %s <span style="margin-right:10px;">%s (%s)</span> <span style="margin-left:auto;">%s (%s)</span></li>',$rank_num,$avatar,$user_info->user_login,$vip_info['name'],$result->meta_value,get_site_coin_name());
            }
            echo '</ul>';
        }else{
            echo '<p style="padding:20px;text-align:center;">暂无数据</p>';
        }
        echo '</div>';
    }

    //全站销量排行榜
    public function top_widget_6() {

        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT post_id, COUNT(post_id) AS count,SUM(pay_price) AS sum FROM {$wpdb->cao_order_tbl} WHERE pay_status=1 AND order_type = 1 GROUP BY post_id ORDER BY count DESC LIMIT 10"
        );

        echo '<div class="card-body">';
        if (!empty($results)) {
            echo '<ul class="card-body-ul">';
            $rank_num = 0;
            foreach ( $results as $result ) {
                $rank_num++;

                if (get_permalink($result->post_id)) {
                    $post = sprintf('<a target="_blank" href=%s>%s</a>', get_permalink($result->post_id),get_the_title($result->post_id));
                }else{
                    $post = '其他PID : '. $result->post_id;
                }
                
                echo sprintf('<li class="text-muted"><span class="badge bg-secondary" style="margin-right:10px;">%s</span>%s <span style="margin-left:auto;">￥%s (%s单)</span></li>',$rank_num,$post,$result->sum,$result->count);
            }
            echo '</ul>';
        }else{
            echo '<p style="padding:20px;text-align:center;">暂无数据</p>';
        }
        echo '</div>';
    }

    //全站下载排行榜
    public function top_widget_7() {

        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT post_id, COUNT(post_id) AS count FROM {$wpdb->cao_down_tbl} WHERE 1=1 GROUP BY post_id ORDER BY count DESC LIMIT 10"
        );

        echo '<div class="card-body">';
        if (!empty($results)) {
            echo '<ul class="card-body-ul">';
            $rank_num = 0;
            foreach ( $results as $result ) {
                $rank_num++;

                if (get_permalink($result->post_id)) {
                    $post = sprintf('<a target="_blank" href=%s>%s</a>', get_permalink($result->post_id),get_the_title($result->post_id));
                }else{
                    $post = '其他PID : '. $result->post_id;
                }
                
                echo sprintf('<li class="text-muted"><span class="badge bg-secondary" style="margin-right:10px;">%s</span>%s <span style="margin-left:auto;">%s (次)</span></li>',$rank_num,$post,$result->count);
            }
            echo '</ul>';
        }else{
            echo '<p style="padding:20px;text-align:center;">暂无数据</p>';
        }
        echo '</div>';
    }

    //全站推广排行榜
    public function top_widget_8() {

        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT aff_uid, COUNT(aff_uid) AS count FROM {$wpdb->cao_aff_tbl} WHERE 1=1 GROUP BY aff_uid ORDER BY count DESC LIMIT 10"
        );

        echo '<div class="card-body">';
        if (!empty($results)) {
            echo '<ul class="card-body-ul">';
            $rank_num = 0;
            foreach ( $results as $result ) {
                $rank_num++;

                $user_info = get_userdata( $result->aff_uid );
                $vip_info = get_user_vip_data($result->aff_uid);
                $avatar = get_avatar($result->aff_uid, 50);
                echo sprintf('<li class="text-muted"><span class="badge bg-secondary" style="margin-right:10px;">%s</span> %s <span style="margin-right:10px;">%s (%s)</span> <span style="margin-left:auto;">%s (单)</span></li>',$rank_num,$avatar,$user_info->user_login,$vip_info['name'],$result->count);
            }
            echo '</ul>';
        }else{
            echo '<p style="padding:20px;text-align:center;">暂无数据</p>';
        }
        echo '</div>';
    }




    //年度统计
    public function widget_1() {


        $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']

        $diff_seconds = time() - current_time('timestamp'); //相差时区多少秒

        $year_start_time = mktime(0, 0, 0, 1, 1, wp_date('Y')) + $diff_seconds; //今年开始时间

        // 今年总计
        $res = $this->query_pay_info_for_time($year_start_time,time());

        $retval1 = (!empty($res[1]->pay_price)) ? $res[1]->pay_price : 0 ;
        $retval2 = (!empty($res[1]->count)) ? $res[1]->count : 0 ;
        $title = sprintf('(%s总计) 营业额：￥%s | %s条' ,date('Y'),$retval1,$retval2);


        $series_data  = [];
        $series_data2 = [];
        $series_data3 = [];
        $cat_data     = [];

        //循环
        for ($i = 0; $i < intval(wp_date('m')); $i++) {
            $this_m = sprintf("%02d",$i+1);
            $star = ( mktime(0,0,0,$this_m,1,wp_date('Y')) + $diff_seconds );
            $end  = ( mktime(23,59,59,$this_m,wp_date('t'),wp_date('Y')) + $diff_seconds );
            $date = wp_date('m', $star);

            // var_dump($date);die;

            $cat_data[$i] = $date;
            $series_data[$i]  = 0;
            $series_data2[$i] = 0;
            $series_data3[$i] = 0;

            $res = $this->query_pay_info_for_time($star, $end);

            foreach ($res as $k => $v) {
                if ($v->pay_status == 0) {
                    $series_data[$i] = $v->pay_price;
                } elseif ($v->pay_status == 1) {
                    $series_data2[$i] = $v->pay_price;
                }
                $series_data3[$i] += $v->count;
            }

        }

        $options = [
            'title'      => [
                'text' => $title,
                'floating' => true,
                'offsetY' => 0,
                'margin' => 40,
                'align' => 'left',
                'align' => 'center',
                'style' => [
                    'color' => '#444'
                ],
            ],
            'chart'      => [
                'type' => 'bar',
                'height' => 480,
            ],
            'series'     => [
                ['name' => '总订单金额/￥', 'data' => $series_data],
                ['name' => '已付款金额/￥', 'data' => $series_data2],
                ['name' => '订单数量/条', 'data' => $series_data3],
            ],
            'xaxis'      => [
                'categories' => $cat_data,
            ],
            'plotOptions'     => [
                'bar' => [
                    'horizontal' => false,
                    'borderRadius' => 5,
                    'columnWidth' => '55%',
                    'endingShape' => 'rounded'
                ],
            ],
            'stroke'     => [
                'show' => true,
                'width' => 2,
                'colors' => ['transparent']
            ],
            'fill'      => [
                'opacity' => 1,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];

        

        $options = json_encode($options);

        ob_start();?>
        <div id="chart-nian" style="min-height:180px;"></div>
        <script>
            jQuery(document).ready(function($){
                var options = <?php echo $options; ?>;
                options.tooltip = {
                    x: {
                        formatter: function(val) {
                            return val + "月详情"
                        },
                    }
                };
                options.xaxis.labels = {
                    formatter: function(val) {
                        return val + "月"
                    },
                };
                var chart = new ApexCharts(document.querySelector('#chart-nian'), options)
                chart.render()
            });
        </script>
        <?php echo ob_get_clean();
    }


    //统计图表 最近三十天
    public function widget_2() {
        $diff_seconds = time() - current_time('timestamp'); //相差时区多少秒
        
        // 本月总计
        $star_m = mktime(0, 0, 0, wp_date('m'), 1, wp_date('Y')) + $diff_seconds; //
        $end_m = mktime(23,59,59,wp_date('m'),wp_date('t'),wp_date('Y')) + $diff_seconds; //
        $res = $this->query_pay_info_for_time($star_m, $end_m);
        $retval1 = (!empty($res[1]->pay_price)) ? $res[1]->pay_price : 0 ;
        $retval2 = (!empty($res[1]->count)) ? $res[1]->count : 0 ;
        $title = sprintf('(%s总计) 营业额：￥%s | %s条' ,date('m月'),$retval1,$retval2);


        global $wpdb,$pay_type_not_in;
        $results = $wpdb->get_results(
            "SELECT 
                DATE_FORMAT(FROM_UNIXTIME(create_time), '%Y-%m-%d') as `date`, 
                SUM(pay_price) AS amount, 
                SUM(IF(pay_status = 1, pay_price, 0)) AS paid_amount, 
                COUNT(*) AS count,
                SUM(IF(pay_status = 1, 1, 0)) AS paid_count
            FROM {$wpdb->cao_order_tbl}
            WHERE 
                pay_type NOT IN ({$pay_type_not_in}) AND pay_status IN (0, 1) AND create_time BETWEEN UNIX_TIMESTAMP(DATE_FORMAT(NOW(), '%Y-%m-01')) AND UNIX_TIMESTAMP(NOW()) 
            GROUP BY 
                `date`"
        );

        $data = array();
        foreach ($results as $row) {
            $date_parts = date_parse($row->date);
            $day = $date_parts['day'];
            $data[$day] = $row;
        }

        $categories = $amounts = $paid_amounts = $counts = $paid_counts = [];
        $sum_day = wp_date('t'); //本月共多少天
        $end_day = wp_date('d'); //今天是第几天

        //循环
        for ($i = 0; $i < $sum_day; $i++) {
            $__key = $i+1; //几号
            if ($i == $end_day) {
                break;
            }

            $categories[$i] = $__key;
            //赋值转化格式
            $amounts[$i] = $paid_amounts[$i] = $counts[$i] = $paid_counts[$i] = '0';

            if (isset($data[$__key])) {
                $setData = $data[$__key];
                $amounts[$i]  = (float)$setData->amount;
                $paid_amounts[$i] = (float)$setData->paid_amount;
                $counts[$i] = (int)$setData->count;
                $paid_counts[$i] = (int)$setData->paid_count;
            }

        }

        $options = [
            'title'      => [
                'text' => $title,
                'floating' => false,
                'offsetY' => 0,
                'margin' => 40,
                'align' => 'center',
                'style' => [
                    'color' => '#444'
                ],
            ],
            'chart'      => [
                'type' => 'area',
                'height' => 480,
            ],
            'series'     => [
                ['name' => '总订单金额', 'data' => $amounts],
                ['name' => '已付款金额', 'data' => $paid_amounts],
                ['name' => '总订单数量', 'data' => $counts],
                ['name' => '已付款订单数量', 'data' => $paid_counts],
            ],
            'xaxis'      => [
                'categories' => $categories,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'stroke'     => [
                'curve' => 'smooth',
            ],
        ];

        $options = json_encode($options);



        ob_start();?>
     	<div id="chart-yue" style="min-height:180px;"></div>
     	<script>
     		jQuery(document).ready(function($){
				var options = <?php echo $options;?>;
                options.tooltip = {
                    x: {
                        formatter: function(val) {
                            return "日期：" + val + "号详情"
                        },
                    }
                };
				var chart = new ApexCharts(document.querySelector('#chart-yue'), options)
				chart.render()
			});
     	</script>
     	<?php echo ob_get_clean();
    }

    

    

    //查询数据库 不统计站内币支付
    public function query_pay_info_for_time($startime, $endtime) {
        global $wpdb,$pay_type_not_in;

        $table = $wpdb->cao_order_tbl;

        $data = $wpdb->get_results(
            $wpdb->prepare("SELECT pay_status,COUNT(id) as count,SUM(pay_price) as pay_price FROM {$table} WHERE pay_type NOT IN (%s) AND create_time BETWEEN %s AND %s GROUP BY pay_status ORDER BY pay_status ASC", $pay_type_not_in,$startime, $endtime)
        );
        return $data;
    }

}
