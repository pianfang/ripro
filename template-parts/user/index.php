<?php

defined('ABSPATH') || exit;
###########################################

/**
 * 用户中心 UC 页面入口模板
 */


if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/login'));exit;
}

get_header();

$menus = get_uc_menus();

$page_action = (array_key_exists(get_query_var('uc-page-action'), $menus)) ? get_query_var('uc-page-action') : 'profile' ;

$bg_image = get_template_directory_uri() . '/assets/img/bg.png';

?>


<div class="container mt-2 mt-sm-4">

    <div class="row g-2 g-md-3 g-lg-4">

        <div class="d-none d-lg-block col-md-12 col-lg-3 h-100" data-sticky>
        <?php get_template_part('/template-parts/user/part/menu');?>
        </div>

        <div class="col-md-12 col-lg-9" data-sticky-content>
        <?php get_template_part('/template-parts/user/part/' . $page_action);?>
        </div>

    </div>

</div>
<?php

get_footer();
