<?php

new ZB_Clean();
/**
 * 主题加速优化清理
 */
class ZB_Clean {

    /**
     * [wordpress filter AND action load]
     * @Author Dadong2g
     * @date   2022-01-21
     */
    public function __construct() {

        // 移除wp自带顶部导航条
        if (_cao('show_admin_bar', true)) {
            add_filter('show_admin_bar', '__return_false');
        }

        //关闭wp自带loaz
        // add_filter('wp_lazy_loading_enabled', '__return_false');

        /**
         * 关闭古腾堡编辑器
         */
        if (!_cao('gutenberg_edit')) {
            add_filter('use_block_editor_for_post', '__return_false');
            remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
        }

        // 禁用古腾堡小工具
        if (!_cao('gutenberg_widgets', false)) {
            // Disables the block editor from managing widgets in the Gutenberg plugin.
            add_filter('gutenberg_use_widgets_block_editor', '__return_false');
            // Disables the block editor from managing widgets.
            add_filter('use_widgets_block_editor', '__return_false');
        }

        //禁用wordpress经典编辑器转码转义功能
        if(_cao('remove_wptexturize',false)){
            remove_filter('the_content', 'wptexturize');
        }

        //移除wp后台底部版本信息
        if (_cao('remove_admin_foote_wp', true)) {
            add_filter('admin_footer_text', '__return_empty_string');
            add_filter('update_footer', '__return_empty_string', 11);
        }

        //禁用搜索功能
        if (_cao('remove_site_search', false)) {
            add_action('parse_query', array($this, 'search_parse_query'));
            add_filter('get_search_form', '__return_empty_string');
        }

        add_action('wp_before_admin_bar_render', array($this, 'admin_bar_render'), 999);
        add_action('admin_menu', array($this, 'admin_render'), 999);
        add_action('admin_init', array($this, 'admin_init'), 999);
        add_action('init', array($this, 'theme_init'));
        add_action('after_setup_theme', array($this, 'after_setup_theme'));

        add_action(
            'wp_dashboard_setup',
            function () {
                // Remove the 'Welcome' panel
                remove_action('welcome_panel', 'wp_welcome_panel');

                // Remove 'Site health' metabox
                // remove_meta_box('dashboard_site_health', 'dashboard', 'normal');

                // Remove the 'At a Glance' metabox
                // remove_meta_box('dashboard_right_now', 'dashboard', 'normal');

                // Remove the 'Activity' metabox
                remove_meta_box('dashboard_activity', 'dashboard', 'normal');

                // Remove the 'WordPress News' metabox
                remove_meta_box('dashboard_primary', 'dashboard', 'side');

                // Remove the 'Quick Draft' metabox
                // remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
            }
        );

        if (_cao('remove_wp_xmlrpc', true)) {
            add_filter('xmlrpc_enabled', '__return_false');
        }
    }

    /**
     *  Hide or create new menus and items in the admin bar
     * @Author Dadong2g
     * @date   2022-01-21
     * @return [type]
     */
    public function admin_bar_render() {

        if (!_cao('remove_admin_bar_menu', true)) {
            return;
        }

        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo'); // Remove the WordPress logo
        $wp_admin_bar->remove_menu('about'); // Remove the about WordPress link
        $wp_admin_bar->remove_menu('wporg'); // Remove the about WordPress link
        $wp_admin_bar->remove_menu('documentation'); // Remove the WordPress documentation link
        $wp_admin_bar->remove_menu('support-forums'); // Remove the support forums link
        $wp_admin_bar->remove_menu('feedback'); // Remove the feedback link
        $wp_admin_bar->remove_menu('comments'); // Remove the comments link

    }

    public function admin_render() {

        if (_cao('remove_admin_menu', true)) {
            // Remove Dashboard
            remove_menu_page('index.php');
            // Remove Dashboard -> Update Core notice
            remove_submenu_page('index.php', 'update-core.php');
        }
        // Remove Appearance -> Editor
        remove_submenu_page('themes.php', 'theme-editor.php');

    }

    public function admin_init() {

        //删除仪表盘
        if (_cao('remove_admin_menu', true)) {
            global $pagenow; // Get current page
            $redirect = get_admin_url(null, 'edit.php'); // Where to redirect

            if ($pagenow == 'index.php') {
                wp_redirect($redirect, 301);
                exit;
            }
        }

    }

    public function theme_init() {
        if (_cao('remove_emoji', true)) {
            // Front-end
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_print_styles', 'print_emoji_styles');

            // Admin
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('admin_print_styles', 'print_emoji_styles');

            // Feeds
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');

            // Embeds
            remove_filter('embed_head', 'print_emoji_detection_script');

            // Emails
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

            // Disable from TinyMCE editor. Disabled in block editor by default
            add_filter(
                'tiny_mce_plugins',
                function ($plugins) {
                    if (is_array($plugins)) {
                        $plugins = array_diff($plugins, array('wpemoji'));
                    }

                    return $plugins;
                }
            );

            /**
             * Finally, disable it from the database also, to prevent characters from converting
             *  There used to be a setting under Writings to do this
             *  Not ideal to get & update it here - but it works :/
             */
            if ((int) get_option('use_smilies') === 1) {
                update_option('use_smilies', 0);
            }
        }

        if (_cao('remove_wp_head_more', true)) {
            // Remove the Really Simple Discovery service link
            remove_action('wp_head', 'rsd_link');

            // Remove the link to the Windows Live Writer manifest
            remove_action('wp_head', 'wlwmanifest_link');

            // Remove the general feeds
            remove_action('wp_head', 'feed_links', 2);

            // Remove the extra feeds, such as category feeds
            remove_action('wp_head', 'feed_links_extra', 3);

            // Remove the displayed XHTML generator
            remove_action('wp_head', 'wp_generator');

            // Remove the REST API link tag
            remove_action('wp_head', 'rest_output_link_wp_head', 10);

            // Remove oEmbed discovery links.
            remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

            // Remove rel next/prev links
            remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

            // Remove prefetch url
            remove_action('wp_head', 'wp_resource_hints', 2);
        }

        if (_cao('remove_wp_rest_api', false)) {
            // 屏蔽 REST API
            if (version_compare(get_bloginfo('version'), '4.7', '>=')) {
                function lxtx_disable_rest_api($access) {
                    return new WP_Error('rest_api_cannot_acess', '无访问权限', array('status' => rest_authorization_required_code()));
                }
                add_filter('rest_authentication_errors', 'lxtx_disable_rest_api');
            } else {
                // Filters for WP-API version 1.x
                add_filter('json_enabled', '__return_false');
                add_filter('json_jsonp_enabled', '__return_false');
                // Filters for WP-API version 2.x
                add_filter('rest_enabled', '__return_false');
                add_filter('rest_jsonp_enabled', '__return_false');
            }
            // 移除头部 wp-json 标签和 HTTP header 中的 link
            remove_action('template_redirect', 'rest_output_link_header', 11);
            remove_action('wp_head', 'rest_output_link_wp_head', 10);
            remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
        }
    }

    public function search_parse_query($query, $error = true) {
        if (is_search()) {
            $query->is_search       = false;
            $query->query_vars['s'] = false;
            $query->query['s']      = false;
            // Send to 404
            $query->is_404 = true;
        }
    }

    public function after_setup_theme() {
        if (_cao('remove_wp_img_attributes', true)) {
            /**
             * Remove srcset on images
             */
            add_filter('wp_calculate_image_srcset', '__return_false');

            /**
             * Remove lazy loading
             */
            add_filter('wp_lazy_loading_enabled', '__return_false');
            add_filter('wp_lazy_loading_enabled', '__return_false', 'img'); // disable only on img elements
            add_filter('wp_lazy_loading_enabled', '__return_false', 'iframe'); // disable only on iframe element

            /**
             * Remove size attributes from images
             */
            if (!function_exists('remove_size_attributes')) {
                function remove_size_attributes($html) {
                    return preg_replace('/(width|height)="\d*"/', '', $html);
                }

                // Remove size attributes from thumbnail images
                add_filter('post_thumbnail_html', 'remove_size_attributes');

                // Remove size attributes in the editor
                add_filter('image_send_to_editor', 'remove_size_attributes');

                // Remove size attributes from the_content
                add_filter('the_content', 'remove_size_attributes');
            }
        }

    }

}
