<?php

$post_id = get_the_ID();
$user_id = get_current_user_id();

$content = $args['content'];
$atts = shortcode_atts(array(
    'size'    => '',
    'color'   => 'primary',
    'outline' => 0,
    'rounded' => 0,
    'href'    => '#',
    'blank'   => 0,
    'content' => '这是按钮',
), $args['atts']);

$target = (empty($atts['blank'])) ? '_self' : '_blank';
$classes = (!empty($atts['outline'])) ? 'btn btn-outline-' . $atts['color'] : 'btn btn-' . $atts['color'];

if (!empty($atts['size'])) {
	$classes .= ' '.$atts['size'];
}
if (!empty($atts['rounded'])) {
	$classes .= ' rounded-pill';
}

?>

<a target="<?php echo esc_attr($target);?>" class="<?php echo esc_attr($classes);?> px-4 m-2" href="<?php echo esc_attr($atts['href']);?>" role="button" rel="noreferrer nofollow"><?php echo esc_attr($content);?></a>

