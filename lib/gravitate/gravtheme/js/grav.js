/*
*  Gravitate Common Object
*/

var grav = {};

jQuery(function($)
{

    /////////////////////////////
    //  Variables
    /////////////////////////////

    grav.scrollPos = 0;
    grav.documentHeight = 0;
    grav.windowHeight = 0;
    grav.scrollPos = 0;

    grav.lastScrollPos = $(window).scrollTop();




    /////////////////////////////
    //  Functions
    /////////////////////////////


    /*
    *  Store the Scroll Position as a Variable
    *  for other functions
    */

    grav.setScrollVars = function()
    {
        grav.scrollPos = $(window).scrollTop();
    }



    /*
    *  Scrolling function to animate to a
    *  selector, with optional offset
    */

	grav.scrollTo = function(selector, offset)
    {
		var element;

		if(typeof selector == 'string')
        {
			element = $(selector);
		}
        else
        {
			element = selector;
		}

        if(typeof offset === 'undefined')
        {
			offset = 0;
		}

		$('html, body').animate({
			scrollTop: (element.offset().top - offset)
		}, 500);
	}



    /*
    *  Store the Scroll Position as a Variable
    *  for other functions
    */

    grav.setHeightVars = function()
    {
        grav.documentHeight = $(document).height();
        grav.windowHeight = $(window).height();
    }



    /*
    *  Add Taget="_blank" to links that are external
    *  Also add class "external-link"
    */

    grav.filterLinks = function()
	{
		/* Make all External Links and PDF's open in a new Tab */
	    var host = new RegExp('/' + window.location.host + '/');

	    $('a').each(function()
        {
		    if ((!host.test(this.href) && this.href.slice(0, 1) != "/" && this.href.slice(0, 1) != "#" && this.href.slice(0, 1) != "?") || this.href.indexOf('.pdf') > 0)
            {
			    $(this).attr({ 'target': '_blank', 'rel': 'noopener' });
			    $(this).addClass('external-link');
		    }
		});
	}



    /*
    *  Add Class to HTML Tag to specify the Scroll direction
    */

    grav.updateScrollClasses = function(threshold)
    {
        if(typeof(threshold) === 'undefined')
        {
            threshold = 0;
        }

        // Update Document Height
        grav.documentHeight = $(document).height();

        if(!grav.scrollPos)
        {
            $('html').addClass('scroll-top');
        }
        else
        {
            $('html').removeClass('scroll-top');
        }

        // Prevent Scroll Past Bottom issues with browsers
        if(grav.scrollPos < (grav.documentHeight - grav.windowHeight))
        {
            if(grav.scrollPos > threshold)
            {
                if (!$('html').hasClass('scroll-down') && grav.scrollPos >= grav.lastScrollPos)
                {
                    $('html').removeClass('scroll-up').addClass('scroll-down');
                }
                else if(!$('html').hasClass('scroll-up') && grav.scrollPos < grav.lastScrollPos)
                {
                    $('html').removeClass('scroll-down').addClass('scroll-up');
                }

                if(!$('html').hasClass('scrolled'))
                {
                    $('html').addClass('scrolled');
                }
            }
            else if ($('html').hasClass('scroll-up') || $('html').hasClass('scroll-down') || $('html').hasClass('scroll-down'))
            {
                $('html').removeClass('scroll-up scroll-down scrolled');
            }

            grav.lastScrollPos = grav.scrollPos;
        }
    }


    grav.addDropDownsToSubMenus = function()
    {
        $('li.menu-item-has-children > a').after('<span class="nav-dropdown"></span>');
        $('.nav-dropdown').on('click', function(){
            $(this).parent().toggleClass('open');
        });
    }


    grav.addListenerToMobileMenuButton = function()
    {
        $('.button-mobile-menu').on('click', function(){
            $('html').toggleClass('mobile-menu-open');
        });

        $('.global-content').on('click', function()
        {
            if($('html').hasClass('mobile-menu-open'))
            {
                $('html').removeClass('mobile-menu-open');
            }
        });
    }

    grav.updateInputItems = function()
    {
        $('.input-wrapper-checkbox input, .input-wrapper-radio input').each(function(){
            if($(this).is(':checked'))
            {
                $(this).closest('.input-wrapper').addClass('checked');
            }
            else
            {
                $(this).closest('.input-wrapper').removeClass('checked');
            }
        });
    }

    grav.wrapInputItems = function()
    {
        $('input[type=checkbox]:not(.no-wrap)').wrap( "<span class='input-wrapper input-wrapper-checkbox'></span>" );
        $('input[type=radio]:not(.no-wrap)').wrap( "<span class='input-wrapper input-wrapper-radio'></span>" );
        $('input[type=submit]:not(.no-wrap)').wrap( "<span class='input-wrapper input-wrapper-submit'></span>" );
        $('select:not(.no-wrap)').wrap( "<span class='input-wrapper input-wrapper-select'></span>" );

        $('.input-wrapper-checkbox input, .input-wrapper-radio input').on('change', function(){
            grav.updateInputItems();
        });

        grav.updateInputItems();
    }
});
