<?php 

$post_id = get_the_ID();

$bg_image = zb_get_thumbnail_url($post_id);
$single_style = _cao('single_style','general'); //general hero shop

?>

<?php if ( !empty( _cao('single_top_breadcrumb',false) ) ): ?>
<div class="container-full bg-white">
	<nav class="container d-none d-md-flex py-2" aria-label="breadcrumb">
		<?php zb_the_breadcrumb('breadcrumb mb-0');?>
	</nav>
</div>
<?php endif;?>


<?php 

//视频模块
if (get_post_meta($post_id, 'cao_video', true)) {
	$single_style = 'general';
	get_template_part('template-parts/single/video-hero');
}elseif ($single_style=='hero') {
	get_template_part('template-parts/single/entry-hero');
}elseif ($single_style=='shop' && !post_is_down_pay($post_id)) {
	$single_style = 'general';
}

?>


<div class="container mt-2 mt-sm-4">
	<div class="row g-2 g-md-3 g-lg-4">

		<div class="content-wrapper col-md-12 col-lg-9" data-sticky-content>
			<div class="card">

				<?php if ($single_style=='general') :?>
					<div class="article-header">
						<?php the_title('<h1 class="post-title mb-2 mb-lg-3">', '</h1>');?>
						<div class="article-meta">
							<?php get_template_part('template-parts/single/entry-meta');?>
						</div>
					</div>
				<?php elseif ($single_style=='shop'):?>
					<?php get_template_part('template-parts/single/entry-shop');?>
				<?php endif;?>

				<?php get_template_part('template-parts/single/entry-media-preview');?>

				
				<?php if (!empty(_cao('is_single_new_style',1))) {
					get_template_part('template-parts/single/post-content-new');
				}else{
					get_template_part('template-parts/single/post-content');
				}?>

			</div>
			
			<?php get_template_part('template-parts/single/entry-navigation');?>
			
			<?php get_template_part('template-parts/single/entry-related-posts');?>

			<?php
			  if ( empty(_cao('is_single_new_style',1)) && (comments_open() || get_comments_number()) ) :
			    comments_template();
			  endif;
			?>

		</div>

		<div class="sidebar-wrapper col-md-12 col-lg-3 h-100" data-sticky>
			<div class="sidebar">
				<?php dynamic_sidebar( 'single-sidebar' ); ?>
			</div>
		</div>

	</div>
</div>