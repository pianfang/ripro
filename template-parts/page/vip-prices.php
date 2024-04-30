<?php

get_header();

global $current_user;
$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();
$uc_vip_info = get_user_vip_data($current_user->ID);

//颜色配置
$vip_colors = [
    'no' => 'secondary',
    'vip' => 'success',
    'boosvip' => 'warning',
];

$bg_image = get_template_directory_uri() . '/assets/img/bg2.png';
$price_shape = get_template_directory_uri().'/assets/img/price_shape.png';

?>

<div class="archive-hero text-center">
    <div class="archive-hero-bg lazy" data-bg="<?php echo $bg_image; ?>"></div>
    <div class="container py-3 py-md-5">
    	<h1 class="archive-title mb-0"><i class="far fa-gem me-1 me-1"></i><?php _e('本站VIP', 'ripro');?></h1>
    	<div class="archive-desc mt-2 mb-0"><p><?php _e('加入本站VIP，畅享海量资源', 'ripro');?></p></div>
    </div>
</div>


<section class="container">


	<div class="row row-cols-1 row-cols-md-4 g-3 justify-content-center">
		<?php foreach ($site_vip_buy_options as $day => $item) : 
			if ($item['day_num']==9999) {
				$day_title = __('永久', 'ripro');
			}else{
				$day_title = sprintf(__('%s天', 'ripro'),$item['day_num']);
			}
		?>
		
		<div class="col">
			<div class="price-card text-center bg-white rounded-3">
			    <div class="price-header bg-<?php echo $vip_colors[$item['type']];?> bg-opacity-75">
			        <span class="price-plan"><?php echo $item['buy_title'];?></span>
			        <span class="price-sub"><i class="far fa-gem me-1"></i><?php printf(__('会员有效期%s', 'ripro'),$day_title);?></sup></span>
			    </div>
			    <div class="price-body">
			    	<h3 class="price-ammount"><?php echo $item['coin_price'];?><sup><?php echo get_site_coin_name();?></sup></h3>
			    	<p class="price-day"><?php printf(__('尊享%s特权%s', 'ripro'),$item['name'],$day_title);?></p>
			        <ul class="price-desc">
			        	<?php foreach ($item['desc'] as $text) :?>
			        	<li><?php echo $text;?></li>
			        	<?php endforeach;?>
			        </ul>
			    </div>
			    <div class="price-footer">
			    	<?php if (is_user_logged_in()) : ?>
			    	<?php 
			    	$btn_text = __('立即开通', 'ripro');
			    	$disabled = '';
			    	if ($uc_vip_info['type'] == 'boosvip') {
		    			$btn_text = __('已获得权限', 'ripro');
		    			$disabled = 'disabled';
		    		}elseif ($uc_vip_info['type'] == 'vip' && $item['type']=='vip') {
		    			$btn_text = __('立即续费', 'ripro');
		    		}elseif ($uc_vip_info['type'] == 'vip' && $item['type']=='boosvip') {
		    			$btn_text = __('立即升级', 'ripro');
		    		}
			    	?>
			    	<button class="btn btn-<?php echo $vip_colors[$item['type']];?> js-pay-action px-4 rounded-pill" data-id="0" data-type="3" data-info="<?php echo $item['day_num'];?>" <?php echo $disabled;?>><i class="far fa-gem me-1"></i><?php echo $btn_text;?></button>

			    	<?php else : ?> 
			    	<a class="login-btn btn btn-dark-soft px-4 rounded-pill" rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>"><i class="far fa-user me-1"></i><?php _e('登录后升级', 'ripro');?></a>
			    	<?php endif; ?> 

			    </div>
			</div>
		</div>
		<?php endforeach;?>

	</div>



</section>


<div class="container py-5">
	<div class="text-center mb-4">
		<h3><?php _e('VIP会员说明', 'ripro');?></h3>
		<p class="text-muted mb-0"><?php _e('开通会员常见问题说明及介绍', 'ripro');?></p>
	</div>

	<div class="row row-cols-1 row-cols-md-2 g-4">
	<?php foreach (_cao('site_buyvip_desc',array()) as $text) {
		echo '<div class="col"><div class="p-3 bg-info bg-opacity-10 rounded-2"><i class="fas fa-info-circle me-1"></i>'.$text['content'].'</div></div>';
	}?>
	</div>

</div>



<?php

// ri_home_catbox_widget(array(
// 	'id' => 'home-center',
//     'before_widget' => '<div class="home-widget home-cat-box">',
//     'after_widget'  => '</div>',
// ), array());

?>

<?php get_footer();?>