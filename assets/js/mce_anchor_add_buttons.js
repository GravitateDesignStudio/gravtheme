(function() {
    tinymce.PluginManager.add('grav_add_anchor_btn', function( editor, url ) {

        editor.addButton( 'grav_add_anchor_btn', {
            text: 'Btn',
            icon: false,
            classes: 'anchor-add-btn widget btn disabled',
            onclick: function() {
                selNode = editor.selection.getNode();

                var selector;

                if(!jQuery('.mce-anchor-add-btn').hasClass('mce-disabled'))
                {

                	if(selNode.nodeName.toLowerCase() == 'a')
	                {
	                	selector = jQuery(selNode);
	                	selector.toggleClass('button');
	                }
	                else
	                {
	                	selector = jQuery(selNode).find('a');
	                	selector.toggleClass('button');
	                }

	                if(selector.hasClass('button'))
	                {
	                	jQuery('.mce-anchor-add-btn').addClass('mce-active');
	                }
	                else
	                {
	                	jQuery('.mce-anchor-add-btn').removeClass('mce-active');
	                }

				}
            }
        });

		editor.onNodeChange.add(function(ed, l) {

			selNode = ed.selection.getNode();

			var selector;

			if(selNode.nodeName.toLowerCase() == 'a')
			{
				selector = jQuery(selNode);
			}
			else
			{
				selector = jQuery(selNode).find('a');
			}

			if(selector.length)
            {
            	jQuery('.mce-anchor-add-btn').removeClass('mce-disabled');

            	if(selector.hasClass('button'))
            	{
            		jQuery('.mce-anchor-add-btn').addClass('mce-active');
	            }
	            else
	            {
	            	jQuery('.mce-anchor-add-btn').removeClass('mce-active');
	            }
            }
            else
            {
            	jQuery('.mce-anchor-add-btn').removeClass('mce-active');
            	jQuery('.mce-anchor-add-btn').addClass('mce-disabled');
            }
		});


    });
})();