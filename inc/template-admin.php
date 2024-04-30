<?php

new ZB_Admin();
/**
 * 后台优化 管控 修改登录安全验证等信息
 */
class ZB_Admin {

    private $safe_login_key; //自带登录页参数
    private $safe_login_password; //自带登录页密码
    private $safe_login_user_id; //允许进入后台管理员ID

    public function __construct() {

        $this->safe_login_key      = 'security';
        $this->safe_login_password = _cao('site_login_security_param', '');
        $this->safe_login_user_id  = '';

        add_action('admin_menu', array($this, 'admin_menu'));
        // 保护后台页面 防止非管理id为1的用户进入
        add_action('admin_init', array($this, 'redirect_wp_admin'));
        //保护自带登录页面
        add_action('login_enqueue_scripts', array($this, 'safe_login_redirect'));
        //用户安全登录注册钩子
        add_action('wp_login', array($this, 'login_hook_action'), 10, 2);
        add_action('user_register', array($this, 'register_hook_action'), 10, 1);
        //删除个人资料后台页面无用字段
        add_action('admin_init', array($this, 'remove_profile_fields'));

        //移除后台页面标题中的“ — WordPress”
        add_filter('admin_title', function ($admin_title, $title) {
            return $title . ' &lsaquo; ' . get_bloginfo('name');
        }, 10, 2);

        add_filter('login_title', function ($login_title, $title) {
            return $title . ' &lsaquo; ' . get_bloginfo('name');
        }, 10, 2);

        add_filter('login_headerurl', function ($url) {
            return home_url();
        });

        add_filter('login_display_language_dropdown', '__return_false');

        //安全验证
        if (!empty($this->safe_login_password)) {
            add_action('login_form', array($this, 'loginform'));
        }

        add_filter('manage_users_columns', array($this, 'add_admin_user_column'));
        add_filter('manage_users_sortable_columns', array($this, 'users_sortable_columns'));
        add_action('manage_users_custom_column', array($this, 'output_users_columns'), 10, 3);
        add_filter('views_users', array($this, 'admin_views_users'));
        add_action('pre_user_query', array($this, 'admin_pre_user_query'));


        add_filter('manage_posts_columns', array($this, 'manage_posts_columns'));
        add_action('manage_posts_custom_column', array($this, 'manage_posts_custom_column'), 10, 2);

        //隐藏用户真实id
        add_action('pre_get_posts', array($this, 'alter_query'), 99);
        add_filter('author_link', array($this, 'alter_link'), 99, 3);
        add_filter('body_class', array($this, 'alter_body_class'), 99, 2);

        //上传文件重命名
        add_filter('sanitize_file_name', array($this, 'update_file_md5_rename'), 10);

        // 仅在媒体库查询时应用过滤器
        add_action('ajax_query_attachments_args', array($this, 'restrict_media_library'));
        //默认可视化编辑器
        add_filter('wp_default_editor', function(){
            return 'tinymce';
        });
        
    }
    


    public function restrict_media_library($query) {

        // 仅当用户具有manage_options权限时应用过滤器
        if (current_user_can('manage_options')) {
            return $query;
        }

        // 获取当前登录用户的ID
        $current_user_id = get_current_user_id();

        // 设置查询参数，仅返回当前用户上传的附件
        $query['author'] = $current_user_id;

        return $query;

    }


    public function update_file_md5_rename($filename) {

        if (!_cao('site_update_file_md5_rename',false)) {
            return $filename;
        }

        $info = pathinfo($filename);
        $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
        $name = basename($filename, $ext);
        return substr(md5($name), 0, 15) . $ext;

    }


    public function alter_link($link, $author_id, $author_nicename) {

        return str_replace('/' . $author_nicename, '/' . ZB_Code::encid($author_id), $link);

    }

    public function alter_query($query) {
        if ($query->is_author() && $query->query_vars['author_name'] != '') {
            if (ctype_xdigit($query->query_vars['author_name'])) {
                $user = get_user_by('id', ZB_Code::decid($query->query_vars['author_name']));
                if ($user) {
                    $query->set('author_name', $user->user_nicename);
                } else {
                    $query->is_404     = true;
                    $query->is_author  = false;
                    $query->is_archive = false;
                }
            } else {
                $query->is_404     = true;
                $query->is_author  = false;
                $query->is_archive = false;
            }
        }
        return;
    }

    public function alter_body_class($classes, $class) {
        if (is_author()) {
            global $wp_query;
            $author = $wp_query->get_queried_object();
            if (isset($author->user_nicename)) {
                $authorclass = array('author-' . sanitize_html_class($author->user_nicename, $author->ID));
                $classes     = array_diff($classes, $authorclass);
            }
        }
        return $classes;
    }


    public function manage_posts_columns($columns) {

        $post_type = get_post_type();
        if ($post_type == 'post') {
            return array_merge($columns, array(
                'cao_price' => '售价',
                'cao_vip_rate' => '会员折扣',
                'pay_info' => '销售数据')
            );
        } else {
            return $columns;
        }

    }

    public function manage_posts_custom_column($column, $post_id) {
        switch ($column) {
        case 'cao_price':
            $meta = get_post_meta($post_id, 'cao_price', true);
            if ($meta == '0') {
                echo sprintf('<b class="vip_badge">%s</b>','免费');
            } else {
                $meta = ($meta == '') ? '无' : (float) $meta . get_site_coin_name();
                echo sprintf('<b class="vip_badge">%s</b>',$meta);
            }

            break;
        case 'cao_vip_rate':
            $meta = get_post_meta($post_id, 'cao_vip_rate', true);
            $meta = ($meta == '' || $meta == '1') ? '无' : ($meta * 10) . '折';
            echo sprintf('<b class="vip_badge">%s</b>',$meta);
            break;
        case 'pay_info':
            global $wpdb;

            $data = $wpdb->get_row(
                $wpdb->prepare("SELECT COUNT(post_id) as count,SUM(pay_price) as sum_price FROM {$wpdb->cao_order_tbl} WHERE post_id = %d AND pay_status = 1", $post_id)
            );
            echo sprintf('销量：<b>%s</b><small style="display:block;color: green;">销售额：￥%s</small>',$data->count,sprintf('%0.2f', $data->sum_price));
            break;
        }

    }



    //后台用户列表钩子定制
    public function add_admin_user_column($columns) {

        unset($columns['posts']);
        unset($columns['role']);
        $columns['uid']           = 'UID';
        $columns['vip_type']      = '会员类型';
        $columns['cao_balance']   = get_site_coin_name().'余额';
        $columns['registered']    = '注册时间';
        $columns['last_login']    = '最近登录';
        $columns['userr_is_fuck'] = '账号状态';
        $columns['user_bind_ref'] = '推荐人';
        return $columns;
    }

    public function output_users_columns($var, $column_name, $user_id) {

        switch ($column_name) {
        case "uid":
            return sprintf('<code>%s</code>', $user_id);
            break;
        case "cao_balance":
            $balance = (int)get_user_meta($user_id,'cao_balance',true);
            return sprintf('<b>%s</b>', $balance);
            break;
        case "vip_type":
            $vip_data = get_user_vip_data($user_id);
            return sprintf('<code class="vip_badge %s">%s</code>', $vip_data['type'], $vip_data['name']);
            break;
        case "registered":
            $user = get_userdata($user_id);
            return sprintf('%s<br><small style="display:block;color: green;">IP：%s</small>', get_date_from_gmt($user->user_registered), get_user_meta($user_id, 'register_ip', true));
            break;
        case "last_login":
            $session = get_user_meta($user_id, 'session_tokens', true);
            if (!empty($session)) {
                $session = end($session);

                $ua = analyzeUserAgent($session['ua']);
                $ua_info = $ua['os'] .' - '. $ua['browser'];

                return sprintf('%s<small title="%s" style="display:block;color: green;">IP：%s</small>', wp_date('Y-m-d H:i:s', $session['login']), $ua_info, $session['ip']);
            } else {
                return '';
            }
            break;
        case "userr_is_fuck":
            $retVal = (empty(get_user_meta($user_id, 'cao_banned', true))) ? '<span style="color: green;">正常</span>' : '<span style="color: red;">封号</span>';
            return $retVal;
            break;
        case "user_bind_ref":
            $refuid = absint(get_user_meta($user_id, 'cao_ref_from', true));
            if (!empty($refuid)) {
                $refuser    = get_userdata($refuid);
                $user_login = (!empty($refuser->user_login)) ? $refuser->user_login : 'NULL';
                return sprintf('%s<small style="display:block;color: green;">UID：%s</small>', $user_login, $refuid);
            }
            break;
        }
    }

    public function users_sortable_columns($sortable_columns) {
        $sortable_columns['registered'] = 'registered';
        return $sortable_columns;
    }

    public function admin_pre_user_query($query) {

        global $pagenow, $wpdb;

        if (!is_admin() && 'users.php' != $pagenow) {
            return;
        }

        if (!isset($_REQUEST['orderby']) || $_REQUEST['orderby'] == 'registered') {
            $order = (isset($_REQUEST['order'])) ? $_REQUEST['order'] : '';
            if (!in_array($order, array('asc', 'desc'))) {
                $order = 'desc';
            }
            $query->query_orderby = "ORDER BY user_registered " . $order . "";
        }

        //封号筛选
        $is_type = isset($_REQUEST['is_type']) ? sanitize_text_field($_REQUEST['is_type']) : '';
        if (!empty($is_type)) {
            $query->query_where = str_replace(
                'WHERE 1=1',
                "WHERE 1=1 AND {$wpdb->users}.ID IN (
                    SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
                        WHERE {$wpdb->usermeta}.meta_key = 'cao_banned'
                        AND {$wpdb->usermeta}.meta_value ='1')",
                $query->query_where
            );
        }

        // VIP筛选
        $vip_type = isset($_REQUEST['vip_type']) ? sanitize_text_field($_REQUEST['vip_type']) : '';
        if (!empty($vip_type)) {

            if ($vip_type == 'vip') {

                $current_date       = wp_date('Y-m-d');
                $query->query_where = str_replace(
                    'WHERE 1=1',
                    "WHERE 1=1 AND {$wpdb->users}.ID IN (
                        SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
                            WHERE ({$wpdb->usermeta}.meta_key = 'cao_user_type' AND {$wpdb->usermeta}.meta_value ='vip')
                                  AND (SELECT meta_value FROM $wpdb->usermeta WHERE user_id = {$wpdb->users}.ID AND meta_key='cao_vip_end_time') > '{$current_date}'
                                  AND (SELECT meta_value FROM $wpdb->usermeta WHERE user_id = {$wpdb->users}.ID AND meta_key='cao_vip_end_time') != '9999-09-09'
                    )",
                    $query->query_where
                );
            } elseif ($vip_type == 'boosvip') {
                $query->query_where = str_replace(
                    'WHERE 1=1',
                    "WHERE 1=1 AND {$wpdb->users}.ID IN (
                        SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
                            WHERE ({$wpdb->usermeta}.meta_key = 'cao_user_type' AND {$wpdb->usermeta}.meta_value ='vip')
                                  AND (SELECT meta_value FROM $wpdb->usermeta WHERE user_id = {$wpdb->users}.ID AND meta_key='cao_vip_end_time') = '9999-09-09'
                    )",
                    $query->query_where
                );
            }

        }

    }

    public function admin_views_users($views) {
        global $wpdb;
        $site_vip_options = get_site_vip_options();

        unset($site_vip_options['no']);

        $date = wp_date('Y-m-d');

        foreach ($site_vip_options as $key => $item) {

            if ($key == 'vip') {
                $sql = "SELECT COUNT(DISTINCT {$wpdb->users}.ID)
                        FROM {$wpdb->users}
                        INNER JOIN {$wpdb->usermeta} AS vip_type
                            ON ({$wpdb->users}.ID = vip_type.user_id AND vip_type.meta_key = 'cao_user_type' AND vip_type.meta_value = 'vip')
                        INNER JOIN {$wpdb->usermeta} AS vip_end_time
                            ON ({$wpdb->users}.ID = vip_end_time.user_id AND vip_end_time.meta_key = 'cao_vip_end_time' AND vip_end_time.meta_value > '{$date}' AND vip_end_time.meta_value != '9999-09-09')";

            } elseif ($key == 'boosvip') {
                $sql = "SELECT COUNT(DISTINCT {$wpdb->users}.ID)
                        FROM {$wpdb->users}
                        INNER JOIN {$wpdb->usermeta} AS vip_type
                            ON ({$wpdb->users}.ID = vip_type.user_id AND vip_type.meta_key = 'cao_user_type' AND vip_type.meta_value = 'vip')
                        INNER JOIN {$wpdb->usermeta} AS vip_end_time
                            ON ({$wpdb->users}.ID = vip_end_time.user_id AND vip_end_time.meta_key = 'cao_vip_end_time' AND vip_end_time.meta_value = '9999-09-09')";
            }
            $count       = $wpdb->get_var($sql);
            $views[$key] = '<a href="' . admin_url('users.php') . '?vip_type=' . $key . '">' . $item['name'] . '<span class="count">（' . $count . '）</span></a>';
        }

        $count = $wpdb->get_var("SELECT count(a.ID) FROM $wpdb->users a INNER JOIN $wpdb->usermeta b ON ( a.ID = b.user_id ) WHERE 1=1 AND (  ( b.meta_key = 'cao_banned' AND b.meta_value = '1' )  )");

        $views['is_cao_banned'] = '<a href="' . admin_url('users.php') . '?is_type=cao_banned">封账号用户<span class="count">（' . $count . '）</span></a>';
        return $views;
    }

    public function loginform() {
        echo '<input type="hidden" name="' . $this->safe_login_key . '" value="' . $this->safe_login_password . '">';
    }

    public function safe_login_redirect() {

        if (empty($this->safe_login_password)) {
            return;
        }
        if (empty($_REQUEST[$this->safe_login_key]) || $_REQUEST[$this->safe_login_key] != $this->safe_login_password) {
            wp_safe_redirect(home_url('/user'));
        }
    }

    //后台菜单
    public function admin_menu() {

        $menu_role  = 'manage_options';
        $menu_slug  = 'zb-admin-page';
        $menu_icon  = 'dashicons-image-filter';
        $menu_title = '商城管理中心';

        //总 订单 会员 推广 下载

        if (isset($menu_role)) {
            global $wpdb;
            $today_time = get_today_time_range(); //今天时间戳信息 $today_time['start'],$today_time['end']
            $startime   = $today_time['start']; //今天开始时间戳
            $endtime    = $today_time['end']; //今天结束时间戳

            $order_num = $wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->cao_order_tbl} WHERE pay_status = 1 AND create_time BETWEEN %s AND %s", $startime, $endtime)
            );

        }

        add_menu_page($menu_title, '商城管理', $menu_role, $menu_slug, array($this, 'zb_admin_page_index'), $menu_icon);

        add_submenu_page(
            $menu_slug, $menu_title . '-商城总览V' . _THEME_VERSION, '商城总览', $menu_role, $menu_slug, array($this, 'zb_admin_page_index')
        );

        $_menu_title = $order_num ? sprintf('订单管理 <span class="awaiting-mod">+%d</span>', $order_num) : '订单管理';
        add_submenu_page(
            $menu_slug, $menu_title . '-订单管理', $_menu_title, $menu_role, 'zb-admin-page-order', array($this, 'zb_admin_page_order')
        );

        add_submenu_page(
            $menu_slug, $menu_title . '-充值卡管理', '卡券管理', $menu_role, 'zb-admin-page-cdk',array($this, 'zb_admin_page_cdk')
        );

        add_submenu_page(
            $menu_slug, $menu_title . '-推广中心', '推广中心', $menu_role, 'zb-admin-page-aff', array($this, 'zb_admin_page_aff')
        );

        add_submenu_page(
            $menu_slug, $menu_title . '-下载日志', '下载日志', $menu_role, 'zb-admin-page-down', array($this, 'zb_admin_page_down')
        );

        $ticket_num = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(id) FROM {$wpdb->cao_ticket_tbl} WHERE 1=1 AND status=0 AND create_time BETWEEN %s AND %s", $startime, $endtime)
        );
        $_menu_title = $ticket_num ? sprintf('工单管理 <span class="awaiting-mod">+%d</span>', $ticket_num) : '工单管理';
        add_submenu_page(
            $menu_slug, $menu_title . '-工单管理', $_menu_title, $menu_role, 'zb-admin-page-ticket', array($this, 'zb_admin_page_ticket')
        );


        add_submenu_page(
            $menu_slug, $menu_title . '-后台充值余额开通VIP', '后台充值', $menu_role, 'zb-admin-page-adminpay', array($this, 'zb_admin_page_adminpay')
        );

        add_submenu_page(
            $menu_slug, $menu_title . '-批量修改文章价格字段', '批量修改', $menu_role, 'zb-admin-page-editposts', array($this, 'zb_admin_page_editposts')
        );

        add_submenu_page(
            $menu_slug, $menu_title . '-缓存/临时/历史数据清理', '数据清理', $menu_role, 'zb-admin-page-clear', array($this, 'zb_admin_page_clear')
        );
    }

    public function load_view($template) {
        $views_dir = get_template_directory() . '/admin/pages/';
        if (file_exists($views_dir . $template)) {
            include_once $views_dir . $template;
            return true;
        }
        return false;
    }

    public function zb_admin_page_index() {
        $this->load_view('index.php');
    }

    public function zb_admin_page_order() {
        $this->load_view('order.php');
    }

    public function zb_admin_page_cdk() {
        $this->load_view('cdk.php');
    }
    
    public function zb_admin_page_aff() {
        $this->load_view('aff.php');
    }

    public function zb_admin_page_down() {
        $this->load_view('down.php');
    }

    public function zb_admin_page_ticket() {
        $this->load_view('ticket.php');
    }
    public function zb_admin_page_adminpay() {
        $this->load_view('admin-pay.php');
    }
    public function zb_admin_page_editposts() {
        $this->load_view('edit-posts.php');
    }

    public function zb_admin_page_clear() {
        $this->load_view('clear.php');
    }

    public function remove_profile_fields() {
        global $pagenow;

        // apply only to user profile or user edit pages
        if ($pagenow !== 'profile.php' && $pagenow !== 'user-edit.php') {
            return;
        }

        add_action('admin_footer', function () {
            ob_start();?>
            <script>
            jQuery(document).ready( function($) {
                $('.user-admin-color-wrap').closest('tr').remove();
                // $('.user-admin-color-wrap').parents('.form-table').remove();
                $('.application-passwords').closest('div').remove();
            });
            </script>
            <?php echo ob_get_clean();
        });

    }

    public function redirect_wp_admin() {
        global $pagenow; // Get current page

        if (!current_user_can('manage_options') && !wp_doing_ajax()) {
            wp_safe_redirect(home_url('/user'));
            exit();
        }
    }

    ########################用户类##############################

    //每次登录删除用户其他登录token设备 减轻数据库usermeta表压力
    public function destroy_the_user_all_logitoken($user_id) {
        // 排除管理员
        if ($user_id == 1 || current_user_can('manage_options')) {
            return false;
        } else {
            delete_user_meta($user_id, 'session_tokens');
        }
        return true;
    }

    public function get_the_user_all_logitoken($user_id) {
        $manager = WP_Session_Tokens::get_instance($user_id);
        // Destroy all others.
        return $manager->get_all();
    }

    //删除全站所有用户当前登录token T出所有用户登录
    public function drop_all_user_logitoken() {
        global $wpdb;
        return $wpdb->delete($wpdb->usermeta, array('meta_key' => 'session_tokens'));
    }

    //用户登录时触发
    public function login_hook_action($user_login, WP_User $user) {
        
        $user_id = $user->ID;
        
        //踢出其他登录设备信息
        // $this->destroy_the_user_all_logitoken($user->ID);

        //封号用户t出登录
        if (!empty(get_user_meta($user_id, 'cao_banned', true))) {
            wp_logout();
        }

        //发送消息推送"\n"
        if (site_push_server('admin', 'login')) {
            do_action('zb_send_mail_msg', [
                'email' => get_bloginfo('admin_email'),
                'title' => '用户登录提醒',
                'msg'   => sprintf('用户名：%s , </br>登录IP：%s ', $user->user_login, get_ip_address()),
            ]);
        }

        //添加网站动态
        ZB_Dynamic::add([
            'info' => __('成功登录本站', 'ripro'),
            'uid' => $user_id,
        ]);

    }

    // 用户注册时触发
    public function register_hook_action($user_id) {

        //注册时写入推荐人ID 没有推荐人 并且当前推荐人不是自己则写入
        $ref_id = zb_get_site_current_aff_id($user_id);
        if (!empty($ref_id)) {
            update_user_meta($user_id, 'cao_ref_from', $ref_id);
        }

        //记录注册IP地址
        $ip_address = get_ip_address();
        update_user_meta($user_id, 'register_ip', $ip_address);
        $user_obj = get_user_by('id', $user_id);

        //发送消息推送"\n"
        if (site_push_server('admin', 'register')) {
            do_action('zb_send_mail_msg', [
                'email' => get_bloginfo('admin_email'),
                'title' => '新用户注册提醒',
                'msg'   => sprintf('用户名：%s , </br>注册邮箱：%s , </br>注册IP：%s ', $user_obj->user_login, $user_obj->user_email, $ip_address),
            ]);
        }

        //添加网站动态
        ZB_Dynamic::add([
            'info' => __('成功注册加入本站', 'ripro'),
            'uid' => $user_id,
        ]);

    }

}
