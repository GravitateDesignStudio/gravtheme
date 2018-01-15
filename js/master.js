/* global jQuery */

import objectFitImages from 'object-fit-images';
import Swiper from 'swiper';
import gravUtil from './util/grav-util';
import siteHeader from './components/site-header';

jQuery(function ($) {
	/**
	 * Place items in here to have them run after the Dom is ready
	 */
	$(document).ready(function () {
		gravUtil.filterLinks();
		gravUtil.setHeightVars();
		gravUtil.setScrollVars();
		gravUtil.updateScrollClasses(100);

		siteHeader.init();

		const swiperTest = new Swiper('.swiper-container');

		objectFitImages();

		// $.colorbox({ html: '<h1>Welcome</h1>' });
	});

	/**
	 * Place items in here to have them run the page is loaded
	 */
	$(window).load(function () {
	});

	/**
	 * Place items in here to have them run when the window is scrolled
	 */
	$(window).scroll(function () {
		gravUtil.setScrollVars();
		gravUtil.updateScrollClasses(100);
	});

	/**
	 * Place items in here to have them run when the window is resized
	 */
	$(window).resize(function () {
	});
});
