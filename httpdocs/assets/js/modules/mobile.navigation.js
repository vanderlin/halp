(function() {

	$(document).ready(function($) {

		var x;

		App.nav = {

		  	$hamburger:$("#toggle-nav-left"),
		    $wrap:$('#main-content'),
		    $mainContent:$('#main-content .wrap'),

		    $rightContent:$('.wrap-right'),

		    $cover:$('.mobile-content-cover'),
			$backBtn:$('.back-icon'),
			$mobileContent:$('.mobile-content'),
			
			$menuRightTarget:$("#toggle-nav-right"),
		    
		    $returnTop:$('.return-to-top'),
		    $menuDrawers:$('.menu-drawers .sidebar-menu'),
		   	$leftTitle:$('.left-title'),

		   	$menuRight:$('.menu-right'),

		   	// -------------------------------------
		   	_prevTitle:null,

		    // -------------------------------------
		    _addOpenClass: function() {
		      	this.$hamburger.addClass('open');
	    		this.$wrap.addClass('open');
	    		this.$cover.addClass('open');	    	
		    },
		    _removeOpenClass: function() {
		      	this.$hamburger.removeClass('open');
	    		this.$wrap.removeClass('open');
	    		this.$cover.removeClass('open');
		    },

		    // -------------------------------------
		    _max: function() {
		    	return 220;//$('.menu-drawers').width();	
		    },
		   	
		   	// -------------------------------------
		    _movePanels: function(x) {
		    	this.$wrap[0].style.left = x + 'px'
		    },

			// -------------------------------------
			_setup: function() {
				var self = this;
				this.$returnTop.click(function(e) {
		    		e.preventDefault();
		    		$('body').scrollTo(0, 300, function() {
		    			self.$returnTop.fadeTo(200, 0);
		    			self.$returnTop.data('visible', false);
		    		});
		    	});

				var toTopVisible = false;
		    	self.$returnTop.fadeTo(0,0);
		      	$(document).scroll(function(e) {
		    		var visible = self.$returnTop.data('visible');
		    		if ($('body').scrollTop() > 100 && !visible) {
		    			self.$returnTop.fadeTo(200, 1);
		    			self.$returnTop.data('visible', true);
		    		}
		    		else if($('body').scrollTop() < 100 && visible) {
		    			self.$returnTop.fadeTo(200, 0);
		    			self.$returnTop.data('visible', false);
		    		}
		    	});


		    	var startTime = null;
			    this.$hamburger.on('touchstart', function(event) {   
			    	x = event.originalEvent.targetTouches[0].pageX - self.$wrap.position().left;
			    	startTime = $.now();
			    })
			    .on('touchmove', function(e) {
			    	self._addOpenClass();
			        var change = e.originalEvent.targetTouches[0].pageX - x
			        change = Math.min(Math.max(0, change), self._max()) // restrict to -100px left, 0px right
			        self._movePanels(change);

			        var left = parseFloat(self.$wrap[0].style.left);
			        var pct  = left / self._max();

			        self.$menuDrawers.css({
			        	opacity:pct,
			        	left:(1.0-pct)*-20,
			        });

			        if (change < -10) disable_scroll() // disable scroll once we hit 10px horizontal slide
			    })
			    .on('touchend', function(e) {
			        var left = parseFloat(self.$wrap[0].style.left);
			        var pct  = left / self._max();
			        var diffInTime = $.now() - startTime;
			        if(diffInTime < 200) {
			        	self.toggleLeftMenu();
			        	return;
			        }
			        else {
			        	if(pct>0.4) {
				          self.openLeftMenu();
				        }
						else {
				          self.closeLeftMenu()
				        }   
			        }
			    });

			   	this.$menuRightTarget.click(function(e) {
		    		e.preventDefault();
		    		if(self.onRightButtonClicked) {
		    			self.onRightButtonClicked(e);
		    		}
		    		$(this).fadeOut(200);
		    		
		    	});

			   	this.$backBtn.click(function(e) {
		    		e.preventDefault();
		    		self.hideBackButton();
		    		self.closeRightMenu();
		    		self.setLeftTitle(self._prevTitle);
		    		self._prevTitle = null;
		    		self.$menuRightTarget.fadeTo(200, 1);
		    	});
		    	// this.$rightContent.css('left', $(document).width());
			    
			},

			// -------------------------------------
		    openLeftMenu:function() {
		      
		      	var self = this;
		      	this._addOpenClass();
		    	
		    	this.$wrap.animate({left: this._max()}, 200, function() {
		        	self.$mobileContent.click(function(e) {
			        	e.preventDefault();
			        	self.closeLeftMenu();
			        });
		     	})

		     	self.$menuDrawers.animate({opacity:1, left:0}, 200);

		    },

		    // -------------------------------------
		    closeLeftMenu:function() {
		    	var self = this;
		    	this.$wrap.animate({left:0}, 200, function() {
		        	self.$wrap.unbind('click');
		        	self._removeOpenClass();
		      	});
		    },

		    // -------------------------------------
		    toggleLeftMenu: function() {
				if(this.$wrap.hasClass('open')) {
		      		this.closeLeftMenu();
		      	}
		      	else {
					this.openLeftMenu();
		      	}
		    },

		    // -------------------------------------		    
		    setLeftTitle: function(html, callback) {
		    	this._prevTitle = this.$leftTitle.html();
		    	this.$leftTitle.html(html);
		    	this.$leftTitle.animate({opacity:1}, 200, callback);
		    },


		    // -------------------------------------
		    showBackButton: function(callback) {
		    	this.$backBtn.delay(220).fadeIn(200);
				this.$leftTitle.animate({left:20, opacity: 0}, 200, function() {
					if(callback) {
						callback(this)
					}
				});
		  	},
		  	hideBackButton: function() {
				this.$leftTitle.delay(100).animate({left:0}, 200);
		    	this.$backBtn.fadeOut(200);
		  	},


		    // -------------------------------------
		    openRightMenu: function(callback) {
		    	
		    	this.$hamburger.addClass('open');
	    		this.$wrap.addClass('open');
	    		this.$cover.addClass('open');

		    	var w = this.$wrap.width();

		    	this.$mainContent.animate({left: -w}, 200, function() {
		     	
		     	});
		    	
		    	
		    	this.$rightContent.animate({left:0}, 200);
		    	this.showBackButton(callback);
		    	//this.$menuRight.animate({left:0}, 200, callback);
		  		//this.$menuRight.addClass('open');
		    },
		    closeRightMenu: function(callback) {
		    	
		    	this.$hamburger.removeClass('open');
	    		this.$wrap.removeClass('open');
	    		this.$cover.removeClass('open');

		    	var w = this.$wrap.width();
		    	this.$mainContent.animate({left: 0}, 200, function() {
		     	});
		    	this.$rightContent.animate({left:w}, 200);
		    	this.hideBackButton();
		    	//this.$menuRight.animate({left:0}, 200, callback);
		  		//this.$menuRight.addClass('open');
		    },



			/*
			



		    

		    // -------------------------------------
		    App.closeRightMenu = function() {
		    	$mainContent.animate({left: 0}, 200, function() {
		     		_removeOpenClass();
		     	});
		    }

		  

		    var swiper = new Swiper('#content-swiper', {
	        	initialSlide:0,
	     		onSlideChangeEnd:function(swpr) {
	            	console.log(swpr);
	            }
	        });

		    
		  	var $mapContainer = $('.map-container');
		  


		  	function showBackButton() {
				$leftTitle.animate({left:10}, 100);
		    	$backBtn.delay(220).fadeIn(200);
		  	}
		  	function hideBackButton() {
				$leftTitle.delay(100).animate({left:0}, 200);
		    	$backBtn.fadeOut(200);
		  	}

		  	App.openMap = function(callback) {
		  		$mapContainer.animate({left:0}, 200, callback);
		  		$mobileContent.addClass('open');
		  	}

		  	App.closeMap = function(callback) {
		  		$mapContainer.animate({left:$(document).width()}, 200, callback);	
		  		$mobileContent.removeClass('open');
		  	}

		  	App.setRightButton = function() {

		  	}

		    $menuRightTarget.click(function(e) {
		    	e.preventDefault();
		    	App.setRightButton();
		    	return;
		    	showBackButton();
		    	openMap();
		    	// $mapContainer.animate({left:0}, 200);
		    	// $.ajax({
		    	// 	url: '/itineraries/29/map',
		    	// 	type: 'GET',
		    	// 	dataType: 'html',
		    	// })
		    	// .always(function(e) {
		    	// 	swiper.appendSlide(['<div class="swiper-slide">'+e+'</div>']);
		    	// 	swiper.slideTo(1);
		    	// });
		    	
		    	
		    });
		    $backBtn.click(function(e) {
		    	e.preventDefault();
		    	hideBackButton();
		    	closeMap();
		    });
*/
		}

		App.nav._setup();

	});
})();
