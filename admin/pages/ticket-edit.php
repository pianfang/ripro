<?php

defined('WPINC') || exit;

// ticket-edit.php

$ticket_id = absint($_GET['id']);
$Data = ZB_Ticket::get($ticket_id);

if (empty($Data)) {
	wp_die('获取工单数据失败。请返回重试');
}

$creator_user = ($user = get_user_by('ID', $Data->creator_id)) ? $user->user_login : '';
$statuses = array(
    '0'     => ZB_Ticket::get_status(0),
    '1' => ZB_Ticket::get_status(1),
    '2'   => ZB_Ticket::get_status(2),
    '3' => ZB_Ticket::get_status(3)
);

if ( isset( $_POST['save_record'] ) ) {

	check_admin_referer( 'rimini_edit_nonce_action', 'rimini_edit_nonce_val' );

	$status = intval($_POST['status']);

	if ($Data->status < 2 && !empty($_POST['reply_content'])) {
		$status = 2;
	}

	$updata = [
		'reply_content'=> wp_kses_post($_POST['reply_content']),
		'reply_time'=> time(),
		'updated_time'=> time(),
		'assignee_id'=> get_current_user_id(),
		'status'=> $status
	];

	if (ZB_Ticket::update($updata,['id'=>$Data->id])) {
		$message = '工单更新成功';
		$Data = ZB_Ticket::get($ticket_id);
	}else{
		$message = '工单更新失败';
	}

}

$current_status = $Data->status;

?>



<!-- 主页面 -->
<form method="post">
    <?php wp_nonce_field( 'rimini_edit_nonce_action', 'rimini_edit_nonce_val' ); ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">编辑/回复工单</h1>
        <a class="add-new-h2" href="admin.php?page=zb-admin-page-ticket">返回工单列表</a>
        <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>
        <hr class="wp-header-end">
        <div id="poststuff">
            
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" class="edit-form-section edit-comment-section">
                    <div id="namediv" class="stuffbox">
                        <div class="inside">
                            <h2 class="edit-comment-author">工单详情信息</h2>
                            <fieldset>
                                <table class="form-table editcomment" role="presentation">
                                    <tbody>
                                        <tr>
                                            <td class="first"><label>工单创建人</label></td>
                                            <td><input type="text" size="30" value="<?php echo $creator_user;?>" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label>工单类型</label></td>
                                            <td><input type="text" size="30" value="<?php echo ZB_Ticket::get_type($Data->type);?>" disabled></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label>问题标题：</label></td>
                                            <td><?php echo $Data->title;?></td>
                                        </tr>
                                        <tr>
                                            <td class="first"><label>问题内容：</label></td>
                                            <td><div style=" background: #eee; padding: 10px; "><?php echo $Data->content;?></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <div id="postdiv" class="postarea">
                    	<p>回复内容：</p>
	                    <?php
						$content = $Data->reply_content;
						$editor_id = 'reply_content'; // 自定义编辑器 ID
						$settings = array(
						    'textarea_name' => 'reply_content', // 这个 textarea 的 name 属性值
						    'media_buttons' => false, // 是否显示添加媒体按钮
						    'tinymce'       => false, // 是否开启 TinyMCE 编辑器
						    'quicktags'     => true, // 是否显示快速标签工具栏
						    'editor_height' => 200, // 是否显示快速标签工具栏
						);
						wp_editor( $content, $editor_id, $settings );
						?>
					</div>
                </div><!-- /post-body-content -->
                <div id="postbox-container-1" class="postbox-container">
                    <div id="submitdiv" class="stuffbox">
                        <h2>保存更新</h2>
                        <div class="inside">
                            <div class="submitbox">
                                <div>
                                    <div>
                                    	<div class="misc-pub-section">
                                    		<p style=" margin: 5px 0; ">创建时间：<b><?php echo (!empty($Data->create_time)) ? wp_date('Y-m-d H:i:s', $Data->create_time) : '';?></b></p>
                                    		<p style=" margin: 5px 0; ">回复时间：<b><?php echo (!empty($Data->reply_time)) ? wp_date('Y-m-d H:i:s', $Data->reply_time) : '';?></b></p>
                                    		<p style=" margin: 5px 0; ">更新时间：<b><?php echo (!empty($Data->updated_time)) ? wp_date('Y-m-d H:i:s', $Data->updated_time) : '';?></b></p>
										</div>
                                        <div class="misc-pub-section">
                                        	当前状态： <span style=" margin-bottom: 10px; display: inline-block; "><?php echo ZB_Ticket::get_status($current_status); ?></span>
                                            <fieldset>
                                                <?php foreach ( $statuses as $value => $label ) : ?>
							                    	<label style=" margin-bottom: 10px; display: inline-block; ">
							                        <input type="radio" name="status" value="<?php echo $value; ?>" <?php checked( $current_status, $value ); ?>><?php echo $label; ?>
							                        </label><br>
							                    <?php endforeach; ?>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="major-publishing-actions">
                                    <div id="publishing-action">
                                        <input type="submit" name="save_record" class="button button-primary button-large" value="更新工单"></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /submitdiv -->
                </div>
            </div><!-- /post-body -->
        </div>
    </div>
</form>