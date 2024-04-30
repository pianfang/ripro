<?php if (post_password_required() || !comments_open() || !is_site_comments()) return; ?>

<div id="comments" class="entry-comments">
	<?php $fields =  array(
		'author' => '<div class="comment-form-author"><input id="author" name="author" type="text" placeholder="' . __('*昵称:', 'ripro') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . ($req ? ' class="required"' : '') . '></div>',
		'email'  => '<div class="comment-form-email"><input id="email" name="email" type="text" placeholder="' . __('*邮箱:', 'ripro') . '" value="' . esc_attr($commenter['comment_author_email']) . '"' . ($req ? ' class="required"' : '') . '></div>',
		'url'    => '<div class="comment-form-url"><input id="url" name="url" type="text" placeholder="' . __('网址:', 'ripro') . '" value="' . esc_attr($commenter['comment_author_url']) . '" size="30"></div>',
		'cookies' => '<input type="hidden" name="wp-comment-cookies-consent" value="yes">'
	); ?>


	<h2 class="comments-title"><i class="fas fa-comment-dots me-1"></i><?= sprintf(__('评论(%s)', 'ripro'), number_format_i18n(get_comments_number())); ?></h2>

	<?php comment_form(array(
		'title_reply'        => __('提示：请文明发言', 'ripro'),
		'title_reply_to'     => '',
		'fields'             => $fields,
		'comment_field'      => '<div class="comment-form-comment"><textarea id="comment" name="comment" rows="3" placeholder="' . __('请输入评论内容...', 'ripro') . '"></textarea></div>',
		'must_log_in'        => '<div class="d-flex align-content-center justify-content-center"><a rel="nofollow noopener noreferrer" href="'.esc_url( wp_login_url(get_current_url())).'" class="login-btn btn btn-sm btn-dark px-4 rounded-pill mb-5"><i class="far fa-user me-1"></i>'.__('登录后评论', 'ripro').'</a></div>', //登录提示
		'logged_in_as'       => '', //已经登录提示
		'label_submit'       => __('提交评论', 'ripro'),
		'format'             => 'html5'
	)); ?>



	<?php if (have_comments()): ?>
		<ul class="comments-list">
			<?= wp_list_comments(array(
					'walker'      => new ZB_Walker_Comment,
					'style'       => 'ul',
					'short_ping'  => true,
					'type'        => 'comment',
					'avatar_size' => '40',
					'format'      => 'html5'
				));
			?>
		</ul>

	    <?php if ($the_paginate = paginate_comments_links(array('echo'=> false))) : ?>
	    	<!-- 评论翻页按钮 -->
			<div class="comments-nav infinite-scroll">
				<div class="comments-pagination">
				<?= $the_paginate;?>
				</div>
				<?= zb_get_infinite_scroll_button();?>
			</div>
		<?php endif;?>


	<?php endif; ?>


</div>
