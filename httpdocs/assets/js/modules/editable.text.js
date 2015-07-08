



(function($) {
    $.widget('lo.editableText', {

    	// -------------------------------------
    	options: {
    		name:null,
    		emptyText:'Click to edit',
    		url:null,
    		type:'textarea',
    	},
    	
    	_originalText:null,
    	_form:null,
    	_p:null,
    	
    	
    	// -------------------------------------
    	_post: function() {
    		var self = this;
    		var data = new FormData(this._form[0]);
    		
    		$.ajax({
    			url:this.options.url,
    			type: 'POST',
    			dataType: 'json',
    			processData: false,
                contentType: false,
    			data: data,
    		})
    		.always(function(e) {
    			console.log(e);
    			if(e.status == 200) {
    				self._removeEditBox( Utils.nl2br(self._getValue()) );
    			}
    			var $formStatus = $("#form-status");
    			if($formStatus.length) {
    				$formStatus.formStatus(e, {
						fadeOut:true, 
						onDone:function(e) {
						}
					});
				}
    		});
    	},

    	// -------------------------------------
    	_getValue: function() {
    		return $.trim(this._form.find('.edit-in-place').val());
    	},

    	// -------------------------------------
    	_removeEditBox: function(newText) {
			this._form.remove();

			this.element.removeClass('active');

			if(newText!==undefined) {
				if(newText.length != 0) this.element.removeClass('empty');
				if(newText.length == 0) this.element.addClass('empty');
				if(newText.length == 0) {
					newText = this.options.emptyText;
				}
				this._target.html(newText).fadeTo(0, 0).delay(100).fadeTo(400, 1);
				
			}
			else {
				this._target.show();
			}

			this.element.addClass('cte');
    	},


    	// -------------------------------------
		_create: function() {
			
			this.options.url = this.element.data('url');
			if(this.options.url && this.options.url[0] !== '/') this.options.url = "/"+this.options.url;

			this.options.name = this.element.attr('name') || null;
			this.options.type = this.element.attr('type') || 'textarea';
			var self = this;			
			this._target = $(this.element.data('target'));
			
			if(this._target.length == 0) {
				console.log("Error:", this.element);
				throw new Error('missing target');
				return;
			}

			if(this.options.name == null) {
				console.log("Error:", this.element);
				throw new Error('missing field name');
				return;
			}


			this.element.addClass('cte');

			this.element.click(function(e) {
				
				if(self.element.hasClass('active') || $(e.target).hasClass('edit-btn')	) return;

			
				var $self = $(this);
				var fontSize = parseInt(self._target.css('font-size'));
				self._originalText = $.trim( Utils.br2nl(self._target.html()) );
				self.element.addClass('active');
				self.element.removeClass('cte');

				var isEmpty = $(this).hasClass('empty') || self._originalText == "";
				if($(this).hasClass('empty')) {
					self.options.emptyText =  self._originalText;
				}

				var formHTML = "";
					formHTML += '<form method="POST" class="editable-form" action="'+self._url+'">';
						formHTML += '<input type="hidden" value="PUT" name="_method">';
						if(self.options.type == 'text') {
							formHTML += '<input autocomplete="off" name="'+self.options.name+'" type="text" class="form-control edit-in-place" value="'+(isEmpty?"":self._originalText)+'">';	
						}
						else {
							formHTML += '<textarea name="'+self.options.name+'" class="form-control edit-in-place">'+(isEmpty?"":self._originalText)+'</textarea>';	
						}
						formHTML += '\
						<div class="edit-controls">\
							<a class="btn btn-default edit-btn save-btn">Save</a>\
							<a class="btn btn-default edit-btn cancel-btn">Cancel</a>\
						</div>';
					formHTML += '</form>';
					
				
				
				self._form = $(formHTML);
				var $textbox = self._form.find('.edit-in-place');
					// $textbox.css('font-size', fontSize);
					
				
				self._target.hide();
				$(this).prepend(self._form);

				autosize($textbox);
				Utils.moveCursorToEnd($textbox);

				var $saveBtn = self._form.find('.save-btn');
				var $cancelBtn = self._form.find('.cancel-btn');


				$cancelBtn.click(function(e) {
					e.preventDefault();
					self._removeEditBox();
				});

				$saveBtn.click(function(e) {
					e.preventDefault();
					self._post();
				});
			});
			
		
    	},
    	

    });

}(jQuery));















