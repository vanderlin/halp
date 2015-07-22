(function($){
	$.fn.extend({
		
		// ------------------------------------------------------------------------
		openCreateTaskPopup: function(options) {
			var taskForm = {
				
				$form:null,
				$popup:null,

				// -------------------------------------
		    	_submit: function(e) 
		    	{
		    	
		    	
		    		var self = this;
		    		var url = this.$form.prop('action')+'?view=true';
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
		    			self._showTaskCreated(e);
		    		});
		    		
		    	},

		    	// -------------------------------------
		    	_showSuccessMessage: function(e)
		    	{
		    		var self = this;
		    		this.$form.find('.create-task-buttons').fadeOut(200);
	    			var $h2 = this.$form.find('h2');
	    			self.$form.find('.form-field').fadeOut(300);
	
	    			$h2.html(e.notice);
	    			self.$popup.css({height: 100});		
		    			
	    			setTimeout(function() {
						App.closeClaimPopup();
    					self._addViewToPage(e);
	    			}, 1000);


	    		
		    		
		    	},

		    	// -------------------------------------
		    	_addViewToPage: function(e)
		    	{
		    		console.log('_addViewToPage');
					var $content = $('#tasks-content');
					var $view = $(e.view);
					$content.prepend($view);
					$view.addClass('task-focused');
					$view.hide().fadeIn(300);

					var $delbtn = $view.find('.halp-delete-task-button');
					App.addDeleteTaskEvent($delbtn);
		    	},

		    	// -------------------------------------
		    	_showTaskCreated: function(e)
		    	{
		    		
		    		this._showSuccessMessage(e);
					
					this.$form.find('.input').removeClass('input--filled');
					this.$form[0].reset();

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
		    						$( "#datepicker" ).datepicker({showAnim:'slideDown'});
		  						});  
		  						self.$popup = $('.white-popup .popup-content');
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
					var $error = $(	'<div class="input-error">\
										<span>'+message+'</span>\
  									</div>');

					$input.parent().parent().append($error);
					$error.hide().fadeIn(200);
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
					this._data = {
						title:$title.val(),
						project:$project.val(),
						duration:$duration.val(),
					};
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














