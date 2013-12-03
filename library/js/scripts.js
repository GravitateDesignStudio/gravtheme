//[Dependencies], (Exports As...)
define(['jquery','tracking'],function($, tracking){
	return function(){
		var self = this;

		$('a[href$=".pdf"]').attr('target', '_blank');

		console.log(gScriptsConfig)

		this.eventTracking =  new tracking;
		this.eventTracking.runConfig();


	}();
});