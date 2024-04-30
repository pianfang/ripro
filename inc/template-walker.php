<?php

class ZB_Walker_Nav_Menu extends Walker_Nav_Menu {

    public $db_fields = array('parent' => 'menu_item_parent', 'id' => 'db_id');
    protected $mega;

    public function __construct($mega = false) {
        $this->mega = $mega;
    }

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $keep_classes = ['menu-item','menu-item-has-children'];
        $classes = array_intersect( $classes, $keep_classes );

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts           = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $nav_icon = get_post_meta($item->ID, 'menu_icon', true);
        if (!empty($nav_icon)) {
            $item->title = '<i class="' . $nav_icon . ' me-1"></i>' . $item->title;
        }

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = array()) {
        $output .= "</li>\n";
    }


    public static function fallback($args) {
        extract($args);

        $fb_output = null;

        if ($container) {
            $fb_output = '<' . $container;

            if ($container_id) {
                $fb_output .= ' id="' . $container_id . '"';
            }

            if ($container_class) {
                $fb_output .= ' class="' . $container_class . '"';
            }

            $fb_output .= '>';
        }

        $fb_output .= '<ul';

        if ($menu_id) {
            $fb_output .= ' id="' . $menu_id . '"';
        }

        if ($menu_class) {
            $fb_output .= ' class="' . $menu_class . '"';
        }

        $fb_output .= '>';
        $fb_output .= '<li class="menu-item"><a href="' . esc_url(admin_url('nav-menus.php')) . '">Add Menus</a></li>';
        $fb_output .= '</ul>';

        if ($container) {
            $fb_output .= '</' . $container . '>';
        }

        echo wp_kses($fb_output, array(
            'ul'   => array('id' => array(), 'class' => array()),
            'li'   => array('class' => array()),
            'a'    => array('href' => array()),
            'span' => array(),
        ));
    }

}

/**
 * 评论
 */
class ZB_Walker_Comment extends Walker_Comment {

    public function html5_comment( $comment, $depth, $args ) {
        $GLOBALS['comment'] = $comment;

        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        $author = get_comment_author();
        
        if( $comment->user_id){
            
            $author = $author.' '.zb_get_user_badge($comment->user_id,'span');
        }else if( $comment->comment_author_url ){
            $author = '<a href="'.esc_url($comment->comment_author_url).'" target="_blank" rel="nofollow">'.$author.'</a>';
        }
        
        ?>
        <<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        
        <div id="div-comment-<?php comment_ID() ?>" class="comment-inner">
            <div class="comment-author">
                <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
            </div>
            <div class="comment-body">
                <div class="nickname"><?php echo $author;?></div>
                <?php if ( $comment->comment_approved == '0' ) : ?>
                    <div class="comment-awaiting-moderation"><?php _e('您的评论正在等待审核。', 'ripro'); ?></div>
                <?php endif; ?>
                <div class="comment-content"><?php comment_text(); ?></div>

                <div class="comment-meta">

                    <span class="comment-time"><?php echo sprintf(__('%s前', 'ripro'),human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?></span>
                    <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                </div>

            </div>

            
        </div>
        <?php
    }
}
