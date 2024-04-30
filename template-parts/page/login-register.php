<?php

defined('ABSPATH') || exit;
###########################################


/**
 * 用户登录注册页面模板
 */


if (is_user_logged_in()) {
    wp_safe_redirect(get_uc_menu_link());exit;
}


$is_login_action   = (get_query_var('uc-login-page') == 1) ? true : false;
$is_reg_action     = (get_query_var('uc-register-page') == 1) ? true : false;
$is_lostpwd_action = (get_query_var('uc-lostpwd-page') == 1) ? true : false;


?>

<!DOCTYPE html>
<html <?php language_attributes();?> data-bs-theme="<?php echo get_site_default_color_style();?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head();?>
</head>

<body>


<!-- **************** MAIN CONTENT START **************** -->
<main>

<?php

$args = [
	'bg_type'=> _cao('site_loginpage_bg_type','img'),
	'bg_img'=> _cao('site_loginpage_bg_img',get_template_directory_uri() . '/assets/img/bg.jpg'),
	'color'=> _cao('site_loginpage_color',array('bgcolor'=>'#005588','color'=>'#ededed')),
];

if ($args['bg_type']=='img') {
	$classex = 'bg-type-'.$args['bg_type'].' lazy';
}else{
	$classex = 'bg-type-'.$args['bg_type'];

	$vantas = [

		'waves' => [
			'fun'=>'WAVES',
			'opt'=> [
				'el' => '.login-and-register',
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
				'el' => '.login-and-register',
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
				'el' => '.login-and-register',
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
				'el' => '.login-and-register',
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

?>

<div class="login-and-register <?php echo $classex;?>" data-bg="<?php echo esc_url($args['bg_img']); ?>">
	<div class="container h-100 d-flex px-0 px-sm-4">
		<div class="row justify-content-center align-items-center m-auto">
			<div class="col-12">
				<div class="bg-white shadow rounded-3 overflow-hidden my-4">
					<div class="p-4 p-lg-5 py-2 py-md-4 text-center">
						<!-- Logo -->
						<a class="text-center" href="<?php echo esc_url( home_url() );?>">
							<img class="logo regular mb-2" src="<?php echo esc_url( _cao('site_logo','') );?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) );?>">
						</a>
						<?php echo get_template_part('template-parts/page/login-form');?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
</main>

<?php if ($args['bg_type']!='img') :?>
<script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/three.min.js';?>" defer></script>
<script src="<?php echo get_template_directory_uri() . '/assets/js/vantajs/vanta.'.$args['bg_type'].'.min.js';?>" defer></script>
<script>
	$(document).ready(function() {
		var vanta = <?php echo json_encode($vanta);?>;
		var effect = VANTA[vanta.fun](vanta.opt);
	});
</script>
<?php endif;?>

<?php wp_footer();?>

</body>
</html>

