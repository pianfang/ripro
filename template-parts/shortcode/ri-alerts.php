<?php

$post_id = get_the_ID();
$user_id = get_current_user_id();

$content = $args['content'];
$atts = shortcode_atts(array(
   'color' => 'primary',
), $args['atts']);


?>

<div class="ri-alerts-shortcode">
	<div class="alert alert-<?php echo esc_attr( $atts['color'] );?>" role="alert"><?php echo wp_kses_post( $content );?></div>
</div>

