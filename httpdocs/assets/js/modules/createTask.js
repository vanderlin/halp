
(function($) {

	var TaskValidator = {

		// -------------------------------------
		_setErrorMessage: function($error, message)
		{
			$error.find('span').html(message);
			var pw = $error.parent().find('input').width();
			var ew = $error.width();
			var cx = (pw-ew)/2;
			$error.css('left', cx);

		},

		// -------------------------------------
		_addErrorToInput: function($input, message)
		{	
			var id = "input-error-"+$input.attr('name');
			if($("."+id).length == 0) {
				var message = message || ($input.data('error-message') || "Input Error");
				var $error = $(	'<div class="input-error '+id+'">\
									<span>'+message+'</span>\
								</div>');
				var $parent = $input.parent().parent();
				$parent.append($error);
				$error.hide().fadeIn(200);

				var pw = $input.width();
				var ew = $error.width();
				var cx = (pw-ew)/2;
				$error.css('left', cx);
				$(window).resize(function(event) {
					var pw = $input.width();
					var ew = $error.width();
					var cx = (pw-ew)/2;
					$error.css('left', cx);
				});		
				
			}
			return $("."+id);
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


	}

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
		addValidationListener: function(options) {
			
			var $form = $(this);
    		var $title = $form.find('input[name="title"]');
    		var $error = null;
    		var maxChars = 20;					

			$title.on('keyup', function(event) {

				var length = $(this).val().length;
				
				if (length > maxChars && $error==null) {		
					$error = TaskValidator._addErrorToInput($title, "Too many letters +"+overChar);
				}
				if (length > maxChars && $error) {		
					var overChar = (length-maxChars);
					TaskValidator._setErrorMessage($error, "Too many letters +"+overChar);
				}
				else if($error && length <= maxChars) {
					$error.fadeOut(200, function() {
						$error.remove();
						$error = null;
					});
				}
				
			});

		},

		// ------------------------------------------------------------------------
		validateTask: function(options) {

			var validator = {
				
				// -------------------------------------
				_data:null,

				// -------------------------------------
		    	_validate: function($form)
		    	{
		    		var $title = $form.find('input[name="title"]');
		    		var $project = $form.find('input[name="project"]');
		    		var $duration = $form.find('input[name="duration"]');

		    		TaskValidator._addEventsToInput($title);
		    		TaskValidator._addEventsToInput($project);
		    		TaskValidator._addEventsToInput($duration);
					this._data = {
						title:$title.val(),
						project:$project.val(),
						duration:$duration.val(),
					};
		    		var isValid = true;

		    		if($title.val() == "") 
		    		{
		    			TaskValidator._addErrorToInput($title);
		    			isValid = false;
		    		}
		    		if($project.val() == "") 
		    		{
		    			TaskValidator._addErrorToInput($project);
		    			isValid = false;	
		    		}
		    		if($duration.val() == "") 
		    		{
		    			TaskValidator._addErrorToInput($duration);
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














