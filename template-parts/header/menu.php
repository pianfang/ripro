<header class="site-header">

    <div class="container"> <!-- container-fluid px-lg-5 -->
	    <div class="navbar">
	      <!-- logo -->
	      <?php get_template_part( 'template-parts/header/logo-wrapper'); ?>

	      <div class="sep"></div>
	      
	      <nav class="main-menu d-none d-lg-block">
	        <?php 

	        // 定义缓存的ID和过期时间
			$cache_id = 'main-menu-cache';
			$cache_expiration = 5 * 24 * 3600; // 缓存一天
			
			// 尝试从缓存获取菜单
			if (_cao('is_site_menu_cache',true)) {
				$cached_menu = get_transient( $cache_id );
			}else{
				$cached_menu = false;
			}
			
			// 如果没有缓存，重新生成并缓存菜单
			if (false === $cached_menu ) {
			    
			    $cached_menu = wp_nav_menu( array(
		          'container' => true,
		          'fallback_cb' => 'ZB_Walker_Nav_Menu::fallback',
		          'menu_id' => 'header-navbar',
		          'menu_class' => 'nav-list',
		          'theme_location' => 'main-menu',
		          'walker' => new ZB_Walker_Nav_Menu( true ),
		          'echo' => false, // 返回html内容
		        ) );

			    set_transient( $cache_id, $cached_menu, $cache_expiration );
			}
			// 输出菜单
			echo $cached_menu;

	        ?>
	      </nav>
	      
	      <div class="actions">
	        <?php get_template_part( 'template-parts/header/action-hover'); ?>
	        <div class="burger d-flex d-lg-none"><i class="fas fa-bars"></i></div>
	      </div>

	      <?php if ( empty(_cao('remove_site_search',false)) ) : ?>
	      <div class="navbar-search"><?php get_search_form();?></div>
		  <?php endif;?>
	      
	    </div>
    </div>

</header>

<div class="header-gap"></div>

