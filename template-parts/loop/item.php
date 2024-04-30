<?php

$defaults = zb_get_archive_item_config();

$args = wp_parse_args( $args, $defaults );

$post_id = get_the_id();

$post_prices = get_post_price_data($post_id);
$post_price = $post_prices['default'];

// 获取当前文章的格式
$post_format = get_post_format();
$format_icons = array('image'=>'fas fa-image', 'video'=>'fas fa-play', 'audio'=>'fas fa-music');

if ($post_format && isset($format_icons[$post_format])) {
	$post_format_icon = $format_icons[$post_format];
}else{
	$post_format_icon = false;
}


if (in_array($post_format, array('video','audio')) && zb_get_media_preview_url()) {
	$args['media_class'] = $args['media_class'] . ' media-' .$post_format;
}


?>


<?php if ( $args['type'] == 'grid' ) : ?>
	<div class="col">
		<article class="post-item item-grid">

			<div class="tips-badge position-absolute top-0 start-0 z-1 m-2">
				<?php if (is_sticky()) :?>
				<div class="badge bg-dark bg-opacity-75 text-white"><?php _e('置顶', 'ripro'); ?></div>
				<?php endif;?>

				<?php if ($args['is_vip_icon'] && post_is_vip_pay($post_id)) :?>
				<div class="badge bg-warning bg-opacity-75"><?php _e('VIP', 'ripro'); ?></div>
				<?php endif;?>

			</div>
			<?php get_template_part('template-parts/loop/entry-media-part','', $args);?>
			<div class="entry-wrapper">
				<?php if ($args['is_entry_cat']): ?>
					<div class="entry-cat-dot"><?php zb_meta_category(2);?></div>
				<?php endif; ?>

				<h2 class="entry-title">
					<a target="<?php echo get_target_blank();?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
				</h2>

				<?php if ($args['is_entry_desc']): ?>
					<div class="entry-desc"><?php echo zb_get_post_excerpt(40);?></div>
				<?php endif; ?>

				<?php if ($args['is_entry_meta']){
					get_template_part('template-parts/loop/entry-meta-part');
				}?>
			</div>
		</article>
	</div>

<?php elseif ( $args['type'] == 'grid-overlay' ) : ?>
	<div class="col">
		<article class="post-item item-grid grid-overlay">

			<div class="tips-badge position-absolute top-0 start-0 z-1 m-2">
				<?php if (is_sticky()) :?>
				<div class="badge bg-dark bg-opacity-75 text-white"><?php _e('置顶', 'ripro'); ?></div>
				<?php endif;?>

				<?php if ($args['is_vip_icon'] && post_is_vip_pay($post_id)) :?>
				<div class="badge bg-warning bg-opacity-75"><?php _e('VIP', 'ripro'); ?></div>
				<?php endif;?>

			</div>

			<?php get_template_part('template-parts/loop/entry-media-part','', $args);?>

			<div class="entry-wrapper">
				<?php if ($args['is_entry_cat']): ?>
					<div class="entry-cat-dot"><?php zb_meta_category(2);?></div>
				<?php endif; ?>

				<h2 class="entry-title">
					<a target="<?php echo get_target_blank();?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
				</h2>

				<?php if ($args['is_entry_desc']): ?>
					<div class="entry-desc"><?php echo zb_get_post_excerpt(40);?></div>
				<?php endif; ?>

				<?php if ($args['is_entry_meta']){
					get_template_part('template-parts/loop/entry-meta-part');
				}?>


			</div>
		</article>
	</div>


<?php elseif ( $args['type'] == 'list' ) : ?>
	<div class="col">
		<article class="post-item item-list">

			<div class="tips-badge position-absolute top-0 start-0 z-1 m-3 m-md-3">
				<?php if (is_sticky()) :?>
				<div class="badge bg-dark bg-opacity-75 text-white"><?php _e('置顶', 'ripro'); ?></div>
				<?php endif;?>

				<?php if ($args['is_vip_icon'] && post_is_vip_pay($post_id)) :?>
				<div class="badge bg-warning bg-opacity-75"><?php _e('VIP', 'ripro'); ?></div>
				<?php endif;?>
			</div>

			<div class="entry-media ratio ratio-3x2 col-auto">
				<a target="<?php echo get_target_blank();?>" class="media-img lazy <?php echo esc_attr($args['media_size_type']);?> <?php echo esc_attr($args['media_fit_type']);?>" href="<?php the_permalink();?>" title="<?php the_title();?>" data-bg="<?php echo zb_get_thumbnail_url();?>">
					<?php if ($post_format_icon):?>
						<div class="post-format-icon"><i class="<?php echo $post_format_icon;?>"></i></div>
					<?php endif;?>
				</a>
			</div>
			<div class="entry-wrapper">
				<div class="entry-body">

					<?php if ($args['is_entry_cat']): ?>
						<div class="entry-cat-dot"><?php zb_meta_category(2);?></div>
					<?php endif; ?>

					<h2 class="entry-title">
						<a target="<?php echo get_target_blank();?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
					</h2>
					<?php if ($args['is_entry_desc']): ?>
						<div class="entry-desc"><?php echo zb_get_post_excerpt(40);?></div>
					<?php endif; ?>
				</div>
				<?php if ($args['is_entry_meta']): ?>
					<div class="entry-footer">
						<?php get_template_part('template-parts/loop/entry-meta-part');?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	</div>

<?php elseif ( $args['type'] == 'title' ) : ?>
	<div class="col">
		<article class="post-item item-list">

			
			<?php

			$metaOption = _cao('archive_item_entry_footer',array(
				'date', 'likes', 'views', 'likes', 'fav','price'
			));

			$post_prices = get_post_price_data($post_id);
			$post_price = $post_prices['default'];

			?>


			<div class="entry-wrapper">
				<div class="entry-body">
					<h2 class="entry-title">
						<a target="<?php echo get_target_blank();?>" href="<?php the_permalink();?>" title="<?php the_title();?>"><?php the_title();?></a>
					</h2>
					<?php if ($args['is_entry_desc']): ?>
						<div class="entry-desc"><?php echo zb_get_post_excerpt(40);?></div>
					<?php endif; ?>
				</div>
				<?php if ($args['is_entry_meta']): ?>
					<div class="entry-footer">
						<div class="entry-meta">

							<?php if (is_sticky()) :?>
							<div class="badge bg-dark bg-opacity-75 text-white me-1"><?php _e('置顶', 'ripro'); ?></div>
							<?php endif;?>

							<?php if ($args['is_vip_icon'] && post_is_vip_pay($post_id)) :?>
								<div class="badge bg-warning bg-opacity-75 me-1"><?php _e('VIP', 'ripro'); ?></div>
							<?php endif;?>

							<?php if ($args['is_entry_cat']): ?>
								<div class="entry-cat-dot d-none d-md-inline-block mb-0 me-3"><?php zb_meta_category(1);?></div>
							<?php endif; ?>

							<?php if (in_array('date', $metaOption)): ?>
							<span class="meta-date"><i class="far fa-clock me-1"></i><?php zb_meta_datetime();?></span>
							<?php endif;?>

							<?php if (in_array('likes', $metaOption)): ?>
							<span class="meta-likes d-none d-md-inline-block"><i class="far fa-heart me-1"></i><?php echo zb_get_post_likes();?></span>
							<?php endif;?>

							<?php if (in_array('fav', $metaOption)): ?>
							<span class="meta-fav d-none d-md-inline-block"><i class="far fa-star me-1"></i><?php echo zb_get_post_fav();?></span>
							<?php endif;?>

							<?php if (in_array('views', $metaOption)): ?>
							<span class="meta-views"><i class="far fa-eye me-1"></i><?php echo zb_get_post_views();?></span>
							<?php endif;?>

							<?php if (in_array('price', $metaOption) && is_site_shop() && post_is_pay($post_id)) :?>
							<span class="meta-price"><i class="<?php echo get_site_coin_icon();?> me-1"></i><?php echo $post_price;?></span>
							<?php endif;?>

						</div>

					</div>
				<?php endif; ?>
			</div>
		</article>
	</div>

<?php endif; ?>