<?php

defined('WPINC') || exit;

/**
 * 下载记录
 */

$Ri_List_Table = new Ri_List_Table();
$Ri_List_Table->prepare_items();
$message = $Ri_List_Table->message;
?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">下载记录列表/管理</h1>
    <p>可根据用户，下载产品ID，ip地址搜索</p>
    <p>删除下载记录日志会导致被删除下载记录的用户今日下载次数变动</p>
    <p>用户单独购买获取的资源，或者今日已经通过权限免费获取下载的资源，重复点击下载链接不会重复扣次数</p>

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
        $table_name = $wpdb->cao_down_tbl;

        //排序分页
        $orderby   = isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())) ? $_REQUEST['orderby'] : 'id';
        $order     = isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')) ? $_REQUEST['order'] : 'desc';
        $order_str = sanitize_sql_orderby($orderby . ' ' . $order);
        $limit_from = ($page_number - 1) * $per_page;
        
        $search_term = (!empty($_REQUEST['s'])) ? trim($_REQUEST['s']) : '';
        $search_user_id = get_user_id_from_str($search_term);
        $search_like  = '%' . $wpdb->esc_like($search_term) . '%';

        //筛选
        $where = 'WHERE 1=1';
        if (!empty($search_user_id)) {
            $where .= $wpdb->prepare(" AND user_id = %d", $search_user_id);
        }elseif (!empty($search_term)) {
            $where .= $wpdb->prepare(" AND ip LIKE %s", $search_like);
        }

        $order = "ORDER BY {$order_str} LIMIT {$limit_from}, {$per_page}";

        //查询
        $data        = $wpdb->get_results("SELECT * FROM {$table_name} {$where} {$order}",ARRAY_A);
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} {$where}");

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
            'user_id'     => '下载用户',
            'post_id'     => '关联文章',
            'create_time' => '下载时间',
            'ip'          => '下载IP',
            'note'   => '其他信息',
        ];
        return $columns;
    }

    //可排序列字段
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id'          => array('id', false),
            'user_id'     => array('user_id', false),
            'post_id'     => array('post_id', false),
            'create_time' => array('create_time', false),
        );

        return $sortable_columns;
    }

    public function no_items() {
        _e('没有找到相关数据');
    }

    //列数据显示
    public function column_default($item, $i) {
        switch ($i) {
        case 'user_id':
            $retVal = ($user = get_user_by('ID', $item[$i])) ? $user->user_login : '游客';
            return sprintf('<small>%s<br>ID：%s</small>', $retVal, $item[$i]);
            break;
        case 'create_time':
            return (!empty($item[$i])) ? wp_date('Y-m-d H:i:s', $item[$i]) : '';
            break;
        case 'post_id':
            if (get_permalink($item[$i])) {
                return sprintf('<a target="_blank" href=%s>%s</a>', get_permalink($item[$i]),get_the_title($item[$i]));
            }else{
                return sprintf('文章ID：%s',$item[$i]);
            }
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
        // null
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    // 在ID字段添加操作信息
    public function column_id($item) {
        $row_id            = $item['id'];
        $actions           = array();
        $actions['delete'] = sprintf(
            '<a href="admin.php?page=zb-admin-page-down&action=delete&id=%s&_nonce=%s" onclick="return confirm(\'确定删除这条记录?\')">删除</a>',
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
                // status 状态 -1 失效 0未使用 1已使用
                $sql = $wpdb->query("DELETE FROM $wpdb->cao_down_tbl WHERE id IN($ids)");
                if ($sql) {
                    $this->set_message(sprintf('成功删除 %d 条记录',$sql));
                } else {
                    $this->set_message('删除失败，找不到数据或者已使用成功无法删除');
                    return false;
                }

            }

        }

    }

}
