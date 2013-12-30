define(['jquery'], function($) {
	return function(){
	    var self = this;
        this.images = [];
        this.load = function(src, callBack, failBack){
            
            if(src.indexOf('undefined') != -1 || src == undefined ){
                if(console && console.warn)
                    console.warn('404 Prevented from: ', src);
                return false;
            } 
            
            img = new Image();
            
            if(typeof callBack == 'function')
                $(img).on('load',callBack);

            if(typeof failBack == 'function')
                $(img).on('error',failBack);

            img.src = src;

            return self.images.push(img);
        };
        
        this.loadAll = function(images, bursts){
            return false;
            /*
            if(bursts){
                if(bursts === true)
                    bursts = {length: 5, delay: 250};
                var i = 0,
                    count = 1;
                    do = function(){
                        x = (bursts.length*count > bursts.length)? bursts.length*count : bursts.length;
                        for (var i = 0; i =< images.length - 1; i++) {
                            
                        };
                    };


            } else {
                images.forEach(function(img){
                    if(img.src)
                        self.load(img.src,img.callBack,img.failBack);
                });
            }
            */
        };
        
    };
});