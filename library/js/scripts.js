(function($){

	//console.log('Hey No Love for AMD? /r/n Ok, I\'m sure there\'s probably a good reason you dont need to')
	
	$('a[href$=".pdf"]').attr('target', '_blank');
	hostname = new RegExp("//" + location.host + "($|/)");
	$("a").each(function() {
		var a = $(this).attr("href");
		if(a != undefined){
			if (hostname.test(a) || a.slice(0, 1) == "/") {} else {
				if (a.slice(0, 1) == "#") {} else {
					if ($(this).html() != 'Blog') {
						$(this).attr('target', '_blank');
					}
				}
			}
		}
	});

})(jQuery)
