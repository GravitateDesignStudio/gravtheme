define([], function() {
	
	return function(){
	    var self = this;
        this.images = [];
        this.load = function(src){
            if(src.indexOf('undefined') != -1 || src == undefined ){
                if(console && console.warn)
                    console.warn('404 Prevented from: ', src);
                return;
            } else {
                img = new Image();
                img.src = src;
                self.images.push(img);
            }
        };
	   
    };
	
}); 