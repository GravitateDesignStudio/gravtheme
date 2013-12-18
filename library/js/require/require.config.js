"use strict";
require.config({
	// [RequireJS](http://requirejs.org/) 2.0+ plus has error callbacks (errbacks)
	// which provide per-require error handling. To utilize this feature
	// enforceDefine must be enabled and non-AMD dependencies must be shimmed.
	enforceDefine: true,

	baseUrl: gData.themeURI + '/library/js/',
	//Causes the scripts.js to be automatically loaded on intialzation
	deps: ['scripts'],
	//Shims are for including libraries that do not natively support require defines (which is most)
	shim: {
		
		'jquery': {
			exports: '$'
		},
		'jquery_ui': {
			deps: ['jquery'],
			exports: '$.ui'
    	},
       	'cycle':{
    	    deps: ["jquery"],
    	    exports: "$.fn.cycle"
    	},
    	'carousel':{
    	    deps: ['jquery','cycle']
    	}
		
	},
	// Paths contains the module name (the one you will use to declare a dependency) and the path to that file, ommitting the .js
	paths: {
		'jquery'			: 'inc/jquery',
		'cycle'				: 'inc/jquery.cycle',
		'carousel'			: 'inc/jquery.cycle.carousel',
		'session-storage'	: 'utils/session-storage',
		'image-preloader'	: 'utils/image-preloader',
		'tracking' 			: 'utils/tracking',
		'tracking-config'	: 'utils/tracking.config'
	}
	
});

// Define call just to make enforceDefine check happy
define(function() {
}); 