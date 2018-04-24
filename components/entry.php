<?php
$post_id = isset($post_id) ? $post_id : get_the_ID();
$permalink = isset($permalink) ? $permalink : get_the_permalink($post_id);
$title = isset($title) ? $title : get_the_title($post_id);
$title_attr = isset($title_attr) ? $title_attr : the_title_attribute(array('post' => $post_id, 'echo' => false));
$time = isset($time) ? strtotime($time) : get_the_time('U', $post_id);
$tags = (isset($tags) && is_array($tags)) ? $tags : get_the_tags($post_id);

$tag_links = $tags ? array_map(function($tag_term) {
    return '<a href="'.get_term_link($tag_term).'">'.esc_html($tag_term->name).'</a>';
}, $tags) : array();

?>
<div class="post">
    <h2 class="post__title">
        <a href="<?php echo esc_url($permalink); ?>" rel="bookmark" title="<?php echo esc_attr($title_attr); ?>">
            <?php echo esc_html($title); ?>
        </a>
    </h2>
    <p class="post__meta">
        <span class="post__meta-date">Posted On: <time datetime="<?php echo esc_attr(date('Y-m-d', $time)); ?>"><?php echo esc_html(date('F jS, Y', $time)); ?></time></span>
        <span class="post__meta-category">Filed Under: <?php the_category(', ', '', $post_id); ?></span>
        <?php if (comments_open($post_id)) { ?><span class="post__meta-comments"><?php comments_popup_link(); ?></span><?php } ?>
    </p>
    <div class="post__content">
        <?php
        echo Grav\WP\Content::get_excerpt(array(
            'post_id' => $post_id, 
            'more' => '<a class="post__read-more" href="'.esc_url($permalink).'">Read More</a>'
        ));
        ?>
    </div>
    <p class="post__tags">
        <span class="post__tags-title">Tags:</span>
        <?php echo implode(', ', $tag_links); ?>
    </p>
</div>
