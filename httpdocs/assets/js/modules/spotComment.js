




























(function($) {


    $.widget('lo.spotComment', {
    	options: {
        	spotid: null,
        	target:null,
        	countElem:null,
        	element:null,
        	template:function(options) {
        			var html = '<div class="popover comment-popover comment-popover-'+options.id+'" role="tooltip"> \
				        			<div class="arrow"></div>\
				        				<div class="title-container">	\
				        					<img class="comment-icon" src="/assets/content/common/comment-icon.svg">\
				        					<h5><a href="#open" class="open-comment" data-toggle="collapse" data-target="#comment-collapse-'+options.id+'">'+options.title+'</a></h5>\
				        					<a href="#close" class="close-comment pull-right"><span class="glyphicon glyphicon-remove-circle"></span></a>\
				        				</div> \
										<div class="comment-container" id="comment-collapse-'+options.id+'">\
											<textarea class="form-control" placeholder="Enter comment">'+options.body+'</textarea>\
										</div>\
										<div class="comment-footer">\
											<div class="pull-left">\
												<small class="char-count">255 characters left</small><br>\
												<small class="note">⌘ enter to submit</small>\
											</div>\
											<a class="pull-right post-comment-btn btn btn-default">'+options.submit+'</a>\
										</div>\
			        			</div>';
				return html;
        	},
    	},

    	// -------------------------------------
    	_showWarningMessage: function(msg) {
    		
    		var $warning  = $(this.options.popoverID).find('.char-count');
    		var prevText  = $warning.html();
			$warning.hide()
					.addClass('yellow-color')
					.html(msg).fadeIn(200).delay(1000).fadeOut(function() {
						$(this).removeClass('yellow-color').html(prevText).fadeIn(100);
					});
			
    	},
    	
    	// -------------------------------------
    	_destroy: function() {
			console.log("Comment widget killed: "+this.uuid);
    	},

    	// -------------------------------------
    	close: function() {
			this.options.target.popover('destroy');
			this.destroy();
    	},

    	// -------------------------------------
    	_post: function(options) {
    		
    		var self = this;
    		$.ajax({
				url: this.options.commentID ? '/comments/'+this.options.commentID : '/spots/'+this.options.spotid+'/comments',
				type: 'POST',
				dataType: 'json',
				data: {
					body:options.body,
					_method:this.options.commentID?'PUT':'POST'
				},
			})
			.done(function(evt) {
				console.log("success", evt);
				if(evt.status == 200) {
					self.close();
					if(self.options.commentID) {
						var $comment = $('.spot-comment-'+self.options.commentID);
						var $blockquote = $comment.find('blockquote');
						$blockquote.delay(200)
								   .fadeOut(200)
								   .html(evt.data.body)
								   .fadeIn(200);
							
					}
					else {
						self._showNewComment(evt.data.html);
					}
				}
			})
			.fail(function(evt) {
				console.log("error", evt);
				if(evt.responseJSON) {
					var data = evt.responseJSON;
					var msg = "";
					for (var i = 0; i < data.errors.length; i++) {
						msg += data.errors[i];
					};
					self._showWarningMessage(msg);
				}
			})
    	},
    	
    	// -------------------------------------
		_updateTextCount: function() {
			var $textarea  = $(this.options.popoverID).find('textarea');
			var $charcount = $(this.options.popoverID).find('.char-count');
			if($textarea.length>0) {
				//255 characters left
				var nChars = $textarea.val().length
				var count  = (255-nChars);
				if(count <= 0) {
					$charcount.addClass('yellow-color');	
				}
				else {
					$charcount.removeClass('yellow-color');		
				}
				$charcount.html(count+' characters left');
			}
		},

		// -------------------------------------
		_showNewComment: function(html) {

			var $popover   		= $(this);
			var $commentWindow  = $(this.options.popoverID);
			var $charcount 		= $(this.options.popoverID).find('.char-count');
			var $textarea  		= $(this.options.popoverID).find('textarea');
			var $closebtn  		= $(this.options.popoverID).find('.close-comment');
			var $submitBtn 		= $(this.options.popoverID).find('.post-comment-btn');
			
      		App.scrollTo($("#comments"), 500, function() {
      			
      			$comments = $("#comments .content");


      			$topRow = $($comments.children()[0]);

      			if($topRow.children().length>=3 || $comments.children().length==0) {
      				$newComment = $('<div class="row"><div class="col-sm-4">'+html+'</div></div>');
					$comments.prepend($newComment);
	      			$newComment.fadeTo(0,0).delay(200).fadeTo(1000, 1);

      			}
      			else {
      				$newComment = $('<div class="col-sm-4">'+html+'</div>');
					$topRow.prepend($newComment);
	      			$newComment.fadeTo(0,0).delay(200).fadeTo(1000, 1);
      			}
      		});
		
		},

		// -------------------------------------
		_submitComment: function() {
			var $textarea = $(this.options.popoverID).find('textarea');
			var body = $.trim($textarea.val());
			if(body.length>0 && body.length<=255) {
				this._post({
					body:$textarea.val()
				});
			}
			else if(body.length>255) {
				this._showWarningMessage('Too many characters');
			}
			else {
				this._showWarningMessage('You need to enter a comment first');
			}
		},

    	// -------------------------------------
	    open: function() {
				
			var self = this;
			var $comment = $('.spot-comment-'+this.options.commentID);
			var $blockquote = $comment.find('blockquote');
			
			this.options.target.popover({
				title:'Comment Box',
				container:'body',
				placement:'top',
				template:this.options.template({
					id:this.uuid,
					title:$comment.length?'Edit Comment':'Add Comment',
					body:$blockquote.length?$blockquote.html():'',
					submit:$blockquote.length?'Update':'Submit',
				})
				
				
			}).popover('show');

			this.options.target.on('hide.bs.popover', function (e) {
				self.destroy();
			});
			
			// the popover has shown
			this.options.target.on('shown.bs.popover', function (e) {
					

				
				// first close all other popovers 
				$(".comment-popover").not('.comment-popover-'+self.uuid).each(function(index, elem) {
					$(elem).popover('destroy');	
				});
				
				var $popover   		= $(this);
				var $commentWindow  = $(self.options.popoverID);
				var $charcount 		= $(self.options.popoverID).find('.char-count');
				var $textarea  		= $(self.options.popoverID).find('textarea');
				var $closebtn  		= $(self.options.popoverID).find('.close-comment');
				var $submitBtn 		= $(self.options.popoverID).find('.post-comment-btn');
				
				// $commentWindow.css('top', $commentWindow.position().top-10);
				// focus on the textarea
				$textarea.focus();

				
				// the close button
				$closebtn.click(function(event) {
					event.preventDefault();
					self.close();
				});

				// the submit button
				$submitBtn.click(function(event) {
					event.preventDefault();
					self._submitComment();
				});

				// textarea
				$textarea.keyup(function() {self._updateTextCount() });
				$textarea.keydown(function(event) {
					if((event.metaKey || event.ctrlKey) &&  event.keyCode == 13) {
						console.log("CMD: Post the comment");
						self._submitComment();
					}
					self._updateTextCount();
				});
				
			})
			
	    },

    	// -------------------------------------
		_create: function() {
			var id 	 	= this.options.spotid||this.element.attr('data-id');
			var self 	= this;

			
			// we need a id to move forward
			if(id) {
	
				this.options.element = this.element;
				this.options.spotid =  this.options.spotid || id;
				this.options.collapseID = '#comment-collapse-'+this.uuid;
				this.options.popoverID = '.comment-popover-'+this.uuid;
				var targetID = this.element.attr('data-target');
				var target = this.element.find(targetID);
				
				if(target.length == 0) target = this.element;
				this.options.target = $(target);
	
				// this.options.target.click(function(event) {
				// 	event.preventDefault();
				// 	//console.log(self.options.popover());
				// });
			
				this.open();
				

				
			}
			else {
				console.log("Missing data-id", this.element);
			}
    	},
    	

    });

}(jQuery));















