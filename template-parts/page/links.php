<?php

get_header();

$args = array(
	'taxonomy' => array('link_category'),
	'orderby'  => 'name',
	'order'    => 'asc',
	'hide_empty' => true // for development
);

$link_category = get_terms($args);

$bg_image = get_template_directory_uri() . '/assets/img/bg2.png';

?>

<div class="archive-hero text-center">
    <div class="archive-hero-bg lazy" data-bg="<?php echo $bg_image; ?>"></div>
    <div class="container py-3 py-md-4">
    	<h1 class="archive-title mb-0"><i class="fab fa-staylinked me-1"></i><?php _e('网址导航', 'ripro');?></h1>
    	
    </div>
</div>

<section class="container">

	<?php if ($link_category): foreach ( $link_category as $key => $cat ) : $color = zb_get_color_class($key);?>
	<h2 class="position-relative mb-3 mt-4 btn btn-sm btn-<?php echo esc_attr( $color );?> px-4 mx-1 active" href="/tags/?orderby=count">
		<i class="fas fa-desktop me-1"></i><?php echo $cat->name;?>
		<span>(<?php echo $cat->count;?>)</span>
	</h2>

	<div id="<?php echo esc_attr( $cat->term_id );?>" class="links-page-warp row g-2 g-md-3 g-lg-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
			
			<?php 
			$bookmarks = get_bookmarks( array('orderby' => 'link_rating','category' => $cat->term_id) );
			if ($bookmarks) {
				
				foreach ( $bookmarks as $bookmark ): 
				$color = zb_get_color_class(mt_rand(1, 6));
				$link_image = (!empty($bookmark->link_image)) ? $bookmark->link_image : '';
				$link_nofollow = 'nofollow noopener noreferrer';
				?>

					<div class="col">
						<a target="<?php echo esc_attr($bookmark->link_target);?>" class="link-item p-2 p-md-3" href="<?php echo home_url('/goto?url=' . $bookmark->link_url);?>" rel="<?php echo esc_attr($link_nofollow);?>" title="<?php echo $bookmark->link_name;?>">
					      <div class="d-flex align-items-center">
					        <div class="link-img lazy bg-opacity-10 bg-<?php echo esc_attr( $color );?> text-<?php echo esc_attr( $color );?>" data-bg="<?php echo $link_image;?>">
					        	<?php echo empty($link_image) ? mb_substr($bookmark->link_name, 0, 1) : '' ?>
					        </div>
					        <div class="ms-3">
					          <b class="text-dark"><?php echo $bookmark->link_name;?></b>
					          <p class="mb-0 small text-muted"><?php echo $bookmark->link_description;?></p>
					        </div>
					      </div>
				      	</a>
					</div>

				<?php endforeach;
				
			}else{
				get_template_part('template-parts/loop/item', 'none');
			}
			?>

	</div>

	<?php endforeach;
	else:
		get_template_part('template-parts/loop/item', 'none');
	endif;?>

</section>

<?php get_footer();?>