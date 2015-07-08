


(function($) {


    $.widget('lo.spotVisit', {
    	options: {
        	spotid: null,
        	target:null,
    	},

    	// -------------------------------------
		_create: function() {
			var id 	 	= this.element.attr('data-id');
			var self 	= this;
			// console.log("Spot Visit id: "+id);
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
					
					// image active/notactive
					var dataActiveImg = target.attr('data-image-active');
					var dataNotActiveImg = target.attr('data-image-not-active');
					this.options.activeImg = dataActiveImg ? dataActiveImg : '/assets/content/common/heart-icon-active.svg';
					this.options.notActiveImg = dataNotActiveImg ? dataNotActiveImg : '/assets/content/common/heart-icon.svg';


					this.options.target.mouseover(function(event) {
						var imgURL = !self.options.active ? self.options.activeImg : self.options.notActiveImg;
						self.options.imageTarget.attr('src', imgURL);
					});

					this.options.target.mouseout(function(event) {
						var imgURL = self.options.active ? self.options.activeImg : self.options.notActiveImg;
						self.options.imageTarget.attr('src', imgURL);	
					});
				}

				// -------------------------------------
				this.options.target.click(function(event) {
					
					event.preventDefault();

					var method = self.options.active==false ? 'POST' : 'DELETE';
		            
		            self._trigger( "onClick", null, { data:this } );

					$.ajax({
						url: '/visits/'+self.options.spotid,
						type: 'POST',
						dataType: 'json',
						data:{'_method':method}
					})
					.done(function(evt) {

						console.log("success", evt);
						if (evt.status == 200) {

							self.options.active = !self.options.active;
							console.log(self.options.active);
							self.element.attr('data-active', self.options.active);

							if(self.options.imageTarget) {
								var imgURL = self.options.active ? self.options.activeImg : self.options.notActiveImg;
								self.options.imageTarget.attr('src', imgURL);
							}
							if(self.options.active) {
								self.element.addClass('active');	
					            self._trigger( "onVisited", null, evt );

							} 
							else {
								self.element.removeClass('active');
					            self._trigger( "onUnvisited", null, evt );
							}
							console.log(evt);
							self._trigger( "onUpdate", null, evt );

						};
						
					})
					.fail(function(evt) {
						console.log("error", evt.responseJSON);
					})
					
					return true;
				});

			}
			else {
				console.log("Missing data-id", this.element);
			}
    	},
    	

    });

}(jQuery));




