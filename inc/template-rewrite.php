<?php

new ZB_Rewrite();
/**
 * 伪静态路由等配置
 */
class ZB_Rewrite {

    public function __construct() {
        //路由伪静态
        add_action('generate_rewrite_rules', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'));
        add_action('template_include', array($this, 'template_include'));
        // 页面跳转捕获
        add_action('template_redirect', array($this, 'custom_redirect'));

        if (_cao('site_no_categoty', false)) {
            add_action('init', array($this, 'no_category_base_permastruct'));
            add_action('created_category', array($this, 'no_category_base_refresh_rules'));
            add_action('delete_category', array($this, 'no_category_base_refresh_rules'));
            add_action('edited_category', array($this, 'no_category_base_refresh_rules'));

            add_filter('category_rewrite_rules', array($this, 'no_category_base_rewrite_rules'));
            add_filter('query_vars', array($this, 'no_category_base_query_vars')); // Adds 'category_redirect' query variable
            add_filter('request', array($this, 'no_category_base_request')); // Redirects if 'category_redirect' is set
        }
    }

    public function no_category_base_refresh_rules() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    public function no_category_base_permastruct() {
        global $wp_rewrite;
        global $wp_version;

        if ($wp_version >= 3.4) {
            $wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
        } else {
            $wp_rewrite->extra_permastructs['category'][0] = '%category%';
        }
    }

    public function no_category_base_rewrite_rules($category_rewrite) {
        global $wp_rewrite;
        $category_rewrite = array();

        /* WPML is present: temporary disable terms_clauses filter to get all categories for rewrite */
        if (class_exists('Sitepress')) {
            global $sitepress;

            remove_filter('terms_clauses', array($sitepress, 'terms_clauses'));
            $categories = get_categories(array('hide_empty' => false));
            add_filter('terms_clauses', array($sitepress, 'terms_clauses'));
        } else {
            $categories = get_categories(array('hide_empty' => false));
        }

        foreach ($categories as $category) {
            $category_nicename = $category->slug;

            if ($category->parent == $category->cat_ID) {
                $category->parent = 0;
            } elseif ($category->parent != 0) {
                $category_nicename = get_category_parents($category->parent, false, '/', true) . $category_nicename;
            }

            $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$']    = 'index.php?category_name=$matches[1]&feed=$matches[2]';
            $category_rewrite["({$category_nicename})/{$wp_rewrite->pagination_base}/?([0-9]{1,})/?$"] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
            $category_rewrite['(' . $category_nicename . ')/?$']                                       = 'index.php?category_name=$matches[1]';
        }

        // Redirect support from Old Category Base
        $old_category_base                               = get_option('category_base') ? get_option('category_base') : 'category';
        $old_category_base                               = trim($old_category_base, '/');
        $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';

        return $category_rewrite;
    }

    public function no_category_base_query_vars($public_query_vars) {
        $public_query_vars[] = 'category_redirect';
        return $public_query_vars;
    }

    public function no_category_base_request($query_vars) {
        if (isset($query_vars['category_redirect'])) {
            $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
            status_header(301);
            header("Location: $catlink");
            exit();
        }

        return $query_vars;
    }

    public function rewrite_rules($wp_rewrite) {

        $new_rules = array(
            '^oauth/([A-Za-z]+)?$'              => 'index.php?oauth=$matches[1]',
            '^oauth/([A-Za-z]+)/callback?$'     => 'index.php?oauth=$matches[1]&oauth_callback=1',
            '^pay/callback/([A-Za-z]+)/(\w+)?$' => 'index.php?pay_callback=$matches[1]&pay_callback_file=$matches[2]',
            '^goto?$'                           => 'index.php?goto=1',
            '^vip-prices?$'                     => 'index.php?vip-prices-page=1',
            '^tags?$'                           => 'index.php?tags-page=1',
            '^series?$'                         => 'index.php?series-page=1',
            '^links?$'                          => 'index.php?links-page=1',
            '^tougao?$'                         => 'index.php?tougao-page=1',
            '^user/?$'                          => 'index.php?uc-page=1',
            '^user/([^/]*)/?$'                  => 'index.php?uc-page=1&uc-page-action=$matches[1]',
            '^login?$'                          => 'index.php?uc-login-page=1',
            '^register?$'                       => 'index.php?uc-register-page=1',
            '^lostpwd?$'                        => 'index.php?uc-lostpwd-page=1',
        );

        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
        return $wp_rewrite->rules;

    }

    public function query_vars($query_vars) {
        $custom_query_vars = [
            'oauth',
            'oauth_callback',
            'goto',
            'vip-prices-page',
            'tags-page',
            'series-page',
            'links-page',
            'tougao-page',
            'uc-page',
            'uc-page-action',
            'aff',
            'pay_callback',
            'pay_callback_file',
            'uc-login-page',
            'uc-register-page',
            'uc-lostpwd-page',
        ];

        return array_merge($query_vars, $custom_query_vars);
    }

    public function template_include($template) {
        global $wp_query;
        //模板load
        $templates = array(
            'vip-prices-page'  => '/template-parts/page/vip-prices.php',
            'tags-page'        => '/template-parts/page/tags.php',
            'series-page'      => '/template-parts/page/series.php',
            'links-page'       => '/template-parts/page/links.php',
            'tougao-page'      => '/template-parts/page/tougao.php',
            'uc-page'          => '/template-parts/user/index.php',
            'uc-login-page'    => '/template-parts/page/login-register.php',
            'uc-register-page' => '/template-parts/page/login-register.php',
            'uc-lostpwd-page'  => '/template-parts/page/login-register.php',
            'oauth'            => '/inc/sns/%s/%s.php',
            'goto'             => '/inc/goto.php',
            'pay_callback'     => '/inc/shop/%s/%s.php',
        );

        if (!is_site_tags_page()) {
            unset($templates['tags-page']);
        }
        if (!is_site_link_manager_page()) {
            unset($templates['links-page']);
        }
        if (!is_site_vip_price_page()) {
            unset($templates['vip-prices-page']);
        }
        if (!is_site_tougao()) {
            unset($templates['tougao-page']);
        }

        foreach ($templates as $query_var => $tpl) {

            if (get_query_var($query_var)) {

                switch ($query_var) {
                case 'oauth':
                    $sns    = strtolower(get_query_var('oauth'));
                    $sns_cb = strtolower(get_query_var('oauth_callback'));
                    $file   = !empty($sns_cb) ? 'callback' : 'login';
                    $tpl    = sprintf($tpl, $sns, $file);
                    break;
                case 'pay_callback':
                    $pay_type = strtolower(get_query_var('pay_callback'));
                    $pay_file = strtolower(get_query_var('pay_callback_file'));
                    $tpl      = sprintf($tpl, $pay_type, $pay_file);
                    break;
                }

                if (is_file(get_stylesheet_directory() . $tpl)) {
                    $load_file = get_stylesheet_directory() . $tpl;
                }elseif (is_file(get_template_directory() . $tpl)) {
                    $load_file = get_template_directory() . $tpl;
                }else{
                    $load_file = false;
                }

                if ($load_file) {
                    if (isset($wp_query)) {
                        $wp_query->is_home = false;
                    }
                    return $load_file;
                }
            }
        }

        return $template;

    }

    //自定义页面跳转拦截
    public function custom_redirect() {
        // 捕获推荐人ID aff
        if ($affid = get_query_var('aff')) {
            ZB_Cookie::set('aff', absint($affid)); //设置推荐人ID 到cookie到浏览器缓存
        }
    }

}
