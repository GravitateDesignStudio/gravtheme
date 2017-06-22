/*
* Custom Theme JS
*/

jQuery(function($){


	/*
	*
	* Custom Functions
	*
	*/










	/*
	* End Functions
	*/



	/*
	*
	*	Place items in here to have them run after the Dom is ready
	*
	*/
	$(document).ready(function(){

		grav.filterLinks();
		grav.setHeightVars();
		grav.setScrollVars();
		grav.addDropDownsToSubMenus();
		grav.addListenerToMobileMenuButton();
		grav.wrapInputItems();

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
