<?php if(get_sub_field('heading')){ ?>
	<div class="block-inner">
		<div class="row">
			<div class="columns">
				<h2<?php if(get_sub_field('center')){?> class="text-center"<?php } ?>><?php the_sub_field('heading');?></h2>
			</div>
		</div>
	</div>
<?php } ?>