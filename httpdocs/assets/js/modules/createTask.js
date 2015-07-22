
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
				$input.data().$error.fadeOut(200, function() {
					$(this).remove();
					$input.data('$error', null);
				});	
			}
		}

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
		validateInput: function(options)
		{

			var valid = true;
			var $input = $(this);
			var len = $input.val().length;
    		var maxChars = $input.data('max');
    		var required = $input.data('required') || false;
    		
    		// console.log($input.attr('name')+" required:",required);
			
			// check if the value is longer than the max
			if(maxChars !== undefined)
			{
				if(len > maxChars) 
				{
					valid = false;
				}
				if(len > maxChars && $input.data().$error == null) 
				{
					var $error = TaskValidator._addErrorToInput($input, "Too many letters +"+(len-maxChars));
					$input.data('$error', $error);
				}
				else if(len <= maxChars && $input.data().$error)
				{
					TaskValidator._removeErrorInput($input);
				}
			}
			else {

				if(len==0) 
				{
					valid = false;
				}
				// this field is required
				if(len==0 && $input.data().$error == null)
				{
					var $error = TaskValidator._addErrorToInput($input);
					$input.data('$error', $error);
				}
				else if($input.data().$error && len!=0) {
					TaskValidator._removeErrorInput($input);
				}
			}
			return valid;
		},

		// ------------------------------------------------------------------------
		addValidationListener: function(options) {
			var $form  = $(this);
    	
    		$form.find('input.validate').each(function(index, el) {
    			
    			var $input = $(el);  
    			if($input.data().validator == null) {  			
	    			
	    			$input.validateInput();

	    			$input.focusin(function(e) {
	    				$(this).validateInput();
					});
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
    					var valid = $input.validateInput();
    					console.log($input.attr('name'), valid);
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

			// 
			if(validator._validate($(this))) 
			{
				console.log(validator._data);
				$(this).openCreateTaskPopup({data:validator._data});
			}


		}
 	});
})(jQuery);
