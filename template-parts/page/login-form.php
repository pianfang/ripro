<?php

defined('ABSPATH') || exit;
###########################################


/**
 * 用户登录注册页面表单
 */


$is_login_action   = (get_query_var('uc-login-page') == 1) ? true : false;
$is_reg_action     = (get_query_var('uc-register-page') == 1) ? true : false;
$is_lostpwd_action = (get_query_var('uc-lostpwd-page') == 1) ? true : false;


?>

<!-- Form START -->
<form id="account-from" class="text-start account-from">

	
	<?php if ($is_login_action) :?>

	<?php if (!is_site_user_login()) :?>
		<div class="p-2">
			<p class="h3"><?php echo __('本站登录功能暂时关闭', 'ripro');?></p>
		</div>
	<?php else :?>

	<!-- 登录表单 -->
	<div class="mb-3">
		<label class="form-label"><?php _e('邮箱或用户名', 'ripro'); ?></label>
		<input type="text" class="form-control" name="user_name">
	</div>
	<div class="mb-3">
		<label class="form-label"><?php _e('密码', 'ripro'); ?><a class="login-btn ms-2 small text-muted" href="<?php echo esc_url( wp_lostpassword_url()); ?>"><?php _e('忘记密码？', 'ripro'); ?></a></label>
		<input class="form-control" type="password" name="user_password">
	</div>

    <!-- 图片验证码 -->
    <?php get_template_part( 'template-parts/page/img-captcha-input'); ?>

    <div class="mb-3 d-sm-flex justify-content-between">
		<div>
			<input id="rememberCheck" type="checkbox" class="form-check-input" name="remember" checked>
			<label class="form-check-label" for="rememberCheck"><?php _e('记住登录状态？', 'ripro'); ?></label>
		</div>
	</div>
	<input type="hidden" name="action" value="zb_user_login">
	
	<?php if (is_site_user_register()):?>
	<p class="mb-3"><?php _e('新用户？', 'ripro'); ?><a class="login-btn btn-link text-primary" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php _e('注册账号', 'ripro'); ?></a></p>
	<?php endif;?>

	<!-- Button -->
	<div><button type="submit" id="click-submit" class="btn btn-primary w-100 mb-3"><?php _e('立即登录', 'ripro'); ?></button></div>
	<?php endif;?>


    <?php elseif ($is_reg_action) :?>

    <?php if (!is_site_user_register()) :?>
		<div class="p-2">
			<p class="h3"><?php echo __('本站注册功能暂时关闭', 'ripro');?></p>
		</div>
	<?php else :?>

	<!-- 注册表表单 -->
	<div class="mb-3">
		<label class="form-label"><?php _e('用户名*', 'ripro'); ?></label>
		<input type="text" class="form-control" name="user_name" placeholder="<?php _e('英文名称', 'ripro'); ?>">
	</div>
	<div class="mb-3">
		<label class="form-label"><?php _e('邮箱*', 'ripro'); ?></label>
		<input type="email" class="form-control" name="user_email" placeholder="<?php _e('邮箱地址', 'ripro'); ?>">
	</div>

	<?php if (is_site_mail_captcha()) :?>
	<!-- 邮箱验证码 -->
	<div class="mb-3">
		<div class="input-group">
          <input type="text" class="form-control" placeholder="<?php echo esc_html__('邮箱验证码 *','ripro' ); ?>" name="mail_captcha_code" aria-label="<?php echo esc_html__('请输入邮箱验证码','ripro' ); ?>" disabled="disabled">
          <div class="input-group-append">
            <button class="btn btn-outline-info" type="button" id="captcha-mail"><?php echo esc_html__('发送验证码','ripro-v2' ); ?></button>
          </div>
        </div>
	</div>
	<?php endif;?>

	<div class="mb-3">
		<label class="form-label"><?php _e('密码*', 'ripro'); ?></label>
		<input class="form-control" type="password" name="user_password" placeholder="<?php _e('密码', 'ripro'); ?>">
	</div>
	<div class="mb-3">
		<input class="form-control" type="password" name="user_password_ok" placeholder="<?php _e('确认输入密码', 'ripro'); ?>">
	</div>

	<?php if (is_site_invitecode_register()):?>
	<div class="mb-3">
		<label class="form-label"><?php _e('邀请码* ', 'ripro');?><a target="_blank" class="ms-2 small text-danger" href="<?php echo _cao('site_invitecode_get_url');?>"><?php _e('获取邀请码', 'ripro');?></a></label>
		<input type="text" class="form-control" name="invite_code" placeholder="必填">
	</div>
	<?php endif;?>

	<!-- 图片验证码 -->
    <?php get_template_part( 'template-parts/page/img-captcha-input'); ?>
    
    <input type="hidden" name="action" value="zb_user_register">

    <?php if (is_site_user_login()):?>
    <p class="mb-3"><?php _e('已有账号？', 'ripro'); ?><a class="login-btn btn-link text-primary" href="<?php echo esc_url( wp_login_url()); ?>"><?php _e('登录账号', 'ripro'); ?></a></p>
    <?php endif;?>

	<!-- Button -->
	<div><button type="submit" id="click-submit" class="btn btn-primary w-100 mb-3"><?php _e('立即注册', 'ripro'); ?></button></div>
	<?php endif;?>

	
    <?php elseif ($is_lostpwd_action) :?>

    	<?php
    	$riresetpass  = wp_unslash(get_param('riresetpass', false, 'get'));
		$rifrp_action = wp_unslash(get_param('rifrp_action', false, 'get'));
		$key          = wp_unslash(get_param('key', false, 'get'));
		$uid          = wp_unslash(get_param('uid', false, 'get'));
		$DataArr      = compact('riresetpass', 'rifrp_action', 'key', 'uid');

		foreach ($DataArr as $key => $value) {
		    $is_riresetpass_from = (!empty($value)) ? true : false;
		}

        if (!empty($is_riresetpass_from)) :
	        foreach ($DataArr as $key => $value) {
        		echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
        	}?>

	        <p class="mb-3 small text-danger text-center"><?php _e('您正在重新设置账号密码', 'ripro'); ?></p>
			<div class="mb-3">
				<label class="form-label"><?php _e('新密码', 'ripro'); ?></label>
				<input class="form-control" type="password" name="user_password" placeholder="<?php _e('密码', 'ripro'); ?>">
			</div>
			<div class="mb-3">
				<label class="form-label"><?php _e('确认新密码', 'ripro'); ?></label>
				<input class="form-control" type="password" name="user_password_ok" placeholder="<?php _e('确认输入密码', 'ripro'); ?>">
	        	<input type="hidden" name="action" value="zb_user_restpwd">
			</div>

			<!-- 图片验证码 -->
    		<?php get_template_part( 'template-parts/page/img-captcha-input'); ?>

	        <?php if (is_site_user_login()):?>
			<p class="mb-3"><?php _e('想起密码？', 'ripro'); ?><a class="login-btn btn-link text-primary" href="<?php echo esc_url( wp_login_url()); ?>"><?php _e('登录账号', 'ripro'); ?></a></p>
			<?php endif;?>

			<div><button type="submit" id="click-submit" class="btn btn-danger w-100 mb-3"><?php _e('立即重置密码', 'ripro'); ?></button></div>


        <?php else:?>
        <!-- 找回密码表单 -->
		<div class="mb-3">
			<label class="form-label"><?php _e('账号绑定的邮箱*', 'ripro'); ?></label>
			<input type="email" class="form-control" name="user_email" placeholder="<?php _e('邮箱地址', 'ripro'); ?>">
			<input type="hidden" name="action" value="zb_user_lostpwd">
		</div>

		<!-- 图片验证码 -->
    	<?php get_template_part( 'template-parts/page/img-captcha-input'); ?>

		<p class="mb-3 small text-danger"><?php _e('重置密码链接会发送到您的邮箱，请通过重置链接修改新密码。', 'ripro'); ?></p>
		<p class="mb-3"><?php _e('想起密码？', 'ripro'); ?><a class="login-btn btn-link text-primary" href="<?php echo esc_url( wp_login_url()); ?>"><?php _e('登录账号', 'ripro'); ?></a></p>
		<div><button type="submit" id="click-submit" class="btn btn-danger w-100 mb-3"><?php _e('找回密码', 'ripro'); ?></button></div>
		<?php endif;?>

	<?php endif;?>


	<?php do_action( 'login_footer' );?>

	<!-- oauth mode -->
	<?php if (($is_login_action || $is_reg_action)) :?>
		<?php if (_cao('is_sns_qq',false) || _cao('is_sns_weixin',false)) :?>
		<div class="position-relative my-4">
			<hr>
			<p class="small bg-white position-absolute top-50 start-50 translate-middle px-2"><?php _e('快捷登录/注册', 'ripro'); ?></p>
		</div>
		<div class="d-grid gap-2 d-md-block text-center">
			<?php if (_cao('is_sns_qq',false)) :?>
			<a href="<?php echo get_oauth_permalink('qq');?>" class="btn btn-sm btn-info"><i class="fab fa-qq me-1"></i><?php _e('QQ登录', 'ripro'); ?></a>
			<?php endif;?>
			<?php if (_cao('is_sns_weixin',false)) :?>
			<a href="<?php echo get_oauth_permalink('weixin');?>" class="btn btn-sm btn-success"><i class="fab fa-weixin me-1"></i><?php _e('微信登录', 'ripro'); ?></a>
			<?php endif;?>
		</div>
		<?php endif;?>
	<?php endif;?>

	<!-- Copyright -->
	<p class="mb-0 mt-2 text-center small">
		<small class="text-muted">
			<?php _e('注册&登录即表示同意本站', 'ripro'); ?>
			<a target="_blank" class="btn-link" href="<?php echo _cao('site_user_agreement_href','#');?>"><?php _e('用户协议', 'ripro'); ?></a>、<a target="_blank" class="btn-link" href="<?php echo _cao('site_privacy_href','#');?>"><?php _e('隐私政策', 'ripro'); ?></a>
		</small>
		<br>
		<?php printf('<small class="text-muted">©%s <a target="_blank" href="%s">%s</a> All rights reserved</small>',wp_date('Y', time()),esc_url( home_url() ),get_bloginfo( 'name' ));?>
	</p>
</form>
<!-- Form END -->