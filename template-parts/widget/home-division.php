<?php

if (empty($args)) {
    return;
}

?>


<section class="container">
	<div class="row row-cols-2 row-cols-lg-4 g-2 g-md-4">

		<?php foreach ($args['div_data'] as $key => $item) : ?>

		<div class="col">
			<div class="division-item">
				
				<div class="division-icon <?php echo esc_attr( $args['icon_style'] );?>" style="background-color:<?php echo $item['color'];?>"> <i class="<?php echo esc_attr( $item['icon'] );?>"></i></div>
			    <div class="division-warp">
			        <h4 class="division-title">
			        	<?php if (!empty($item['link'])) : ?>
					    	<a href="<?php echo $item['link'];?>"><?php echo $item['title'];?></a>
					    <?php else:?>
					    	<?php echo $item['title'];?>
					    <?php endif;?>
			        </h4>
			        <p class="division-desc"><?php echo $item['desc'];?></p>
			    </div>
				
			</div>
		</div>

		<?php endforeach;?>
	  	
	</div>
</section>

