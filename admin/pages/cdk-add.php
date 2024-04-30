<?php

defined('WPINC') || exit;

/**
 * 卡券添加页面
 */

$_type = intval( get_param('type','1') );
$_amount = floatval( get_param('amount','0') );
$_expiry_day = intval( get_param('expiry_day','1') );
$_count = intval( get_param('count','1') );
$_vip_type = absint( get_param('vip_type','0') );
$message = false;
$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();

if ( isset( $_POST['save_record'] ) ) {

    check_admin_referer( 'ri_add_cdk_nonce_action', 'ri_add_cdk_nonce_val' );
    
    if ($_type <= 0) {
        $message = '请选择卡券类型';
    }

    if ( $_type == 2 && !isset($site_vip_buy_options[$_vip_type]) ) {
        $message = '请选择卡券会员类型';
    }

    if ($_type == 1 && $_amount <= 0 ) {
        $message = '卡券金额数量不能为0';
    }

    if ($_count <= 0) {
        $message = '生成数量不能为0';
    }

    if ($_expiry_day <= 0) {
        $message = '有效期天数不能为0';
    }

    $_info = '';
    if ($message===false) {
        if ($_type == 2) {
            $_info = trim($_vip_type);
            $_amount = 0;
        }
        
        $add_cdk_count = 0;
        for ($i=0; $i < $_count; $i++) { 
            $add_cdk_satus = ZB_Cdk::add_cdk([
                'amount' => $_amount,
                'type' => $_type,  // 0 无 1充值  2会员兑换
                'create_time' => time(),
                'expiry_time' => strtotime('+'.$_expiry_day.' day'),
                'code'        => substr(md5(uniqid()), 5, 10),
                'info'        => $_info, //卡券信息
                'status'      => 0,
            ]);
            if ($add_cdk_satus) {
                $add_cdk_count++;
            }
        }
        if ($add_cdk_count>0) {
            echo sprintf('<div id="message" class="updated fade"><p>成功生成（%s）条卡券</p></div>',$add_cdk_count);
        }

    }

}
?>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <h1 class="wp-heading-inline">添加卡券</h1>
    <a class="add-new-h2" href="admin.php?page=zb-admin-page-cdk">卡券管理</a>
    <a class="add-new-h2" href="admin.php?page=zb-admin-page-cdk&action=outputcdk">导出卡券</a>
    <p>您可在此添加会员兑换卡、余额充值卡、注册邀请码。</p>

    <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>

    <hr class="wp-header-end">

    <div id="post-body-content">


        <div class="postbox">
            <div class="inside">

                <form method="post">
                    <?php wp_nonce_field( 'ri_add_cdk_nonce_action', 'ri_add_cdk_nonce_val' ); ?>
                    <table class="form-table">

                        <tr valign="top">
                            <th scope="row">卡券类型</th>
                            <td>
                                <select id="type" name="type">
                                    <option value="">选择卡券类型</option>
                                    <?php $_types = [1,2,3];
                                    foreach ($_types as $value) {
                                        echo sprintf('<option value="%s">%s</option>',$value,ZB_Cdk::get_cdk_type($value));
                                    }?>
                                </select>
                                <p>必选</p>
                            </td>
                        </tr>
                        <tr id="vip_type_tr" valign="top" style="display: none;">
                            <th scope="row">会员类型</th>
                            <td>
                                <select id="vip_type" name="vip_type">
                                    <option value="">请选择会员类型</option>
                                    <?php 
                                    foreach ($site_vip_buy_options as $value) {
                                        echo sprintf('<option value="%s">%s(%d)天</option>',$value['day_num'],$value['buy_title'],$value['day_num']);
                                    }?>
                                </select>
                                <p>必选</p>
                            </td>
                        </tr>

                        <tr id="amount_tr" valign="top" style="display: none;">
                            <th scope="row">充值金额(<?php echo get_site_coin_name();?>)</th>
                            <td>
                                <input name="amount" type="text" id="amount" min="0" value="<?php echo $_amount;?>" size="6" />
                                <p>充值卡数量，如果是会员兑换卡，请填写0</p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">有效期天数</th>
                            <td>
                                <input name="expiry_day" type="number" id="expiry_day" min="0" value="<?php echo $_expiry_day;?>" size="6" />
                                <p>从添加时间起之后多少天内有效</p>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">生成卡券数量</th>
                            <td>
                                <input name="count" type="number" id="count" min="0" value="<?php echo $_count;?>" size="6" />
                                <p>批量生成多少个卡券</p>
                            </td>
                        </tr>

                    </table>


                    <div class="submit">
                        <input type="submit" class="button-primary" name="save_record" value="立即添加" />
                    </div>
                </form>


            </div>
        </div>
    </div>
    <br class="clear">
</div>
<script type="text/javascript">

jQuery(document).ready(function($){

    $("#type").change(function() {
        var now_type = $(this).val(),vip_type_tr = $("#vip_type_tr"),amount_tr = $("#amount_tr");
        if (now_type==2) {
            vip_type_tr.show(),amount_tr.hide();
        }else if(now_type==3){
            vip_type_tr.hide(),amount_tr.hide();
        }else{
            vip_type_tr.hide(),amount_tr.show();
        }

    });

});
</script>
<!-- 主页面END -->
