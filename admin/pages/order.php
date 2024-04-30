<?php

defined('WPINC') || exit;

// 订单管理页面
// add_thickbox(); //支持弹窗
$Ri_List_Table = new Ri_List_Table();
$Ri_List_Table->prepare_items();
$message = $Ri_List_Table->message;
?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">订单列表/管理</h1>
    <p>可根据用户数字ID，登录名，订单号，支付单号，优惠码，支付单号搜索</p>
    <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>

    <hr class="wp-header-end">

    <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
            <form method="get">
                <?php $Ri_List_Table->search_box('搜索', 'post_id');?>
                <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
                <?php wp_nonce_field('zb-admin-page-nonce', '_nonce');?>
                <?php $Ri_List_Table->display();?>
            </form>
        </div>
    </div>
    <br class="clear">
</div>
<script type="text/javascript">

jQuery(document).ready(function($){
    jQuery('input#doaction').click(function(e) {
        return confirm('确实要对所选条目执行此批量操作吗?');
    });


});
</script>
<!-- 主页面END -->

<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Ri_List_Table extends WP_List_Table {
    public $message = '';
    public function __construct() {
        parent::__construct(array(
            'singular' => 'item',
            'plural'   => 'items',
            'ajax'     => false,
        ));
    }

    public function set_message($message) {
        $this->message = $message;
    }

    public function prepare_items() {
        $columns  = $this->get_columns();
        $sortable = $this->get_sortable_columns();

        $per_page     = 10;
        $current_page = $this->get_pagenum();
        $total_items  = $this->get_pagenum();

        $this->set_pagination_args(array(
            'total_items' => 0,
            'per_page'    => $per_page,
            'total_pages' => 0,
        ));

        $this->_column_headers = array($columns, array(), $sortable);
        $this->process_bulk_action();

        $this->items = $this->table_data($per_page, $current_page);
    }

    //获取数据库数据
    private function table_data($per_page = 5, $page_number = 1) {
        global $wpdb;

        $orderby    = !empty($_GET['orderby']) ? wp_strip_all_tags($_GET['orderby']) : 'id';
        $order      = !empty($_GET['order']) ? wp_strip_all_tags($_GET['order']) : 'DESC';
        $order_str  = sanitize_sql_orderby($orderby . ' ' . $order);
        $limit_from = ($page_number - 1) * $per_page;

        $order_table = $wpdb->cao_order_tbl; // 订单表 a
        $aff_table   = $wpdb->cao_aff_tbl; // 推广表 c

        $search_term = (!empty($_REQUEST['s'])) ? trim($_REQUEST['s']) : '';
        $search_user_id = get_user_id_from_str($search_term);
        $search_like  = '%' . $wpdb->esc_like($search_term) . '%';

        $after_select = "FROM {$order_table} AS `a` LEFT JOIN {$aff_table} AS `c` ON `a`.id = `c`.order_id WHERE 1=1";

        if (!empty($search_user_id)) {
            $after_select .= $wpdb->prepare(" AND `a`.user_id = %d", $search_user_id);
        }elseif (!empty($search_term)) {
            $after_select .= $wpdb->prepare(" AND (`a`.order_trade_no LIKE %s OR `a`.pay_trade_no LIKE %s)", $search_like,$search_like);
        }

        if (isset($_GET['status']) && $_GET['status'] !== 'all') {
            $after_select .= $wpdb->prepare(" AND `a`.pay_status = %d", $_GET['status']);
        }
        if (isset($_GET['order_type']) && $_GET['order_type'] !== 'all') {
            $after_select .= $wpdb->prepare(" AND `a`.order_type = %d", $_GET['order_type']);
        }

        $after_query = "GROUP BY `a`.id ORDER BY {$order_str} LIMIT {$limit_from}, {$per_page}";

        $select = "SELECT `a`.*, `c`.aff_uid,`c`.aff_rate";

        $query = "{$select} {$after_select} {$after_query}";

        $data = $wpdb->get_results($query, ARRAY_A);

        $total_items  = intval($wpdb->get_var("SELECT COUNT(*) {$after_select}"));

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);

        return $data;
    }

    // 获取列列表
    public function get_columns() {
        $columns = [
            'cb'             => '<input type="checkbox" />',
            'id'             => 'ID',
            'user_id'        => '用户',
            'order_type'     => '订单类型',
            'post_id'        => '产品信息',
            'order_price'    => '订单价格',
            'order_trade_no' => '本地订单号',
            'create_time'    => '创建时间',
            'pay_type'       => '支付方式',
            'pay_price'       => '支付金额',
            'pay_time'       => '支付时间',
            'pay_trade_no'   => '支付单号',
            'aff_uid'       => '推广人',
            'aff_rate'      => '佣金',
            'pay_status'         => '支付状态',
        ];
        return $columns;
    }

    //可排序列字段
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id'          => array('id', false),
            'user_id'     => array('user_id', false),
            'order_price' => array('order_price', true),
            'pay_price' => array('pay_price', true),
            'pay_type'    => array('pay_type', false),
            'pay_time'    => array('pay_time', false),
        );

        return $sortable_columns;
    }

    public function no_items() {
        _e('没有找到相关数据');
    }

    //列数据显示
    public function column_default($item, $i) {

        $order_info = maybe_unserialize($item['order_info']);
        switch ($i) {
        case 'user_id':
            $retVal = ($user = get_user_by('ID', $item[$i])) ? $user->user_login : '游客';
            $avatar = get_avatar($item[$i], 50);
            return sprintf('<div>%s <small>ID：%s<br>%s</small></div>',$avatar,$item[$i],$retVal);
            break;
        case 'aff_uid':
            $retVal = ($user = get_user_by('ID', $item[$i])) ? $user->user_login : false;
            if (!empty($retVal)) {
                return sprintf('<small>%s<br>ID：%s</small>', $retVal, $item[$i]);
            } else {
                return '';
            }
            break;
        case 'aff_rate':
            if (!empty($item[$i])) {
                return sprintf('￥%s (%s%%)', $item[$i]*$item['pay_price'],$item[$i]*100);
            } else {
                return '';
            }
            break;
        case 'post_id':
            if ($item['order_type'] == 1) {
                return sprintf('<a target="_blank" href=%s>%s</a>', get_permalink($item[$i]),get_the_title($item[$i]));
            }elseif ($item['order_type'] == 2) {
                return sprintf('%s余额充值',get_site_coin_name());
            }elseif ($item['order_type'] == 3) {
                
                $vip_type = (isset($order_info['vip_type'])) ? $order_info['vip_type'] : 'vip';
                $vip_day = (isset($order_info['vip_day'])) ? absint($order_info['vip_day']) : 0;
                $vip_options = get_site_vip_options();
                $__name = $vip_options[$vip_type]['name'];
                $buy_options = get_site_vip_buy_options();
                if (isset($buy_options[$vip_day])) {
                    $__sub_name = $buy_options[$vip_day]['buy_title'];
                }else{
                    $__sub_name = sprintf('套餐（%s）已下架',$vip_day);
                }

                return sprintf('%s-%s',$__name,$__sub_name);
            }
            return '';
            break;
        case 'order_type':
            return ZB_Shop::get_order_type($item[$i]);
            break;
        case 'pay_type':
            return ZB_Shop::get_pay_type($item[$i]);
            break;
        case 'pay_status':
            return sprintf('<code class="pay_badge pay_status_%s">%s</code>', $item[$i], ZB_Shop::get_pay_status($item[$i]));
            break;
        case 'order_price':
        case 'pay_price':
            return sprintf('<b class="badge bg-secondary">￥%s</b><br><b class="badge bg-white">%s:%s</b>', $item[$i],get_site_coin_name(),site_convert_amount($item[$i],'coin'));
            break;
        case 'create_time':
        case 'pay_time':
            return (!empty($item[$i])) ? wp_date('Y-m-d H:i:s', $item[$i]) : '';
            break;
        default:
            return sprintf('<i>%s</i>', $item[$i]);
            break;
        }
    }

    // 显示分页
    public function display_tablenav($which) {
        ob_start();?>
        <div class="tablenav mb-4 <?php echo esc_attr($which); ?>">
            <?php if ('top' === $which) { ?>
            <div class="alignleft actions">
                <?php $this->bulk_actions();?>
            </div>
            <?php } ?>
            <?php 
                $this->extra_tablenav($which);
                $this->pagination($which);
            ?>
            <?php if ($which == 'bottom') {}?>
            <br class="clear" />
        </div>
        <?php echo ob_get_clean();
    }

    //在批量操作和分页之间显示的额外控件
    public function extra_tablenav($which) {
        if ($which == 'top') {
            // Add filter dropdown here
            $typees = array(
                'all' => '全部订单类型',
                '1'   => ZB_Shop::get_order_type(1),
                '2'   => ZB_Shop::get_order_type(2),
                '3'   => ZB_Shop::get_order_type(3),
                '4'   => ZB_Shop::get_order_type(4),
            );

            $statuses = array(
                'all' => '全部支付状态',
                '0'   => ZB_Shop::get_pay_status(0),
                '1'   => ZB_Shop::get_pay_status(1),
            );
            $current_status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $current_type   = isset($_GET['order_type']) ? $_GET['order_type'] : 'all';
            ?>
            <div class="alignleft actions">
                <select name="status">
                    <?php foreach ($statuses as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php selected($current_status, $value);?>><?php echo $label; ?></option>
                    <?php endforeach;?>
                </select>
                <select name="order_type">
                    <?php foreach ($typees as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php selected($current_type, $value);?>><?php echo $label; ?></option>
                    <?php endforeach;?>
                </select>
                <input type="submit" name="filter_action" id="filter_action" class="button" value="筛选">
            </div>
            <?php
}
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    // 在ID字段添加操作信息
    public function column_id($item) {
        $row_id  = $item['id'];
        $actions = array(
            'delete' => sprintf(
                '<a href="admin.php?page=zb-admin-page-order&action=delete&id=%s&_nonce=%s" onclick="return confirm(\'确定删除这个订单?\')">删除</a>',
                $row_id,
                wp_create_nonce('zb-admin-page-nonce')
            ),
        );

        return sprintf(
            '<b>%1$s</b> <span style="color:silver"></span>%2$s',
            $item['id'],
            $this->row_actions($actions)
        );
    }

    public function current_action() {
        if (isset($_REQUEST['filter_action']) && !empty($_REQUEST['filter_action'])) {
            return false;
        }
        if (isset($_REQUEST['action']) && -1 != $_REQUEST['action']) {
            return $_REQUEST['action'];
        }
        if (isset($_REQUEST['action2']) && -1 != $_REQUEST['action2']) {
            return $_REQUEST['action2'];
        }
        return false;
    }

    // 批量操作参数
    public function get_bulk_actions() {
        $actions = array(
            'delete' => '删除',
        );
        return $actions;
    }

    //批量操作触发
    public function process_bulk_action() {
        global $wpdb;

        if ('delete' === $this->current_action()) {

            $ids    = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            $_nonce = isset($_REQUEST['_nonce']) ? $_REQUEST['_nonce'] : '';

            if (!wp_verify_nonce($_nonce, 'zb-admin-page-nonce')) {
                $this->set_message('nonce验证失败，请返回刷新重试');
                return false;
            }

            if (is_array($ids)) {
                $ids = implode(',', $ids);
            }

            if (!empty($ids)) {
                $sql = $wpdb->query("DELETE FROM $wpdb->cao_order_tbl WHERE pay_status <> 2 AND id IN($ids)");
                if ($sql) {
                    $this->set_message(sprintf('成功删除 %d 条记录', $sql));
                } else {
                    $this->set_message('删除失败，找不到订单或者订单已支付订单');
                    return false;
                }

            }

        }

    }

}
