<?php

defined('WPINC') || exit;

/**
 * 优惠券添加页面
 */



global $wpdb;

$output_cdk_txt = '';

$_count = intval( get_param('count','20') );
$_type = intval( get_param('type','1') );

if ( isset( $_POST['output_record'] ) ) {

    check_admin_referer( 'rimini_output_cdk_nonce_action', 'rimini_output_cdk_nonce_val' );

    global $wpdb;

    $cdk_table = $wpdb->cao_cdk_tbl; //cdk表
    $cdks = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$cdk_table} WHERE type = %d AND status=0 ORDER BY id DESC LIMIT %d",$_type, $_count)
    );

    foreach ($cdks as $key => $cdk) {
        $output_cdk_txt .= $cdk->code . PHP_EOL;
    }

}

?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">导出卡券</h1>
    <a class="add-new-h2" href="admin.php?page=zb-admin-page-cdk">卡券管理</a>
    <a class="add-new-h2" href="admin.php?page=zb-admin-page-cdk&action=addcdk">添加卡券</a>
    <p>您可在此导出会员兑换卡，立减优惠码，折扣优惠码，注册邀请码，全选 Ctrl + C 按钮然后使用 Ctrl + A 快捷键手动复制（只导出选中类型并且未被使用的卡券）</p>

    <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>

    <hr class="wp-header-end">

    <div id="post-body-content">


        <div class="postbox">
            <div class="inside">

                <form method="post">
                    <?php wp_nonce_field( 'rimini_output_cdk_nonce_action', 'rimini_output_cdk_nonce_val' ); ?>
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row">导出卡券类型</th>
                            <td>
                                <select name="type">
                                    <?php $_types = [1,2,3];
                                    foreach ($_types as $value) {
                                        echo sprintf('<option value="%s">%s</option>',$value,ZB_Cdk::get_cdk_type($value));
                                    }?>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">导出卡券数量</th>
                            <td>
                                <input name="count" type="number" id="count" value="<?php echo $_count;?>" size="6" />
                            </td>
                        </tr>

                    </table>


                    <div class="submit">
                        <input type="submit" class="button-primary" name="output_record" value="生成导出列表" />
                    </div>
                </form>

                <div style="margin-top: 30px;">
                    <textarea name="output_cdk_txt" rows="30" cols="50" id="output_cdk_txt" class="large-text code" readonly><?php echo $output_cdk_txt;?></textarea>
                </div>


            </div>
        </div>
    </div>
    <br class="clear">
</div>
<script type="text/javascript">

jQuery(document).ready(function($){
    

});
</script>
<!-- 主页面END -->
