const SiteHeader = (function ($) {
	return class {
		constructor($el) {
			this.$el = $el;
			this.headerHeight = 0;

			this.updateHeaderHeightVar();
			this.setupEventHandlers();
		}

		setupEventHandlers() {
			// mobile menu button handler
			this.$el.find('.site-header__mobile-menu-button').on('click tap', function () {
				$('html').toggleClass('mobile-menu-active');
			});

			$(window).resize(() => {
				// update header height on resize
				this.updateHeaderHeightVar();
			});

			$(window).scroll(() => {
				// update header height on scroll
				this.updateHeaderHeightVar();
			});
		}

		updateHeaderHeightVar() {
			this.headerHeight = this.$el.outerHeight();
		}
	};
})(jQuery);

export default SiteHeader;
