<?php

defined('WPINC') || exit;

/**
 * 佣金管理页面
 */

$Ri_List_Table = new Ri_List_Table();
$Ri_List_Table->prepare_items();
$message = $Ri_List_Table->message;
?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">推广中心/管理</h1>
    <p>可根据推荐人数字ID或登录名，订单号搜索，关联订单号查询，如果订单状态失效或者订单删除则佣金失效</p>
    <p>提现方式说明：</p>
    <p>1，用户通过前台个人中心申请提现后，用户的推广单状态将变为提现中</p>
    <p>2，用户联系网站客服，提供申请提现的用户名，也就是网站登录名，核对无误后手动转账提现</p>
    <p>3，管理员在此页面，搜索这个用户的登录名，勾选推荐人为这位用户的所有推广单</p>
    <p>4，在左上角选择批量操作，选择更改为已提现，点击应用即可</p>

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

        $aff_table   = $wpdb->cao_aff_tbl; //AFF表 a
        $order_table = $wpdb->cao_order_tbl; //order b

        $search_term = (!empty($_REQUEST['s'])) ? trim($_REQUEST['s']) : '';
        $search_user_id = get_user_id_from_str($search_term);
        $search_like  = '%' . $wpdb->esc_like($search_term) . '%';


        $after_select = "FROM {$aff_table} AS `a` LEFT JOIN {$order_table} AS `b` ON `a`.order_id = `b`.id WHERE `b`.id IS NOT NULL";

        if (!empty($search_user_id)) {
            $after_select .= $wpdb->prepare(" AND `a`.aff_uid = %d", $search_user_id);
        }elseif (!empty($search_term)) {
            $after_select .= $wpdb->prepare(" AND `b`.order_trade_no LIKE %s", $search_like);
        }

        $after_query = "GROUP BY `a`.id ORDER BY {$order_str} LIMIT {$limit_from}, {$per_page}";

        $select = "SELECT `a`.* ,CONVERT(`a`.aff_rate * `b`.pay_price, DECIMAL(10,2)) AS aff_money,`b`.pay_price,`b`.user_id AS pay_user,`b`.post_id,`b`.order_type,`b`.order_trade_no";

        $query = "{$select} {$after_select} {$after_query}";

        $data = $wpdb->get_results($query, ARRAY_A);

        $total_items  = intval($wpdb->get_var("SELECT COUNT(*) {$after_select}"));


        $this->set_pagination_args(
            array(
                'total_items' => $total_items, // WE have to calculate the total number of items.
                'per_page'    => $per_page, // WE have to determine how many items to show on a page.
                'total_pages' => ceil($total_items / $per_page), // WE have to calculate the total number of pages.
            )
        );

        return $data;
    }

    // 获取列列表
    public function get_columns() {
        $columns = [
            'cb'          => '<input type="checkbox" />',
            'id'          => 'ID',
            'aff_uid'     => '推荐人',
            'pay_user'     => '购买人',
            'order_type'  => '订单类型',
            'note'  => '推广类型',
            'pay_price'   => '订单支付金额',
            'aff_rate'   => '佣金比例',
            'aff_money'   => '佣金收益',
            'order_trade_no' => '关联订单号',
            'create_time' => '创建时间',
            'apply_time'  => '申请提现时间',
            'comple_time'  => '结算时间',
            'status'      => '状态',
        ];
        return $columns;
    }

    //可排序列字段
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id', false),
            'order_type' => array('order_type', false),
            'pay_price' => array('pay_price', true),
            'aff_rate' => array('aff_rate', true),
            'aff_money' => array('aff_money', true),
            'create_time' => array('create_time', true),
            'apply_time' => array('apply_time', true),
            'comple_time' => array('comple_time', true),
            'status' => array('status', true),
        );

        return $sortable_columns;
    }

    public function no_items() {
        _e('没有找到相关数据');
    }

    //列数据显示
    public function column_default($item, $i) {
        switch ($i) {
        case 'pay_user':
        case 'aff_uid':
            $retVal = ($user = get_user_by('ID', $item[$i])) ? $user->user_login : '游客';
            return sprintf('<small>%s<br>ID：%s</small>', $retVal, $item[$i]);
            break;
        case 'order_type':
            return ZB_Shop::get_order_type($item[$i]);
            break;
        case 'aff_rate':
            return sprintf('%s%%', $item[$i]*100);
            break;
        case 'pay_price':
        case 'aff_money':
            return sprintf('<b class="badge bg-secondary">￥%s</b>', $item[$i]);
            break;
        case 'create_time':
        case 'apply_time':
        case 'comple_time':
            return (!empty($item[$i])) ? wp_date('Y-m-d H:i:s', $item[$i]) : '';
            break;
        case 'status':
            return sprintf('<b>%s</b>', ZB_Aff::get_aff_status($item[$i]));
            break;
        default:
            return sprintf('<i>%s</i>', $item[$i]);
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
        // echo '';
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    // 在ID字段添加操作信息
    public function column_id($item) {
        $row_id = $item['id'];

        $actions = array();
        if (!empty($item['order_trade_no'])) {
            $actions['edit'] = sprintf('<a href="admin.php?page=zb-admin-page-order&s=%s">订单详情</a>', $item['order_trade_no']);
        }
        
        $actions['delete'] = sprintf(
            '<a href="admin.php?page=zb-admin-page-aff&action=delete&id=%s&_nonce=%s" onclick="return confirm(\'确定删除这条记录?\')">删除</a>',
            $row_id,
            wp_create_nonce('zb-admin-page-nonce')
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
        //状态 -1失效 | 0未提现 | 1提现中  2已提现
        $actions = array(
            'update_0' => '更改为-未提现',
            'update_1' => '更改为-提现中',
            'update_2' => '更改为-已提现',
            'delete' => '删除',
        );
        return $actions;
    }

    //批量操作触发
    public function process_bulk_action() {
        global $wpdb;
        if (empty($this->current_action())) {
            return false;
        }

        $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
        $_nonce = isset($_REQUEST['_nonce']) ? $_REQUEST['_nonce'] : '';
        if (!wp_verify_nonce($_nonce, 'zb-admin-page-nonce')) {
            $this->set_message('nonce验证失败，请返回刷新重试');
            return false;
        }

        switch ($this->current_action()) {
            case 'update_0':
            case 'update_1':
            case 'update_2':
                if (empty($ids)) {
                    return false;
                }
                $data = ['update_0' => 0,'update_1' => 1,'update_2' => 2];
                $status = $data[$this->current_action()];
                $num = 0;
                foreach ($ids as $id) {

                    if ($status===2) {
                        // 结算操作写入结算时间
                        $update = ZB_Aff::update_aff_log(
                            array('status' => $status,'comple_time'=>time()),
                            array('id' => $id),
                            array('%d','%s'),
                            array('%d')
                        );
                    }elseif ($status===0) {
                        $update = ZB_Aff::update_aff_log(
                            array('status' => $status,'apply_time'=>0,'comple_time'=>''),
                            array('id' => $id),
                            array('%d','%s','%s'),
                            array('%d')
                        );
                    }else{
                        $update = ZB_Aff::update_aff_log(
                            array('status' => $status,'apply_time'=>time(),'comple_time'=>''),
                            array('id' => $id),
                            array('%d','%s','%s'),
                            array('%d')
                        );
                    }

                    if ($update) {
                        $num++;
                    }
                }
                $this->set_message(sprintf('成功更新 %d 条记录的提现状态', $num ));
                break;
            case 'delete':
                if (is_array($ids)) {
                    $ids = implode(',', $ids);
                }
                if (!empty($ids)) {
                    $sql = $wpdb->query("DELETE FROM $wpdb->cao_aff_tbl WHERE status = 0 AND id IN($ids)");
                    if ($sql) {
                        $this->set_message(sprintf('成功删除 %d 条记录', $sql ));
                    } else {
                        $this->set_message('删除失败，找不到数据或者已结算成功无法删除');
                        return false;
                    }
                }
                break;
            default:
                break;
        }
    }

}
