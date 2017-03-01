/*
* Custom Theme JS
*/

jQuery(function($){


	/*
	*
	* Custom Functions
	*
	*/

	function filter_links()
	{
		/* Make all External Links and PDF's open in a new Tab */
	    var host = new RegExp('/' + window.location.host + '/');
	    $('a').each(function() {
		    if ((!host.test(this.href) && this.href.slice(0, 1) != "/" && this.href.slice(0, 1) != "#" && this.href.slice(0, 1) != "?") || this.href.indexOf('.pdf') > 0) {
			    $(this).attr({'target': '_blank'});
		    }
		});
	}

	/*
	* End Functions
	*/



	/*
	*
	*	Place items in here to have them run after the Dom is ready
	*
	*/
	$(document).ready(function(){

		filter_links();

	});

	/*
	*
	*	Place items in here to have them run the page is loaded
	*
	*/
	$(window).load(function() {

	});

	/*
	*
	*	Place items in here to have them run when the window is scrolled
	*
	*/
	$(window).scroll(function() {

	});

	/*
	*
	*	Place items in here to have them run when the window is resized
	*
	*/
	$(window).resize(function() {

	});



});
