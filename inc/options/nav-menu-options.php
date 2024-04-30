<?php

defined('ABSPATH') || exit;

if (!class_exists('CSF')) {
    return;
}

$prefix = '_prefix_menu_options';

CSF::createNavMenuOptions($prefix, array(
    'data_type' => 'unserialize',
));

CSF::createSection($prefix, array(
    'fields' => array(
        array(
            'id'    => 'menu_icon',
            'type'  => 'icon',
            'title' => '菜单图标',
        ),
    ),
));

unset($prefix);
