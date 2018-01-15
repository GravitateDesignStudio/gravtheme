const GravUtil = (function ($) {
	return class {
		constructor() {
			this.scrollPos = 0;
			this.documentHeight = 0;
			this.windowHeight = 0;
			this.lastScrollPos = $(window).scrollTop();
		}

		/*
		*  Store the Scroll Position as a Variable
		*  for other functions
		*/
		setScrollVars() {
			this.scrollPos = $(window).scrollTop();
		}

		/*
		*  Scrolling function to animate to a
		*  selector, with optional offset
		*/
		scrollTo(selector, offset) {
			let element;

			if (typeof selector === 'string') {
				element = $(selector);
			} else {
				element = selector;
			}

			if (typeof offset === 'undefined') {
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
		setHeightVars() {
			this.documentHeight = $(document).height();
			this.windowHeight = $(window).height();
		}

		/*
		*  Add Taget="_blank" to links that are external
		*  Also add class "external-link"
		*/
		filterLinks() {
			/* Make all External Links and PDF's open in a new Tab */
			const host = new RegExp('/' + window.location.host + '/');

			$('a').each(function () {
				if ((!host.test(this.href) && this.href.slice(0, 1) !== '/' && this.href.slice(0, 1) !== '#' && this.href.slice(0, 1) !== '?') || this.href.indexOf('.pdf') > 0) {
					$(this).attr({ target: '_blank', rel: 'noopener' });
					$(this).addClass('external-link');
				}
			});
		}

		/*
		*  Add Class to HTML Tag to specify the Scroll direction
		*/
		updateScrollClasses(threshold) {
			if (typeof threshold === 'undefined') {
				threshold = 0;
			}

			// Update Document Height
			this.documentHeight = $(document).height();

			if (!this.scrollPos) {
				$('html').addClass('scroll-top');
			} else {
				$('html').removeClass('scroll-top');
			}

			// Prevent Scroll Past Bottom issues with browsers
			if (this.scrollPos < (this.documentHeight - this.windowHeight)) {
				if (this.scrollPos > threshold) {
					if (!$('html').hasClass('scroll-down') && this.scrollPos >= this.lastScrollPos) {
						$('html').removeClass('scroll-up').addClass('scroll-down');
					} else if (!$('html').hasClass('scroll-up') && this.scrollPos < this.lastScrollPos) {
						$('html').removeClass('scroll-down').addClass('scroll-up');
					}

					if (!$('html').hasClass('scrolled')) {
						$('html').addClass('scrolled');
					}
				} else if ($('html').hasClass('scroll-up') || $('html').hasClass('scroll-down') || $('html').hasClass('scroll-down')) {
					$('html').removeClass('scroll-up scroll-down scrolled');
				}

				this.lastScrollPos = this.scrollPos;
			}
		}
	};
})(jQuery);

// export const gravUtil = new GravUtil();
export default new GravUtil();
