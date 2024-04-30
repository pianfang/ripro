<?php 

global $current_user;

?>


<div class="card mb-2 mb-md-4 coin-balance-body p-4">

	<?php if (is_site_qiandao()) :?>
	<div class="balance-qiandao">
		<?php if (!is_user_today_qiandao($current_user->ID)) :?>
			<a class="user-qiandao-action btn btn-sm text-danger" href="javascript:;"><i class="fa fa-check-square-o me-1"></i><?php _e('签到领取', 'ripro');?><?php echo get_site_coin_name();?></a>
		<?php else:?>
			<a class="btn btn-sm btn-link text-secondary" href="javascript:;"><i class="fa fa-check-square-o me-1"></i><?php _e('今日已签到', 'ripro');?></a>
		<?php endif;?>
	</div>
	<?php endif;?>

	<div class="balance-info">
		<div class="fs-5"><?php _e('当前账户余额', 'ripro'); ?></div>
		<hr>
		<div class="fs-2"><?php printf('<i class="%s me-1"></i>%s%s',get_site_coin_icon(),get_user_coin_balance($current_user->ID),get_site_coin_name());?></div>
	</div>
</div>

<div class="card mb-2 mb-md-4">
	
	<div class="card-body mb-4">
		<div class="card-header mb-3">
			<h5 class="fw-bold mb-0"><?php _e('充值余额', 'ripro'); ?></h5>
		</div>
		<?php 
		$site_mycoin_pay_arr = _cao('site_mycoin_pay_arr');
		$site_mycoin_pay_arr = empty($site_mycoin_pay_arr) ? [] : explode(",", $site_mycoin_pay_arr);
		?>

		<div class="row row-cols-2 row-cols-md-4 g-2 g-md-4">
			<?php foreach ($site_mycoin_pay_arr as $num) : ?>
			
			<div class="col">
				<div class="coin-pay-card text-center" data-num="<?php echo absint($num);?>">
				    <h5 class="mb-1 text-warning"><?php echo absint($num) . get_site_coin_name();?></h6>
				    <p class="m-0 text-muted">￥<?php echo site_convert_amount(absint($num),'rmb');?></p>
				</div>
			</div>
			<?php endforeach;?>

		</div>

		<div class="py-4 text-center">
            <button class="btn btn-warning px-5 js-pay-action" data-id="0" data-type="2" data-info="0" data-text="<?php _e('充值','ripro');?>" disabled><i class="fab fa-shopify me-1"></i><span><?php _e('请选择充值数量','ripro');?></span></button>
        </div>

	</div>

	<div class="card-body mb-4">
		<div class="card-header mb-3">
			<h5 class="fw-bold mb-0"><?php _e('充值说明', 'ripro'); ?></h5>
		</div>
		<ol class="list-group list-group-numbered">
		<?php 
		$mycoin_pay_desc = _cao('site_mycoin_pay_desc');
		$mycoin_pay_desc = empty($mycoin_pay_desc) ? [] : explode("\n", $mycoin_pay_desc);
		?>
		<?php foreach ($mycoin_pay_desc as $text) {
			echo '<li class="list-group-item list-group-item-light text-muted">'.$text.'</li>';
		}?>
		</ol>
	</div>


</div>


<?php if (!empty(_cao('is_site_cdk_pay',true))) :?>
<div class="card mb-2 mb-md-4 vip-cdk-body">
	<div class="card-header mb-3">
		<h5 class="fw-bold mb-0"><?php printf('%s%s',get_site_coin_name(),__('兑换','ripro'));?></h5>
	</div>
	
	<div class="card-body">
		<h5 class="text-center mb-4 text-muted"><?php _e('使用CDK码兑换站内币','ripro');?></h5>
		<form class="row" id="vip-cdk-action">
            <div class="col-12 mb-3">
                <input type="text" class="form-control" name="cdk_code" placeholder="兑换码/CDK卡号" value="">
            </div>
            <div class="col-12 input-group mb-3">
	          <input type="text" class="form-control rounded-2" name="captcha_code" placeholder="验证码">
	          <img id="captcha-img" class="rounded-2 lazy" role="button" data-src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/captcha.png');?>" title="<?php _e('点击刷新验证码','ripro');?>" />
	        </div>
            <div class="col-12 mb-3 mt-3 text-center">
                <input type="hidden" name="action" value="zb_vip_cdk_action">
                <button type="submit" id="vip-cdk-submit" class="btn btn-danger text-white px-4"><i class="fas fa-gift me-1"></i><?php _e('立即兑换','ripro');?></button>
                <a class="btn btn-warning" target="_blank" href="<?php echo _cao('site_cdk_pay_link');?>" rel="nofollow noopener noreferrer"><i class="fas fa-external-link-alt me-1"></i><?php _e('购买CDK','ripro');?></a>

            </div>
        </form>
	</div>
</div>
<?php endif;?>

<div class="card mb-2 mb-md-4">
	<div class="card-header mb-2">
		<caption class="fw-bold mb-0"><?php printf('%s%s',get_site_coin_name(),__('获取记录（最近10条）','ripro'));?></caption>
	</div>

	<div class="card-body pay-vip-log">
	    <?php 
	    global $wpdb;
	    $data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->cao_order_tbl} WHERE user_id = %d AND order_type=2 AND pay_status=1 ORDER BY create_time DESC LIMIT 10", $current_user->ID));

	    if (empty($data)) {
	        echo '<p class="p-4 text-center">' . __('暂无记录', 'ripro') . '</p>';
	    } else {
	        echo '<ul class="list-group mt-2">';
	        foreach ($data as $key => $item) { ?>
	            <div class="list-group-item list-group-item-action">
	                <small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time);?></small>
	                <small class="text-muted"><?php printf(__('充值金额：￥%1$s（%2$s）', 'ripro'),$item->order_price, site_convert_amount($item->order_price,'coin') . get_site_coin_name() );?></small>
 
	                <small class="text-muted"><?php printf(__('支付方式：%s', 'ripro'), ZB_Shop::get_pay_type($item->pay_type));?></small>

	            </div>
	        <?php }
	        echo '</ul>';
	    }
	    ?>
	</div>
</div>


<script type="text/javascript">
	jQuery(function($) {

		$(".coin-pay-card").click(function() {
		    var amount = $(this).data("num");
		    var paybtn = $(".js-pay-action");
		    paybtn.data("info", amount).removeAttr("disabled");
		    paybtn.find("span").text(paybtn.data("text")+amount);
    		$(this).addClass("active").parent().siblings().find(".coin-pay-card").removeClass("active");
		});

	    // vip-cdk-submit
	    $("#vip-cdk-submit").on("click", function(e) {
	        e.preventDefault();
	        var _this = $(this);
	        var formData = $("#vip-cdk-action").serializeArray();
	        var data = {
	          nonce: zb.ajax_nonce,
	        };

		    formData.forEach(({ name, value }) => {
		        data[name] = value;
		    });
		    
	        ri.ajax({
	            data,
	            before: () => {
	                _this.attr("disabled", "true")
	            },
	            result: ({
	                status,
	                msg
	            }) => {
	                ri.notice(msg);
	                if (status == 1) {
	                    setTimeout(function() {
	                        window.location.reload()
	                    }, 2000)
	                }
	            },
	            complete: () => {
	                _this.removeAttr("disabled")
	            }
	        });
	    });
	});
</script>