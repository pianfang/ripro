<?php

if (empty($args)) {
    return;
}

// owl-carousel
$config = [
    'lazyLoad' => false,
    'autoplay' => (bool)$args['is_autoplay'],
    'loop'     => true,
    'nav'      => true,
    'dots'     => false,
];

// $data = ZB_Dynamic::delete();
$data = ZB_Dynamic::get();

if (empty($data)) {
    $data = [
    	[
			'info' => sprintf( __('欢迎访问%s，网址：%s', 'ripro'), get_bloginfo('name'),home_url()),
            'uid' => get_current_user_id(),
            'href' => home_url(),
            'time' => time(),
    	],
    	[
			'info' => sprintf( __('欢迎访问%s，网址：%s', 'ripro'), get_bloginfo('name'),home_url()),
            'uid' => get_current_user_id(),
            'href' => home_url(),
            'time' => time(),
    	],
    	[
			'info' => sprintf( __('欢迎访问%s，网址：%s', 'ripro'), get_bloginfo('name'),home_url()),
            'uid' => get_current_user_id(),
            'href' => home_url(),
            'time' => time(),
    	]
    ];
}

?>


<section class="container pt-3 pb-0">
	<div class="row g-0">

		<div class="col-12 bg-<?php echo esc_attr( $args['bg_color'] );?> bg-opacity-10 p-2 rounded">
			<div class="dynamic-warp">
				<div class="me-3">
					<span class="badge bg-<?php echo esc_attr( $args['bg_color'] );?> px-2"><i class="fa fa-volume-up me-1"></i><?php echo esc_html( $args['title'] );?></span>
				</div>

				<div class="dynamic-slider owl-carousel owl-theme" data-config='<?php echo json_encode($config);?>'>
				<?php foreach ($data as $key => $item) : ?>

				<div class="item">
					<div class="dynamic-slider-item">
						<?php 
						$times  = sprintf( __( '%s前','ripro' ), human_time_diff( $item['time'], time() ) );
						$u_name = get_user_meta(intval($item['uid']),'nickname',1);
						$u_avatar = get_avatar_url($item['uid']);
						$info = (empty($item['href'])) ? $item['info'] : '<a target="_blank" href="'.$item['href'].'">'.$item['info'].'</a>';
						if (empty($u_name)) {
			                $u_name = __('游客', 'ripro');
			            }else{
			                $u_name = zb_substr_cut($u_name);
			            }
						?>
						<div class="d-flex align-items-center">
							<div class="avatar avatar-xs"><img class="avatar-img rounded-circle border border-white border-2 shadow" src="<?php echo $u_avatar;?>"></div>
							<b class="name"><?php echo $u_name;?></b>
							<p class="info"><?php echo $info;?><span class="times"><?php echo $times;?></span></p>
						</div>
					</div>
				</div>

				<?php endforeach;?>
			  	</div>

			</div>
		</div>

		
	</div>
</section>

