(function($){
	$.fn.extend({
		
		// ------------------------------------------------------------------------
		openCreateTaskPopup: function(options) {
			var taskForm = {
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
		    	_open: function(options)
		    	{
		    		var self = this;
		    		$.magnificPopup.open({
			            tLoading: 'Loading some halp!...',
			            closeOnContentClick: false,
			            closeOnBgClick:false,
			            mainClass: 'mfp-fade',
			            ajax: {
		                	settings: {
		                		data:options.data
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
			        console.log("Open Create Task Form");
		    	}				
			}

			//
			taskForm._open(options);
		},

		// ------------------------------------------------------------------------
		validateTask: function(options) {

			var validator = {
				
				// -------------------------------------
				_data:null,

				// -------------------------------------
    			_addErrorToInput: function($input)
    			{
    				var message = $input.data('error-message') || "Input Error";
					$input.parent().parent().append($(	'<div class="input-error">\
													<span>'+message+'</span>\
			  									</div>'));
    			},
		
				// -------------------------------------
		    	_addEventsToInput: function($input)
		    	{
					$input.focusin(function(e) {
		    			$(this).parent().parent().find('.input-error').fadeOut(200, function() {
		    				$(this).remove();
		    			});
		    		});
		    	},

				// -------------------------------------
		    	_validate: function($form)
		    	{
		    		var $title = $form.find('input[name="title"]');
		    		var $project = $form.find('input[name="project"]');
		    		var $duration = $form.find('input[name="duration"]');

		    		this._addEventsToInput($title);
		    		this._addEventsToInput($project);
		    		this._addEventsToInput($duration);
		    		console.log(this);
					this._data = {
						title:$title.val(),
						project:$project.val(),
						duration:$duration.val(),
					};
					console.log(this._data);
		    		var isValid = true;

		    		if($title.val() == "") 
		    		{
		    			this._addErrorToInput($title);
		    			isValid = false;
		    		}
		    		if($project.val() == "") 
		    		{
		    			this._addErrorToInput($project);
		    			isValid = false;	
		    		}
		    		if($duration.val() == "") 
		    		{
		    			this._addErrorToInput($duration);
		    			isValid = false;
		    		}

					return isValid;
		    	},    					

			}

			// 
			if(validator._validate($(this))) 
			{
				console.log(validator._data);
				$(this).openCreateTaskPopup({data:validator._data});
			}


		}
 	});
})(jQuery);

/*
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
    	_addErrorToInput: function($input)
    	{
    		var message = $input.data('error-message') || "Input Error";
			$input.parent().parent().append($(	'<div class="input-error">\
													<span>'+message+'</span>\
			  									</div>'));

    	},

    	// -------------------------------------
    	_addEventsToInput: function($input)
    	{
			$input.focusin(function(e) {
    			$(this).parent().parent().find('.input-error').fadeOut(200, function() {
    				$(this).remove();
    			});
    		});
    	},

    	// -------------------------------------
    	_validate: function($form)
    	{
    		var $title = $form.find('input[name="title"]');
    		var $project = $form.find('input[name="project"]');
    		var $duration = $form.find('input[name="duration"]');

    		this._addEventsToInput($title);
    		this._addEventsToInput($project);
    		this._addEventsToInput($duration);
    		
    		var isValid = true;

    		if($title.val() == "") 
    		{
    			this._addErrorToInput($title);
    			isValid = false;
    		}
    		if($project.val() == "") 
    		{
    			this._addErrorToInput($project);
    			isValid = false;	
    		}
    		if($duration.val() == "") 
    		{
    			this._addErrorToInput($duration);
    			isValid = false;
    		}

			return isValid;
    	},

    	// -------------------------------------
    	open: function() 
    	{
    		var self = this;
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
	        console.log("Open Create Task Form");
    	},

    	// -------------------------------------
		_create: function() 
		{

			var self = this;
			if(this.options.validate && this._validate(this.options.validate))
			{
				this.open();
				return;
			}
			
			

    	},    	
    });
}(jQuery));
*/














