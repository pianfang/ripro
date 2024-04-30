<?php

if (empty($args)) {
    return;
}

?>

<div class="bg-warp <?php echo esc_attr($args['bg_style']);?> lazy" data-bg="<?php echo esc_url($args['bg_img']); ?>">
	<div class="container py-5">
		<?php if (!empty($args['title'])) : ?>
		<h4 class="bg-title"><?php echo $args['title'];?></h4>
		
		<?php endif; ?>
		<?php if (!empty($args['desc'])) : ?>
		<p class="bg-desc"><?php echo $args['desc'];?></p>
		<?php endif; ?>

		<div class="bg-btns">
		<?php foreach ($args['btn_data'] as $key => $item) : ?>
		<a class="btn btn-<?php echo esc_attr( $item['color'] );?> rounded-pill m-1 px-4" href="<?php echo $item['link'];?>"><i class="<?php echo esc_attr( $item['icon'] );?> me-1"></i><?php echo $item['title'];?></a>
		<?php endforeach;?>
		</div>
	</div>
</div>

