<?php

global $current_user, $wpdb;

$page_action = get_param('action','','get');
$ticket_link = get_uc_menu_link('ticket');
$list_link = add_query_arg(array('action' => 'list'),$ticket_link);
$new_link = add_query_arg(array('action' => 'new'),$ticket_link);
$view_id = get_param('id',0,'get');
$is_delete = get_param('delete',0,'get');

?>

<?php if ($page_action == 'new') : ?>
<!-- 新建工单 -->
<div class="card">
	<div class="card-header mb-3">
		<h5 class="fw-bold mb-0 d-flex align-content-center"><?php _e('新建工单', 'ripro');?>
			<a href="<?php echo esc_url($list_link);?>" class="badge bg-dark btn-dark bg-opacity-75 ms-2"><?php _e('返回工单列表', 'ripro');?></a>
		</h5>
		<hr>
	</div>
	<div class="card-body">
		<form class="row g-4" id="ticket-form">
            <!-- Input item -->
            <div class="col-lg-6">
                <label class="form-label"><?php _e('创建人', 'ripro');?></label>
                <input type="text" class="form-control" value="<?php echo $current_user->display_name;?> (<?php echo $current_user->user_login; ?>)" disabled>
            </div>
            <!-- Input item -->
            <div class="col-lg-6">
                <label class="form-label"><?php _e('工单类型', 'ripro');?></label>
                <select name="type" class="form-select">
                	<?php $option = [1,2,3,4];
                	foreach ($option as $value) {
                		echo '<option value="'.$value.'">'.ZB_Ticket::get_type($value).'</option>';
                	}?>
				</select>

            </div>
            <div class="col-12">
                <label class="form-label"><?php _e('工单标题', 'ripro');?></label>
                <input type="text" class="form-control" name="title" placeholder="" value="">
            </div>
            <!-- Textarea item -->
            <div class="col-12">
                <label class="form-label"><?php _e('描述', 'ripro');?></label>
                <textarea class="form-control" rows="8" name="content"></textarea>
            </div>

            <!-- Save button -->
            <div class="d-sm-flex justify-content-end mt-3">
                <input type="hidden" name="action" value="zb_user_save_ticket">
                <button type="submit" id="save-ticket" class="btn btn-dark mb-0"><?php _e('提交工单', 'ripro');?></button>
            </div>
        </form>
	</div>
</div>


<?php elseif ($page_action == 'view') : ?>
<!-- 查看工单详情 -->
<div class="card">
	<div class="card-header mb-2">
		<h5 class="fw-bold mb-0 d-flex align-content-center"><?php _e('工单详情', 'ripro');?>
			<a href="<?php echo $list_link;?>" class="badge bg-dark btn-dark bg-opacity-75 ms-2"><?php _e('返回工单列表', 'ripro');?></a>
		</h5>
		<hr>
	</div>
	<div class="card-body">
		<?php 

		$data = ZB_Ticket::get($view_id);

		if (empty($data)) : ?>
			<p class="p-4 text-center fs-4 text-muted"><?php _e('获取工单信息失败', 'ripro');?></p>
		<?php else : ?>
			
			<?php if ($is_delete==1) : ZB_Ticket::delete($data->id);?>
				<script type="text/javascript">
					var url = window.location.href;
					url = url.split('?')[0]; // 截取问号及其后面的字符串
					window.location.replace(url);
				</script>
			<?php exit; endif;?>
			
			<?php if ($data->status == 2) {
				// 更新查看状态关闭工单
				ZB_Ticket::update(
					['status'=>3,'updated_time'=> time()],
					['id'=>$data->id],['%d','%s'],['%d']
				);
			}?>
			<div class="mb-3">
	    		<div class="mb-3 small text-muted d-flex align-items-center flex-wrap">
                	<span class="avatar avatar-xs">
						<img class="avatar-img rounded-circle" src="<?php echo get_avatar_url($data->creator_id); ?>">
					</span>
                    <span class="ms-1"><?php echo $current_user->display_name;?> <?php echo wp_date('Y-m-d H:i',$data->create_time); ?> 提交</span>
                    <span class="ms-2">【<?php echo ZB_Ticket::get_type($data->type); ?>】</span>
                    <span class="ms-2">状态：(<?php echo ZB_Ticket::get_status($data->status); ?>)</span>
                </div>
	    		<h5 class="fw-bold mb-2"><i class="fas fa-question-circle me-1"></i><?php echo esc_html($data->title); ?></h5>
                <div class="p-2 p-lg-3 bg-info bg-opacity-25 rounded-2">
                	<?php echo $data->content;?>
                	<?php if (!empty($data->file)) : ?>
                    <div class="mt-2"><a class="btn-link text-muted" href="<?php echo esc_url($data->file);?>" onclick="event.preventDefault(); document.getElementById('flieImage').src=this.href"><?php _e('查看附件', 'ripro');?></a><img class="border border-white border-3 shadow" id="flieImage" src=""></div>
                    <?php endif;?>
                </div>
                
	    	</div>

	    	<?php if (!empty($data->reply_content)) : ?>
	    	<hr>
	    	<div class="mb-3">
                <div class="mb-3 small text-muted d-flex align-items-center flex-wrap">
                	<span class="avatar avatar-xs">
						<img class="avatar-img rounded-circle" src="<?php echo get_avatar_url($data->assignee_id); ?>">
					</span>
                    <span class="ms-1"><?php echo get_userdata($data->assignee_id)->display_name;?> <?php echo wp_date('Y-m-d H:i',$data->reply_time); ?> <?php _e('工单回复内容：', 'ripro');?></span>
                </div>
                <div class="p-2 p-lg-3 bg-success bg-opacity-25 rounded-2">
                	<?php echo $data->reply_content;?>
                </div>
			</div>
	    	<?php endif;?>
	    	
	    	<div class="d-sm-flex justify-content-end mt-3">
                <a href="<?php echo esc_url(add_query_arg(array('action' => 'view','id' => $data->id,'delete'=>1),$ticket_link));?>" class="delete-ticket btn btn-danger mb-0"><?php _e('删除工单', 'ripro');?></a>
            </div>
	    	
		<?php endif; ?>


	</div>
</div>


<?php else : ?>
<!-- 工单列表 -->
<div class="card">
	<div class="card-header mb-4">
		<h5 class="fw-bold mb-0 d-flex align-content-center"><?php _e('工单列表', 'ripro');?>
			<a href="<?php echo esc_url($new_link);?>" class="badge bg-dark btn-dark bg-opacity-75 ms-2"><?php _e('新建工单', 'ripro');?></a>
		</h5>
	</div>
	<div class="card-body">
		
		<?php 
		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->cao_ticket_tbl} WHERE creator_id = %d ORDER BY create_time DESC,updated_time DESC LIMIT 20", $current_user->ID));

        $counts = $wpdb->get_results(
            $wpdb->prepare("SELECT status,COUNT(id) as count FROM {$wpdb->cao_ticket_tbl} WHERE creator_id = %d GROUP BY status ORDER BY status ASC",$current_user->ID)
        ,ARRAY_A);

        ?>
        <div class="row row-cols-2 row-cols-md-4 g-2 g-md-4 mb-4">
            <?php 
            $item = [0,1,2,3];
            $color_key = 0;
            foreach ($item as $key => $status) : 
                $target_count = 0;
                foreach ($counts as $count) {
                    if (isset($count["status"]) && $count["status"] == $status) {
                        $target_count = $count["count"];
                        break;
                    }
                }
                $color_key++;
            ?>
            <div class="col">
                <div class="card text-center bg-<?php echo zb_get_color_class($color_key);?> bg-opacity-25 p-4 h-100 rounded-2">
                    <h4 class="fw-bold text-<?php echo zb_get_color_class($color_key);?>"><?php echo $target_count;?></h4>
                    <span class="h6 mb-0 text-muted"><?php echo ZB_Ticket::get_status($key);?></span>
                </div>
            </div>
            <?php endforeach;?>
        </div>

        <div class="card-header mb-2"><?php _e('最近20条','ripro' );?></div>

        <?php 
		if (empty($data)) {
			echo '<p class="p-4 text-center">' . __('暂无记录','ripro' ) . '</p>';
		}else{

			echo '<div class="list-group">';
			foreach ($data as $item) : ?>

				<?php switch ($item->status) {
					case '2': $color = 'success'; break;
					case '3': $color = 'dark'; break;
					default: $color = 'danger'; break;
				}?>

				<a href="<?php echo esc_url(add_query_arg(array('action' => 'view','id' => $item->id),$ticket_link));?>" class="ticket-item list-group-item list-group-item-light">
					<div class="d-flex align-items-sm-center">
						<div class="avatar flex-shrink-0 me-2">
							<img class="avatar-img rounded-pill" src="<?php echo get_avatar_url($item->creator_id); ?>">
						</div>
						<div class="w-100">
						    <div class="d-block d-md-flex w-100 justify-content-between">
								<h6 class="fw-bold mb-1"><?php echo esc_html($item->title);?></h6>
								<small class="text-muted"><?php echo wp_date('Y-m-d H:i',$item->create_time);?></small>
							</div>
						    <span class="badge bg-opacity-10 text-<?php echo esc_attr( $color );?> bg-<?php echo esc_attr( $color );?>"><?php echo ZB_Ticket::get_status($item->status);?></span>
						    <span class="badge text-muted"><?php echo ZB_Ticket::get_type($item->type);?></span>
				    	</div>
				    </div>
				</a>

			<?php endforeach;
			echo '</div>';
		}
		?>
        
	</div>
</div>

<?php endif; ?>



<script type="text/javascript">
	
jQuery(function($) {

	$('.delete-ticket').click(function(e) {
        return confirm(zb.gettext.__is_delete_n);
    });

    //提交工单
    $("#save-ticket").on("click", function(e) {
        e.preventDefault();
        var _this = $(this);
        var formData = $("#ticket-form").serializeArray();

        var data = {
          nonce: zb.ajax_nonce,
        };

        formData.forEach(({ name, value }) => {
          data[name] = value;
        });
        

        if (!data.title) {
        	$('input[name="title"]').focus();
        	return;
        }
        if (!data.content) {
        	$('textarea[name="content"]').focus();
        	return;
        }

        var url = window.location.href;
    	url = url.split('?')[0]; // 截取问号及其后面的字符串

    	ri.ajax({data,
          before: () => {_this.attr("disabled", "true")},
          result: ({status,msg}) => {
              ri.notice(msg);
              if (status == 1) {
                  setTimeout(function() {
                      window.location.replace(url);
                  }, 2000)
              }
          },
          complete: () => {_this.removeAttr("disabled")}
	    });

    });

});
</script>
