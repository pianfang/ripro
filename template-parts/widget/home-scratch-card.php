<?php

if (empty($args)) {
    return;
}

$cdk_datas = array_map('trim', explode(PHP_EOL, $args['cdk_data']));

?>


<section class="container">
	<?php 
	    $section_title = $args['title'];
	    $section_desc = $args['desc'];
	?>
	<?php if ($section_title): ?>
	    <div class="section-title text-center mb-4">
	      <h3><?php echo $section_title ?></h3>
	      <?php if (!empty($section_desc)): ?>
	        <p class="text-muted mb-0"><?php echo $section_desc ?></p>
	      <?php endif; ?>
	    </div>
	<?php endif; ?>

	<div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-2 g-md-4">


		<?php foreach ($cdk_datas as $key => $code) : 

			$cdk_code = esc_sql(trim($code));
			$cdk_data = ZB_Cdk::get_cdk($cdk_code);

			$_title = __('已被领取使用', 'ripro');

			if (!empty($cdk_data) && $cdk_data->status == 0 && $cdk_data->expiry_time > time()) {
				$_title = ZB_Cdk::get_cdk_type($cdk_data->type);
			}

		?>

		<div class="col">
			<div class="scratch-item">
                <div class="code-content">
                    <div class="code-area">
                        <h4 class="code-title"><?php echo $_title;?></h4>
                        <p><img class="gift lazy" data-src="<?php echo get_template_directory_uri();?>/assets/img/gift.png" alt="gift" width="100"></p>
                        <div class="txt-code">
                            <span class="copycouponcode user-select-all"><?php echo $code;?></span><i class="far fa-copy ms-1"></i>
                        </div>

                    </div>
                </div>
	        </div>
		</div>

		<?php endforeach;?>
	  	
	</div>
</section>

