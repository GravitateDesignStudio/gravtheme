(function($){

	
	
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
