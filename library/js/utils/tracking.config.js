//Event Tracking Config Ver 0.5
define({
	debug : true,
	trackThese : [
	/*  Add events here following this example, leave the comma off the last item
		{
			target: 'jQuery style selector'
			query: [google analytics query to send]
		},
	*/
		{
			target : '.Test1',
			query : ['_trackEvent','Form','Completions','0_ContactForm']
		},
		{
			target : '.Test2',
			query : ['_trackEvent','Form','Completions','0_ContactForm']
		},
		{
			target : '.Test3',
			query : ['_trackEvent','Form','Completions','0_ContactForm']
		}
	]
});