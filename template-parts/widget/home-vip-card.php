<?php

if (empty($args)) {
    return;
}

$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();
//颜色配置
$vip_colors = [
    'no' => 'secondary',
    'vip' => 'success',
    'boosvip' => 'warning',
];

if (count($site_vip_buy_options)>=4) {
	$row_css = 'row justify-content-center row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3';
}else{
	$row_css = 'row justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 g-md-3';
}

?>

<section class="container">
	<?php 
	    $section_title = $args['title'];
	    $section_desc = $args['desc'];
	?>
	<?php if ($section_title): ?>
	    <div class="section-title text-center mb-4">
	      <h3><?php echo $section_title ?></h3>
	      <?php if (!empty($section_desc)): ?>
	        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
	      <?php endif; ?>
	    </div>
	<?php endif; ?>


	<div class="<?php echo esc_attr($row_css);?>">

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
			    		<a class="btn btn-<?php echo $vip_colors[$item['type']];?> px-4 rounded-pill" rel="nofollow noopener noreferrer" href="<?php echo esc_url( get_uc_menu_link('vip') ); ?>"><i class="far fa-gem me-1"></i><?php _e('详情介绍', 'ripro');?></a>

			    	<?php else : ?> 
			    	<a class="login-btn btn btn-dark-soft px-4 rounded-pill" rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>"><i class="far fa-user me-1"></i><?php _e('登录后升级', 'ripro');?></a>
			    	<?php endif; ?> 

			    </div>
			</div>
		</div>

		<?php endforeach;?>
	  	
	</div>
</section>

