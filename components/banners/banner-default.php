<?php
$post_id = isset($post_id) ? $post_id : get_the_ID();
$title = isset($title) ? $title : get_the_title($post_id);

?>
<div class="banner">
	<h1 class="banner__title"><?php echo esc_html($title); ?></h1>
</div>
