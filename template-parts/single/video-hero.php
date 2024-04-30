<?php
//文章顶部视频模块

$post_id = get_the_ID();
$user_id = get_current_user_id();


//是否免费视频
$is_free_video = (bool)get_post_meta( $post_id, 'cao_is_video_free', true);


if (!is_site_shop() && !$is_free_video) {
	return;
}


// 用户是否已购买或者可免费获取
$user_pay_post_status = get_user_pay_post_status($user_id, $post_id);

//是否免费资源并且需要登录后查看
$is_user_login_get_status = $user_pay_post_status === true && empty($user_id);

// 免费资源下载无需登录
if (!empty(_cao('is_free_nologin_down', 1))) {
    $is_user_login_get_status = false;
}


$video_data = get_post_meta( $post_id, 'video_url_new', true);
// $video_test_src = get_post_meta( $post_id, 'thumb_video_src', true);


//格式化数据
foreach ($video_data as $key => $item) {

	$_title = sprintf(__('第%d集', 'ripro'),$key+1);
	if (!empty($item['title'])) {
		$_title = $item['title'] . '-' .$_title;
	}
	
	$_src = ($user_pay_post_status || $is_free_video) && !$is_user_login_get_status ? $item['src'] : '';

	$_img = $item['img'];
	$_type = zb_get_video_source_types($item['src']);
	// 视频信息
    $video_data[$key] = [
        'title' => $_title,
        'src' => $_src,
        'img' => $_img,
        'type' => $_type,
    ];

}

//默认封面
$hero_bg = (!empty($video_data[0]['img'])) ? $video_data[0]['img'] : esc_url(zb_get_thumbnail_url());
$poster_attr = (!empty($video_data[0]['img'])) ? 'poster="'.$video_data[0]['img'].'"' : '';


?>

<div class="archive-hero video-hero text-center">
    <div class="archive-hero-bg lazy" data-bg="<?php echo $hero_bg; ?>"></div>
        <div class="container video-hero-container">

	        	<div class="row g-0">

	        		<div class="col-lg-9 col-12">
		            	<div class="ri-video-warp">
						<?php if (($user_pay_post_status || $is_free_video) && !$is_user_login_get_status) : ?>
							<video class="video-js vjs-16-9" <?php echo $poster_attr;?> controls data-setup="{}" oncontextmenu="return false;">
								<source src="<?php echo $video_data[0]['src'];?>" type="<?php echo $video_data[0]['type'];?>">
							</video>
						<?php else: ?>
							<!-- 购买区域STAR -->
							<div class="ri-video-view">
								<div class="video-buy-warp">
									

									<?php if ($is_user_login_get_status):?>
										<div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本内容登录后免费播放', 'ripro');?></div>
										<div class="buy-btns">
										  <a rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>" class="login-btn btn btn-outline-info px-4 rounded-pill"><i class="far fa-user me-1"></i><?php _e('登录后播放', 'ripro');?></a>
										</div> 

									<?php else:?>
										<div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本内容需权限播放', 'ripro');?></div>
										<div class="buy-btns">
										  <button class="btn btn-danger px-4 rounded-pill js-pay-action" data-id="<?php echo $post_id;?>" data-type="1" data-info=""><i class="fab fa-shopify me-1"></i><?php _e('购买播放权限', 'ripro');?></button>
										</div>

										<div class="buy-desc">

											<ul class="prices-info">
											<?php 
											$site_vip_options = get_site_vip_options();

											$price_names = [
												'default' => __('原价','ripro'),
												'no' => $site_vip_options['no']['name'],
												'vip' => $site_vip_options['vip']['name'],
												'boosvip' => $site_vip_options['boosvip']['name'],
											];
											//价格组
											$post_price_data = get_post_price_data($post_id);

											$default_price = $post_price_data['default'];

											foreach ($post_price_data as $type => $coin_price) {
												// zb_dump($type,$coin_price);
												if ($type=='default') {
													continue;
												}

												if ($coin_price===false) {
													$__price_span = '<span>' . __('不可购买','ripro') . '</span>';

												}elseif ($coin_price==0) {
													$__price_span = '<span>' . __('免费','ripro') . '</span>';

												}elseif ($coin_price < $default_price) {
													$__rate = $coin_price/$default_price*10;
													$__price_span = '<span><i class="fas '.get_site_coin_icon().' me-1"></i>' . $coin_price . get_site_coin_name() . '<sup class="ms-1">' . sprintf(__('%s折','ripro'),$__rate) . '<sup></span>';

												}else{
													$__price_span = '<span><i class="fas '.get_site_coin_icon().' me-1"></i>' . $coin_price . get_site_coin_name() . '</span>';

												}

												echo '<li class="price-item '.$type.'">' . $price_names[$type] . ': ' . $__price_span . '</li>';

											}?>
											</ul>

										</div>

										<?php 
										$sales_count = absint(get_post_meta($post_id, 'cao_paynum', true));
										if ($sales_count>0) {
											echo '<div class="buy-count"><i class="fab fa-hotjar me-1"></i>'.sprintf(__('已有<span>%d</span>人解锁播放','ripro'),$sales_count).'</div>';
										}
										?>
									<?php endif;?>

									
								</div>
								<video class="video-js vjs-16-9" poster="<?php echo esc_attr($hero_bg);?>" data-setup="{}"></video>
							</div>

							<!-- 购买区域END -->
							
						<?php endif; ?>
						</div>
					</div>

					<div class="col-lg-3 col-12">
						<div class="ri-video-list">

							<div class="video-title">
								<?php if (($user_pay_post_status || $is_free_video) && !$is_user_login_get_status) : ?>
									<i class="fas fa-unlock me-1"></i>
								<?php else: ?>
									<i class="fas fa-lock me-1"></i>
								<?php endif; ?>
								<span class="title-span"><?php echo $video_data[0]['title'];?></span>
								<p class="title-count"><?php printf(__('(共%d集)', 'ripro'),count($video_data));?></p>
							</div>

							<?php if (count($video_data)>1) : ?>
							<div class="video-nav">
								<?php 

								foreach ($video_data as $key => $item) {
									$active = ($key==0) ? ' active' : '';
									printf('<a href="javascript:;" class="switch-video%s" title="%s" data-index="%d"><span>%s</span></a>',$active,$item['title'],$key,$key+1);
								}

								?>
							</div>
							<?php endif; ?>
						</div>

					</div>

	        	</div>

        </div>
</div>


<script>
jQuery(function($) {
	var video_data = <?php echo json_encode($video_data);?>;
	ri.heroVideoJs(video_data);
});
</script>