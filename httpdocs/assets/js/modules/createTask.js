
(function($) {

	var TaskValidator = {
		
		maxTitleChars:2,

		// -------------------------------------
		_setErrorMessage: function($error, message)
		{
			$error.find('span').html(message);
			this._positionError($error);
		},

		// -------------------------------------
		_positionError: function($error)
		{
			var $parent = $error.parent();
			var $target = $error.data().target || $error.parent();
			var pw = $target.width();
			var ew = $error.width();
			var cx = (pw-ew)/2;
			$error.css({
				'top':$parent.height(),
				'left':($parent.outerWidth()-$error.outerWidth())/2,
			});

			console.log($error.data());

			/*
			$parent.css('border', '1px solid red');
			$target.css('border', '1px solid red');
			$error.css('border', '1px solid red');
			*/
		},

		// -------------------------------------
		_addErrorToInput: function($input, message)
		{	
			var self = this;
			var id = "input-error-"+$input.attr('name');
			var $error = $("."+id);
			if($error.length == 0) {

				var message = message || ($input.data('error-message') || "Input Error");
					$error = $(	'<div class="input-error '+id+'">\
									<span>'+message+'</span>\
								</div>');

				var $parent = $input.data('parent') ? $($input.data('parent')) : $input.parent();
				
				console.log("parent", $parent);

				$parent.append($error);

				if($input.data('parent')!==undefined)
				{
					$error.data('parent', $($input.data('parent')));
				}

				$error.data('target', $input);
				setTimeout(function() {
					self._positionError($error);
				}, 20);
				$error.hide().fadeIn(200, function() {
				});

				this._positionError($error);
				$(window).resize(function(event) {
					self._positionError($error);
				});		
				
			}
			console.log($error);
			return $error;
		},

		// -------------------------------------
		_addEventsToInput: function($input)
		{
			$input.focusin(function(e) {
				console.log(e);
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
			console.log('addValidationListener', $(this).attr('id'));
			var $form  = $(this);
    		var $title = $form.find('input[name="title"]');
    		var $error = null;
    		var maxChars = TaskValidator.maxTitleChars;	
    		
    		$form.find('input').each(function(index, el) {
    			
    			var $input = $(el);
    			var maxChars = $input.data('max');
    			

    			// check if the value is longer than the max
    			if(maxChars !== undefined)
    			{
    				var len = $input.val().length;
    				if(len > maxChars && $input.data().$error == null) {
    					var $error = TaskValidator._addErrorToInput($input, "Too many letters +"+(len-maxChars));
    					$input.data('$error', $error);

    				}
    			}
    		
    		});

    		return;

    		$title.focusin(function(e) {
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
		    		else if($title.val().length > TaskValidator.maxTitleChars) 
		    		{
		    			TaskValidator._addErrorToInput($title, "Too Many Letters +"+($title.val().length-TaskValidator.maxTitleChars));
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














