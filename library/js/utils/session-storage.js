define(['jquery'], function($) {

    // Our Store is represented by a single JS object in *sessionStorage*. Create it
    // with a meaningful name, like the name you'd give a table.
    var Store = function(name) {
        this.name = name;
        var store = sessionStorage.getItem(this.name);
        this.data = (store && JSON.parse(store)) || {};
				
		this.refresh = function(){
			store = sessionStorage.getItem(this.name);
        	this.data = (store && JSON.parse(store)) || {};
		};
		
        // Save the current state of the **Store** to *sessionStorage*.
        this.save = function() {
            sessionStorage.setItem(this.name, JSON.stringify(this.data));
        };
		
		// Create new key-value obj within store
        this.create = function(id, obj) {
        	this.refresh();
            
            var new_obj = $.extend(true, {}, obj);

            this.data[id] = new_obj;
            this.save();
            return obj;
        };
        
        // Update a model by replacing its copy in `this.data`.
        this.update = function(id, obj) {
        	this.refresh();
        	
            this.data[id] = obj;
            this.save();
            return obj;
        };

        this.get = function(id) {
        	this.refresh();
        	if(this.data.hasOwnProperty(id))
        		return this.data[id];
        	
        	return null;
        };

        this.find = function(query) {
            this.refresh();
            
            if (query !== undefined) {
                for (var key in this.data) {// for each item stored
                    var pass = false// if pass is false, DO return this item

                    for (var attribute in query) {

                        if (this.data[key].hasOwnProperty(attribute) && this.data[key][attribute] == query[attribute]) {
                            // attribute found and matches

                        } else {
                            pass = true;
                        }

                        if (pass)
                            continue;
                    }
                    if (!pass)
                        return this.data[key];

                };
            }

            return null;
        };
        
        // Return the array of all models currently in storage.
        this.findAll = function() {
            return _.values(this.data);
        };
        
        // Delete a model from `this.data`, returning it.
        this.destroy = function(id) {
            try {
                delete this.data[id];
                this.save();
                return true;
            } catch(e) {
                return false
            }
        };

        this.empty = function() {
            sessionStorage.removeItem(this.name);
            var store = sessionStorage.getItem(this.name);
            this.data = (store && JSON.parse(store)) || {};
        };
    };
    return Store;
}); 