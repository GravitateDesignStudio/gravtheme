 define(['tracking-config'],function(config){
	 return function(){

		var self = this;
		//this.config = new config;
		console.log(config)
		this.debugMessage = function(msg){
			if(config.debug == true && console && console.log)
				console.log('[Grav Tracking]  ' + msg);
		};

		this.setupAnalytics = function(trackThis){
	
			self.debugMessage('Attempting setup of: ' + trackThis.target );

			var	element = document.querySelector(trackThis.target);
			
			if(element){

				var trackEvent = function(){
					
					self.debugMessage('Target Found');

					if(!trackThis.hasFired){
					
						self.debugMessage('Target Clicked Sent' + trackThis.query );
					
						_gaq.push(trackThis.query);
						trackThis.hasFired = true;

					} else {
						self.debugMessage('Target Event Already Fired');
					};

				};
			
				element.addEventListener('click', trackEvent);
				element.addEventListener('touchstart',trackEvent)

			} else if(trackThis.asyncElement){
			
					if(trackThis.tries < 3){
					
					self.debugMessage('Target Not Found, Trying Again in 2 seconds\n\r');

					setTimeout(
						function(){
							setupAnalytics(trackThis);
					},2000);
					
					}
					if(trackThis.tries >= 3){
						self.debugMessage('Target Was Not Found After 3 Tries\n\r');
					}

					++trackThis.tries;

			} else {
				self.debugMessage('Target Was Not Found. Set \"Async Element\" to true to enable multiple tries');
			};
				

				
		};
		
		this.runConfig = function(){
			config.trackThese.forEach(function(trackThis){
				
				if(trackThis.asyncElement)
					trackThis.tries = 0;
				
				trackThis.hasFired = false;

				self.setupAnalytics(trackThis);
			
			});
		};
	};
});