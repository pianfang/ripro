<?php
if (empty(_cao('is_single_bottom_navigation',true))) {
    return;
}
?>


<div class="entry-navigation">
    <div class="row g-3">
        <?php if( $pre = get_previous_post() ): ?>
        <div class="col-lg-6 col-12">
            <a class="entry-page-prev" href="<?php echo get_the_permalink($pre->ID);?>" title="<?php echo esc_attr(get_the_title($pre->ID));?>">
                <div class="entry-page-icon"><i class="fas fa-arrow-left"></i></div>
                <div class="entry-page-info">
                    <span class="d-block rnav"><?php echo esc_html( '上一篇','ripro' );?></span>
                    <div class="title"><?php echo get_the_title($pre);?></div>
                </div>
            </a>
        </div>
        <?php endif; ?>
        <?php if( $next = get_next_post() ): ?>
        <div class="col-lg-6 col-12">
            <a class="entry-page-next" href="<?php echo get_the_permalink($next->ID);?>" title="<?php echo esc_attr(get_the_title($next->ID));?>">
                <div class="entry-page-info">
                    <span class="d-block rnav"><?php echo esc_html( '下一篇', 'ripro' );?></span>
                    <div class="title"><?php echo get_the_title($next);?></div>
                </div>
                <div class="entry-page-icon"><i class="fas fa-arrow-right"></i></div>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>