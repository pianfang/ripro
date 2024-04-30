<?php 

$post_id = get_the_ID();
$user_id = get_current_user_id();


// 用户是否已购买或者可免费获取
$user_pay_post_status = get_user_pay_post_status($user_id, $post_id);

//是否免费资源并且需要登录后查看
$is_user_login_get_status = $user_pay_post_status === true && empty($user_id);

// 免费资源下载无需登录
if (!empty(_cao('is_free_nologin_down', 1))) {
    $is_user_login_get_status = false;
}


//下载地址格式化
$cao_downurl_new = get_post_meta( $post_id, 'cao_downurl_new', true);
if (!empty($cao_downurl_new) && is_array($cao_downurl_new)) {
    foreach ($cao_downurl_new as $key => $item) {
        $cao_downurl_new[$key]['name'] = (!empty($item['name'])) ? trim($item['name']) : __('下载地址', 'ripro') . ($key + 1);
        $cao_downurl_new[$key]['pwd']  = (!empty($item['pwd'])) ? $item['pwd'] : '';
        $cao_downurl_new[$key]['url']  = get_post_endown_url($post_id, $key);
    }
}else{
    $cao_downurl_new = array();
}

?>


<div class="article-header">
	<?php the_title('<h1 class="post-title mb-2 mb-lg-3">', '</h1>');?>
</div>


<div class="archive-shop my-3">

	<div class="row">

		<?php if (true || has_post_thumbnail($post_id)) :?>
		<div class="col-lg-4">
			<div class="img-box">
    			<div class="views-rounded-dot"></div>
                <img class="lazy" src="<?php echo esc_attr(zb_get_thumbnail_url($post_id));?>" alt="<?php echo esc_attr(get_the_title($post_id));?>" />
            </div>
        </div>
    	<?php endif;?>

        <div class="col my-2 my-lg-0 info-box">

        	<?php
	        $cao_info = get_post_meta( $post_id, 'cao_info', true);
	        $sales_count = absint(get_post_meta($post_id, 'cao_paynum', true));
	        if (empty($cao_info)) {
	            $cao_info = array();
	        }
	        ?>

        	<div class="article-meta">
				<li><?php _e('资源分类: ', 'ripro');?><?php zb_meta_category(1);?></li>
				<li><?php _e('浏览热度: ', 'ripro');?>(<?php echo zb_get_post_views();?>)</li>
				<li><?php _e('发布时间: ', 'ripro');?><?php echo get_the_time('Y-m-d');?></li>
				<li><?php _e('最近更新: ', 'ripro');?><?php echo get_the_modified_time('Y-m-d');?></li>
				<?php if (!empty($cao_info)){
				foreach ($cao_info as $item){
					printf('<li>%s: %s</li>',$item['title'],$item['desc']);
				}
				}?>
			</div>


			<div class="ri-down-warp mt-1 mt-lg-2">
			    
			    <?php if ($user_pay_post_status && !$is_user_login_get_status): ?>
			        
			        <div class="down-buy-warp">
			            <div class="buy-title"><i class="fas fa-unlock me-1"></i><?php echo $text = ($user_pay_post_status===true) ? __('免费下载', 'ripro') : __('已获得下载权限', 'ripro');?></div>
			            <?php if (!empty($cao_downurl_new)):?>
			                <div class="d-block gap-2 mt-0">
			                <?php foreach ($cao_downurl_new as $item): ?>
			                    <div class="btn-group mt-1">
			                        <a target="_blank" href="<?php echo esc_attr($item['url']);?>" class="btn btn-success" rel="nofollow noopener noreferrer"><i class="fas fa-cloud-download-alt me-1"></i><?php echo $item['name'];?></a>
			                        <?php if (!empty($item['pwd'])):?>
			                          <button type="button" class="user-select-all copy-pwd btn btn-success opacity-75" data-pwd="<?php echo esc_attr($item['pwd']);?>" title="<?php echo esc_attr($item['pwd']);?>"><?php _e('密码', 'ripro');?><i class="far fa-copy ms-1"></i></button>
			                        <?php endif;?>
			                    </div>
			                <?php endforeach ?>
			                </div>
			            <?php endif;?>
			        </div> 

			    <?php else: ?>
			        <div class="down-buy-warp">
			        <?php if ($is_user_login_get_status):?>
			            <div class="buy-title"><i class="fas fa-lock me-1"></i><?php _e('本资源登录后免费下载', 'ripro');?></div>
			            <div class="buy-btns">
			              <a rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>" class="login-btn btn btn-info px-4 mt-1"><i class="far fa-user me-1"></i><?php _e('登录后下载', 'ripro');?></a>
			            </div> 

			        <?php else:?>
			            <div class="buy-title"><i class="fas fa-lock me-1"></i></i><?php _e('本资源需权限下载', 'ripro');?></div>
			            
			            <div class="buy-desc">
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
			                ?>

			                <div class="prices-descs">

		                        <ul class="prices-info">
		                        <?php 
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

			            </div>

			            <div class="buy-btns">
			              <button class="btn btn-danger px-4 mt-1 js-pay-action" data-id="<?php echo $post_id;?>" data-type="1" data-info=""><i class="fab fa-shopify me-1"></i><?php _e('购买下载权限', 'ripro');?></button>

			           		<?php
			              	$cao_demourl = trim(get_post_meta( $post_id, 'cao_demourl', true));
					        $cao_diy_btn = array_filter(explode('|', get_post_meta( $post_id, 'cao_diy_btn', true)));
					        $btns = []; //DIY按钮
					        if (!empty($cao_demourl)) {
					            $btns[] = array('name'=>__('查看预览','ripro'),'url'=>$cao_demourl);
					        }
					        if (!empty($cao_diy_btn)) {
					            $btns[] = ['name'=>$cao_diy_btn[0],'url'=>$cao_diy_btn[1]];
					        }

					    	?>

					    	<?php if (!empty($btns)):?>
					            <?php foreach ($btns as $item): ?>
					                <a target="_blank" href="<?php echo esc_attr($item['url']);?>" class="btn px-4 mt-1 btn-dark" rel="nofollow noopener noreferrer"><i class="fas fa-link me-1"></i><?php echo esc_attr($item['name']);?></a>
					            <?php endforeach ?>
					        <?php endif;?>

			            </div>

			        <?php endif;?>

			        </div>

			    <?php endif; ?>

			</div>


		</div>


	</div>

</div>