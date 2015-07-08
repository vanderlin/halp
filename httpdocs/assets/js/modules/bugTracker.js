(function($) {


    $.widget('lo.bugTracker', {
    	options: {
    		characterLimit:false,
        	spotid: null,
        	countElem:null,
        	element:null,
        	template:function(options) {
        			var html = '<div class="popover comment-popover bug-tracker-popover bug-tracker-popover-'+options.id+'" role="tooltip"> \
				        			<div class="arrow"></div>\
				        				<div class="title-container">	\
				        					<img class="comment-icon" src="/assets/content/common/comment-icon.svg">\
				        					<h5><a href="#open" class="open-comment" data-toggle="collapse" data-target="#comment-collapse-'+options.id+'">'+options.title+'</a></h5>\
				        					<a href="#close" class="close-comment pull-right"><span class="glyphicon glyphicon-remove-circle"></span></a>\
				        				</div> \
				        				<div class="form-group">\
				        					<input type="text" class="form-control issue-title" placeholder="subject">\
				        				</div>\
										<div class="form-group" id="comment-collapse-'+options.id+'">\
											<textarea class="form-control" placeholder="What\'s the issue?">'+options.body+'</textarea>\
										</div>\
										<div class="comment-footer">\
											<div class="pull-left">\
												'+(options.characterLimit?'<small class="char-count">255 characters left</small><br>':'')+'\
												<small class="note">⌘ enter to submit</small>\
											</div>\
											<div class="pull-right">\
												<a class="post-comment-btn btn btn-default">'+options.submit+'</a>\
											</div>\
										</div>\
			        			</div>';
				return html;
        	},
    	},

    	/*<span class="btn btn-default btn-file">\
			Add Image <input type="file">\
		</span>\*/
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
    	_showThanks: function() {
    		var self = this;
    		var h = $(this.options.popoverID).height();
    		$(this.options.popoverID).height(h);

    		$(this.options.popoverID).find('.form-group').hide();
    		$(this.options.popoverID).find('.comment-footer').hide();

    		var html = $('<div class="thanks-container">\
    				    	<div class="thanks">\
    							<h4>Thanks for the feedback</h4>\
    						</div>\
    					</div>');	

			$(this.options.popoverID).append(html);

			html.fadeOut(0,0).delay(100).fadeIn(300).delay(1000).fadeOut(200, function() {
				self.close();
			});
			
			
    	},

    	// -------------------------------------
    	_post: function(options) {
	

    		var self = this;
    		$.ajax({
				url: '/bugs',
				type: 'POST',
				dataType: 'json',
				data: {
					body:options.body,
					title:options.title,
				},
			})
			.done(function(evt) {
				console.log("success", evt);
				self._showThanks();
				// if(evt.status == 200) {
				// 	self.close();
				// 	if(self.options.commentID) {
				// 		var $comment = $('.spot-comment-'+self.options.commentID);
				// 		var $blockquote = $comment.find('blockquote');
				// 		$blockquote.delay(200)
				// 				   .fadeOut(200)
				// 				   .html(evt.data.body)
				// 				   .fadeIn(200);
							
				// 	}
				// 	else {
				// 		self._showNewComment(evt.data.html);
				// 	}
				// }
			})
			.fail(function(evt) {
				console.log("error", evt);
				// if(evt.responseJSON) {
				// 	var data = evt.responseJSON;
				// 	var msg = "";
				// 	for (var i = 0; i < data.errors.length; i++) {
				// 		msg += data.errors[i];
				// 	};
				// 	self._showWarningMessage(msg);
				// }
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
			var $title 	  = $(this.options.popoverID).find('.issue-title');
			var body = $.trim($textarea.val());
			if(body.length>0 && body.length<=255) {
				this._post({
					body:$textarea.val(),
					title:$title.val(),
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
    	toggle: function() {
			this.element.popover('toggle');
    	},

    	// -------------------------------------
    	close: function() {
			this.element.popover('destroy');
			this.destroy();
    	},


    	// -------------------------------------
	    open: function() {
				
			var self = this;
			
			this.element.popover({
				title:'Bug Tracker Box',
				container:'body',
				placement:'top',
				template:this.options.template({
					characterLimit:self.options.characterLimit,
					id:this.uuid,
					title:'Report a bug',
					body:'',
					submit:'Submit',
				})
				
				
			}).popover('show');

			this.element.on('hide.bs.popover', function (e) {
				self.destroy();
			});
			
			// the popover has shown
			this.element.on('shown.bs.popover', function (e) {
					

				
				// first close all other popovers 
				$(".bug-tracker-popover").not('.bug-tracker-popover-'+self.uuid).each(function(index, elem) {
					$(elem).popover('destroy');	
				});

								
				var $popover   		= $(this);
				var $commentWindow  = $(self.options.popoverID);
				var $charcount 		= $(self.options.popoverID).find('.char-count');
				var $textarea  		= $(self.options.popoverID).find('textarea');
				var $closebtn  		= $(self.options.popoverID).find('.close-comment');
				var $submitBtn 		= $(self.options.popoverID).find('.post-comment-btn');
				var $title 		    = $(self.options.popoverID).find('.issue-title');

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
				if(self.options.characterLimit) {
					$textarea.keyup(function() {self._updateTextCount() });
				}
				$textarea.keydown(function(event) {
					if((event.metaKey || event.ctrlKey) &&  event.keyCode == 13) {
						console.log("CMD: Post the comment");
						self._submitComment();
					}
					if(self.options.characterLimit) self._updateTextCount();
				});
				
				
			})
			
	    },

    	// -------------------------------------
		_create: function() {
		
			var self 	= this;
		
			this.options.element = this.element;
			this.options.popoverID = '.bug-tracker-popover-'+this.uuid;
			
			// var targetID = this.element.attr('data-target');
			// var target = this.element.find(targetID);
			
			// if(target.length == 0) target = this.element;
			// this.options.target = $(target);

			// this.options.target.click(function(event) {
			// 	event.preventDefault();
			// 	//console.log(self.options.popover());
			// });
			this.element.click(function(event) {
				event.preventDefault();
				self.open();		
			});
		
		
    	},
    	

    });

}(jQuery));















