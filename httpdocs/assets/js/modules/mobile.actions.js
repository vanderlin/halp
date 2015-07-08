(function($) {


    $.widget('lo-mobile.actions', {
    	options: {
    		type: null,
        	itemID: null,
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
    	_postAction: function(options) {
    		var method = options.active==false ? 'DELETE' : 'POST';
		    this._trigger( "onVisit", null, { data:options.active } );

			$.ajax({
				url: options.url,
				type: 'POST',
				dataType: 'json',
				data:{'_method':method}
			})
			.done(function(evt) {
				console.log(evt);
			});
    	},

    	// -------------------------------------
    	_post: function(options) 
    	{

    		console.log(options);
    		if(options.body.length == 0) {
    			console.log("Sorry you need to have a comment to post");
    			return;
    		}
    		
    		if(options.id!=undefined && options.type == undefined) {
    			console.log("Sorry you need set comment type");
    			return;	
    		}
    		
    		this.hidePopover();

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

	    	this._closeAllItems();
			
	    },

	    // -------------------------------------
	    _closeAllItems: function()
	    {
	    	var self = this;
	    	$('.comment-item .comment-body-container.open').each(function(index, el) {
	    		self._closeItem($(el));
	    	});
	    },

	    // -------------------------------------
	    _closeItem: function(element) {
	    	if(element.length) {
		    	element.animate({left:0}, 200, function() {
	    			$(this).removeClass('open');
	    		}); 
	    	}
	    },

	    // -------------------------------------
	    _makeCommentEditable: function(options) 
	    {

	    	var self = this;
	    	var $commentItem = $('.comment-item[data-id="'+options.id+'"]');
	    	var $comment = $commentItem.find('.comment-body');


	    	// close all other comment boxes
	    	// this._closeAllEditors();
			//this._activeCommentItem = $commentItem;

	    	var value = $comment.text();

			//this._activeTextarea = $('<textarea rows="1" data-id="'+options.id+'" class="form-control edit-comment-box edit-comment-box-'+options.id+'" placeholder="Add Comment" >'+content+'</textarea><span class="comment-help">Press Esc to <a href="#cancel-comment-'+options.id+'">cancel</a><span class="char-count"></span></span>');
			//$comment.append(this._activeTextarea);
			//autosize(this._activeTextarea);
			this._showPopover({
				element:options.element, 
				id:options.id, 
				comment:value,
				onShow:function(e) {
					var $input = self._popover.find('.comment-input');
					self._moveCursorToEnd($input);
				}
			});
			return;

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
	    	if(el.length == 0) {
	    		console.log("No input found");
	    		return;
	    	}
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

	    	if(options.id===undefined) return;

	    	var self = this;
			var really = confirm("Are you sure you want to delete this comment?");

			
			if(really) {

				var $commentItem = $('.comment-item[data-id="'+options.id+'"]');	
				$commentItem.slideUp('fast', function() {
					$(this).remove();
				})

				$.ajax({
					url: "/comments/"+options.id,
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(evt) {
					if(evt.status == 200) {
						console.log('comment deleted '+options.id);
					}
				})
			}	
			else {
				this._closeAllItems();
			}
	    },

	    // -------------------------------------
	    _makeBodyHTML: function(body) {
	    	return body.split("\n").join("<br />");
	    },

	    // -------------------------------------
	    _updateComment: function(options) 
	    {
	    	
	    	var $item = $('.comment-item[data-id="'+options.id+'"]');
	    	
	    	this._closeItem($item.find('.comment-body-container'));

	    	var body = this._makeBodyHTML(options.comment.body)
	    	$item.find('.comment-body').fadeOut(200).delay(200).html(body).fadeIn(200);
	    	

	    	this.hidePopover();

	    },

	    // -------------------------------------
	    _addNewComment: function(options)
	    {

	    	var $newComment = $(options.comment.html);
	    	var body = $newComment.find('.comment-body').text();
	    	$newComment.find('.comment-body').html(this._makeBodyHTML(body));

	    	this._commentsList.prepend($newComment);	    	
	    	$newComment.slideUp(0).delay(100).slideDown(500);
	    	this._setupComment($newComment);
	    },

	    // -------------------------------------
	    _setupComment: function(element) 
	    {

	    	if(element.hasClass('can-edit') == false) return;
	    	
	    	var self = this;
	    	
		    var x;
		    var prevDraggedCell = null;
		    var $comment = element.find('.comment-body-container');

	        $comment.on('touchstart', function(e) {

	        	$('.comment-item .comment-body-container.open').not(this).each(function(index, el) {
	        		self._closeItem($(el));
	        	});
	        	var max = $(this).parent().find('.behind').width();

	            
	            $('.swipe-delete li > a.open').css('left', '0px').removeClass('open') // close em all
	            $(e.currentTarget).addClass('open')
	            x = e.originalEvent.targetTouches[0].pageX - $(this).position().left; // anchor point
	        	
	        })
	        .on('touchmove', function(e) {
	        	var max = $(this).parent().find('.behind').width();
	            var change = e.originalEvent.targetTouches[0].pageX - x
	            change = Math.min(Math.max(-max, change), 0) // restrict to -100px left, 0px right
	            e.currentTarget.style.left = change + 'px'
	            // disable_scroll() // disable scroll once we hit 10px horizontal slide
	        })
	        .on('touchend', function(e) {
	            var left = parseInt(e.currentTarget.style.left)
	           	var max = $(this).parent().find('.behind').width();
	            var new_left;
	            if (left < -35) {
	                new_left = -max+'px'
	            } else {
	                new_left = '0px'
	            }
	            // e.currentTarget.style.left = new_left
	            $(e.currentTarget).animate({left: new_left}, 200, function() {
	            	if(new_left == '0px') {
	            		$(this).removeClass('open');
	            	}
	            })
	            enable_scroll()
	            prevDraggedCell = e.currentTarget;
	        });

			element.find('.delete-btn').on('touchend', function(e) {
				e.preventDefault()
				var id = $(this).data('id');
				self._deleteComment({id:id});
			})
			element.find('.edit-btn').click(function(e) {
				e.preventDefault()
				var id = $(this).data('id');
				self._makeCommentEditable({element:$(this), id:id});
			})

	    	/*if(element.hasClass('can-edit')) {
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
			}*/
	    },

	    // -------------------------------------
	    _showPopover: function(options) 
	    {	
	    	var self = this;
			
			
	    	
	    	options.element = options.element || this.element;
	    	options.element.addClass('active');
	    	options.comment = options.comment || '';
	    	
	    	var uid = options.id ? ('edit-'+options.id)  : ('new-'+options.itemID);

	    	options.element.popover({
				html:true,
				trigger:'manual',
				template:'\
						<div class="popover container mobile-comment-popover info-popover comment-popover-'+uid+'" role="tooltip">\
							<div class="arrow"></div>\
							<h3 class="popover-title"></h3>\
							<div class="popover-content"></div>\
						</div>',
				content:function() {
					return '\
					<div class="comment-box">\
				      <div class="form-group">\
				        <textarea class="form-control comment-input" placeholder="Enter comment">'+options.comment+'</textarea>\
				       	<span class="comment-help">press enter to post <span class="char-count"></span></span>\
				      </div>\
				      <div class="form-group text-right">\
				        <button class="btn btn-default cancel-comment">Cancel</button>\
				        <button class="btn btn-default save-comment">Save</button>\
				      </div>\
				    </div>';
				},
				container:'body',
				placement:'top',
			})
			.popover('toggle')	
			.on('shown.bs.popover', function (e) {
				
				self._activePopover = options.element;
				
				if(options.onShow) {
					options.onShow(e);
				}


				$('.comment-popover-'+uid+' .cancel-comment').click(function(e) {
					e.preventDefault();
					self.hidePopover();
				});

				autosize($('.comment-popover-'+uid+' .comment-input'));
				self._setupKeyEvents(options);
			})


			// this is a comment update
			if(options.id !== undefined) {
				$('.comment-popover-'+uid+' .save-comment').html('Update');
				$('.comment-popover-'+uid+' .save-comment').click(function(e) {
					e.preventDefault();
					self.hidePopover();
					self._post({
			        	id:options.id, 
			        	type:self.options.type,
			        	body:$('.comment-popover-'+uid+' .comment-input').val()
			        });
				});
				
			}

			// post a new comment
			else if(options.itemID !== undefined) {
				$('.comment-popover-'+uid+' .save-comment').html('Save');
				$('.comment-popover-'+uid+' .save-comment').click(function(e) {
					e.preventDefault();
					self.hidePopover();
					self._post({
			        	itemID:options.itemID, 
			        	type:self.options.type,
			        	body:$('.comment-popover-'+uid+' .comment-input').val()
			        });
				});
			}

			// this is weird ios defers the click event so we need to do this
			// right away. needs testing on other devices
			$('.comment-popover-'+uid+' .comment-input').focus();
			self._popover = $('.comment-popover-'+uid);



			if(Utils.isIOS()) {
				var t = options.element.offset().top - ($('.comment-popover-'+uid).height() + 130);
				$('body').scrollTo(t);
			}

	    },

	    // -------------------------------------
	    _setupKeyEvents: function(options) {


	    	var self = this;
			this._commentBox = self._popover.find('.comment-input');
			var $commentBoxCharCount = self._popover.find('.char-count');


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

		    		if(options.id) {
						self._post({
				        	id:options.id,
				        	type:self.options.type,
				        	body:body
				        });	
		    		}
		    		else {
			    		self._post({
				        	itemID:options.itemID,
				        	type:self.options.type,
				        	body:body
				        });	
		    		}
			        

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
				self._closeAllItems();
				self._updateCharCount({feedback:$commentBoxCharCount, textarea:$(this)});
			});
			
			this._commentBox.bind('paste', function(e) {
				self._updateCharCount({feedback:$commentBoxCharCount, textarea:$(this)});	
			});
			this._commentBox.bind('keydown', function(e) {
				self._updateCharCount({feedback:$commentBoxCharCount, textarea:$(this)});	
			});
	    },

	    // -------------------------------------
	    _updateCharCount: function(options)  
	    {
	    	var total = this.options.maxChars - options.textarea.val().length;
	    	
	    	if(total < 0) {
	    		options.feedback.addClass('danger');	
	    	}
	    	else {
				options.feedback.removeClass('danger');	
	    	}
	    	
	    	//{feedback:$charCount, textarea:$(this)}
	    	options.feedback.html(total);
	    	
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
			
			this.options.itemID = this.element.data('id');
			this.options.type = this.element.data('type');
			this.options.view = this.element.data('view') || this.options.view;
			this._commentsList = $(this.element.data('comment-list'));

			if(this.options.itemID == null || this.options.type == null || this._commentsList.length == 0) {
				throw new Error('missing id or type');
				return;
			}
			

			var $comment = this.element.find('.comment a');
			var $been = this.element.find('.been-here a');
			var $favorite = this.element.find('.favorite a');
			


			$comment.click(function(e) {
				e.preventDefault();
				self._closeAllItems();
				self._showPopover({element:$(this), itemID:self.options.itemID});
			});

			$been.click(function(e) {
				e.preventDefault();
				$(this).parent().toggleClass('active');
				var active = $(this).parent().hasClass('active');
				self._postAction({
					url:'/visits/'+self.options.itemID,
					active:active
				});
				console.log(active);
			});

			$favorite.click(function(e) {
				e.preventDefault();
				$(this).parent().toggleClass('active');
				var active = $(this).parent().hasClass('active');
				self._postAction({
					url:'/favorites/'+self.options.itemID,
					active:active
				});
			});


			this._commentsList.find('.comment-item').each(function(index, el) {
				self._setupComment($(el));
			});

			
			var $viewMore = this._commentsList.find('.view-more-comments');
			if($viewMore.length) {
				$viewMore.find('a').click(function(e) {
					e.preventDefault();
					var skip = $(this).data('skip');
					
					$viewMore.fadeTo(200, 0, function() {
						$(this).addClass('text-center');
						$(this).html('<span class="text-center text-muted fa fa-refresh fa-spin"></span>').fadeTo(200, 1);

					});
					

					$.ajax({
						url: '/api/comments',
						type: 'GET',
						dataType: 'json',
						data: {
							type: self.options.type,
							id:self.options.itemID,
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















