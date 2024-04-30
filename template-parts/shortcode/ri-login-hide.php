<?php

$content = $args; //模板参数赋值
$post_id = get_the_ID();
$user_id = get_current_user_id();



// 登录可见权限
$user_pay_post_status = is_user_logged_in();

?>

<div class="ri-hide-warp ri-login-hide">
	<?php if ($user_pay_post_status): ?>
		<span class="hide-msg"><i class="fas fa-unlock me-1"></i><?php _e('已获得查看权限', 'ripro'); ?></span>
		<?php echo $content;?>
	<?php else: ?>
		<span class="hide-msg"><i class="fas fa-lock me-1"></i><?php _e('隐藏内容', 'ripro'); ?></span>
		<div class="hide-buy-warp">
			<div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本内容需登录后查看', 'ripro');?></div>
			<div class="buy-btns">
			  <a rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>" class="login-btn btn btn-primary px-4 rounded-pill"><i class="far fa-user me-1"></i><?php _e('登录后查看', 'ripro');?></a>
			</div>
		</div>
	<?php endif; ?>
</div>

