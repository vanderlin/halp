
(function($) {

    $.widget('lo.spotItinerary', {
    	
    	// -------------------------------------
    	_post:function(options) {
    		
    		var data = options.params || {};
    			data._method = options.method;
    		
			$.ajax({
				url: options.url,
				type: 'POST',
				dataType: 'json',
				data:data,
			})
			.done(function(evt) {
				if(options.done) options.done(evt);
				if (evt.status == 200) {
					
				};
				console.log("success", evt);
			})
			.fail(function(evt) {
				console.log("error", evt);
			})		
    	},

    	// -------------------------------------
    	options: {
    		widget:null,
    		locationID: null,
    		spotid:null,
        	target:null,
        	opening:false,
        	isOpen:false,
        	template:function(options) {
        			var html = '<div class="popover itinerary-popover itinerary-popover-'+options.id+'" role="tooltip"> \
				        			<div class="arrow"></div>\
				        				<div class="itineraries-container" id="itineraries-collapse-'+options.id+'">';
											
											html += '<ul class="list-unstyled itineraries-list">';

												html += '<li class="add-new">';
											
													html += '<img src="/assets/content/common/itinerary/pencil-icon.svg">';
													html += '<input type="text" class="new-itinerary-input" placeholder="NEW ITINERARY">';
											
												html += '</li>';

												console.log(options.itineraries, options.itineraries.length);
					        				for (var i = 0; i < options.itineraries.length; i++) {
					        					var itinerary = options.itineraries[i];
					        					console.log(itinerary);
					        					var isInItinerary = this.widget._inItinerary(itinerary);	
					        					console.log(itinerary);
					        					if(itinerary.title == 'Favorites') {
					        						html += '<li class="favorites '+(isInItinerary?'active':'')+'" data-id="'+itinerary.id+'">';
					        							html += '<a href="#add-to-itinerary" class="add-to-itinerary">';
						        							html += '<img src="/assets/content/common/itinerary/heart-icon.svg">';
						        							html += itinerary.title;
						        						html += '</a>';
					        						html += '</li>';
					        					}
					        					else {
					        						html += '<li class="itinerary '+(itinerary.isMine == false?'shared':'')+' '+(isInItinerary?'active':'')+'" data-id="'+itinerary.id+'">';
					        						html += '<a href="#add-to-itinerary" class="add-to-itinerary">';
					        							if(itinerary.isMine == false) {
						        							html += '<img src="/assets/content/common/itinerary/shared.svg">';
					        							}
					        							html += itinerary.title;
					        						html += '</a>';
					        						html += '</li>';
					        					}
					        					
					        					
					        				};
					        				html += '</ul>';
									html +=	'</div>\
			        			</div>';
				return html;
        	},
    	},
    	
    	// -------------------------------------
    	_inItinerary: function(itinerary) {
    		for (var i = 0; i < itinerary.spot_ids.length; i++) {
    			var spotid = itinerary.spot_ids[i];
    			if(this.options.spotid == spotid) return true;
    		};
    		return false;
    	},

    	// -------------------------------------
    	_destroy: function() {
    		this.options.target.attr('data-toggle', '');
			console.log("*** itinerary widget killed: "+this.uuid+" ***");
    	},

    	// -------------------------------------
    	_popover: function() {
    		
    		var self = this;
    		this.options.target.popover({
    			title:self.uuid,
				container:'body',
				content:'itinerary',
				placement:'top',
				trigger:'manual',
				template:this.options.template({id:this.uuid, itineraries:User.itineraries})
			}).popover('show');

    		this.options.target.on('hide.bs.popover', function (e) {
				self.options.isOpen = false;
				self.destroy();
			});

    		this.options.target.on('shown.bs.popover', function () {
    			
    			self.options.opening = false;
    			self.options.isOpen  = true;

    			var id = $(this).attr('aria-describedby');
				// first close all other popovers 
				$(".itinerary-popover").each(function(index, elem) {
					if(id != $(elem).attr('id')) {
						// $(elem).popover('destroy');
					}
				});

				var $input  	 = $('#'+id).find('.new-itinerary-input');
				var $addNewIcon  = $('#'+id).find('.add-new img');
				var $list 		 = $('#'+id).find('.itineraries-list');
				var $favsItem    = $list.find('.favorites');
				var $itineraries = $('#'+id).find('.itineraries-container');

				// -------------------------------------
				$input.keyup(function(event) {
					event.preventDefault();
	 				if(event.which == 13) {
	 					var title = $(this).val();
	 					$(this).val('')
	 					console.log('Create new itinerary: '+title);
	 					
	 					var url = '/itineraries';
	 					var params = {title:title, locations:[self.options.locationID]};
	 					self._post({url:url, 
	 								method:'POST', 
	 								params:params, 
	 								done:function(evt) {
	 									console.log($list);
					 					$('<li class="itinerary active new"><a href="#add-to-itinerary">'+title+'</a></li>').insertAfter( $favsItem );
					 					var $newItem = $list.find('.itinerary.new').slideUp(0);

					 					$newItem.delay(100).slideDown(300, function() {
				 							$(this).removeClass('new');
					 					});

					 					// update the itinaries
					 					User.itineraries = evt.itineraries;

	 								}
	 							});	
	 				}	
				});

				// -------------------------------------
				$(".add-to-itinerary").click(function(event) {
					event.preventDefault();
					
					var spotid = self.options.spotid;
					var $item = $(this).parent('li');
					var id = $item.attr('data-id');
					var active = $item.hasClass('active');

					if(id && User.itineraries.length>0) {
						var itinerary = User.itineraries.filter(function(obj) {
							return obj.id == id;
						});
						if(itinerary) {
							var url = '/itineraries/'+id+'/locations';
							var method = active ? 'DELETE' : 'PUT';
							self._post({
								url:url, 
								method:method, 
								params:{spot_id:spotid},
								done: function(evt) {
									if(active) {
										$item.removeClass('active');
									}
									else {
										$item.addClass('active');
									}
									User.itineraries = evt.itineraries;
									console.log(evt);
								}
							});
						}
					}
					else {
						console.log("itineraries not loaded");
					}

				});

				// -------------------------------------
				$itineraries.mCustomScrollbar({
				    axis:"y", 
			        theme:"lo"
				});


			});

    	},

    	_loadItineraries: function() {

			// do we have the itineraries for the auth user
			var self = this;
			
			console.log("You need to loaded the itineraries");
			
			self.options.opening = true;
			User.itineraries = {};
			$.ajax({
				url: '/users/'+User.id+'/itineraries',
				type: 'GET',
				dataType: 'json',
			})
			.done(function(evt) {
				User.itineraries = evt;

				self._popover();
			})
    	},

    	// -------------------------------------
    	toggle: function() {
    		if(this.options.isOpen) {
    			this.close();
    		}
    		else {
    			this.open();
    		}
    	},

    	// -------------------------------------
    	open: function(options) {
    		if(this.options.opening == false) {
	    		if(User.itineraries == null) {
	    			this._loadItineraries();
	    		}
	    		else {
	    			console.log('open');	
	    			this._popover();
	    		}
    		}
    		this.element.closest('.spot-action-container').addClass('active');
    	},

    	// -------------------------------------
    	close: function() {
    		console.log('close');
			this.options.target.popover('destroy');
			this.element.closest('.spot-action-container').removeClass('active');
    	},

    	// -------------------------------------
		_create: function() {
			var id 	 	= this.element.attr('data-id');
			var self 	= this;
			
			// we need a id and a User to move forward
			if(id && User.id!=undefined) {

				this.options.widget = this;
				this.options.element = this.element;
				this.options.locationID = id;
				this.options.spotid = this.element.data('spot-id');

				// get the target 
				var targetID = this.element.attr('data-target');
				var target = this.element.find(targetID);
				if(target.length == 0) target = this.element;
				this.options.target = $(target);
				
				//console.log(User);			
				target.attr("data-toggle", "popover");
				//$("[data-toggle=popover]").popover();	

				// -------------------------------------
				/*this.options.target.click(function(event) {
					event.preventDefault();
						
					if(self.options.opening == false) {
					
						self.options.opening = true;

						// do we have the itineraries for the auth user
						if(User.itineraries == null) {
							console.log("You need to loaded the itineraries");
							User.itineraries = {};
							$.ajax({
								url: '/users/'+User.id+'/itineraries',
								type: 'GET',
								dataType: 'json',
							})
							.done(function(evt) {
								console.log("success", evt);
								User.itineraries = evt;
								self.popover();
							})
						}
						else {
							self.popover();
						}

					}
					
					

				});*/

			}
			else {
				console.log("Missing data-id", this.element);
			}
    	},
    	

    });
	

}(jQuery));




