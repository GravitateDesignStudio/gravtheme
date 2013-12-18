//[Dependencies], (Exports As...)
define(['jquery','tracking','image-preloader','session-storage'],function($, tracking, imagePreloader, sessionStoreage){
	return function(){
		var self = this;

		$('a[href$=".pdf"]').attr('target', '_blank');


		/* ============= Event Tracking ==================
		 * Source: utils/tracking & utils/tracking.config
		 * 
		 * Step One: Don't delete the two lines below
		 * Step Two: Setup in utils/tracking.config.js
		 * Step Three: Move on
		 * Step Four: ????
		 * Step Five: Profit 
		 */

		this.eventTracking =  new tracking;
		this.eventTracking.runConfig();
		


		/* ============= Image Preloader ==================
		 * Source : utils/image-preloader.js
		 * .properties [type, description]
		 * .methods  (arguments) [returns]
		 *
		 * .load(source, success callback function, fail callback function) [array key || false]
		 * .loadAll(objects, boolean || config {}
		 *
		 * .images [array, img elements of loaded images]
		 */
		
		// ~~~~~~~~ Usage Example A ~~~~~~~~~~~~
		
		//Intialize preloader container by calling a new instance of the module as it was passed in with the define ^^^
		this.preloader  = new imagePreloader;
		
		//Use the load a single method to preload a large image that is not immediately nessecary
		var callBack = function(){ console.log('The Image Finished Loading')/* Maybe do some jQuery here? */},
			failBack = function(){ console.log('The Image Failed To Load') /* Error Handling Here? */};

		this.preloader.load(gData.themeURI + '/images/', callBack, failBack);
		
		
		// ~~~~~~~~ Usage Example B ~~~~~~~~~~~~
		
		/* METHOD STILL IN DEVELOPMENT DO NOT USE -Corban 12/2/2013 
		 * Use the loadAll() method to load many images at a time
		 * Pass a boolean false to load all images sequenially w/o breaks, 
		 * Pass true to break up request into bursts to allow for other http traffic to pass while loading
		 * Optionally pass a config object in place of a boolean to control burst lengths (int) and delays in ms (int) (length: ,delay: )
		 */
		var loadThese = [
			{
				src: gData.themeURI + '/images/example1.png',
				callBack: function(){/* Do Good Stuff */},
				failBack: function(){/* Do Bad Stuff */}
			},
			{
				src: gData.themeURI + '/images/example2.png',
				callBack: function(){/* Do Stuff */},
				failBack: function(){/* Do Stuff */}
			}

		];

		//Load all images immediately
		this.preloader.loadAll(loadThese,false);

		//Load all images with default burst length and delay
		this.preloader.loadAll(loadThese, true);

		//Load all images with custom burst length and delay
		//This config would yield: 10 images requested in one sequence, with a wait 500ms before loading the next 10.
		this.preloader.loadAll(loadThese,{length: 10, delay: 500})




		/* ========== Session Storage Helper ==============
		 * Source : utils/session-storage.js
		 * .properties [type, description]
		 * .methods  (arguments) [returns]
		 * 
		 * .create(id,data) [data]
		 * .update(id,data) [data]
		 *
		 * .find(id) 		[data || null]
		 * .findAll()		[data]
		 *
		 * .destroy(id) 	[true || false]
		 * .empty()			[true || false]
		 */
		
		// ~~~~~~~~ Usage Example ~~~~~~~~~~~~
		//Create a new storage container by calling a new instance of the module as it was passed in with the define ^^^
		this.store = new sessionStoreage('GravData');
		
		//Now that you have established a container, create the data objects
		this.store.create('test',{
			test: 'me',
			name: 'value',
			pairs: 34
		})
		//Logs the retrieved data to console
		console.log(this.store.find('test')));


	}();
});