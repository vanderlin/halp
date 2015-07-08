


(function($) {


    $.widget('lo.spotFavorite', {
    	options: {
        	spotid: null,
        	target:null,
        	imageTarget:null,
    	},

    	// -------------------------------------
		_create: function() {
			var id 	 	= this.element.attr('data-id');
			var self 	= this;

			// we need a id to move forward
			if(id) {
				this.options.element = this.element;
				this.options.spotid = id;

				// get the target 
				var targetID = this.element.attr('data-target');
				var target = this.element.find(targetID);
				if(target.length == 0) target = this.element;
				this.options.target = $(target);
		
				this.options.active = this.element.attr('data-active')=='false'?false:true;

				var dataImageTaget = target.attr('data-image-target');				
				var imageTarget;

				if(dataImageTaget) {
					imageTarget = this.element.find(dataImageTaget).first();
				}
				else {
					imageTarget = this.element.find('img').first();
				}
				if(imageTarget.length) {
					this.options.imageTarget = imageTarget;
				}

				// image active/notactive
				var dataActiveImg = target.attr('data-image-active');
				var dataNotActiveImg = target.attr('data-image-not-active');
				this.options.activeImg = dataActiveImg ? dataActiveImg : '/assets/content/common/heart-icon-active.svg';
				this.options.notActiveImg = dataNotActiveImg ? dataNotActiveImg : '/assets/content/common/heart-icon.svg';

				// -------------------------------------
				this.options.target.hover(function() {
					var imgURL = !self.options.active ? self.options.activeImg : self.options.notActiveImg;
					self.options.imageTarget.attr('src', imgURL);
				}, function() {
					var imgURL = self.options.active ? self.options.activeImg : self.options.notActiveImg;
					self.options.imageTarget.attr('src', imgURL);
				});

				// -------------------------------------
				this.options.target.click(function(event) {
					self.click(event);	
				});

			}
			else {
				console.log("Missing data-id", this.element);
			}
    	},
    	
    	// -------------------------------------
    	click: function(event) {
    		var self = this;
			event.preventDefault();	
			
			var method = self.options.active==false ? 'POST' : 'DELETE';
			console.log(self.options.active, method);
			$.ajax({
				url: '/favorites/'+self.options.spotid,
				type: 'POST',
				dataType: 'json',
				data:{'_method':method, spot_id:self.options.spotid}
			})
			.done(function(evt) {
				if (evt.status == 200) {
					var baseURL = '/assets/content/common/';
					
					self.element.attr('data-active', !self.options.active);
				
					self.options.active = self.element.attr('data-active')=='false'?false:true;
					var imgURL = self.options.active ? self.options.activeImg : self.options.notActiveImg;
					self.options.imageTarget.attr('src', imgURL);
					if(self.options.active) {
						self.element.addClass('active');	
					} 
					else {
						self.element.removeClass('active');
					}
				};
				console.log("success", evt);
			})
			.fail(function(evt) {
				console.log("error", evt);
			})
			

    	},

    	// -------------------------------------
    	mouseover: function() {

    	},

    	// -------------------------------------
    	mouseout: function() {

    	},

    });

}(jQuery));




