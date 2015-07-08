

(function($) {
    $.widget('lo.usersFinder', {
    
    	
    	// -------------------------------------
    	options: {
    		placeholder:'Search for users',
    	},

    	// -------------------------------------
    	_modal:null,
    	_users:null,
    	_popover:null,
    	_list:null,
    	_target:null,
    	_input:null,
    	_exclude:null,
    	_addedUsers:null,
    	_index:0,

    	// -------------------------------------
    	_clearResults: function() {
			this._list.find('li:not(.search)').remove();
    	},

    	// -------------------------------------
    	instance: function() {
    		return this;
    	},

    	// -------------------------------------
    	_getUserFromId: function(id) {
    		for (var i = 0; i < this._users.length; i++) {
    			if(this._users[i].id == id) {
    				return this._users[i];
    			}
    		};
    	},

    	// -------------------------------------
    	_createSharedUserItem: function(user) {
    		var item = "";
			item += '<li class="shared-user" data-id="'+user.id+'">';
				item += '<a class="user" href="#user-'+user.id+'">';
					item += $(user.view).find('img')[0].outerHTML;
					item += user.name;
				item += '</a>';
				item += '<a class="remove-user" href="#remove-user-'+user.id+'"><i class="fa fa-times"></i></a>';
			item += '</li>';

			return $(item);
    	},

    	// -------------------------------------
    	_addUserToSharedList: function(user) {
			
			this._input.val('');
			this._input.focus();

			var $item = this._createSharedUserItem(user);
    		
			this._popover.find('.shared-users-list').append($item);
			$item.fadeOut(0).fadeIn(400);


			if(this._popover.find('.dropdown').hasClass('open')) {
				this._list.html('');
				var $dropdown = this._popover.find('.user-search');
					$dropdown.dropdown().dropdown('toggle');
			}

			this._addEventsToNewSharedUser($item);
			this._trigger( "onSelect", null, user);
			this._index = 0;

			console.log(	this._popover.find('.save-btn'));
			this._popover.find('.save-btn').removeAttr('disabled');
    	},

    	// -------------------------------------
    	_removeUserFromList: function($item) {
    		$item.fadeOut(300, function() {
				$(this).remove();
			});
    		this._popover.find('.save-btn').removeAttr('disabled');
    	},

		// -------------------------------------
    	_addEventsToNewSharedUser: function($item) {
    		var self = this;
    		$item.find('.remove-user').click(function(e) {
				e.preventDefault();
				self._removeUserFromList($item);
			});
    	},

    	// -------------------------------------
    	_updateListEvents: function() {
    		var self = this;
    		this._list.find('li.user a').click(function(e) {
    			e.preventDefault();
    			var id = $(this).data('id');
    			var user = self._getUserFromId(id);	
    			self._addUserToSharedList(user);
    		});

    		this._list.find('li').hover(function() {
    			self._index = $(this).index();
    			self._list.children().removeClass('active');
    		}, function() {
    			/* Stuff to do when the mouse leaves the element */
    		});
    	},

    	// -------------------------------------
    	_userInSharedList: function(user) {
    		var t = this._popover.find('.shared-users-list li[data-id="'+user.id+'"]');
    		for (var i = 0; i < this._exclude.length; i++) {
    			if(this._exclude[i] == user.id) {
    				return true;
    			}
    		};
    		return t.length == 1;
    	},

    	// -------------------------------------
    	_nextInResultList: function() {
    		var total = this._list.children().length;
    		
    		if(this._index < total-1) {
    			if(this._list.find('li.active').length > 0) this._index ++;		
    			console.log(total, this._index);
    			var $item = $(this._list.children()[this._index]);
    				this._scrollToItem($item);
    				
    		}
    	},
    	_prevInResultList: function() {
    		if(this._index > 0) this._index --;
			var $item = $(this._list.children()[this._index]);
				this._scrollToItem($item);
				console.log(this._index);
    	},
    	_selectResult: function() {
    		var $item = $(this._list.children()[this._index]);
    		if($item.length && $item.hasClass('active')) {
    			var user = this._getUserFromId($item.data('id'));
    			this._addUserToSharedList(user);
    		}
    	},
    	_scrollToItem: function($item) {
    		this._list.children().removeClass('active');
    		$item.addClass('active');
    		this._list.scrollTo($item, 0, {offset:{top:-(46*3)}});
    	},

    	// -------------------------------------
    	_search: function(term) {

    		var $dropdown = this._popover.find('.user-search');
	    	var $list = this._popover.find('.dropdown-menu');

    		
    		var self = this;		
  			
  			term = term.replace(new RegExp("([\\.\\\\\\+\\*\\?\\[\\^\\]\\$\\(\\)\\{\\}\\=\\!\\<\\>\\|\\:\\-])", "g"), "\\$1");
  			term = term.replace(/%/g, '.*').replace(/_/g, '.');
  			var search = new RegExp(term, 'gi');
			var results = jQuery.grep(this._users, function (value) {
				var t = search.test(value.name);;
				return t;
			});
			
			// this._clearResults();
			if(results.length) {
				var list = "";

				for (var i = 0; i < results.length; i++) {
					if(this._userInSharedList(results[i]) == false) {
						list += '<li class="user" data-id="'+results[i].id+'">';
							var $link = $(results[i].view);
								$link.append(results[i].name);
								list += $link[0].outerHTML;
						list += '</li>';
					}
				};
				$list.html(list);
				
				if(this._popover.find('.dropdown').hasClass('open') == false) {
					$dropdown.dropdown().dropdown('toggle');
				}
				this._list = $list;
				this._updateListEvents();
			}
			else {
				if(this._popover.find('.dropdown').hasClass('open') == true) {
					$dropdown.dropdown().dropdown('toggle');
				}
			}

			

    	},

    	// -------------------------------------
    	_getSharedUsers: function() {
    		var self = this;
			var users = [];
			this._popover.find('.shared-users-list li').each(function(index, el) {
    			var user = self._getUserFromId($(el).data('id'));
    			users.push(user);
    		});
			return users;
    	},

    	// -------------------------------------
    	_save: function() {
    		var users = this._getSharedUsers();
    		var self = this;
    		this._trigger( "onSave", null, {users:users});
			this._close();
    	},

    	// -------------------------------------
    	_close: function() {
			this.element.magnificPopup('close');
    	},

    	// -------------------------------------
    	_cancel: function() {

    		var $userList = this._popover.find('.shared-users-list');
    		var list = "";
			for (var i = 0; i < this._addedUsers.length; i++) {
				var user = this._getUserFromId(this._addedUsers[i]);
				var $item = this._createSharedUserItem(user);
				list += $item[0].outerHTML;
			};
			$userList.html(list);
			this._close();
    	},

    	// -------------------------------------
    	_getContent: function() {
    		var list = '<div class="white-popup-block lo-user-finder-popover">';
	    			list += '<div class="content">';
	    				if(this.options.title) list += '<h2>'+this.options.title+'</h2>';
						list += '<div class="dropdown">';
							list += '<input data-toggle="dropdown" type="text" name="users" class="lo-input form-control user-search" placeholder="'+this.options.placeholder+'">';
							// <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
							list += '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1"><ul>';
						list += '</div>';

						list += '<ul class="list-inline shared-users-list">';
						
							for (var i = 0; i < this._addedUsers.length; i++) {
								var user = this._getUserFromId(this._addedUsers[i]);
								var $item = this._createSharedUserItem(user);
									list += $item[0].outerHTML;
							};

						list += '</ul>';
						
						list += '<ul class="text-right list-inline">';
							list += '<li><a class="btn btn-default save-btn" disabled>Save</a></li>';
							list += '<li><a class="btn btn-default cancel-btn">Cancel</a></li>';
						list += '</ul>';

					list += '</div>';	
				list += '</div>';


				return list;
    	},
    	
	    // -------------------------------------
	    _getAllUsers: function(callback) {
	    	var self = this;
	    	$.ajax({
	    		url: '/api/users?with_view=true',
	    		type: 'GET',
	    		dataType: 'json',
	    	})
	    	.done(function(e) {
	    		self._users = e;
	    		if(callback!==undefined) callback();
	    	})
	    },

    	// -------------------------------------
    	_create: function() {
    		
    		
    		var self = this;

    		this._getAllUsers(function() {
	    		
	    		self.options.placeholder = self.element.attr('placeholder') || self.options.placeholder;
	    		self._target = $(self.element.data('target'));

	    		var exclude = self.element.attr('data-exclude-users') || "";
	    		self._exclude = exclude.split(",");
	    		for (var i = 0; i < self._exclude.length; i++) self._exclude[i] = parseInt(self._exclude[i]);
	    			
	    		self.options.title = self.element.data('title');
	    		
	    		var addedUsers = self.element.attr('data-added-users');
	    		self._addedUsers = addedUsers.split(",");
	    		for (var i = 0; i < self._addedUsers.length; i++) self._addedUsers[i] = parseInt(self._addedUsers[i]);

				self.element.magnificPopup({
					items: {
						src: $(self._getContent()),
					},
					enableEscapeKey:false,
					type: 'inline',
					callbacks: {
						close: function() {
							self._input.val('');
							self._trigger( "onClose", null, {users:self._getSharedUsers()});
						},
					    open: function() {
							var modal = this;

							var $input = this.content.find('.user-search');
							self._modal = this;
							self._input = $input;
							self._popover = this.content;
							setTimeout(function() {
								$input.focus();
								$input.keypress(function(e) {					
									var key = String.fromCharCode(e.which);
									var value = $(this).val();
									self._search(value);
								});
								$input.keydown(function(e) {
									
									if(self._list) {
									 	switch (e.keyCode) {
									 		case 13:
									 		self._selectResult();
									 		e.preventDefault();
									 		break;
								        	case 40:
								           	self._nextInResultList();
								           	e.preventDefault();
								            break;
								        	case 38:
								            self._prevInResultList();
								            e.preventDefault();
								            break;
								    	}
									}

								});


							}, 100);

							this.content.find('.shared-user').each(function(index, el) {
								self._addEventsToNewSharedUser($(el));	
							});
							
							this.content.find('.cancel-btn').click(function(e) {
								e.preventDefault();
								self._cancel();
							});
							this.content.find('.save-btn').click(function(e) {
								e.preventDefault();
								self._save();
							});
							self._trigger( "onOpen", null, {users:self._getSharedUsers()});
					    },
					}
				});

				// self.element.magnificPopup('open');
			});
    	},

    	

	



    });
}(jQuery));