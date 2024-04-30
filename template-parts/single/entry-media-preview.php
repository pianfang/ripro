<?php 


if (empty(_cao('single_media_preview',true))) {
	return;
}


$post_id = get_the_ID();
$bg_image = zb_get_thumbnail_url($post_id);
// 获取当前文章的格式
$post_format = get_post_format($post_id);

$preview_url = zb_get_media_preview_url();
$preview_type = zb_get_video_source_types($preview_url);

if (!in_array($post_format, array('video','audio')) || empty($preview_url)){
	return;
}


// 视频信息
$video_data = [[
    'title' => '',
    'src' => $preview_url,
    'img' => $bg_image,
    'type' => $preview_type,
]];

?>


<div class="archive-media-preview">
	<div class="preview-text"><?php echo $post_format;?> <?php echo __( '预览', 'ripro' );?></div>

	<?php if ($post_format=='video'):?>
		<video class="video-js vjs-16-9" poster="<?php echo esc_attr($bg_image);?>" controls data-setup="" oncontextmenu="return false;">
			<source src="<?php echo esc_attr($preview_url);?>" type="<?php echo esc_attr($preview_type);?>">
		</video>
	<?php elseif ($post_format=='audio'):?>
		<audio class="video-js vjs-16-2" poster="<?php echo esc_attr($bg_image);?>" controls data-setup='{"controls": true}' oncontextmenu="return false;">
			<source src="<?php echo esc_attr($preview_url);?>" type="<?php echo esc_attr($preview_type);?>">
		</audio>
	<?php endif;?>

</div>

<script>
jQuery(function($) {
	var video_data = <?php echo json_encode($video_data);?>;
	ri.heroVideoJs(video_data);
});
</script>
