<?php

// 菜单
global $current_user;
$uc_vip_info = get_user_vip_data($current_user->ID);
?>

<div class="bg-white rounded overflow-hidden">
	<div class="card text-center">
		<div class="avatar avatar-xl mb-2">
			<img class="avatar-img rounded-circle border border-white border-3 shadow" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="">
		</div>
		<h5 class="d-flex justify-content-center align-items-center mb-1">
			<?php echo $current_user->display_name; ?>
		</h5>
		<p class="mb-1"><?php echo $current_user->user_login; ?></p>
		<?php if(is_site_shop()):?>
		<p class="mb-1"><?php echo zb_get_user_badge($current_user->ID,'span','mb-0'); ?></p>
		<?php endif;?>

		<?php if ($uc_vip_info['type'] != 'no') {
            echo "<sub>" . sprintf(__('到期时间：%s', 'ripro'), $uc_vip_info['end_date']) . "</sub>";
        } else {
            echo "<sub>" . sprintf(__('注册时间：%s', 'ripro'), date('Y-m-d', strtotime($current_user->user_registered))) . "</sub>";
        }?>
	</div>

	<?php if(is_site_shop()):?>
	<div class="card small text-center bg-success bg-opacity-10">
	    <p class="mb-2 text-success"><?php printf(__('每天可下载数(%d)', 'ripro'), $uc_vip_info['downnums']['total']);?></p>
	    <span class="badge bg-primary bg-opacity-10 text-primary mb-1"><?php printf(__('今日已用(%d)', 'ripro'), $uc_vip_info['downnums']['used']);?></span>
	    <span class="badge bg-primary bg-opacity-10 text-primary mb-1"><?php printf(__('今日剩余(%d)', 'ripro'), $uc_vip_info['downnums']['not']);?></span>
	</div>
	<?php endif;?>
</div>

<div class="bg-white rounded overflow-hidden mt-4 mb-4">
	<?php 
	$uc_action = get_query_var('uc-page-action');
	$uc_menus = get_uc_menus();
	$menu_items = '<ul class="uc-menu-warp">';
	foreach ($uc_menus as $key => $item) {
	  $class = ($uc_action === $key) ? 'menu-item current-menu-item' : 'menu-item';
	  $menu_items .= sprintf(
	    '<li class="%s"><a href="%s"><i class="%s me-1"></i>%s</a></li>',
	    esc_attr($class),
	    esc_url(get_uc_menu_link($key)),
	    esc_attr($item['icon']),
	    esc_html($item['title'])
	  );
	}
	$menu_items .='</ul>';
	echo $menu_items;
	?>
</div>
