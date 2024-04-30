<?php 

global $current_user;

?>

<div class="card">
	<div class="card-header mb-2"><h5 class="fw-bold mb-0"><?php _e('订单记录','ripro');?></h5></div>

	<div class="card-body">
		<div class="card-header mb-2"><?php _e('最近20条','ripro');?></div>
		<?php 

		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->cao_order_tbl} WHERE user_id = %d AND order_type=1 AND pay_status=1 ORDER BY create_time DESC LIMIT 20", $current_user->ID));

		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录','ripro' ) . '</p>';
		}else{

			echo '<div class="list-group">';
			foreach ($data as $item) : ?>
				<a target="_blank" href="<?php echo $retVal = (get_permalink($item->post_id)) ? get_permalink($item->post_id) : '';?>" class="list-group-item list-group-item-action">
					<div class="d-block d-md-flex w-100 justify-content-between">
						<h6 class="mb-1"><?php echo $retVal = (get_permalink($item->post_id)) ? get_the_title($item->post_id) : 'Null';?></h6>
						<small class="text-muted"><?php echo wp_date('Y-m-d H:i', $item->create_time);?></small>
					</div>
					<small class="text-muted d-block d-md-inline-block"><?php _e('订单号：','ripro');?><?php echo $item->order_trade_no;?></small>
					<small class="text-muted"><?php _e('支付金额：','ripro');?><?php echo $item->order_price;?></small>
					<small class="text-muted"><?php _e('支付方式：','ripro');?><?php echo ZB_Shop::get_pay_type($item->pay_type);?></small>
				</a>
			<?php endforeach;
			echo '</div>';
		}
		?>
	</div>
	
</div>
