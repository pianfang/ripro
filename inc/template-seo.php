<?php

new ZB_Seo;

// 内置SEO库
class ZB_Seo {

    public $is_seo           = false;
    public $site_no_categoty = true;
    public $site_seo         = array();
    public $separator        = '-';

    public function __construct() {
        $this->is_seo           = _cao('is_theme_seo', false);
        $this->site_no_categoty = _cao('site_no_categoty', true);
        $this->site_seo         = _cao('site_seo');
        $this->separator        = (isset($this->site_seo['separator'])) ? $this->site_seo['separator'] : '-';
        add_filter('excerpt_more', array($this, 'new_excerpt_more'));
        add_action('wp_head', array($this, 'custom_head_favicon'), 6);
        add_filter('pre_get_document_title', array($this, 'other_page_title'), 99);

        if ($this->is_seo && is_array($this->site_seo)) {
            add_filter('document_title_separator', array($this, 'custom_title_separator_to_line'));
            add_filter('document_title_parts', array($this, 'custom_post_document_parts'));
            add_filter('pre_get_document_title', array($this, '_wp_get_document_title'));
            add_filter('excerpt_length', array($this, 'excerpt_length'));
            add_action('wp_head', array($this, 'custom_head'), 5);
        }

        if ($this->site_no_categoty && !function_exists('no_category_base_refresh_rules')) {
            // code...
        }
    }

    //自定义路由页面标题规则
    public function other_page_title($title) {
        
        if (get_query_var('uc-page')) {
            $stitle = __('个人中心', 'ripro');
        } elseif (get_query_var('uc-login-page')) {
            $stitle = __('用户登录', 'ripro');
        } elseif (get_query_var('uc-register-page')) {
            $stitle = __('用户注册', 'ripro');
        } elseif (get_query_var('uc-lostpwd-page')) {
            $stitle = __('找回密码', 'ripro');
        } elseif (get_query_var('tags-page')) {
            $stitle = __('标签云', 'ripro');
        } elseif (get_query_var('links-page')) {
            $stitle = __('网址导航', 'ripro');
        } elseif (get_query_var('vip-prices-page')) {
            $stitle = __('VIP介绍', 'ripro');
        }elseif (get_query_var('tougao-page')) {
            $stitle = __('投稿发布', 'ripro');
        }else{
            $stitle = false;
        }

        $ttitle = ($stitle) ? $stitle . $this->separator . get_bloginfo('name', 'display') : $title;
        return $ttitle;
    }

    public function custom_head_favicon() {
        if ($site_favicon = _cao('site_favicon')) {
            echo "<link href=\"$site_favicon\" rel=\"icon\">\n";
        }
    }

    //修饰更多字符
    public function new_excerpt_more($more) {
        return '...';
    }

    //摘要长度
    public function excerpt_length($length) {
        return 120;
    }

    //标题分隔符修改成 “-”
    public function custom_title_separator_to_line() {
        return $this->separator; //自定义标题分隔符
    }

    //自定义SEO标题 custom_title
    public function custom_post_document_parts($parts) {

        $_meta_title = '';

        if (is_singular()) {
            $_meta_title = get_post_meta(get_the_ID(), 'post_titie', true);
        } elseif (is_category() || is_tag()) {
            # 分类/标签
            $term_id     = get_queried_object_id();
            $_meta_title = get_term_meta($term_id, 'seo-title', true);
        }

        if (!empty($_meta_title)) {
            $parts['title'] = trim($_meta_title);
        }

        return $parts;
    }

    //自定义顶部钩子 添加描述 关键词 meta_og
    public function custom_head() {
        global $post;
        $key     = '';
        $desc    = '';
        $meta_og = array();
        if (is_home()) {
            # 首页
            $key  = $this->site_seo['keywords'];
            $desc = $this->site_seo['description'];
        } elseif (is_singular()) {

            $meta_keywords    = get_post_meta($post->ID, 'keywords', true);
            $meta_description = get_post_meta($post->ID, 'description', true);
            # 文章 页面
            if (!empty($meta_keywords)) {
                $key = trim($meta_keywords);
            } else {
                if (get_the_tags($post->ID)) {
                    foreach (get_the_tags($post->ID) as $tag) {
                        $key .= $tag->name . ',';
                    }
                }
                foreach (get_the_category($post->ID) as $category) {
                    $key .= $category->cat_name . ',';
                }
            }

            if (!empty($meta_description)) {
                $desc = trim($meta_description);
            } else {
                $excerpt = get_the_excerpt($post->ID);
                if (empty($excerpt)) {
                    $excerpt = $post->post_content;
                }
                $desc = wp_trim_words(strip_shortcodes($excerpt), 120, '');
            }

        } elseif (is_category() || is_tag() || is_taxonomy( 'series' )) {
            # 分类/标签
            $termObj = get_queried_object();

            $key  = $termObj->name . ',' . $termObj->slug;
            $desc = trim($termObj->description);

            $meta_keywords    = get_term_meta($termObj->term_id, 'seo-keywords', true);
            $meta_description = get_term_meta($termObj->term_id, 'seo-description', true);

            if (!empty($meta_keywords)) {
                $key = trim($meta_keywords);
            }

            if (!empty($meta_description)) {
                $desc = trim($meta_description);
            }

        }

        if (!empty($key)) {
            echo "<meta name=\"keywords\" content=\"$key\">\n";
        }
        if (!empty($desc)) {
            echo "<meta name=\"description\" content=\"$desc\">\n";
        }

    }

    //标题修正优化
    public function _wp_get_document_title() {

        global $page, $paged;

        $title = array(
            'title' => '',
        );

        // If it's a 404 page, use a "Page not found" title.
        if (is_404()) {
            $title['title'] = __('Page not found');

            // If it's a search, use a dynamic search results title.
        } elseif (is_search()) {
            /* translators: %s: Search query. */
            $title['title'] = sprintf(__('Search Results for &#8220;%s&#8221;'), get_search_query());

            // If on the front page, use the site title.
        } elseif (is_front_page()) {
            $title['title'] = get_bloginfo('name', 'display');

            // If on a post type archive, use the post type archive title.
        } elseif (is_post_type_archive()) {
            $title['title'] = post_type_archive_title('', false);

            // If on a taxonomy archive, use the term title.
        } elseif (is_tax()) {
            $title['title'] = single_term_title('', false);

            /*
         * If we're on the blog page that is not the homepage
         * or a single post of any post type, use the post title.
         */
        } elseif (is_home() || is_singular()) {
            $title['title'] = single_post_title('', false);

            // If on a category or tag archive, use the term title.
        } elseif (is_category() || is_tag()) {
            $title['title'] = single_term_title('', false);

            // If on an author archive, use the author's display name.
        } elseif (is_author() && get_queried_object()) {
            $author         = get_queried_object();
            $title['title'] = $author->display_name;

            // If it's a date archive, use the date as the title.
        } elseif (is_year()) {
            $title['title'] = get_the_date(_x('Y', 'yearly archives date format'));

        } elseif (is_month()) {
            $title['title'] = get_the_date(_x('F Y', 'monthly archives date format'));

        } elseif (is_day()) {
            $title['title'] = get_the_date();
        }

        // Add a page number if necessary.
        if (($paged >= 2 || $page >= 2) && !is_404()) {
            /* translators: %s: Page number. */
            $title['page'] = sprintf(__('Page %s'), max($paged, $page));
        }

        if (is_front_page()) {
            $title['tagline'] = get_bloginfo('description', 'display');
        } else {
            $title['site'] = get_bloginfo('name', 'display');
        }

        $sep = apply_filters('document_title_separator', '-');

        $title = apply_filters('document_title_parts', $title);

        $title = implode("$sep", array_filter($title));

        $title = wptexturize($title);

        $title = convert_chars($title);
        $title = esc_html($title);
        $title = capital_P_dangit($title);

        return $title;
    }

}
