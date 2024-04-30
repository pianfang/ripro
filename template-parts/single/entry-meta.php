<?php

$post_id = get_the_id();
$post_prices = get_post_price_data($post_id);
$post_price = $post_prices['default'];


$metaOption = _cao('single_top_title_meta', array(
    'date', 'cat', 'views', 'likes', 'fav',
));

if (in_array('date', $metaOption)): ?>
	<span class="meta-date"><i class="far fa-clock me-1"></i><?php echo get_the_time('Y-m-d');?></span>
<?php endif;?>

<?php if (in_array('cat', $metaOption)): ?>
	<span class="meta-cat-dot"><i class="far fa-folder me-1"></i><?php zb_meta_category(2);?></span>
<?php endif;?>

<?php if (in_array('fav', $metaOption)): ?>
	<span class="meta-fav d-none d-md-inline-block"><i class="far fa-star me-1"></i><?php echo zb_get_post_fav();?></span>
<?php endif;?>

<?php if (in_array('likes', $metaOption)): ?>
	<span class="meta-likes d-none d-md-inline-block"><i class="far fa-heart me-1"></i><?php echo zb_get_post_likes();?></span>
<?php endif;?>

<?php if (in_array('views', $metaOption)): ?>
	<span class="meta-views"><i class="far fa-eye me-1"></i><?php echo zb_get_post_views();?></span>
<?php endif;?>

<?php if (in_array('comment', $metaOption) && comments_open() && is_site_comments()): ?>
	<span class="meta-comment"><a href="<?php echo esc_url(get_the_permalink() . '#comments'); ?>"><i class="far fa-comments me-1"></i><?php echo get_comments_number();?></a></span>
<?php endif;?>

<?php if (false && is_site_shop() && post_is_pay($post_id)) :?>
	<span class="meta-price"><i class="<?php echo get_site_coin_icon();?> me-1"></i><?php echo $post_price;?></span>
<?php endif;?>

<?php if (!empty($metaOption)): ?>
	<span class="meta-edit"><?php edit_post_link(__('[编辑]','ripro'));?></span></span>
<?php endif;?>
