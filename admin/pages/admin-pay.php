<?php

defined('WPINC') || exit;

/**
 * 后台充值开通
 */

$user_search = sanitize_text_field( get_param('user_search','') );
$search_type = sanitize_text_field( get_param('search_type','id') );
$pay_type = sanitize_text_field( get_param('pay_type','') );
$pay_vip_type = absint( get_param('pay_vip_type',0) );
$recharge_num = abs( floatval(get_param('recharge_num','0')) );
$deduction_num = abs( floatval(get_param('deduction_num','0')) );

$message = false;
$site_vip_options = get_site_vip_options();
$site_vip_buy_options = get_site_vip_buy_options();


if ( isset( $_POST['save_record'] ) ) {

    check_admin_referer( 'ri_add_vip_nonce_action', 'ri_add_vip_nonce_val' );
    

    if (empty($user_search) || empty($search_type) ) {
        $message = '请输入用户信息';
    }else{
        $select_user = get_user_by($search_type,$user_search);
        if ($select_user) {
            $select_user_id = $select_user->ID;
        }else{
            $message = '用户不存在';
        }

        $search_user_id = ($select_user) ? $select_user->ID : 0 ;
        
    }

    if ($message===false) {

        if ($pay_type=='revip') {
            if (update_user_vip_data($search_user_id,$pay_vip_type)) {
                $message = sprintf('用户UID：%s，会员类型已变更成功',$search_user_id);
            }
        }elseif ($pay_type=='recharge') {
            $user_coin_balance = get_user_coin_balance($search_user_id);
            if (change_user_coin_balance($search_user_id, $recharge_num, '+')) {
                $message = sprintf('用户UID：%s，原始余额：%s，充值：%s，充值后余额：%s',$search_user_id,$user_coin_balance,$recharge_num,get_user_coin_balance($search_user_id));
            }

        }elseif ($pay_type=='deduction') {
            $user_coin_balance = get_user_coin_balance($search_user_id);
            if (change_user_coin_balance($search_user_id, $deduction_num, '-')) {
                $message = sprintf('用户UID：%s，原始余额：%s，扣除：%s，扣除后余额：%s',$search_user_id,$user_coin_balance,$deduction_num,get_user_coin_balance($search_user_id));
            }
        }else{
            $message = '请选择充值操作类型';
        }

    }

}
?>

<style type="text/css">

    .wrap h1.wp-heading-inline {
        margin-bottom: 20px;
        text-align: center;
        width: 100%;
    }

    .pay-box .form-wrap{
        padding: 20px;
        background: #fff;
        border-radius: 5px;
        border: solid #2271b1 1px;
    }

    @media screen and (min-width: 782px){
        .pay-box{
            max-width: 600px;
            margin: 0 auto;
            margin-top: 30px;
        }
    }

    
</style>

<!-- 主页面 -->
<div class="wrap zb-admin-page">

    <?php if (!empty($message)) {echo '<div class="notice notice-zbinfo is-dismissible" id="message"><p>' . $message . '</p></div>';}?>

    <div id="post-body-content">

        <div class="pay-box">

            <h1 class="wp-heading-inline">后台充值减扣余额，开通/续费VIP会员</h1>
            <p>手动给用户开通会员，后台开通的会员不统计在商城订单统计里，支持用户UID，用户账号，用户邮箱充值开通，重复开通VIP会自动续费累计到期时长。</p>

            <form method="post" id="pay-form" class="form-wrap">

                <input name="page" type="hidden" value="<?php echo @$_REQUEST['page'];?>">
                <?php wp_nonce_field( 'ri_add_vip_nonce_action', 'ri_add_vip_nonce_val' ); ?>

                <div class="form-field term-parent-wrap">
                    <label>搜索用户方式</label>
                    <?php 
                    $__options = ['id'=>'用户UID','login'=>'用户登录名','email'=>'用户邮箱'];
                    foreach ($__options as $key=>$name) {
                        $checked = ($search_type == $key) ? 'checked' : '';
                        printf('<label style="display: inline-block;margin-right: 10px;"><input type="radio" name="search_type" value="%s" %s>%s</label> ',$key,$checked,$name);
                    }
                    ?>
                </div>
                
                <div class="form-field form-required term-name-wrap">
                    <label>搜索用户信息</label>
                    <input name="user_search" type="text" value="<?php echo $user_search;?>" size="40" placeholder="">
                    <p>根据搜索方式输入用户UID/账号/邮箱</p>
                </div>


                <div class="form-field term-parent-wrap">
                    <label>充值类型</label>
                    <?php 
                    $__options = ['revip'=>'后台开通VIP','recharge'=>'后台充值余额','deduction'=>'后台扣除余额'];
                    foreach ($__options as $key=>$name) {
                        $checked = ($pay_type == $key) ? '' : '';
                        printf('<label style="display: inline-block;margin-right: 10px;"><input type="radio" name="pay_type" value="%s" %s>%s</label> ',$key,$checked,$name);
                    }
                    ?>
                </div>


                <div class="form-field term-parent-wrap" id="pay-type-revip" style="display:none">
                    <label>要开通的会员类型</label>
                    <select name="pay_vip_type" class="postform">
                        <option value="0">普通用户</option>
                        <?php 
                        foreach ($site_vip_buy_options as $item) {
                            $checked = ($pay_vip_type == $item['day_num']) ? 'selected' : '';
                            printf('<option value="%s" %s>%s</option>',$item['day_num'],$checked,$item['buy_title']);
                        }
                        ?>
                    </select>
                </div>

                <div class="form-field form-required term-name-wrap" id="pay-type-recharge" style="display:none">
                    <label>余额充值数量 +（<?php echo get_site_coin_name();?>）</label>
                    <input name="recharge_num" type="text" min="1" value="" >
                </div>

                <div class="form-field form-required term-name-wrap" id="pay-type-deduction" style="display:none">
                    <label>余额扣除数量 -（<?php echo get_site_coin_name();?>）</label>
                    <input name="deduction_num" type="text" min="1" value="" >
                </div>


                <div class="submit">
                    <input type="submit" id="save_record" name="save_record" class="button button-primary" value="确认操作"/>
                </div>
                
            </form>
        </div>

    </div>
    <br class="clear">
</div>
<script type="text/javascript">




jQuery(document).ready(function($) {
   
    $('input#save_record').click(function(e) {
        return confirm('确认为当前用户开通?');
    });

    $('input[name=pay_type]').on('change', function() {
        var val = $(this).val();
        var dsid = '#pay-type-' + val;
        $(dsid).show();
        $('div[id^="pay-type-"]').not(dsid).hide();
    });
});
</script>
<!-- 主页面END -->
