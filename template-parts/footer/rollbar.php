<?php
$site_footer_rollbar = _cao('site_footer_rollbar', array());
if (!empty($site_footer_rollbar) && is_array($site_footer_rollbar)): ?>
	<div class="rollbar">
		<ul class="actions">
			<?php foreach ($site_footer_rollbar as $item) {
				$target = (empty($item['is_blank'])) ? '' : '_blank';
				printf('<li><a target="%s" href="%s" rel="nofollow noopener noreferrer"><i class="%s"></i><span>%s</span></a></li>', $target, $item['href'], $item['icon'], $item['title']);
			}?>
		</ul>
	</div>
<?php endif;?>
