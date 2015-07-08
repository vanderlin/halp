
jQuery.fn.extend({
  deleteSpotButton: function() {
	this.click(function(e) {
	
	e.preventDefault();
	var id = $(this).attr('data-id');
	var really = confirm("Are you sure");

	if(really) {
		$.ajax({
			url: '/admin/spots/'+id,
			type: 'POST',
			dataType: 'json',
			data: {_method:'DELETE'},
		})
		.done(function(evt) {
			if(evt.spot && evt.url) {
				document.location = evt.url;
			}
		}).
		fail(function(evt) {
			$(".save-message").html('There was an error deleting the spot');						
			$(".save-message").fadeIn(200);
		});
	}

	});    
  },


  spotStatusUpdate: function(options) {
  	
  	$(this).find("li a").each(function(i, e) {
  		$(e).click(function(evt) {
  			evt.preventDefault();
  			var id = $(this).attr('data-id');
  			var status = $(this).attr('href').substr(1);

  			console.log(status);
  			var self = this;
	  		$.ajax({
				url: '/admin/spots/'+id,
				type: 'POST',
				dataType: 'json',
				data: {_method:'PUT', 'status':status},
			})
			.done(function(evt) {
				if(options.done) {
					options.done(self, evt);
				}
				else {
					if(evt.spot && evt.url) {
						document.location = evt.url;
					}
					console.log(evt);
				}
			}).
			fail(function(evt) {
				$(".save-message").html('There was an error deleting the spot');						
				$(".save-message").fadeIn(200);
			});		
  		});	
  	})
  	
  	
  }



});

