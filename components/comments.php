<?php
// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
{
	die ('Please do not load this page directly. Thanks!');
}

if (post_password_required())
{
	?>
    <div class="help">
		<p class="nocomments">This post is password protected. Enter the password to view comments.</p>
	</div>
	<?php
	
    return;
}    

if (have_comments())
{
	?>
	<h3 class="comments__title"><?php comments_number('<span>No</span> Responses', '<span>One</span> Response', '<span>%</span> Responses' );?> to &#8220;<?php the_title(); ?>&#8221;</h3>
	
	<nav class="comments__nav">
		<ul>
	  		<li><?php previous_comments_link() ?></li>
	  		<li><?php next_comments_link() ?></li>
	 	</ul>
	</nav>
	
	<ol class="comments__list">
		<?php wp_list_comments('type=comment&callback=grav_comments'); ?>
	</ol>
	
	<nav class="comments__nav">
		<ul>
	  		<li><?php previous_comments_link() ?></li>
	  		<li><?php next_comments_link() ?></li>
		</ul>
	</nav>
	<?php
}
else
{
	// this is displayed if there are no comments so far
	
	if (comments_open())
	{
		?>
		<p>No comments yet.</p>
		<?php
	}
	else
	{
		?>
		<p>Comments are closed.</p>
		<?php
	}
}

if (comments_open())
{
	?>
	<section class="comments__respond-form">
		<h3 class="comments__form-title"><?php comment_form_title( 'Leave a Reply', 'Leave a Reply to %s' ); ?></h3>
		
		<div class="comments__cancel-comment-reply">
			<p><?php cancel_comment_reply_link(); ?></p>	
		</div>
		
		<?php
		if (get_option('comment_registration') && !is_user_logged_in())
		{
			?>
			<div class="comments__help">
				<p>You must be <a href="<?php echo wp_login_url( get_permalink() ); ?>">logged in</a> to post a comment.</p>
			</div>
			<?php
		}
		else
		{
			?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
				<?php
				if (is_user_logged_in())
				{
					?>
					<p class="comments-logged-in-as">Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Log out &raquo;</a></p>
					<?php
				}
				else
				{
					?>
					<ul id="comment-form-elements" class="comments__form-elements">
						<li>
							<label for="author">Name <?php if ($req) echo '(required)'; ?></label>
							<input type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" placeholder="Your Name" tabindex="1" <?php if ($req) echo 'aria-required="true"'; ?> />
						</li>						
						<li>
							<label for="email">Mail <?php if ($req) echo '(required)'; ?></label>
							<input type="email" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" placeholder="Your Email" tabindex="2" <?php if ($req) echo 'aria-required="true"'; ?> />
							<small>(will not be published)</small>
						</li>
						<li>
							<label for="url">Website</label>
							<input type="url" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" placeholder="Your Website" tabindex="3" />
						</li>
					</ul>
					<?php
				}
				?>
				<p>
					<textarea name="comment" id="comment" placeholder="Your Comment Here..." tabindex="4"></textarea>
				</p>
				<p>
					<input name="submit" type="submit" id="submit" tabindex="5" value="Submit Comment" />
					<?php comment_id_fields(); ?>
				</p>
				<div class="comments__help">
					<p class="small"><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></p>
				</div>
		
				<?php do_action('comment_form', $post->ID); ?>
			</form>
			<?php
		}
		?>
	</section>
	<?php
}
