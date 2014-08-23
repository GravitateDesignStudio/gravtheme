(function($){



	/* Make all External Links and PDF's open in a new Tab */
    var host = new RegExp('/' + window.location.host + '/');
    $('a').each(function() {
	    if ((!host.test(this.href) && this.href.slice(0, 1) != "/" && this.href.slice(0, 1) != "#" && this.href.slice(0, 1) != "?") || this.href.indexOf('.pdf') > 0) {
		    $(this).attr({'target': '_blank'});
	    }
	});






})(jQuery)