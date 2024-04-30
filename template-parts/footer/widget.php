<?php

if (empty(_cao('is_site_footer_widget',true))) {
  return;
}

?>


<div class="row d-none d-lg-flex mb-3">
  <div class="col-md-4">
    <div class="logo-wrapper">
      <?php $site_dese = _cao('site_footer_desc', 'RiPro-V5是一款强大的Wordpress资源商城主题，支持付费下载、付费播放音视频、付费查看等众多功能。');?>
    </div>
    <?php get_template_part( 'template-parts/header/logo-wrapper'); ?>
    <p class="small mb-0"><?php echo $site_dese; ?></p>
  </div>

  <div class="col-md-2">
    <h4 class="widget-title"><?php _e('快速导航', 'ripro');?></h4>
    <ul class="list-unstyled widget-links">
      <?php foreach (_cao('site_footer_widget_link1',array()) as $item) {
        printf('<li><a href="%s">%s</a></li>',$item['href'],$item['title']);
      }?>
    </ul>
  </div>

  <div class="col-md-2">
    <h4 class="widget-title"><?php _e('关于本站', 'ripro');?></h4>
    <ul class="list-unstyled widget-links">
      <?php foreach (_cao('site_footer_widget_link2',array()) as $item) {
        printf('<li><a href="%s">%s</a></li>',$item['href'],$item['title']);
      }?>
    </ul>
  </div>

  <div class="col-md-4">
    <h4 class="widget-title"><?php _e('联系我们', 'ripro');?></h4>
    <div class=""><?php echo _cao('site_contact_desc');?></div>
  </div>
</div>

