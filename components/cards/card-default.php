<?php
$post_id = isset($post_id) ? $post_id : get_the_ID();
$title = isset($title) ? $title : get_the_title($post_id);
$content = isset($content) ? $content : Grav\WP\Content::get_excerpt();
$permalink = isset($permalink) ? $permalink : get_the_permalink($post_id);

?>
<a class="card" href="<?php echo esc_url($permalink); ?>">
	<img src="https://picsum.photos/640/480/?random" alt="example card image" class="card__image">
	<h3 class="card__title"><?php echo esc_html($title); ?></h3>
	<div class="card__content">
		<?php echo $content; ?>
	</div>
</a>
