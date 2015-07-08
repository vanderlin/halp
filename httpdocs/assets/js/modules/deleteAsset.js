jQuery.fn.extend({
	deleteAsset: function(options) {
	    options = options || {};
	    var self = $(this)
		var id = $(this).data('id');
		var $target = $($(this).data('target'));
		
	    if(self.length == 0) {
			throw new Error('missing target');
			return;
		}
		if(id == undefined) {
			throw new Error('missing asset id');
			return;
		}


						
		var check = ($(this).data('confirm') || false) ? confirm("Are you sure you want to delete this image?") : true;

		if(check) {

			$.ajax({
					url: '/assets/'+id,
					type: 'POST',
					dataType:'json',
					data: {_method: 'DELETE'},
				})
				.done(function(e) 
				{
					if(e.status == 200) 
					{
						if(typeof options.onComplete == 'function') 
						{	
							e.data = {
								self:self,
								target:$target,
								id:id,
							}
							options.onComplete(e);
							return;
						}

						if($target.length > 0) 
						{
							$target.find('[data-id="'+id+'"]').fadeOut(200, function() {
								$(this).remove();
							});
						}
					}
				})
				.always(function(e) {
					if(typeof options.onAlways == 'function') 
					{
						options.onAlways(e);
					}	
				})
				.fail(function(e) 
				{
					if(typeof options.onError == 'function') 
					{
						options.onError(e);
					}
				});
		}
	},
});
