<?php

/**
 * 数据库初始化新版本数据表采用tbl后缀，防止和老款冲突 
 * 新版数据库采用新数据表设计
 * 时间字段均使用时间戳，方便不同时区地区切换时区后转换日期
 */
class ZB_SetupDb {

    private $db_prefix;
    private $db_tables;

    public function __construct() {
        $this->db_prefix = '';
        $this->db_tables = array(
            'cao_order_tbl', //订单表
            'cao_cdk_tbl', //优惠码表
            'cao_down_tbl', //下载记录表
            'cao_aff_tbl', //佣金记录表
            'cao_ticket_tbl', //工单记录表
        );

        $this->define_tables();
    }

    public function define_tables() {
        global $wpdb;
        $_db_prefix = $this->db_prefix;
        $tables     = $this->db_tables;

        foreach ($tables as $short_name) {
            $table_name            = $wpdb->prefix . $_db_prefix . $short_name;
            $backward_key          = $_db_prefix . $short_name;
            $wpdb->{$backward_key} = $table_name;
        }
    }

    // 安装数据表
    public function install_db() {

        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            if (!empty($wpdb->charset)) {
                $collate .= 'DEFAULT CHARACTER SET ' . $wpdb->charset;
            }

            if (!empty($wpdb->collate)) {
                $collate .= ' COLLATE ' . $wpdb->collate;
            }
        }

        //订单表 [order_type 0无 1文章 2充值 3会员 4其他]  [order_info： vip_rate 折扣0.5 vip_type 购买会员类型 aff_id 推荐ID ip ip地址]
        $execute = $wpdb->query(
            "
            CREATE TABLE IF NOT EXISTS $wpdb->cao_order_tbl(
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                post_id bigint(20) unsigned NOT NULL,
                user_id bigint(20) unsigned NOT NULL,
                order_type int(3) NOT NULL DEFAULT 0,
                order_trade_no varchar(50) DEFAULT NULL,
                order_price double(10,2) DEFAULT NULL,
                create_time int(11) DEFAULT 0,
                pay_type tinyint(3) DEFAULT 0,
                pay_time int(11) DEFAULT 0,
                pay_price double(10,2) DEFAULT NULL,
                pay_trade_no varchar(50) DEFAULT NULL,
                order_info longtext,
                pay_status tinyint(3) DEFAULT 0,
                PRIMARY KEY (id),
                KEY order_trade_no (order_trade_no)
            ) $collate
            "
        );

        //下载记录表
        $execute = $wpdb->query(
            "
            CREATE TABLE IF NOT EXISTS $wpdb->cao_down_tbl(
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                post_id bigint(20) unsigned NOT NULL,
                create_time int(11) DEFAULT 0,
                ip varchar(255) DEFAULT NULL,
                note varchar(255) DEFAULT NULL,
                PRIMARY KEY (id)
            ) $collate
            "
        );

        //推广记录表
        $execute = $wpdb->query(
            "
            CREATE TABLE IF NOT EXISTS $wpdb->cao_aff_tbl(
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                order_id bigint(20) unsigned NOT NULL,
                aff_uid bigint(20) unsigned NOT NULL,
                aff_rate double(10,2) NOT NULL DEFAULT 0,
                create_time int(11) DEFAULT 0,
                apply_time int(11) DEFAULT 0,
                comple_time int(11) DEFAULT 0,
                note varchar(255) DEFAULT NULL,
                status tinyint(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (id)
            ) $collate
            "
        );

        //优惠码表 1 充值卡  2 注册邀请码
        $execute = $wpdb->query(
            "
            CREATE TABLE IF NOT EXISTS $wpdb->cao_cdk_tbl(
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                order_id bigint(20) unsigned NOT NULL,
                type tinyint(3) NOT NULL DEFAULT 0,
                amount double(10,2) NOT NULL DEFAULT 0,
                create_time int(11) DEFAULT 0,
                expiry_time int(11) DEFAULT 0,
                code varchar(50) DEFAULT NULL,
                info longtext,
                status tinyint(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (id),
                KEY code (code)
            ) $collate
            "
        );

        //工单表
        $execute = $wpdb->query(
            "
            CREATE TABLE IF NOT EXISTS $wpdb->cao_ticket_tbl(
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                type tinyint(3) NOT NULL DEFAULT 0 COMMENT '工单类型',
                title varchar(255) NOT NULL COMMENT '工单标题',
                content text DEFAULT NULL COMMENT '工单内容',
                reply_content text DEFAULT NULL COMMENT '工单回复内容',
                file varchar(255) DEFAULT NULL COMMENT '附件',
                reply_file varchar(255) DEFAULT NULL COMMENT '回复附件',
                creator_id int(11) unsigned NOT NULL COMMENT '工单创建者ID',
                assignee_id int(11) unsigned DEFAULT NULL COMMENT '工单处理人员ID',
                create_time int(11) DEFAULT 0 COMMENT '修改时间',
                updated_time int(11) DEFAULT 0 COMMENT '最近更新时间',
                reply_time int(11) DEFAULT 0 COMMENT '回复时间',
                status tinyint(3) NOT NULL DEFAULT 0 COMMENT '工单状态，0：新建，1：处理中，2：已解决，3：已关闭',
                PRIMARY KEY (id)
            ) $collate
            "
        );

        if ($wpdb->last_error) {
            throw new Exception($wpdb->last_error);
        }

        return $execute;

    }

    //迁移订单数据
    public function migrate_old_order_data() {
        global $wpdb;

        $old_db = $wpdb->prefix . 'cao_order';
        $new_db = $wpdb->cao_order_tbl;

        // 从旧订单表中获取数据
        $rows = $wpdb->get_results("SELECT * FROM $old_db WHERE status=1");

        if (empty($rows)) {
            return false;
        }

        $migrate_succes_count = 0;

        foreach ($rows as $row) {

            if (empty($row->status) || $row->status == 0) {
                // 未支付订单不迁移
                continue;
            }

            $__order_info = maybe_unserialize($row->order_info);

            if ($row->order_type == 'charge') {
                $order_type = 2; //充值订单
                $post_id = 0; 
            }elseif (isset($__order_info['vip_type']) && $__order_info['vip_type'] > 0) {
                $order_type = 3; //会员订单
                $post_id = 0; 
            }else{
                $order_type = 1; //文章订单
                $post_id = (int)$row->post_id; 
            }

            $pay_optons = zb_get_pay_optons();

            if (array_key_exists($row->pay_type, $pay_optons)) {
                $pay_type = $row->pay_type;
            } else {
                $pay_type = 0;
            }

            $data = array(
                'post_id'        => $post_id,
                'user_id'        => $row->user_id,
                'order_type'     => $order_type,
                'order_trade_no' => $row->order_trade_no,
                'order_price'    => $row->order_price,
                'create_time'    => $row->create_time,
                'pay_type'       => $pay_type,
                'pay_time'       => $row->pay_time,
                'pay_price'      => $row->order_price,
                'pay_trade_no'   => $row->pay_trade_no,
                'order_info'     => $row->order_info,
                'pay_status'     => $row->status,
            );


            //查询订单信息
            $new = $wpdb->get_var(
                $wpdb->prepare("SELECT id FROM {$new_db} WHERE order_trade_no = %s",$row->order_trade_no)
            );

            if (empty($new)) {
                $result = $wpdb->insert($new_db, $data);
                $migrate_succes_count++;
            }
        }

        return $migrate_succes_count;

    }

    //迁移meta字段
    public function migrate_old_post_meta($paged = 1) {
        set_time_limit(0);
        $migrate_succes_count = 0;
        global $wpdb;
        $meta_key = 'cao_downurl';
        $meta_key_2 = 'video_url';
        $post_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta}
                  WHERE meta_key = %s OR meta_key = %s LIMIT 1000", $meta_key,$meta_key_2));
        
        foreach ($post_ids as $post_id) {
            
            if (!get_the_permalink($post_id)) {
                delete_post_meta( $post_id, 'cao_downurl' ); //删除旧版本地址
                delete_post_meta( $post_id, 'cao_pwd' ); //删除旧版本密码
                continue; //无效文章跳过过
            }
            
            $old_meta = get_post_meta($post_id, 'cao_downurl', 1);
            $new_meta = get_post_meta($post_id, 'cao_downurl_new', 1);
            
            if (empty($new_meta) && !empty($old_meta)) {
                
                $post_down_info = [
                    ['name' => '立即下载','url' => $old_meta,'pwd' => get_post_meta($post_id, 'cao_pwd', true)]
                ]; //整合数组
                
                update_post_meta( $post_id, 'cao_downurl_new', $post_down_info);
                delete_post_meta( $post_id, 'cao_downurl' ); //删除旧版本地址
                delete_post_meta( $post_id, 'cao_pwd' ); //删除旧版本密码

                $migrate_succes_count++;
            }

            //迁移视频地址
            $old_meta = get_post_meta($post_id, 'video_url', 1);
            $new_meta = get_post_meta($post_id, 'video_url_new', 1);

            if (empty($new_meta) && !empty($old_meta)) {
                
                $old_meta_data = explode(PHP_EOL, trim($old_meta));
                $video_data = array();
                //格式化数据
                foreach ($old_meta_data as $k => $v) {

                    if (!empty(trim($v))) {
                        $item_exp = array_map('trim', explode('|', $v));
                        //视频信息
                        $video_data[] = [
                            'src' => (!empty($item_exp[0])) ? trim($item_exp[0]) : '',
                            'title' => (!empty($item_exp[1])) ? trim($item_exp[1]) : '',
                            'img' => (!empty($item_exp[2])) ? trim($item_exp[2]) : '',
                        ];
                        
                    }
                    
                }


                if (!empty($video_data)) {
                    update_post_meta( $post_id, 'video_url_new', $video_data);
                    delete_post_meta( $post_id, 'video_url' );
                }

                $migrate_succes_count++;
            }


        }

        return $migrate_succes_count;
    }


    //迁移自定义筛选字段
    public function migrate_old_filter_meta() {

        $migrate_succes_count = 0;

        $old_meta_opt = _cao_old('custom_post_meta_opt');
        if (empty($old_meta_opt) || !is_array($old_meta_opt)) {
            return 0;
        }

        $site_custom_taxonomy = _cao('site_custom_taxonomy', array());
        if (empty($site_custom_taxonomy) || !is_array($site_custom_taxonomy)) {
            $site_custom_taxonomy = array();
        }

        $__custom_taxonomy = [];

        foreach ($old_meta_opt as $item) {

            // 格式化数据 去除下划线和数值 防止 wp 分类法报错
            $_meta_taxonomy = preg_replace_callback('/_/', function ($matches) {
                return '';
            }, $item['meta_ua']);

            $_meta_taxonomy = preg_replace_callback('/\d+/', function ($matches) {
                $number = intval($matches[0]);
                $letter = chr(ord('a') + ($number - 1)); // 将数字转换为对应的字母
                return $letter;
            }, $_meta_taxonomy);

            $__custom_taxonomy[$item['meta_ua']] = array(
                'name'         => $item['meta_name'],
                'taxonomy'     => $item['meta_ua'],
                'taxonomy_new' => $_meta_taxonomy,
                'opt'          => $item['meta_opt'],
            );

            if (!empty($site_custom_taxonomy) && in_array($item['meta_name'], array_column($site_custom_taxonomy, 'name'))) {
                continue; //已经存在 跳出
            }

            $_opt = array(
                'name'     => $item['meta_name'],
                'taxonomy' => $_meta_taxonomy,
                'type'     => 'simple',
            );

            $site_custom_taxonomy[] = $_opt;

        }

        //更新设置
        if (!empty($site_custom_taxonomy)) {
            $options                         = get_option(_OPTIONS_PRE);
            $options['site_custom_taxonomy'] = $site_custom_taxonomy;
            update_option(_OPTIONS_PRE, $options);
        }

        //更新字段
        foreach ($__custom_taxonomy as $key => $terms) {

            if (!empty($terms['opt'])) {
                $opts = $terms['opt'];

                foreach ($opts as $opt) {
                    // 添加分类到自定义分类法中
                    $term = wp_insert_term(
                        $opt['opt_name'], // 新分类名称
                        $terms['taxonomy_new'], // 目标分类法名称
                        array(
                            'slug' => $opt['opt_ua'], // 别名
                        )
                    );
                }

            }

        }

        global $wpdb;

        foreach ($__custom_taxonomy as $item) {
            $query_meta    = $item['taxonomy'];
            $taxonomy_new  = $item['taxonomy_new'];
            $taxonomy_name = $item['name'];

            //查询有自定义字段的文章 ID
            $post_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta}
                  WHERE meta_key = %s LIMIT 5000", $query_meta));

            if (!empty($post_ids)) {

                foreach ($post_ids as $post_id) {
                    $value = get_post_meta($post_id, $query_meta, true);

                    $post_id = intval($post_id);

                    if (!empty($value)) {

                        wp_set_object_terms(
                            $post_id, // 文章 ID
                            $value, // 分类别名
                            $taxonomy_new // 分类法名称 $taxonomy_name  $taxonomy_new
                        );
                        delete_post_meta($post_id, $query_meta); //删除旧版本meta 防止重复添加
                        $migrate_succes_count++;
                    }

                }

            }

        }

        return $migrate_succes_count;

    }

}