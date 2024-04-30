<!DOCTYPE html>
<html <?php language_attributes();?> data-bs-theme="<?php echo get_site_default_color_style();?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head();?>
</head>

<body <?php body_class();?>>
<?php wp_body_open();?>


<?php get_template_part( 'template-parts/header/menu'); ?>

<!-- **************** MAIN CONTENT START **************** -->
<main>
	