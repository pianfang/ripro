<?php

if (empty($args)) {
    return;
}

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


<div class="ri-down-warp" data-resize="<?php echo esc_attr( $args['resize_position'] );?>">
    <span class="down-msg"><?php _e('下载', 'ripro'); ?></span>
    <?php if ($user_pay_post_status && !$is_user_login_get_status): ?>
        
        <div class="down-buy-warp">
            <div class="buy-title"><i class="fas fa-unlock me-1"></i><?php echo $text = ($user_pay_post_status===true) ? __('免费下载', 'ripro') : __('已获得下载权限', 'ripro');?></div>
            <?php if (!empty($cao_downurl_new)):?>
                <div class="d-grid gap-2 mt-3">
                <?php foreach ($cao_downurl_new as $item): ?>

                    <div class="btn-group">
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
              <a rel="nofollow noopener noreferrer" href="<?php echo esc_url( wp_login_url(get_current_url())); ?>" class="login-btn btn btn-info w-100 rounded-pill"><i class="far fa-user me-1"></i><?php _e('登录后下载', 'ripro');?></a>
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

                <div class="prices-desc">
                    <div class="prices-default">
                        <i class="fas <?php echo esc_attr(get_site_coin_icon());?> me-1"></i>
                        <span><?php echo $default_price;?></span><?php echo get_site_coin_name();?>
                    </div>
                </div> 

                <div class="prices-rate">

                    <?php $vip_link = (is_user_logged_in()) ? get_uc_menu_link('vip') : home_url('/vip-prices');?>

                    <ul class="prices-info">
                        <a class="vip-rete-tips" href="<?php echo esc_url( $vip_link ); ?>" rel="nofollow noopener noreferrer" target="_blank"><i class="far fa-gem me-1"></i><?php _e('VIP折扣', 'ripro');?></a>
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
              <button class="btn btn-danger w-100 rounded-pill js-pay-action" data-id="<?php echo $post_id;?>" data-type="1" data-info=""><i class="fab fa-shopify me-1"></i><?php _e('购买下载权限', 'ripro');?></button>
            </div>


            <?php 
            $sales_count = absint(get_post_meta($post_id, 'cao_paynum', true));
            if (!empty($args['is_sales_count']) && $sales_count>0) {
                echo '<div class="buy-count"><i class="fab fa-hotjar me-1"></i>'.sprintf(__('已有<span>%d</span>人解锁下载','ripro'),$sales_count).'</div>';
            }
            ?>
        <?php endif;?>

        </div>

    <?php endif; ?>


    <div class="down-buy-info">

        <?php
        $cao_info = get_post_meta( $post_id, 'cao_info', true);
        $cao_demourl = trim(get_post_meta( $post_id, 'cao_demourl', true));
        $cao_diy_btn = array_filter(explode('|', get_post_meta( $post_id, 'cao_diy_btn', true)));
        $sales_count = absint(get_post_meta($post_id, 'cao_paynum', true));

        $btns = []; //DIY按钮
        if (!empty($cao_demourl)) {
            $btns[] = array('name'=>__('查看预览','ripro'),'url'=>$cao_demourl);
        }
        if (!empty($cao_diy_btn)) {
            $btns[] = ['name'=>$cao_diy_btn[0],'url'=>$cao_diy_btn[1]];
        }
        if (empty($cao_info)) {
            $cao_info = array();
        }
        
        if (!empty($args['is_sales_count']) && $sales_count>0) {
            array_unshift($cao_info,array('title'=>__('累计销量','ripro'),'desc'=> $sales_count));
        }
        if (!empty($args['is_modified_date'])) {
            array_unshift($cao_info,array('title'=>__('最近更新','ripro'),'desc'=> get_the_modified_time('Y-m-d')));
        }
        if (!empty($args['is_downurl_count']) && !empty($cao_downurl_new) && count($cao_downurl_new)) {
            array_unshift($cao_info,array('title'=>__('包含资源','ripro'),'desc'=> sprintf(__('(%d个)', 'ripro'),count($cao_downurl_new))));
        }
        ?>

        <?php if (!empty($btns)):?>
            <div class="d-grid gap-2 mt-3">
            <?php foreach ($btns as $item): ?>
                <a target="_blank" href="<?php echo esc_attr($item['url']);?>" class="btn btn-secondary-soft rounded-pill" rel="nofollow noopener noreferrer"><i class="fas fa-link me-1"></i><?php echo esc_attr($item['name']);?></a>
            <?php endforeach ?>
            </div>
        <?php endif;?>

        <?php if (!empty($cao_info)):?>
            <ul class="list-group list-group-flush mt-3">
            <?php foreach ($cao_info as $item): ?>
                <li class="small text-muted list-group-item bg-white"><span><?php echo $item['title'];?>: </span> <span><?php echo $item['desc'];?></span></li>
            <?php endforeach ?>
            </ul>
        <?php endif;?>

        <?php if (!empty($args['footer_text'])):?>
        <p class="text-muted mb-0 mt-3 small"><?php echo $args['footer_text'];?></p>
        <?php endif;?>
    </div> 

</div>



