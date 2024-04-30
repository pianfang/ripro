<?php

// 获取当前文章的格式
$post_format = get_post_format();
$format_icons = array('image'=>'fas fa-image', 'video'=>'fas fa-play', 'audio'=>'fas fa-music');
if ($post_format && isset($format_icons[$post_format])) {
	$post_format_icon = $format_icons[$post_format];
}else{
	$post_format_icon = false;
}

?>


<div class="entry-media ratio <?php echo esc_attr($args['media_class']);?>">
	<a target="<?php echo get_target_blank();?>" class="media-img lazy <?php echo esc_attr($args['media_size_type']);?> <?php echo esc_attr($args['media_fit_type']);?>" href="<?php the_permalink();?>" title="<?php the_title();?>" data-bg="<?php echo zb_get_thumbnail_url();?>">

		<?php if ($post_format_icon):?>
			<div class="post-format-icon"><i class="<?php echo $post_format_icon;?>"></i></div>
		<?php endif;?>

		<!-- 音视频缩略图 -->
		<?php if (in_array($post_format, array('video','audio')) && zb_get_media_preview_url()):?>
			<div class="media-preview <?php echo esc_attr($post_format);?>" >
				<?php if ($post_format=='video'):?>

					<video class="media-js" preload="none" loop="loop" muted src="<?php echo zb_get_media_preview_url();?>" style="display: none;"></video>

					<div class="progress-bar">
				        <div class="progress"></div>
				    </div>

				<?php elseif ($post_format=='audio'):?>

					<audio class="media-js" preload="none" loop="loop" src="<?php echo zb_get_media_preview_url();?>" style="display: none;"></audio>

					<div class="centered-html-cd">
						<div class="souse-img">
							<div class="icon-cd"></div>
							<div class="icon-left"></div>
						</div>
					</div>

					<div class="progress-bar">
				        <div class="progress"></div>
				    </div>

				<?php endif;?>
			</div>
		<?php endif;?>

	</a>
</div>