<?php

global $current_user;

?>

<div class="card mb-2 mb-md-4 uc-aff-page">
	<div class="card-header mb-3"><h5 class="fw-bold mb-0"><?php _e('推广中心', 'ripro' );?></h5></div>
	<div class="card-body">
    	
    	<div class="text-center mb-4">
			<h5 class="h5 text-muted mb-3"><i class="fas fa-link me-1"></i><?php _e('您的推广链接', 'ripro' );?></h5>
			<div class="border bg-success bg-opacity-10 border-1 border-warning border-dashed rounded-pill p-2">
				<h5 class="user-select-all mb-0 text-primary"><?php echo get_user_aff_permalink(home_url(),$current_user->ID);?></h5>
			</div>
        </div>

		<div class="row row-cols-2 row-cols-md-4 g-2 g-md-4">
			<?php 

			$user_aff_info = ZB_Aff::get_user_aff_info($current_user->ID);

			$item = [
			    'leiji' => __( '累计佣金', 'ripro' ),
			    'ketixian' => __( '可提现', 'ripro' ),
			    'tixianzhon' => __( '提现中', 'ripro' ),
			    'yitixian' => __( '已提现', 'ripro' ),
			]; 

	        $color_key = 0;
			foreach ($item as $key => $name) : $value = $user_aff_info[$key];$color_key++;?>
			<!-- Counter item -->
			<div class="col">
				<div class="card bg-<?php echo zb_get_color_class($color_key);?> bg-opacity-25 p-4 h-100 rounded-2">
					<h4 class="fw-bold text-<?php echo zb_get_color_class($color_key);?>">￥<?php echo $value;?></h4>
					<span class="h6 mb-0 text-muted"><?php echo $name;?></span>
				</div>
			</div>
			<?php endforeach;?>

		</div>
		<div class="w-100 text-center mt-3">
	        <button id="user-aff-submit" data-action="zb_user_aff_action" class="btn btn-dark text-white px-5"><?php _e('申请提现', 'ripro' );?></button>
	    </div>

		<hr>
		<div class="mb-3">
			<h6 class="d-flex align-content-centermb-2"><?php _e('已成功推广注册','ripro' );?><span class="badge me-1 bg-success ms-2"><?php echo count($user_aff_info['ref_uids']);?></span></h6>
			<?php 
			if (!empty($user_aff_info['ref_uids'])) {
				$user_i = 0;
				foreach ($user_aff_info['ref_uids'] as $uid) {
					$user_i++;
					if ($user_i<=20) {
						$s = zb_substr_cut(get_user_meta(intval($uid),'nickname',1));
						printf('<div class="avatar avatar-sm m-1"><img class="avatar-img rounded-circle border border-white border-3 shadow" src="%s" title="%s"></div>',get_avatar_url($uid),$s);
					}
				}
			}else{
				echo '<p class="text-muted">' . __( '暂无用户通过您的推广链接注册', 'ripro' ) . '</p>';
			}?>
		</div>

		<h6><?php _e('推广说明：','ripro' );?></h6>
		<ol class="list-group list-group-numbered">
			<?php 
			$list = _cao('site_tixian_desc',array());
			foreach ($list as $key => $item) {
				printf('<li class="list-group-item list-group-item-light text-muted">%s</li>',$item['content']);
			}
			?>
		</ol>
		

	</div>
</div>

<div class="card">
	<div class="card-header mb-2"><h5 class="fw-bold mb-0"><?php _e('佣金记录','ripro' );?></h5></div>

	<div class="card-body">
		<div class="card-header mb-2"><?php _e('最近20条','ripro' );?></div>
		<?php 

		global $wpdb;
        // 查询语句
		$query = $wpdb->prepare(
		    "SELECT a.*, CONVERT(a.aff_rate * b.pay_price, DECIMAL(10,2)) AS aff_money,
		            b.pay_price, b.user_id AS pay_user, b.post_id, b.order_type, b.order_trade_no
		     FROM $wpdb->cao_aff_tbl AS a
		     LEFT JOIN $wpdb->cao_order_tbl AS b ON a.order_id = b.id
		     WHERE b.id IS NOT NULL AND a.aff_uid = %d
		     ORDER BY a.create_time DESC
		     LIMIT 20", 
		    $current_user->ID
		);

		$data = $wpdb->get_results($query);

		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录','ripro' ) . '</p>';
		}else{

			echo '<div class="list-group">';
			foreach ($data as $item) : ?>
				<div class="list-group-item list-group-item-action">
					<div class="d-block d-md-flex w-100 justify-content-between">
						<h6 class="mb-1"><?php _e('推广类型','ripro' );?>（<?php echo $item->note;?>）<?php _e('购买人：','ripro' );?><?php echo zb_substr_cut(get_user_meta(intval($item->pay_user),'nickname',1));?></h6>
						<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time);?></small>
					</div>
					<small class="text-muted d-block d-md-inline-block"><?php _e('订单金额：￥','ripro' );?><?php echo $item->pay_price;?></small>
					<small class="text-muted"><?php _e('佣金收益：','ripro' );?><?php echo ($item->aff_rate*100);?>% ~ ￥<?php echo $item->aff_money;?></small>
					<small class="text-muted"><?php _e('状态：','ripro' );?><?php echo ZB_Aff::get_aff_status($item->status);?></small>
				</div>
			<?php endforeach;
			echo '</div>';
		}
		?>
	</div>
	
</div>

<script type="text/javascript">
	
jQuery(function($) {
	// user-aff-submit
    $("#user-aff-submit").on("click", function(e) {
        e.preventDefault();
        var _this = $(this);
        var data = {
            nonce: zb.ajax_nonce,
            action: _this.data("action")
        };
        ri.ajax({data,
          before: () => {_this.attr("disabled", "true")},
          result: ({status,msg}) => {
              ri.notice(msg);
              if (status == 1) {
                  setTimeout(function() {
                      window.location.reload()
                  }, 2000)
              }
          },
          complete: () => {_this.removeAttr("disabled")}
	    });
    });
});
</script>
