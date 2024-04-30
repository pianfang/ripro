<?php

if (empty($args)) {
    return;
}


if (!_cao('remove_site_search',true) && !empty(trim($args['search_hot']))) {
	$search_hot_exp = explode(",", trim($args['search_hot']));
}else{
	$search_hot_exp = array();
}

$vanta_uniqid = uniqid('vanta-bg-');


if (!empty($args['is_mobile_img_bg']) && wp_is_mobile()) {
	$args['bg_type'] = 'img';
}


if ($args['bg_type']=='img') {
	$classex = 'bg-type-'.$args['bg_type'].' jarallax';
	$dataex = 'data-jarallax data-speed="0.2"';
}elseif ($args['bg_type']=='video') {
	$classex = 'bg-type-'.$args['bg_type'].' jarallax';
	$dataex = 'data-jarallax data-video-src="mp4:'.$args['bg_video'].'"';
}else{
	$classex = 'bg-type-'.$args['bg_type'];
	$dataex = '';
	// $args['color']['color']
	// $args['color']['bgcolor']
	// 0x#035abe

	$vantas = [

		'waves' => [
			'fun'=>'WAVES',
			'opt'=> [
				'el' => '#'.$vanta_uniqid,
				'mouseControls' => true,
				'touchControls' => true,
				'gyroControls' => false,
				'minHeight' => 200.00,
				'minWidth' => 200.00,
				'scale' => 1.00,
				'scaleMobile' => 1.00,
				'color' => $args['color']['bgcolor'],
			],
		],
		'clouds' => [
			'fun'=>'CLOUDS',
			'opt'=> [
				'el' => '#'.$vanta_uniqid,
				'mouseControls' => true,
				'touchControls' => true,
				'gyroControls' => false,
				'minHeight' => 200.00,
				'minWidth' => 200.00,
				'skyColor' => $args['color']['bgcolor'],
				'cloudColor' => $args['color']['color'],
			],
		],
		'net' => [
			'fun'=>'NET',
			'opt'=> [
				'el' => '#'.$vanta_uniqid,
				'mouseControls' => true,
				'touchControls' => true,
				'gyroControls' => false,
				'minHeight' => 200.00,
				'minWidth' => 200.00,
				'scale' => 1.00,
				'scaleMobile' => 1.00,
				'backgroundColor' => $args['color']['bgcolor'],
				'color' => $args['color']['color'],
			],
		],
		'halo' => [
			'fun'=>'HALO',
			'opt'=> [
				'el' => '#'.$vanta_uniqid,
				'mouseControls' => true,
				'touchControls' => true,
				'gyroControls' => false,
				'minHeight' => 200.00,
				'minWidth' => 200.00,
				'backgroundColor' => $args['color']['bgcolor'],
			],
		],
	];

	$vanta = $vantas[$args['bg_type']];

}

// zb_dump($args);

?>



<div id="<?php echo $vanta_uniqid; ?>" class="search-bg <?php echo $classex;?>" <?php echo $dataex;?>>

	<?php if ( !empty($args['bg_overlay']) ) : ?>
	<div class="search-bg-overlay"></div>
	<?php endif;?>

	<?php if ( in_array($args['bg_type'], array('img','video')) ) : ?>
		<img class="jarallax-img lazy" data-src="<?php echo esc_url($args['bg_img']); ?>" src="<?php echo get_default_lazy_img_src(); ?>" alt="<?php echo esc_attr($args['title']); ?>">
	<?php endif;?>

	<div class="container search-warp">
		<h1 class="search-title"><?php echo $args['title']; ?></h1>
		<p class="search-desc"><?php echo $args['desc']; ?></p>
		<?php get_search_form();?>

		<?php if ( !empty($search_hot_exp) ) : ?>
		<div class="search-hots">
			<span class="me-1"><?php _e('搜索热词', 'ripro');?></span>
			<?php foreach ($search_hot_exp as $exp) {
				if (!empty($exp)) {
					echo '<span><a href="'.get_search_link($exp).'">'.$exp.'</a></span>';
				}
			}?>
		</div>
		<?php endif;?>
		
	</div>

	

</div>


<?php if (!in_array($args['bg_type'], array('img','video'))) :?>
<script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/three.min.js';?>" defer></script>
<script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/vanta.'.$args['bg_type'].'.min.js';?>" defer></script>
<script>
	$(document).ready(function() {
		var vanta = <?php echo json_encode($vanta);?>;
		var effect = VANTA[vanta.fun](vanta.opt);
	});
</script>
<?php endif;?>

