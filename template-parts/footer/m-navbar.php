<?php
$site_footer_navbar = _cao('site_moblie_footer_menu', array());
if (!empty($site_footer_navbar) && is_array($site_footer_navbar)): ?>
	<div class="m-navbar">
		<ul>
			<?php foreach ($site_footer_navbar as $item) {
				$target = (empty($item['is_blank'])) ? '' : '_blank';
				printf('<li><a target="%s" href="%s" rel="nofollow noopener noreferrer"><i class="%s"></i><span>%s</span></a></li>', $target, $item['href'], $item['icon'], $item['title']);
			}?>
		</ul>
	</div>
<?php endif;?>
