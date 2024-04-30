<?php

if (!defined('_OPTIONS_PRE')) {
    // Replace the version number of the theme on each release.
    define('_OPTIONS_PRE', '_ripro_new_options');
}

/**
 * Custom function for get an option
 */
if (!function_exists('_cao')) {
    function _cao($option = '', $default = null) {
        $options_meta = _OPTIONS_PRE;
        $options      = get_option($options_meta);
        return (isset($options[$option])) ? $options[$option] : $default;
    }
}

//获取旧版设置数据
if (!function_exists('_cao_old')) {
    function _cao_old($option = '', $default = null) {
        $options_meta = '_riprov2_options';
        $options = get_option($options_meta);
        
        if (strpos($option,':') !== false) {
            $array = explode(':', $option);
            $temp = $options;
            $temp_val = $default;
            foreach ($array as $key) {
                if (isset($temp[$key])) {
                    $temp = $temp[$key];
                    $temp_val = $temp;
                }else{
                   break;
                }
            }
            unset($temp);
            return $temp_val;
        } else {
            // 参数中没有冒号，不做处理
            return (isset($options[$option])) ? $options[$option] : $default;
        }
    }
}

//加载配置文件
if (!class_exists('CSF')) {


    $options = array(
        '/plugins/codestar-framework/codestar-framework.php', //core
        '/options/admin-options.php', //admin
        '/options/metabox-options.php', //metabox
        '/options/profile-options.php', //profile
        '/options/nav-menu-options.php', //nav
        '/options/shortcode-options.php', //shortcode
        '/options/widget-options.php', //widget
        '/options/taxonomy-options.php', //taxonomy
    );

    foreach ($options as $dir) {
        require_once get_template_directory() . '/inc' . $dir;
    }
    

}

/**
 * 主题设置初始化
 * @Author   Dadong2g
 * @DateTime 2021-01-16T14:29:56+0800
 * @param    [type]                   $params [description]
 * @return   [type]                           [description]
 */
function zbzhuti_option_init($params) {
    $params['framework_title'] = 'RiPro-V5主题设置 <small>正式版 V' . _THEME_VERSION . '</small>';
    $params['menu_title']      = '主题设置';
    $params['theme']           = 'light'; //  light OR dark
    $params['show_bar_menu']   = false;
    $params['enqueue_webfont'] = false;
    $params['enqueue']         = false;
    $params['show_search']     = false;
    $params['ajax_save']       = false;
    $params['footer_credit']   = '';
    $params['footer_text']     = '感谢您使用RiPro-V5主题进行创作运营，本主题相关文档教程地址 <a href="https://ritheme.com/document/category/ripro-v5" target="_blank">www.ritheme.com</a>';
    return $params;
}
add_filter('csf_' . _OPTIONS_PRE . '_args', 'zbzhuti_option_init');
