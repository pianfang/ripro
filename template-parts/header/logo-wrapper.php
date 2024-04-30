<div class="logo-wrapper">
<?php
$logo_img      = _cao('site_logo', get_template_directory_uri() . '/assets/img/logo.png');
$logo_dark_img = _cao('site_logo_dark', get_template_directory_uri() . '/assets/img/logo-dark.png');

if (get_site_default_color_style() == 'dark') {
    $curr_logo = $logo_dark_img;
}else{
	$curr_logo = $logo_img;
}

$blog_name = get_bloginfo('name');
$home_url  = esc_url(home_url('/'));
if (!empty($logo_img)) {
    $logo_html = sprintf('<img class="logo regular" data-light="%s" data-dark="%s" src="%s" alt="%s">', esc_url($logo_img), esc_url($logo_dark_img), esc_url($curr_logo), esc_attr($blog_name));
} else {
    $logo_html = sprintf('<span class="logo text">%s</span>', esc_html($blog_name));
}
printf('<a rel="nofollow noopener noreferrer" href="%s">%s</a>', esc_url(home_url('/')), $logo_html);
?>
</div>
