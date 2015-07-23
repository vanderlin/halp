
(function($) {

	var TaskValidator = {
		
		maxTitleChars:2,

		// -------------------------------------
		_setErrorMessage: function($error, message)
		{
			if($error)
			{
				$error.find('span').html(message);
				this._positionError($error);
			}
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
								

				$parent.append($error);

				if($input.data('parent')!==undefined)
				{
					$error.data('parent', $($input.data('parent')));
				}

				$input.addClass('has-error');
				$error.data('target', $input);
				setTimeout(function() {
					self._positionError($error);
				}, 20);
				
				$error.hide().fadeIn(200);

				this._positionError($error);
				
				$(window).resize(function(event) {
					self._positionError($error);
				});		
				
			}
			
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

		// -------------------------------------
		_removeErrorInput: function($input)
		{
			if($input.data().$error)
			{
				$input.removeClass('has-error');
				$input.data().$error.fadeOut(200, function() {
					$(this).remove();
					$input.data('$error', null);
				});	
			}
		}

	}





	$.fn.extend({
		
		// ------------------------------------------------------------------------
		popupResponse: function(e, options)
		{
			options = options || {};

			$('.popup-content').fadeTo(200, 0, function() {
                

				var $notice = $('<h2>'+e.notice+'</h2>');
				
                $('.white-popup').first().animate({height:options.height||200}, 500, function() {
                	
                    $('.popup-content').html($notice);

                    $('.popup-content').fadeTo(300, 1, function() {
                    	setTimeout(function() {
                    		App.closePopup(options.callback);
                    	}, options.delay||1000);
                    });
                });
                

            });
		},


		// ------------------------------------------------------------------------
		openCreateTaskPopup: function(options) {
			var taskForm = {
				
				$form:null,
				$popup:null,

				_resetForm: function() 
				{
					var $initForm = $('#init-create-task');
						$initForm.find('.input').removeClass('input--filled');
						$initForm.find('input').each(function(index, el) {
							var $input = $(el);
							$input.val('');
						});
						this.$form[0].reset();
				},

				// -------------------------------------
		    	_submit: function(e) 
		    	{
		    		this._resetForm();
		    	return;
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
		    	_showTaskCreated: function(e)
		    	{
		    		$(this).popupResponse(e, {
                        callback:function() {
                            App.scrollTo($('#tasks-content'), 500, function() {
                                var $view = $(e.view);
                                $('#tasks-content').prepend($view);
                                $view.hide();
                                $view.addClass('task-focused');
                                $view.delay(200).fadeTo(500, 1);
                                
                                var $delbtn = $view.find('.halp-delete-task-button');
								App.addDeleteTaskEvent($delbtn);

                            });
                        }
                        
                    })
				
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
		validateInput: function(options)
		{

			var isValid = true;
			var $input = $(this);
			var $error = $input.data().$error;
			var len = $input.val().length;
    		var maxChars = $input.data('max')||1000000;
    		var isRequired = $input.data('required') || false;
    		var message = null;

    		if(isRequired && len==0)
    		{
				isValid = false;
    		}
    		if(len > maxChars)
    		{
    			message = "Too many letters +"+(len-maxChars);
    			isValid = false;
    		}
    		
    		if(len > maxChars && $error)
    		{
				TaskValidator._setErrorMessage($error, message);
    		}

    		if($error == null && !isValid) {
    			var $error = TaskValidator._addErrorToInput($input, message);
				$input.data('$error', $error);
    		}
    		else if(isValid && $error) {
    			TaskValidator._removeErrorInput($input);
    		}

			
			return isValid;
		},

		// ------------------------------------------------------------------------
		addValidationListener: function(options) {
			var $form  = $(this);
    	
    		$form.find('input.validate').each(function(index, el) {
    			
    			var $input = $(el);  
    			if($input.data().validator == null) {  			
	    			
					$input.focusout(function(e) {
	    				$(this).validateInput();
					});
					$input.on('keyup', function(e) {
						$(this).validateInput();
					});

					$input.data('validator'. true);
				}
				else {
					console.log("*** Validation for input already init ***");
				}
    		
    		});

		},

		// ------------------------------------------------------------------------
		validateTask: function(options) {

			var validator = {
				
				// -------------------------------------
				_data:{},

				// -------------------------------------
		    	_validate: function($form)
		    	{
		    		console.log("--- Checking Task Validation ---");
					var self = this;
					var passes = true;
					$form.find('input.validate').each(function(index, el) {
    					var $input = $(el);
    					
    					if($input.data().$error) {
    						$input.data().$error.addCSSAnimation('pulse', function() {
								console.log("Done with error pulse");
							})
    					}
    					var valid = $input.validateInput();
    					var $error = $input.data().$error;
    					
    					
		    			if(valid != true) {
		    				passes = false;
		    			}
		    			var name = $input.attr('name');
		    			self._data[name] = $input.val(); 
					});
					
					console.log("*** validation:", passes, " ***");
					return passes;
		    	},    					

			} 
			return {valid:validator._validate($(this)) , data:validator._data}; 
		}
 	});
})(jQuery);
