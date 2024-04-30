<?php

global $current_user;

$unsetoauth = get_param('unsetoauth','','get');
// 解绑第三方登录
if ($unsetoauth =='qq' || $unsetoauth =='weixin') {
  $un_meta_array = array('openid','name','avatar');
  foreach ($un_meta_array as $key) {
    $un_meta_key = 'open_' . $unsetoauth . $key;
    delete_user_meta($current_user->ID, $meta_key);
  }
}


?>

<div class="card mb-sm-4">
    <div class="card-header mb-3">
      <h5 class="fw-bold mb-0"><?php _e('基本信息', 'ripro');?></h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
          <!-- Image -->
          <div class="position-relative">
              <div class="d-flex align-items-center">
                  <div class="position-relative me-3">
                      <div class="avatar avatar-xl">
                          <img class="avatar-img rounded-circle border border-white border-3 shadow" src="<?php echo get_avatar_url($current_user->ID); ?>" alt="">
                      </div>
                  </div>
                  <div class="d-block">
                    <label for="inputAvatarFile" type="button" class="btn btn-primary-soft"><?php _e('上传头像', 'ripro');?></label>
                    <input class="d-none" type="file" name="inputAvatarFile" id="inputAvatarFile" accept=".jpg, .gif, .png" resetonclick="true">
                  </div>
              </div>
          </div>
        </div>

        <form class="row g-4" id="user-profile">
            <!-- Input item -->
            <div class="col-lg-6">
                <label class="form-label"><?php _e('显示昵称', 'ripro');?></label>
                <input type="text" class="form-control" name="display_name" placeholder="" value="<?php echo $current_user->display_name; ?>">
            </div>
            <!-- Input item -->
            <div class="col-lg-6">
                <label class="form-label"><?php _e('联系QQ', 'ripro');?></label>
                <input type="text" class="form-control" name="uc_lxqq" value="<?php echo get_user_meta($current_user->ID,'qq',1);?>">
            </div>
            <!-- Textarea item -->
            <div class="col-12">
                <label class="form-label"><?php _e('个人介绍', 'ripro');?></label>
                <textarea class="form-control" rows="3" name="description"><?php echo get_user_meta($current_user->ID,'description',1);?></textarea>
            </div>
            <!-- Save button -->
            <div class="d-sm-flex justify-content-end mt-3">
                <input type="hidden" name="action" value="zb_update_profile">
                <button type="submit" id="save-submit" class="btn btn-dark mb-0"><?php _e('保存资料', 'ripro');?></button>
            </div>
        </form>
    </div>
</div>



<div class="card mb-sm-4">
  <!-- Content -->
  <div class="card-header mb-3">
    <h5 class="fw-bold mb-0"><?php _e('账户绑定', 'ripro');?></h5>
  </div>
  <!-- Button -->

    <form class="row g-4" id="edit-email-form">
      <div class="col-lg-6">
        <label class="form-label"><?php _e('当前邮箱', 'ripro');?></label>
        <input type="text" class="form-control" name="user_email" value="<?php echo $current_user->user_email;?>" disabled>
      </div>

      <div class="col-lg-6">
            <label class="form-label"><?php _e('新邮箱地址', 'ripro');?></label>
            <input type="email" class="form-control" name="new_user_email" value="" autocomplete="off">
      </div>

      <div class="col-lg-12">
            <?php if (_cao('is_sns_qq',false) || _cao('is_sns_weixin',false)) :?>
              <div class="position-relative my-4">
                <hr>
                <p class="small bg-white position-absolute top-50 start-50 translate-middle px-2"><?php _e('快捷登录绑定信息', 'ripro'); ?></p>
              </div>
              <div class="d-grid gap-2 d-md-block text-center">

                <?php if (_cao('is_sns_qq',false)) {
                  if (!empty(get_user_meta($current_user->ID,'open_qq_openid',true))) {
                    echo '<a href="'.get_uc_menu_link('profile').'?unsetoauth=qq" class="btn btn-danger mx-2"><i class="fab fa-qq me-1"></i>'.__('解绑QQ登录', 'ripro').'</a>';
                  }else{
                    echo '<a href="'.get_oauth_permalink('qq').'" class="btn btn-info mx-2"><i class="fab fa-qq me-1"></i>'.__('绑定QQ登录', 'ripro').'</a>';
                  }
                  if (!empty(get_user_meta($current_user->ID,'open_weixin_openid',true))) {
                    echo '<a href="'.get_uc_menu_link('profile').'?unsetoauth=weixin" class="btn btn-danger mx-2"><i class="fab fa-qq me-1"></i>'.__('解绑微信登录', 'ripro').'</a>';
                  }else{
                    echo '<a href="'.get_oauth_permalink('weixin').'" class="btn btn-success mx-2"><i class="fab fa-weixin me-1"></i>'.__('绑定微信登录', 'ripro').'</a>';
                  }
                }?>

              </div>
            <?php endif;?>
      </div>

        <!-- Save button -->
      <div class="d-sm-flex justify-content-end mt-3">
          <input type="hidden" name="action" value="zb_update_new_email">
          <button type="submit" id="edit-email" class="btn btn-danger mb-0"><?php _e('确认修改邮箱', 'ripro');?></button>
      </div>
    </form>

    


</div>



<div class="card">
  <!-- Content -->
  <div class="card-header mb-3">
    <h5 class="fw-bold mb-0"><?php _e('密码修改', 'ripro');?></h5>
  </div>
  <!-- Button -->

    <form class="row g-4" id="edit-password-form">
      <div class="col-lg-6">
        <label class="form-label"><?php _e('邮箱信息', 'ripro');?></label>
        <input type="text" class="form-control" name="user_email" value="<?php echo $current_user->user_email;?>" disabled>
      </div>

      <div class="col-lg-6">
          <label class="form-label"><?php _e('旧密码', 'ripro');?></label>
          <?php if (user_is_oauth_password($current_user->ID)) :?>
            <input type="text" class="form-control" name="old_password" value="第三方注册，请设置新密码" autocomplete="off" disabled>
          <?php else:?>
            <input type="text" class="form-control" name="old_password" value="" autocomplete="off">
          <?php endif;?>
      </div>
      <div class="col-lg-6">
            <label class="form-label"><?php _e('新密码', 'ripro');?></label>
            <input type="text" class="form-control" name="new_password" value="" autocomplete="off">
      </div>
      <div class="col-lg-6">
            <label class="form-label"><?php _e('确认新密码', 'ripro');?></label>
            <input type="text" class="form-control" name="new_password2" value="" autocomplete="off">
      </div>
        <!-- Save button -->
      <div class="d-sm-flex justify-content-end mt-3">
          <input type="hidden" name="action" value="zb_update_password">
          <button type="submit" id="edit-password" class="btn btn-danger mb-0"><?php _e('确认修改密码', 'ripro');?></button>
      </div>
    </form>

</div>






<script type="text/javascript">
jQuery(function($) {

  $("#inputAvatarFile").change(function(e) {
    var formData = new FormData();
    formData.append("nonce",zb.ajax_nonce);
    formData.append("action", "zb_update_avatar");
    formData.append("file", e.currentTarget.files[0]);
    $.ajax({
        url:zb.ajax_url,
        dataType:'json',
        type:'POST',
        async: false,
        data: formData,
        processData : false, // 使数据不做处理
        contentType : false, // 不要设置Content-Type请求头
        success: function(result){
            ri.notice(result.msg);
            if (result.status == 1) {
                setTimeout(function() {
                    window.location.reload()
                }, 2000);
            }
        },
        error:function(e){
            ri.notice(e.responseText);
        }
    });

  });


  $("#save-submit").on("click", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = $("#user-profile").serializeArray();

      var data = {
          nonce: zb.ajax_nonce,
      };

      formData.forEach(({ name, value }) => {
        data[name] = value;
      });

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

  $("#edit-password").on("click", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = $("#edit-password-form").serializeArray();

      var data = {
          nonce: zb.ajax_nonce,
      };

      formData.forEach(({ name, value }) => {
        data[name] = value;
      });
      if (!data.old_password) {
        $('input[name="old_password"]').focus();
        return;
      }
      if (!data.new_password) {
        $('input[name="new_password"]').focus();
        return;
      }
      if (!data.new_password2) {
        $('input[name="new_password2"]').focus();
        return;
      }

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

  $("#edit-email").on("click", function(e) {
      e.preventDefault();
      var _this = $(this);
      var formData = $("#edit-email-form").serializeArray();
      var data = {
          nonce: zb.ajax_nonce,
      };

      formData.forEach(({ name, value }) => {
        data[name] = value;
      });

      if (!data.new_user_email) {
        $('input[name="new_user_email"]').focus();
        return;
      }

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