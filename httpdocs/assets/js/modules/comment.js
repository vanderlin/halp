(function($) {


    $.widget('lo.comment', {
    	options: {
    		type: null,
        	id: null,
        	view:'site.spots.comment',
        	maxChars:255,
    	},

    	// -------------------------------------
    	
    	_comment:null,
    	_commentsList:null,
    	_commentBox:null,
    	_popover:null,
    	
    	_activeCommentItem:null,
    	_activeTextarea:null,
    	_activePopover:null,
    	


    	// -------------------------------------
    	_post: function(options) 
    	{

    		if(options.body.length == 0) {
    			console.log("Sorry you need to have a comment to post");
    			return;
    		}
    		
    		if(options.id!=undefined && options.type == undefined) {
    			console.log("Sorry you need set comment type");
    			return;	
    		}
    		
    		var data = {
    			_method:'POST',
    			body:options.body,
    			view:options.view||this.options.view
    		};

    		if(options.id!=undefined) {
    			data.id = options.id;
    		}

    		if(options.type!=undefined) {
    			data.type = options.type;
    		}
					
					
			if(options.id && options.type) {
				data._method = 'PUT';	
			}

			if(options.itemID != undefined) {
				data.item_id = options.itemID;
			}

    		var self = this;
    		$.ajax({
				url: options.id ? '/comments/'+options.id : '/comments',
				type: 'POST',
				dataType: 'json',
				data:data,
			})
			.done(function(evt) {
				console.log("success", evt);
				if(evt.status == 200) {
					if(data._method == 'PUT') {
						self._updateComment({id:options.id, comment:evt.data});	
					}
					else if(data._method == 'POST') {
						self._addNewComment({comment:evt.data});
					}
					
				}
			})
    	},

    	// -------------------------------------
	    hidePopover: function() 
	    {	
	    	if(this._activePopover) 
	    	{
	    		this._activePopover.popover('destroy');
				this._activePopover.removeClass('active');

	    	}
			
	    },

	    // -------------------------------------
	    _closeAllEditors: function()
	    {
	    	var self = this;
	    	$('.edit-comment-box').each(function(index, el) {
	    		self.cancelEdit({
	    			id:$(el).data('id')
	    		})
	    	});
	    },

	    // -------------------------------------
	    _makeCommentEditable: function(options) 
	    {
	    	var self = this;
	    	var $commentItem = $('.comment-item[data-id="'+options.id+'"]');
	    	var $comment = $commentItem.find('.comment');


	    	// close all other comment boxes
	    	this._closeAllEditors();

	    	this._activeCommentItem = $commentItem;

	    	// hide the current text
	    	$comment.find('p').hide();

	    	var content = $comment.find('p').text();

			this._activeTextarea = $('<textarea rows="1" data-id="'+options.id+'" class="form-control edit-comment-box edit-comment-box-'+options.id+'" placeholder="Add Comment" >'+content+'</textarea><span class="comment-help">Press Esc to <a href="#cancel-comment-'+options.id+'">cancel</a><span class="char-count"></span></span>');
			$comment.append(this._activeTextarea);
			
			autosize(this._activeTextarea);

			this._moveCursorToEnd(this._activeTextarea);

			var $editCommentBoxCharCount = $commentItem.find('.char-count');

			this._activeTextarea.focusin(function(e) {
				e.preventDefault();
				self._updateCharCount({feedback:$editCommentBoxCharCount, textarea:self._activeTextarea});
			});
			
			this._activeTextarea.bind('paste', function(e) {
				self._updateCharCount({feedback:$editCommentBoxCharCount, textarea:self._activeTextarea});	
			});
			this._activeTextarea.bind('keydown', function(e) {
				self._updateCharCount({feedback:$editCommentBoxCharCount, textarea:self._activeTextarea});	
			});
			self._updateCharCount({feedback:$editCommentBoxCharCount, textarea:self._activeTextarea});


			$('a[href="#cancel-comment-'+options.id+'"]').click(function(e) {
				e.preventDefault();
				self.cancelEdit({
	    			id:options.id
	    		})
			});

			this._activeTextarea.keypress(function(e) {
			    if (e.keyCode == 13 && !e.shiftKey) {
			        e.preventDefault();
			    	 

			        var body = self._activeTextarea.val();

		    		if(body.length > self.options.maxChars) {
		    			console.log("Comment is to long");
		    			self._showError({element:$editCommentBoxCharCount, error:'Comment needs to be less than '+self.options.maxChars+' characters'});
		    			return;	
		    		}

			        self._post({
			        	id:options.id, 
			        	type:self.options.type,
			        	body:body
			        });
		        
			    }
			});
			this._activeTextarea.keyup(function(e) {
				if(e.keyCode == 27) { // esc key
					self.cancelEdit({id:options.id});
					e.preventDefault();
				}
			});

	    },

	    // -------------------------------------
	    _canPost: function(options) 
	    {

	    },

	    // -------------------------------------
	    _moveCursorToEnd: function(el) {
	    	el.focus();
	    	el = el[0];
		    if (typeof el.selectionStart == "number") {
		        el.selectionStart = el.selectionEnd = el.value.length;
		    } else if (typeof el.createTextRange != "undefined") {
		        el.focus();
		        var range = el.createTextRange();
		        range.collapse(false);
		        range.select();
		    }
		},

	    // -------------------------------------
	    cancelEdit: function(options) 
	    {
	    	this._removeEditor(options);
	    },
		
		// -------------------------------------
	    cancelComment: function() 
	    {
	    	this._commentBox.val('');
	    	this._commentBox.blur();
	    },

		// -------------------------------------
	    _removeEditor: function(options) 
	    {
	    	var $commentItem = $('.comment-item[data-id="'+options.id+'"]');
	    	$commentItem.find('.comment-help').remove();
	    	$commentItem.find('.comment p').show();	
	    	$('.edit-comment-box-'+options.id).remove();

	    	this.hidePopover();
	    },

	  
	    // -------------------------------------
	    _deleteComment: function(options) 
	    {
	    	var self = this;
			var really = confirm("Are you sure?");
			if(really) {
				$.ajax({
					url: "/comments/"+options.id,
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(evt) {
					if(evt.status == 200) {
						var $commentItem = $('.comment-item[data-id="'+options.id+'"]');	
						$commentItem.fadeOut(500, function() {
							$(this).remove();
						});
					}
				})
				
			}	
	    },

	    // -------------------------------------
	    _updateComment: function(options) 
	    {
	    	
	    	var $commentItem = $('.comment-item[data-id="'+options.id+'"]');
	    	var $comment = $commentItem.find('.comment');

	    	$comment.find('p').html(options.comment.body.split("\n").join("<br />")).fadeIn(200);
	    	
	    	// update the timestamp...
	    	this._removeEditor({id:options.id});
	    	this.hidePopover();

	    },

	    // -------------------------------------
	    _addNewComment: function(options)
	    {

	    	var $commentBox = this._commentsList.find('.comment-box');
	    	var $newComment = $(options.comment.html);

	    	$newComment.insertBefore($commentBox);
	    	$newComment.fadeOut(0).delay(100).fadeIn(500);

	    	this._setupComment($newComment);
	    },

	    // -------------------------------------
	    _setupComment: function(element) 
	    {
	    	if(element.hasClass('can-edit')) {
		    	var $edit = element.find('.edit-comment')
		    	var self = this;
		    	$edit.click(function(e) {
					e.preventDefault();
					self._showPopover({
						id:element.data('id'),
						element:$edit
					});
				});	

				element.dblclick(function(e) {
					e.preventDefault();
					self._makeCommentEditable({id:$(this).data('id')});
				});
			}
	    },

	    // -------------------------------------
	    _showPopover: function(options) 
	    {	
	    	var self = this;

	    	options.element.addClass('active');
	    	options.element.popover({
				html:true,
				trigger:'manual',
				template:'<div class="popover edit-comment-popover info-popover edit-comment-popover-'+options.id+'" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
				content:function() {
					return '\
					<ul class="list-unstyled popover-list comment-actions-'+options.id+'">\
						<li>\
							<a href="#edit" class="edit-link">Edit</a></li>\
						</li>\
						<li>\
							<a href="#delete" class="delete-link">Delete</a></li>\
						</li>\
					</ul>';
				},
				container:'body',
				placement:'top',
			})
			.popover('toggle')	
			.on('shown.bs.popover', function (e) {

			self._activePopover = options.element;
				
				$('.comment-actions-'+options.id+' .delete-link').click(function(e) {
					e.preventDefault();
					self.hidePopover();
					self._deleteComment({id:options.id});
				});
				$('.comment-actions-'+options.id+' .edit-link').click(function(e) {
					e.preventDefault();
					self.hidePopover();
					self._makeCommentEditable({id:options.id});
				});
			})
	    },

	    // -------------------------------------
	    _updateCharCount: function(options)  
	    {

	    	var total = this.options.maxChars - options.textarea.val().length;
	    	console.log(total);
	    	if(total < 0) {
	    		options.feedback.addClass('danger');	
	    	}
	    	else {
				options.feedback.removeClass('danger');	
	    	}
	    	
	    	//{feedback:$charCount, textarea:$(this)}
	    	options.feedback.html(total);
	    	console.log(options.textarea);
	    },

	    // -------------------------------------
	    _showError: function(options) 
	    {
	    	options.element.html(options.error);
	    },

    	// -------------------------------------
		_create: function() 
		{
			
			var self = this;

			this.options.id = this.element.data('id');
			this.options.type = this.element.data('type');
			this.options.view = this.element.data('view') || this.options.view;

			if(this.options.id == null || this.options.type == null) {
				throw new Error('missing id or type');
				return;
			}
			
			
			this._commentsList = this.element.closest('.comments-list');
			this._commentBox = this.element.find('.comment-box textarea');
			var $commentBoxCharCount = self.element.find('.comment-box .char-count');

			// setup the comment box
			autosize(this._commentBox);


			// for the comment box to submit the new comment
			this._commentBox.keypress(function(e) {
			    if (e.keyCode == 13 && !e.shiftKey) {
			        e.preventDefault();
			        
			        var body = self._commentBox.val();

		    		if(body.length > self.options.maxChars) {
		    			console.log("Comment is to long");
		    			self._showError({element:$commentBoxCharCount, error:'Comment needs to be less than '+self.options.maxChars+' characters'});
		    			return;	
		    		}

			        self._post({
			        	itemID:self.options.id,
			        	type:self.options.type,
			        	body:body
			        });

			        $(this).val('');
			        $(this).blur();
			    }
			});

			// esc to cancel the comment
			this._commentBox.keyup(function(e) {
				if(e.keyCode == 27) { // esc key
					self.cancelComment();
					e.preventDefault();
				}
			});

			this._commentBox.focusin(function(e) {
				e.preventDefault();
				self._closeAllEditors();
				self._updateCharCount({feedback:$commentBoxCharCount, textarea:$(this)});
			});
			
			this._commentBox.bind('paste', function(e) {
				self._updateCharCount({feedback:$commentBoxCharCount, textarea:$(this)});	
			});
			this._commentBox.bind('keydown', function(e) {
				self._updateCharCount({feedback:$commentBoxCharCount, textarea:$(this)});	
			});

			
			this.element.find('.comment-item').each(function(index, el) {
				self._setupComment($(el));
			});

			var $viewMore = this.element.find('.view-more-comments');
			if($viewMore.length) {
				$viewMore.find('a').click(function(e) {
					e.preventDefault();
					var skip = $(this).data('skip');
					$viewMore.html('<span class="text-muted fa fa-refresh fa-spin"></span>');

					$.ajax({
						url: '/api/comments',
						type: 'GET',
						dataType: 'json',
						data: {
							type: self.options.type,
							id:self.options.id,
							skip:skip,
							view:self.options.view
						},
					})
					.done(function(evt) {
						if(evt.html) {
							$viewMore.remove();
							var $comments = $(evt.html);
							self._commentsList.prepend($comments);	
							$comments.fadeOut(0).delay(100).fadeIn(500);
							
							$comments.each(function(index, el) {
								var $item = $(el);
								if($item.hasClass('comment-item')) {
									self._setupComment($item);
								}
							});
						}
					});
				});
			}	


    	},    	
    });
}(jQuery));















