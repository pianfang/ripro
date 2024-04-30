<?php 

$site_footer_link = _cao('site_footer_links', array());

if (is_home() && !empty($site_footer_link)) : ?>
  <div class="footer-links small d-none d-lg-block mt-2">
    <span><?php _e('友情链接：', 'ripro');?></span>
    <ul>
      <?php foreach ($site_footer_link as $item) :?>
        <?php printf('<li><a href="%s" target="_blank" rel="" title="%s">%s</a></li>', $item['href'], $item['title'], $item['title']);?>
      <?php endforeach;?>
    </ul>
  </div>
<?php endif;?>