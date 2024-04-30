<?php

defined('WPINC') || exit;

/**
 * 佣金管理页面
 */


$page_action = get_param('action','','get');
if ($page_action=='edit') {
    include_once get_template_directory() . '/admin/pages/ticket-edit.php';
    return;
}

$Ri_List_Table = new Ri_List_Table();
$Ri_List_Table->prepare_items();
$message = $Ri_List_Table->message;
?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">工单管理系统</h1>
    <p>可再此页面回复客户工单，更新工单状态，删除工单等操作</p>
    <p>状态为已关闭则表示用户已经在个人中心查看了回复内容</p>

    <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>

    <hr class="wp-header-end">

    <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
            <form method="get">
                <?php $Ri_List_Table->search_box('搜索用户', 's');?>
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

        $table_name = $wpdb->cao_ticket_tbl;

        //筛选
        $where = 'WHERE 1=1';
        if (!empty($_REQUEST['s'])) {
            // $where .= $wpdb->prepare( " AND creator_id LIKE %s", '%' . sanitize_text_field($_REQUEST['s']) . '%' ); //搜索模式
            $where .= $wpdb->prepare(" AND creator_id = %d", get_user_id_from_str($_REQUEST['s']));
        }
        if (isset($_GET['status']) && $_GET['status'] != 'all') {
            $where .= $wpdb->prepare(" AND status = %d", $_GET['status']);
        }
        if (isset($_GET['type']) && $_GET['type'] != 'all') {
            $where .= $wpdb->prepare(" AND type = %d", $_GET['type']);
        }


        //排序分页
        $offset    = max(0, intval($page_number) - 1) * $per_page;
        $orderby   = isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())) ? $_REQUEST['orderby'] : 'id';
        $order     = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'desc';
        $order_str = sanitize_sql_orderby($orderby . ' ' . $order);

        //查询
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name " . $where);
        $data        = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY $order_str LIMIT $offset, $per_page",ARRAY_A);

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
            'orderby'     => $orderby,
            'order'       => $order,
        ));

        return $data;
    }

    // 获取列列表
    public function get_columns() {
        $columns = [
            'cb'          => '<input type="checkbox" />',
            'id'          => 'ID',
            'creator_id'   => '创建人',
            'type'     => '工单类型',
            'title'     => '问题标题',
            'create_time' => '创建时间',
            'updated_time'  => '更新时间',
            'reply_time' => '回复时间',
            'status'      => '状态',
        ];
        return $columns;
    }

    //可排序列字段
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id', false),
            'type' => array('type', false),
            'creator_id' => array('creator_id', false),
            'create_time' => array('create_time', true),
            'updated_time' => array('updated_time', true),
            'reply_time' => array('reply_time', true),
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
        case 'creator_id':
            $retVal = ($user = get_user_by('ID', $item[$i])) ? $user->user_login : '';
            return sprintf('<small>%s<br>ID：%s</small>', $retVal, $item[$i]);
            break;
        case 'type':
            return ZB_Ticket::get_type($item[$i]);
            break;
        case 'status':
            return sprintf('<b>%s</b>', ZB_Ticket::get_status($item[$i]));
            break;
        case 'title':
            return sprintf('<b>%s</b>', $item[$i]);
            break;
        case 'create_time':
        case 'updated_time':
        case 'reply_time':
            return (!empty($item[$i])) ? wp_date('Y-m-d H:i:s', $item[$i]) : '';
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
        if ( $which == 'top' ) {
            // Add filter dropdown here
            $statuses = array(
                'all'     => '全部状态',
                '0'     => ZB_Ticket::get_status(0),
                '1' => ZB_Ticket::get_status(1),
                '2'   => ZB_Ticket::get_status(2),
                '3' => ZB_Ticket::get_status(3)
            );
            $typees = array(
                'all'     => '全部类型',
                '1'     => ZB_Ticket::get_type(1),
                '2' => ZB_Ticket::get_type(2),
                '3'   => ZB_Ticket::get_type(3),
                '4' => ZB_Ticket::get_type(4)
            );
            $current_status = isset( $_GET['status'] ) ? $_GET['status'] : 'all';
            $current_type = isset( $_GET['type'] ) ? $_GET['type'] : 'all';
            ?>
            <div class="alignleft actions">
                <select name="status">
                    <?php foreach ( $statuses as $value => $label ) : ?>
                        <option value="<?php echo $value; ?>" <?php selected( $current_status, $value ); ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="type">
                    <?php foreach ( $typees as $value => $label ) : ?>
                        <option value="<?php echo $value; ?>" <?php selected( $current_type, $value ); ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
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
        $row_id = $item['id'];

        $actions = array();
      
        $actions['edit'] = sprintf(
            '<a href="admin.php?page=zb-admin-page-ticket&action=edit&id=%s&_nonce=%s">编辑/回复</a>',
            $row_id,
            wp_create_nonce('zb-admin-page-nonce')
        );

        $actions['delete'] = sprintf(
            '<a href="admin.php?page=zb-admin-page-ticket&action=delete&id=%s&_nonce=%s" onclick="return confirm(\'确定删除这条记录?\')">删除</a>',
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
        $actions = array(
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
            case 'delete':
                $ids = (array)$ids;
                if (!empty($ids)) {
                    $i = 0;
                    foreach ($ids as $id) {
                        if (ZB_Ticket::delete($id)) {
                            $i++;
                        }
                    }
                    
                    if ($i>0) {
                        $this->set_message(sprintf('成功删除 %d 条记录', $i ));
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
