<?php

$content = $args; //模板参数赋值
$post_id = get_the_ID();
$user_id = get_current_user_id();



// 评论可见权限
$user_pay_post_status = get_user_has_commented($user_id,$post_id);

?>

<div class="ri-hide-warp ri-reply-hide">
	<?php if ($user_pay_post_status): ?>
		<span class="hide-msg"><i class="fas fa-unlock me-1"></i><?php _e('已获得查看权限', 'ripro'); ?></span>
		<?php echo $content;?>
	<?php else: ?>
		<span class="hide-msg"><i class="fas fa-lock me-1"></i><?php _e('隐藏内容', 'ripro'); ?></span>
		<div class="hide-buy-warp">
			<div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本内容需评论后查看', 'ripro');?></div>
			<div class="buy-btns">
			  <a rel="nofollow noopener noreferrer" href="#respond" class="btn btn-dark px-4 rounded-pill"><i class="far fa-comments me-1"></i><?php _e('评论后查看', 'ripro');?></a>
			</div>
		</div>
	<?php endif; ?>
</div>

