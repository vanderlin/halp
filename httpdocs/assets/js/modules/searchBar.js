



(function($) {
    $.widget('lo.searchBar', {

    	// -------------------------------------
    	options: {
        	target:null,
        	list:null,
        	countElem:null,
        	element:null,
        	delay:200,
    	},

    	// -------------------------------------
    	_search: function(term) {

    		var self = this;


    		$.ajax({
    			url: '/api/search/query/'+term+'?format=search',
    			type: 'GET',
    			dataType: 'json',
    		})
    		.done(function(evt) {
    			console.log(evt);
    			if(evt.status == 200) {

    				var results = evt.results;
    				var html = '';
    				if(results) {

    					for(var resultName in results) {
    						
    						var name   = resultName;
    						var result = results[resultName];
    						var title  = result.title;
							if(result.data.length > 0) {
    							html += '<li class="list-group-item section">'+title+'</li>';	
    							for (var i = 0, item; item = result.data[i]; i++) {
    								
    								if(name == 'spots') {
    									html += '<li class="list-group-item">\
	    											<a href="'+item.url+'">\
														<i class="icon fa '+result.icon+'"></i>\
	    												<span>'+item.name+'</span><br>\
	    												<small>'+item.location.short_address+'</small>\
	    											</a>\
	    										 </li>';	
    								}
    								else if(name == 'locations' || name == 'tags') {
    									html += '<li class="list-group-item">\
	    											<a href="'+item.url+'">\
														<i class="icon fa '+result.icon+'"></i>\
	    												<span>'+item.name+'</span><br>\
	    											</a>\
	    										 </li>';	
    								}
    								else if(name == 'locals') {
    									html += '<li class="list-group-item">\
	    											<a href="'+item.url+'">\
														<i class="icon fa '+result.icon+'"></i>\
	    												<span>'+item.name+'</span><br>\
	    												<small>'+(item.office==null?'No Location':item.office.location.short_address)+'</small>\
	    											</a>\
	    										 </li>';	
    								}
    								
    							};	
    						}
    					}
    					if(self.options.target.is(':visible') == false) {
							self.options.target.fadeIn(200);
					    }
    					self.options.list.html(html);


    					// if(results.spots.length>0) {
    					// 	html += '<li class="section">''</li>'
    					// }


    				}
 
    			}

    			
    		})
    		.fail(function(evt) {
    			console.log("error", evt);
    		})
    	
    	},

    	// -------------------------------------
    	_positionMenu: function() {
			this.options.target.css({	width:this.element.outerWidth(),
										top:this.element.position().top+this.element.outerHeight(),
								   		left:this.element.position().left});
    	},

    	// -------------------------------------
    	hideMenu: function() {
    		if(this.options.target.is(':visible')) {
				this.options.target.fadeOut(100);
			}
    	},

    	// -------------------------------------
    	showMenu: function() {
    		this._positionMenu();
			this.options.target.fadeIn(100);
    	},

    	// -------------------------------------
    	hasResults: function() {
    		return this.options.list.children().length > 0;
    	},


    	// -------------------------------------
		_create: function() {
			

			var targetName = this.element.attr('data-target') || '.search-results';
			this.options.target = $(targetName);
			this.options.list   = $(targetName+" ul");
			this.options.target.hide();

			this._positionMenu();
			


			this.options.scrollbar = this.options.target.mCustomScrollbar({
																		    axis:"y", 
																	        theme:"lo"
																		});



			var self = this;
			var timer;
 			var prevVal;
 			var listIsFocused  = false;
 			var count = 0;
 			
 			$('body').on('click', function (e) {
 				if(!$(self.element).is(e.target)) {
 					self.hideMenu();
 				}
 			});

 			$(window).resize(function(event) {
				self.hideMenu();
			});
	

 			this.element.focus(function(event) {
 				
 				if(self.hasResults() && !self.options.target.is(':visible')) {
 					self.showMenu();
 				}
 			});

 			function updateSelected(count, items) {
 				var c = count;
 				listIsFocused = true;
				console.log("count: "+c, items.length, listIsFocused);

				var $item = $(items[count]);
				self.options.list.find('.active').removeClass('active');
				$item.addClass('active');
				
				self.options.scrollbar.mCustomScrollbar("scrollTo", $item);
 			}

			this.element.keyup(function(event) {
				
				var key = event.which;
				// 38 == up
				// 40 == down
				if(key == 38 || key == 40 || key == 13) {
					
					var items = self.options.list.find('li:not(.section)');
					if(items.length>0) {

						if(key == 13) {
							document.location = $(items[count]).find('a').attr('href');
							return; 
						}

						else if(key == 40 && count<items.length-1) {
							if(listIsFocused == true) {
								count ++;
							}
							var item = $(items[count]);
							updateSelected(count, items);
							
						}
						else if(key == 38 && count>0) {
							
							count --;
							updateSelected(count, items);
							
						}

						
						

						


					}


				}
				else {	    		
					var val = $(this).val();				
					clearTimeout(timer);
					timer = setTimeout(function() {

						if(prevVal != val && val.length>0) {
							self._search(val);
							count = 0;
						}
						prevVal = val;

					}, self.options.delay);	
				}
				
			});

		
    	},
    	

    });

}(jQuery));















