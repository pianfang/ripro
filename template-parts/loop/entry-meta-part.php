<?php

$metaOption = _cao('archive_item_entry_footer',array(
	'date', 'likes', 'views', 'likes', 'fav','price'
));

$post_id = get_the_id();
$post_prices = get_post_price_data($post_id);
$post_price = $post_prices['default'];


?>

<div class="entry-meta">

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