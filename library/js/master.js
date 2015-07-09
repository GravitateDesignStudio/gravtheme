(function($){



	/* Make all External Links and PDF's open in a new Tab */
    var host = new RegExp('/' + window.location.host + '/');
    $('a').each(function() {
	    if ((!host.test(this.href) && this.href.slice(0, 1) != "/" && this.href.slice(0, 1) != "#" && this.href.slice(0, 1) != "?") || this.href.indexOf('.pdf') > 0) {
		    $(this).attr({'target': '_blank'});
	    }
	});


// equal column heights
	function elEQ(e, f) {
	    var estEl = 0, allEl = 0, initialEl = 0, el, curEl, elArray = [];
	    $(e).each(function() {
	    	var section = $(this).find(f);
	    	section.each(function() {
		        el = $(this);
		        $(el).height('auto');
		        initialEl = el.position().top;
		        if (allEl != initialEl) {
		            for (curEl = 0; curEl < elArray.length; curEl++) elArray[curEl].outerHeight(estEl);
			            elArray.length = 0;
			            allEl = initialEl;
			            estEl = el.innerHeight();
			            elArray.push(el);
		        } else {
		            elArray.push(el);
		            estEl = estEl < el.innerHeight() ? el.innerHeight() : estEl;
		        }
		        for (curEl = 0; curEl < elArray.length; curEl++) elArray[curEl].innerHeight(estEl);
		    });
	    });
	}
	function eqHeights() {
		// edit this area only
		// call the elEQ function here for each leveling element, using parent element and child (leveling) element. 
		// e.g.: elEQ('.block-grid', 'li');
	}
	$(window).load(function() {
		eqHeights();
	});
	$(window).resize(function() {
		eqHeights();
	});



})(jQuery)
