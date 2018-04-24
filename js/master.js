/* global jQuery */

import objectFitImages from 'object-fit-images';
import gravUtil from './util/grav-util';
import ThemeWelcome from './components/testing/theme-welcome';
import SiteHeader from './components/site-header';

const instances = {
	components: {
		themeWelcome: null,
		siteHeader: null
	}
};

jQuery(function ($) {
	/**
	 * Place items in here to have them run after the Dom is ready
	 */
	$(document).ready(function () {
		gravUtil.filterLinks();
		gravUtil.setHeightVars();
		gravUtil.setScrollVars();
		gravUtil.updateScrollClasses(100);

		/**
		 * Initialize components
		 */
		// theme welcome -- this can be removed during development
		const $themeWelcomeEls = $('.theme-welcome');

		if ($themeWelcomeEls.length) {
			instances.themeWelcome = new ThemeWelcome($themeWelcomeEls.first());
		}

		// site header
		const $siteHeaderEls = $('.site-header');

		if ($siteHeaderEls.length) {
			instances.components.siteHeader = new SiteHeader($siteHeaderEls.first());
		}

		objectFitImages();
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
