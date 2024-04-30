
</main>
<!-- **************** MAIN CONTENT END **************** -->

<!-- =======================
Footer START -->
<footer class="site-footer py-md-4 py-2 mt-2 mt-md-4">
	<div class="container">

		<?php get_template_part( 'template-parts/footer/widget');?>

		<div class="text-center small w-100">
			<div><?php echo _cao('site_copyright_text','Copyright © 2023 <a target="_blank" href="http://ritheme.com/">RiPro-V5</a> - All rights reserved');?></div>
			<div class=""><?php echo _cao('site_ipc_text','') . _cao('site_ipc2_text','');?></div>
		</div>

		<?php get_template_part( 'template-parts/footer/links');?>

		<?php if (defined('WP_DEBUG') && WP_DEBUG == true) {
			echo '<p id="debug-info" class="m-0 small text-primary w-100 text-center">'.sprintf('SQL：%s',get_num_queries()).'<span class="sep"> | </span>'.sprintf('Pages：%ss',timer_stop(0,5)).'</p>';
		}?>

	</div>
</footer>
<!-- =======================
Footer END -->


<!-- Back to top rollbar-->
<?php get_template_part('template-parts/footer/rollbar');?>
<div class="back-top"><i class="fas fa-caret-up"></i></div>

<!-- m-navbar -->
<?php get_template_part('template-parts/footer/m-navbar');?>

<!-- dimmer-->
<div class="dimmer"></div>
<?php get_template_part( 'template-parts/footer/off-canvas');?>


<?php wp_footer();?>

<!-- 自定义js代码 统计代码 -->
<?php if ( !empty(_cao('site_web_js')) ) echo _cao('site_web_js');?>
<!-- 自定义js代码 统计代码 END -->

</body>
</html>
