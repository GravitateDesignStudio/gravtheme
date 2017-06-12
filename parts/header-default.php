<header class="global-header">

    <div class="row">
        <div class="columns small-4">
            <div class="logo-container">
            	<a href="<?php echo site_url();?>" title="<?php echo bloginfo('name');?>">
            		<div class="logo">
                        <?php if($logo = get_field('theme_options_logo', 'option')){?>
                            <img src="<?php echo $logo['sizes']['large'];?>" alt="<?php echo $logo['alt'];?>">
                        <?php } ?>
                    </div>
            	</a>
            </div>

        	<button class="button-mobile-menu">
        		<span></span>
        	</button>
        </div>

        <div class="columns small-8 text-right">

            <div class="global-header-search-form">
                <?php get_search_form(); ?>
                <span class="search-icon" onclick="jQuery('.s').focus();"></span>
            </div>

            <nav class="global-header-main-links">
                <?php GRAV_FUNC::menu('main-links'); ?>
            </nav>

            <nav class="global-header-main-menu">
                <?php GRAV_FUNC::menu('main-menu'); ?>
            </nav>
        </div>
    </div>

</header>
