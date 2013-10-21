<?php
/*
 * The template for displaying Comments.
 * 
 */
//Do not delete these lines
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!', WPSP_DOMAIN));

	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', WPSP_DOMAIN) ?></p>
<?php
		return;
	}
?>

<!-- COMMENTS LIST -->
<div id="comments-container">

	<span id="comments"></span>
	
<?php if( have_comments() ): ?>	
		<p id="comments-count-head">
			<?php comments_number(__('0 Comments', WPSP_DOMAIN), __('1 Comment', WPSP_DOMAIN), '% '.__('Comments', WPSP_DOMAIN) );?>
		</p>
		
	<?php if ( !empty($comments_by_type['comment']) ): ?>
		<ol class="commentlist clearfix">
			<?php wp_list_comments('type=comment&callback=neotheme_format_comment'); ?>
		</ol>
	<?php endif; ?>
	
		<div class="navigation">
			<div class="alignleft">
				<?php previous_comments_link() ?>
			</div>
			<div class="alignright">
				<?php next_comments_link() ?>
			</div>
		</div>
		
	<?php if( !empty($comments_by_type['pings']) ): ?>
	<div id="container-pings">
		<p id="pings-title"><?php _e('Trackbacks/Pingbacks', WPSP_DOMAIN) ?></p>
		<ol id="pings-list">
			<?php wp_list_comments('type=pings&callback=neotheme_format_comment'); ?>
		</ol>
	</div>
	<?php endif; ?>	
<?php else: //this is displayed if there are no comments so far ?>
   <p id="comments-count-head">Comments</p>
   <div id="comment-section" class="nocomments">
      <?php if( 'open' == $post->comment_status ): ?>         
      <?php else : // comments are closed ?>
            <div id="respond"> 
            </div>
      <?php endif; ?>
   </div>
<?php endif; ?>

<!-- COMMENT FORM (RESPOND) -->
<?php if( 'open' == $post->comment_status ): ?>
	<div id="respond">
		<p id="respond-head">
			<?php comment_form_title( __('Leave a comment',WPSP_DOMAIN), __('Leave a Reply to %s',WPSP_DOMAIN )); ?>
		</p>
		<div class="cancel-comment-reply"> <small>
			<?php cancel_comment_reply_link(); ?>
			</small> </div> <!-- end cancel-comment-reply div -->
		<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
			<p><?php _e('You must be',WPSP_DOMAIN)?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in',WPSP_DOMAIN) ?></a> <?php _e('to post a comment.',WPSP_DOMAIN) ?></p>
		<?php else : ?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
			<?php if ( $user_ID ) : ?>
				<p class="comment-admin-logged"><?php _e('Logged in as',WPSP_DOMAIN) ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account"><?php _e('Log out &raquo;',WPSP_DOMAIN) ?></a></p>
			<?php else : ?>
				<p>
					<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
					<label for="author"><small><?php _e('Name',WPSP_DOMAIN) ?>
						<?php if ($req) echo "<span class='required'>*</span>"; ?>
						</small></label>
				</p>
				<p>
					<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
					<label for="email"><small><?php _e('Mail (will not be published)',WPSP_DOMAIN) ?>
						<?php if ($req) echo "<span class='required'>*</span>"; ?>
						</small></label>
				</p>
				<p>
					<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
					<label for="url"><small><?php _e('Website',WPSP_DOMAIN) ?></small></label>
				</p>
			<?php endif; ?>
			<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php //echo allowed_tags(); ?></code></small></p>-->
			<p>
				<textarea name="comment" id="comment" cols="64" rows="10" tabindex="4"></textarea>
			</p>
			<p>
				<input class='comment-submit' name="submit" type="submit" id="comment-submit" tabindex="5" value="<?php _e('Post Comment',WPSP_DOMAIN)?>" />
				<?php comment_id_fields(); ?>
			</p>
			<?php do_action('comment_form', $post->ID); ?>
			</form>
		<?php endif; // If registration required and not logged in ?>
	</div> <!-- end respond div -->
<?php endif; ?>
</div><!-- #comment-wrap -->