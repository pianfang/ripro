<?php
// TAB模式新布局
$tab_comments = comments_open() || get_comments_number();
$tab_helps = _cao('single_new_style_tab_helps',array());


?>


<div class="single-content-nav">
  <ul class="nav nav-pills" id="pills-tab" role="tablist">

    <li class="nav-item" role="presentation">
      <a class="nav-link active" id="pills-details-tab" data-toggle="pill" href="#pills-details" role="tab" aria-controls="pills-details" aria-selected="true"><i class="far fa-file-alt me-1"></i><?php _e( '详情介绍','ripro' );?></a>
    </li>

    <?php if ( !empty($tab_helps) ) : ?>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="pills-faq-tab" data-toggle="pill" href="#pills-faq" role="tab" aria-controls="pills-faq" aria-selected="false"><i class="far fa-question-circle me-1"></i><?php _e( '常见问题','ripro' );?></a>
    </li>
    <?php endif;?>

    <?php if ( !empty($tab_comments) ) : ?>
    <li class="nav-item" role="presentation">
      <a class="nav-link" id="pills-comments-tab" data-toggle="pill" href="#pills-comments" role="tab" aria-controls="pills-comments" aria-selected="false"><i class="fa fa-comments-o me-1"></i><?php _e( '评论建议','ripro' );?></a>
    </li>
    <?php endif;?>
    
  </ul>
</div>


<div class="tab-content" id="pills-tabContent">
	<div class="tab-pane fade show active" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
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
			        'before' => '<div class="custom-nav mb-3"><ul class="pagination d-flex flex-wrap justify-content-center"><span class="disabled">' . __('内容分页', 'ripro') . '</span>',
			        'after'  => '</ul></div>',
			    )
			);
			?>

			<?php get_template_part('template-parts/single/entry-copyright');?>

		</article>

		<?php do_action('ripro_ads', 'ad_single_bottum'); ?>

		<?php get_template_part('template-parts/single/entry-tags');?>

		<?php get_template_part('template-parts/single/entry-social');?>
	</div>


	<?php if ( !empty($tab_helps) ) : ?>
	<div class="tab-pane fade" id="pills-faq" role="tabpanel" aria-labelledby="pills-faq-tab">
	
	    <ol class="list-group list-group-numbered">
		  <?php foreach ($tab_helps as $key => $item) : ?>
		  	<li class="list-group-item list-group-item-info d-flex justify-content-between align-items-start">
			    <div class="ms-2 me-auto">
			      <div class="fw-bold"><?php echo $item['title'];?></div>
			      <div class="text-muted"><?php echo $item['desc'];?></div>
			    </div>
			</li>
	    <?php endforeach;?>
		</ol>

	</div>
	<?php endif;?>

	<?php if ( !empty($tab_comments) ) : ?>
	<div class="tab-pane fade" id="pills-comments" role="tabpanel" aria-labelledby="pills-comments-tab">
	<?php comments_template();?>
	</div>
	<?php endif;?>

</div>

