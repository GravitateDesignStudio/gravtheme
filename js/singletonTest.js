const SingletonTest = (function ($) {
	return class {
		constructor() {
			console.log('SingletonTest constructor');
			this.random = Math.random();
		}
	};
})(jQuery);

export default new SingletonTest();
