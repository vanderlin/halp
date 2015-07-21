(function($) {
    $.widget('halp.createTask', {
    	options: {
    		
    	},

    	// -------------------------------------
    	$form:null,
    	
    	// -------------------------------------
    	_submit: function(e) 
    	{
    		var url = this.$form.attr('action');
    		var type = this.$form.attr('method');
    		var fd = new FormData(this.$form[0]);    
    		$.ajax({
    			url: url,
    			type: type,
    			dataType: 'json',
    			data: fd,
    			processData: false,
  				contentType: false,
    		})
    		.always(function(e) {
    			console.log("complete", e);
    		});
    		
    	},

    	// -------------------------------------
    	_validate: function($form)
    	{
    		var $title = $form.find('input[name="title"]');
    		var $project = $form.find('input[name="project"]');
    		var $duration = $form.find('input[name="duration"]');

    		if($title.val() == "") 
    		{
    			$title.parent().parent().append($(	'<div class="input-error">\
    													<span>Missing Title</span>\
    			  									</div>'));

    			console.log("Title Invalid");
    		}
    	},

    	// -------------------------------------
		_create: function() 
		{

			var self = this;
			

			if(this.options.validate)
			{
				this._validate(this.options.validate);	
				return;
			}

			$.magnificPopup.open({
	            tLoading: 'Loading some halp!...',
	            closeOnContentClick: false,
	            closeOnBgClick:false,
	            mainClass: 'mfp-fade',
	            ajax: {
                	settings: {
                		data:self.options.data
                	}
                },
	            items: {
	                src: '/tasks/create',
	                type: 'ajax',
	            },
	            callbacks: {
	                parseAjax: function(mfpResponse) {
	                    var task = mfpResponse.xhr.responseText;
	                    mfpResponse.data = $(mfpResponse.xhr.responseText);
	                },
	                ajaxContentAdded: function() {
	                    $(function() {
    						$( "#datepicker" ).datepicker();
  						});   
  						self.$form = $('#create-task-form');
  						self.$form.submit(function(e) {
  							e.preventDefault();
  							self._submit(e);
  						});
	                }
	            }
	        });	

    	},    	
    });
}(jQuery));















