<?php

$post_id = get_the_ID();
$user_id = get_current_user_id();

$content = $args['content'];
$atts = shortcode_atts(array(
    'url'      => '',
    'pic'      => '',
), $args['atts']);


$video_src = $atts['url'];
$poster_attr = (!empty($atts['pic'])) ? 'poster="'.$atts['pic'].'"' : '';
$video_type = zb_get_video_source_types($video_src);

?>

<div class="ri-video-shortcode">
	<video class="video-js vjs-16-9" <?php echo $poster_attr;?> controls data-setup="{}" oncontextmenu="return false;">
		<source src="<?php echo esc_attr($video_src);?>" type="<?php echo esc_attr(zb_get_video_source_types($video_type));?>">
	</video>
</div>

