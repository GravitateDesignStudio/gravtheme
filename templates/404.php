<?php
get_header();

$title = get_field('theme_options_404_title', 'option');
$content = get_field('theme_options_404_content', 'option');

if (!$title) {
	$title = '404 - Not Found';
}
?>

<section class="banner banner-four-oh-four">
	<div class="row">
		<div class="columns small-12 text-center">
			<h1 class="banner__title">404 - Not Found</h1>
		</div>
	</div>
</section>

<?php if ($content) { ?>
<section class="section-container">
	<div class="section-inner">
		<div class="row">
			<div class="columns small-12 wysiwyg">
				<?php echo $content; ?>
			</div>
		</div>
	</div>
</section>
<?php } ?>

<?php
GRAV_BLOCKS::display(array(
	'section' => '404_blocks_grav_blocks',
	'object' => 'option'
));

get_footer();
