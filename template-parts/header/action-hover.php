<?php
global $current_user;
$uc_menus = get_uc_menus();
$site_color = get_site_default_color_style();

?>


<?php if ( !empty(_cao('is_site_dark_toggle',true)) ) :?>
	<span class="action-btn toggle-color" rel="nofollow noopener noreferrer">
		<span title="<?php _e('亮色模式', 'ripro');?>" data-mod="light" class="<?php echo ($site_color=='light') ? ' show' : '';?>"><i class="fas fa-sun"></i></span>
		<span title="<?php _e('深色模式', 'ripro');?>" data-mod="dark" class="<?php echo ($site_color=='dark') ? ' show' : '';?>"><i class="fas fa-moon"></i></span>
	</span>
<?php endif;?>

<?php if ( empty(_cao('remove_site_search',false)) ) : ?>
	<span class="action-btn toggle-search" rel="nofollow noopener noreferrer" title="<?php _e('站内搜索', 'ripro');?>"><i class="fas fa-search"></i></span>
<?php endif;?>

<?php if ( is_site_notify() ) : ?>
	<span class="action-btn toggle-notify" rel="nofollow noopener noreferrer" title="<?php _e('网站公告', 'ripro');?>"><i class="fa fa-bell-o"></i></span>
<?php endif;?>


<?php if ( is_user_logged_in() ) : ?>

<div class="action-hover-menu d-inline-block">
	<a class="avatar-warp" href="<?php echo get_uc_menu_link('vip');?>" rel="nofollow noopener noreferrer">
	   <img class="avatar-img rounded-circle" src="<?php echo get_avatar_url($current_user->ID);?>" width="30" alt="avatar">
	   <span class="ms-2 d-none d-md-block"><?php echo $current_user->display_name;?></span>
	   <?php if(is_site_shop()):?>
	   <?php echo zb_get_user_badge($current_user->ID,'span','d-none d-md-block ms-2'); ?>
	   <?php endif;?>
	</a>

	<?php if (is_site_shop()) :?>
	<div class="hover-warp">
	    <div class="hover-info">
		  <div class="d-flex align-items-center">
			 <div class="me-2">
				<img class="avatar rounded-circle border border-white border-3 shadow" src="<?php echo get_avatar_url($current_user->ID);?>" alt="avatar">
			 </div>
			 <div class="ms-2 lh-1">
			 	<?php echo zb_get_user_badge($current_user->ID,'span'); ?>
				<b class="d-block mt-2"><?php echo $current_user->display_name;?></b>
			 </div>
		  </div>
		  <?php if (is_site_qiandao()) :?>
		  	<div class="balance-qiandao">
				<?php if (!is_user_today_qiandao($current_user->ID)) :?>
					<a class="user-qiandao-action text-danger" href="javascript:;"><i class="fa fa-check-square-o me-1"></i><?php _e('签到领取', 'ripro');?><?php echo get_site_coin_name();?></a>
				<?php else:?>
					<a class="btn-link text-secondary" href="javascript:;"><i class="fa fa-check-square-o me-1"></i><?php _e('今日已签到', 'ripro');?></a>
				<?php endif;?>
			</div>
		  	<?php endif;?>
	    </div>

	    <div class="hover-balance small p-3 pb-1">

	    	<div class="row g-2">

	    		<div class="col-6">
		    		<div class="text-center bg-info text-white bg-opacity-75 rounded-2 p-3">
		    			<div class="mb-2"><?php printf('%s%s',get_site_coin_name(),__('余额','ripro'));?></div>
						<?php printf('<div class="mb-2"><i class="%s me-1"></i>%s</div>',get_site_coin_icon(),get_user_coin_balance($current_user->ID));?>
						<div>
							<a class="btn btn-sm btn-white text-primary w-100 rounded-pill" href="<?php echo get_uc_menu_link('coin');?>" rel="nofollow noopener noreferrer"><?php _e('充值','ripro');?></a>
						</div>
					</div>
				</div>

	    		<div class="col-6"> 
				<?php 
				$vip_options = get_site_vip_options();
				$colors = [
			        'no'        => 'secondary',
			        'vip'     => 'success',
			        'boosvip' => 'warning',
			    ];
				foreach ($vip_options as $key => $item) {
					if ($item['key'] !='no') {
						$color = $colors[$item['key']];
						$link  = get_uc_menu_link('vip');
						echo '<a class="btn btn-sm d-block bg-'.$color.' text-white bg-opacity-75 rounded-2 p-2 py-3 mb-2" href="'.$link.'"><i class="far fa-gem me-1"></i>'. __('本站','ripro') . $item['name'].'</a>';
					}

				}
				?>
				</div>

			</div>

	    </div>

	    <div class="hover-item mt-0 p-3 pt-0">
	    	<div class="hover-link">
		      	<?php 
		      	$menus_item1 = ['profile','coin','vip','fav','order'];
				foreach ($menus_item1 as $key) {
					printf(
						'<a href="%s"><i class="%s"></i>%s</a>',
						get_uc_menu_link($key),$uc_menus[$key]['icon'],$uc_menus[$key]['title']
					);
				}
		      	?>
	    	</div>
		</div>

		<div class="abstop-item">
	    	<?php 
			printf('<a href="%s"><i class="%s me-1"></i>%s</a>',get_uc_menu_link('logout'),$uc_menus['logout']['icon'],$uc_menus['logout']['title']);
			if (in_array( 'administrator', $current_user->roles )) {
				printf('<a target="_blank" href="%s"><i class="fab fa-wordpress me-1"></i>%s</a>',esc_url( home_url('/wp-admin/') ),__('后台管理','ripro'));
			}else{
				printf('<a href="%s"><i class="%s me-1"></i>%s</a>',get_uc_menu_link('aff'),$uc_menus['aff']['icon'],$uc_menus['aff']['title']);
			}

	      	?>
		</div>
	</div>
	<?php endif;?>
</div>
<?php else: ?>

<?php if (is_site_user_login()) : ?>
	<a class="action-btn login-btn btn-sm btn" rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>"><i class="far fa-user me-1"></i><?php _e( '登录', 'ripro' );?></a>
<?php endif;?>

<?php endif;?>

