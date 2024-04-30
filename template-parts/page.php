<?php 

$post_id = get_the_ID();

$is_page_sidebar = !isset($args['full']) || empty($args['full']); //是否显示侧边栏

$content_class = ($is_page_sidebar) ? 'col-lg-9' : 'col-lg-12';

?>



<?php if ( !empty( _cao('single_top_breadcrumb',false) ) ): ?>
<div class="container-full bg-white">
	<nav class="container d-none d-md-flex py-2" aria-label="breadcrumb">
		<?php zb_the_breadcrumb('breadcrumb mb-0');?>
	</nav>
</div>
<?php endif;?>


<div class="container mt-2 mt-sm-4">
	<div class="row g-2 g-md-3 g-lg-4">

		<div class="content-wrapper col-md-12 <?php echo esc_attr( $content_class );?>">
			<div class="card">
				<div class="article-header">
					<?php the_title('<h1 class="post-title mb-2 mb-lg-3">', '</h1>');?>
					<div class="article-meta">
						<?php get_template_part('template-parts/single/entry-meta');?>
					</div>
				</div>
				
				<?php do_action('ripro_ads', 'ad_single_top'); ?>

				<article <?php post_class('post-content');?> >
					<?php
					the_content(
					    sprintf(
					        wp_kses(
					            __('继续阅读<span class="screen-reader-text"> "%s"</span>', 'ripro'),
					            array(
					                'span' => array(
					                    'class' => array(),
					                ),
					            )
					        ),
					        wp_kses_post(get_the_title())
					    )
					);

					wp_link_pages(
					    array(
					        'before' => '<div class="single-page-link"><span class="post-page-numbers">分页：</span>',
					        'after'  => '</div>',
					    )
					);
					?>

					<?php //get_template_part('template-parts/single/entry-copyright');?>

				</article>

				<?php do_action('ripro_ads', 'ad_single_bottum'); ?>
				
				<?php //get_template_part('template-parts/single/entry-tags');?>

				<?php //get_template_part('template-parts/single/entry-social');?>


			</div>
			
			<?php //get_template_part('template-parts/single/entry-related-posts');?>

			<?php
			  if ( comments_open() || get_comments_number() ) :
			    comments_template();
			  endif;
			?>

		</div>

		<?php if ($is_page_sidebar) :?>
		<div class="sidebar-wrapper col-md-12 col-lg-3" data-sticky>
			<div class="sidebar">
				<?php dynamic_sidebar( 'single-sidebar' ); ?>
			</div>
		</div>
		<?php endif;?>

	</div>
</div>