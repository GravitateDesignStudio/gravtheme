const SiteHeader = (function ($) {
	return class {
		constructor() {
			this.initialized = false;
			this.headerHeight = 0;
		}

		init() {
			if (this.initialized) {
				return;
			}

			// mobile menu button handler
			$('.site-header__mobile-menu-button').on('click tap', function () {
				$('html').toggleClass('mobile-menu-active');
			});

			// retrieve inital header height value
			this.updateHeaderHeightVar();

			$(window).resize(() => {
				// update header height on resize
				this.updateHeaderHeightVar();
			});

			$(window).scroll(() => {
				// update header height on scroll
				this.updateHeaderHeightVar();
			});

			this.initialized = true;
		}

		updateHeaderHeightVar() {
			this.headerHeight = $('.site-header').outerHeight();
		}
	};
})(jQuery);

export default new SiteHeader();
