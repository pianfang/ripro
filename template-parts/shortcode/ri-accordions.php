<?php

$post_id = get_the_ID();
$user_id = get_current_user_id();

$content = $args['content'];
$atts = shortcode_atts(array(
   'title' => '自定义标题',
), $args['atts']);


?>

<div class="ri-accordions-shortcode">
	<div class="alert alert-success" role="alert">
	  <h4 class="alert-heading"><?php echo esc_html( $atts['title'] );?></h4>
	  <hr>
	  <div><?php echo wp_kses_post( $content );?></div>
	</div>
</div>

