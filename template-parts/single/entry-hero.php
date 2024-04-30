<?php 

$post_id = get_the_ID();
$bg_image = zb_get_thumbnail_url($post_id);

?>
<div class="archive-hero post-hero text-center">
    <div class="archive-hero-bg lazy" data-bg="<?php echo $bg_image; ?>"></div>
    <div class="container py-3 py-md-4">
    	<div class="article-header mb-0">
			<?php the_title('<h1 class="post-title mb-2 mb-lg-3">', '</h1>');?>
			<div class="article-meta">
				<?php get_template_part('template-parts/single/entry-meta');?>
			</div>
		</div>
    </div>
</div>