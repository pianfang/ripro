<?php 

$cat_id = get_queried_object_id();
$meta_bg = get_term_meta($cat_id, 'bg-image', true);
$bg_img = (!empty($meta_bg)) ? $meta_bg : zb_get_thumbnail_url();

?>

<div class="archive-hero text-center">
    <div class="archive-hero-bg lazy" data-bg="<?php echo esc_url($bg_img); ?>"></div>
        <div class="container py-2 py-md-4">
            <?php
            the_archive_title( '<h1 class="archive-title mb-2">', '</h1>' );

            the_archive_description( '<div class="archive-desc mt-2 mb-0">', '</div>' );
            // get_search_form();
            ?>
        </div>
</div>
