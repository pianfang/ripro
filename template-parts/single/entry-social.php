<?php

$btnOption = _cao('single_bottom_action_btn', array('share', 'fav', 'like'));
if (empty($btnOption)) {
    return;
}

$author_id = get_the_author_meta( 'ID' );

$author_name = get_the_author_meta( 'display_name', $author_id );



?>

<div class="entry-social">

	<div class="row mt-2 mt-lg-3">
		
		<div class="col">
			<?php if (_cao('single_bottom_author',true)) : ?>
			<a class="share-author" href="<?php echo esc_url( get_author_posts_url($author_id,$author_name) );?>">
				<div class="avatar me-1"><img class="avatar-img rounded-circle border border-white border-3 shadow" src="<?php echo get_avatar_url($author_id); ?>" alt="">
				</div><?php echo $author_name;?>
            </a>
            <?php endif;?>
		</div>

		<div class="col-auto">
			
			<?php if (in_array('share',$btnOption)) : ?>
			<a class="btn btn-sm btn-info-soft post-share-btn" href="javascript:void(0);"><i class="fas fa-share-alt me-1"></i><?php _e('分享', 'ripro'); ?></a>
			<?php endif;?>

			<?php if (in_array('fav',$btnOption)) : 
				$is_fav = (zb_is_post_fav()) ? 0 : 1;
				$fav_text   = ($is_fav) ? __('收藏', 'ripro') : __('取消收藏', 'ripro');
			?>
			<a class="btn btn-sm btn-success-soft post-fav-btn" href="javascript:void(0);" data-is="<?php echo $is_fav; ?>"><i class="far fa-star me-1"></i></i><?php echo $fav_text; ?></a>
			<?php endif;?>

			<?php if (in_array('like',$btnOption)) : ?>
			<a class="btn btn-sm btn-danger-soft post-like-btn" href="javascript:void(0);" data-text="已点赞"><i class="far fa-heart me-1"></i><?php _e('点赞', 'ripro'); ?>(<span class="count"><?php echo zb_get_post_likes(); ?></span>)</a>
			<?php endif;?>

		</div>
	</div>

</div>