<?php
  $menu_class = 'mobile-menu d-block d-lg-none';
?>

<div class="off-canvas">
  <div class="canvas-close"><i class="fas fa-times"></i></div>
  
  <!-- logo -->
  <?php get_template_part( 'template-parts/header/logo-wrapper'); ?>

  
  <div class="<?php echo esc_attr( $menu_class ); ?>"></div>

</div>

