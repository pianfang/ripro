
<div class="search-form">
    <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php if (_cao('is_site_pro_search',true)) : ?>
            <div class="search-select">
                <?php 
                wp_dropdown_categories( array(
                    'hide_empty'       => false,
                    'show_option_none' => __('全站','ripro'),
                    'option_none_value' => '',
                    'order'          => 'DESC',
                    'orderby'          => _cao('pro_search_select_order','id'),
                    'hierarchical'     => true,
                    'depth'     => intval(_cao('pro_search_select_depth',1)),
                    'id'     => 'cat-search-select-'.mt_rand(1,10),
                    'class'     => 'form-select',
                ) );?>
            </div>
        <?php endif; ?>

        <div class="search-fields">
          <input type="text" class="" placeholder="<?php _e('输入关键词 回车...', 'ripro'); ?>" autocomplete="off" value="<?php echo esc_attr( get_search_query() ) ?>" name="s" required="required">
          <button title="点击搜索" type="submit"><i class="fas fa-search"></i></button>
      </div>
  </form>
</div>
