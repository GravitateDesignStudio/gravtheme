define(['jquery','cycle'],function($){
	return function(){
		//Catches silly console undefined errors
		if(typeof console != 'function'){var f = function(){};console = {};console.log = f;console.warn = f;console.debug = f;console.error = f;}
		var self = this;
		
		$('a[href$=".pdf"]').attr('target', '_blank');

		//Do your elite hacking stuff here...











	}();
});