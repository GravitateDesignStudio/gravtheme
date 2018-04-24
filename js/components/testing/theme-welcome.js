import Swiper from 'swiper';
require('jquery-colorbox');

const ThemeWelcome = (function ($) {
	return class {
		constructor($el) {
			this.$el = $el;
			this.swiperInstances = [];

			this.initializeSwiper();

			this.$el.find('.colorbox-trigger').on('click tap', function (e) {
				e.preventDefault();
				$.colorbox({ html: $(this).attr('data-modal-content') });
			});
		}

		initializeSwiper() {
			this.$el.find('.swiper-container').each((index, el) => {
				this.swiperInstances.push(new Swiper(el));
			});
		}
	};
})(jQuery);

export default ThemeWelcome;
