

(function($) {


    $.widget('lo.masonryMore', {
    	options: {
    		target:null,
        	element:null,
        	url:null,
        	
        	itemsContainer:'.masonry-container',
        	moreMessage:'More',
        	loadingMessage:'<div class="spinner">\
				  				<div class="bounce1"></div>\
				  				<div class="bounce2"></div>\
				  				<div class="bounce3"></div>\
							</div>',
        	endMessage:'No more',

        	lastPage:null,
        	perPage:null,
        	currentPage:null,
        	next:null,
        	total:null,
        	infiniteScroll:false,
		},

		// -------------------------------------
		_alreadyloading:false,

		// -------------------------------------
		_disableMoreButton: function() {
			this.element.html(this.options.endMessage);
			this.element.addClass('disabled');
		},

		// -------------------------------------
		_enableMoreButton: function() {
			this.element.html(this.options.moreMessage);
			this.element.removeClass('disabled');
		},

		// -------------------------------------
		_setupInfinateScroll: function() {
			var self = this;
			var firstScroll = false;
			$(window).scroll(function() {
				firstScroll = true;
				if ($('body').height() <= ($(window).height() + $(window).scrollTop()) && firstScroll == true) {
			        if (self._alreadyloading == false) {
			        	self._alreadyloading = true;
			        	console.log("Inifate Scroll");
			        	self._loadNextPage();
			        }
			    }
			});
		},

		// -------------------------------------
		_post: function(options) {
			
			var self = this;

			$.ajax({
					url: this.options.url,
					type: 'POST',
					dataType: 'json',
					data:{page:options.page, per_page:this.options.perPage}
				})
				.fail(function(evt) {
					console.log("Error", evt);
				})
				.done(function(evt) {
					if(evt.params) {
						if((evt.params.infs === true || evt.params.infs === "true") && self.options.infiniteScroll == false) {
							console.log("Turn on infiniteScroll");
							self.options.infiniteScroll = true;
							self._setupInfinateScroll();
						}
					}

					$moreinfo = self.options.target.find('.more-info');
					
					// if we cant find it just look for the first one
					if($moreinfo.length == 0) {
						$moreinfo = $('.more-info').first();
					}

					self.options.currentPage = evt.current_page;
					self.options.lastPage    = evt.last_page;

					if($moreinfo.length>0) {

						var nextPage = self.options.currentPage + 1;
						if(nextPage <= self.options.lastPage) {
							self.options.element.attr('href', '?page='+nextPage);
							History.pushState(null, null, "?page="+nextPage); // logs {}, '', "?state=4"
						}
						$moreinfo.html(evt.from+"-"+evt.to+' of '+evt.total);
						
					}

					if(evt.current_page==evt.last_page) {
						self._disableMoreButton();
					}
					else {
						self._enableMoreButton();
					}


					if(evt.data) {

						for (var i = 0; i < evt.data.length; i++) {

							var elem = $(evt.data[i].html);
							console.log(elem);
							if(self.options.container.hasClass('masonry-container')) {
								elem.imagesLoaded(function() {
									$output = this.elements[0];
									self.options.container.append($output)
						    		self.options.container.masonry('appended', $output);
								});
							}
							else {
								elem.imagesLoaded(function() {
									$output = this.elements[0];
									self.options.container.append($output);
								});
							}
						}
					}
					else if(evt.html) {
						var $html = $(decodeURIComponent((evt.html).replace(/\+/g, '%20')));
						
						var elements = [];
						for (var i = 0; i < $html.length; i++) {
							if($html[i].nodeName=='DIV') {
								elements.push($html[i]);
							}
						}

						if(self.options.container.hasClass('masonry-container')) {
							for (var i = 0; i < elements.length; i++) {
								var elem = $(elements[i]);
								elem.imagesLoaded(function() {
									var $output = this.elements[0];
									self.options.container.append($output);
						    		self.options.container.masonry('appended', $output);
								});
							}
						}
						else {
							for (var i = 0; i < elements.length; i++) {
								var elem = $(elements[i]);
								elem.imagesLoaded(function() {
									$output = this.elements[0];
									self.options.container.append($output);
								});
							}
						}
					}

					self._alreadyloading = false;
					setTimeout(function() {
						self._trigger( "onNewPageLoaded", null, { data:self } );
						App.loadModules();
					}, 1200);

				});
		},
		// -------------------------------------
		_loadNextPage:function() {
			var nextPage = this.options.currentPage + 1;
			if(nextPage <= this.options.lastPage) {
				
				$(this.options.element).html(this.options.loadingMessage);
				$(this.options.element).addClass('disabled');

				this._post({page:nextPage});
				
				// thats it no more
				if(this.options.currentPage == this.options.lastPage) {
					this._disableMoreButton();
				}

			}
		},

		// -------------------------------------
		_create: function() {
			var self 	= this;

			
			this.options.infiniteScroll = this.element.data('infinite-scroll') || false;
			this.options.element 	 = this.element;
			this.options.url 		 = this.element.data('url');
			this.options.target 	 = $(this.element.data('target'));
			this.options.currentPage = this.element.data('current-page');
			this.options.perPage 	 = this.element.data('per-page') || 12;
			
			this.options.lastPage 	 = this.element.data('last-page');
			this.options.total 		 = this.element.data('total');
			this.options.itemsContainer = this.element.data('items-container') || '.masonry-container';

			var q = this.element.data('target')+' '+this.options.itemsContainer;
			this.options.container = $(q);

			// console.log(q, this.options.container);
			// console.log('infiniteScroll', this.options.infiniteScroll);

			if(this.options.container.length == 0) {
				this.options.container = this.options.target;
			}
			if(!this.options.url) {
				console.log("Missing data-url");
			}

			if(this.options.target.length>0 && 
			   this.options.container.length>0 && 
			   this.options.url != null &&
			   this.options.lastPage != null &&
			   this.options.currentPage != null) {
				
				if(self.options.currentPage == self.options.lastPage) {
					self._disableMoreButton();
				}
				if(this.options.infiniteScroll) {
					this._setupInfinateScroll();
				}

				this.element.click(function(event) {
					event.preventDefault();
					self._loadNextPage();
				});

			}
			else {
				console.log("You need to pass a data-target to have a more button");
			}
		}

	});

}(jQuery));



