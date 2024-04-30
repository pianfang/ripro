<?php
$copyright = _cao('single_bottom_copyright');
if (empty($copyright)) {
	return;
}
?>
<div class="entry-copyright">
	<?php echo '<i class="fas fa-info-circle me-1"></i>' .wp_kses_post($copyright); ?>
</div>