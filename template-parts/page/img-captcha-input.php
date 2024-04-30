<?php if (is_site_img_captcha()):?>
<div class="input-group mb-3">
  <input type="text" class="form-control rounded-2" name="captcha_code" placeholder="<?php _e('验证码', 'ripro'); ?>">
  <img id="captcha-img" class="rounded-2 lazy" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png');?>" title="<?php _e('点击刷新验证码', 'ripro'); ?>" />
</div>
<?php endif;?>