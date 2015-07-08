
// Lots of help from here:
// https://github.com/Rovak/InlineAttachment/blob/master/src/inline-attachment.js

(function($) {
    $.widget('lo.imageUpload', {

    	// -------------------------------------
    	options: {
    		type:null,
    		id:null,
    		path:null,
    		uploadURL:'/assets/upload',
    		replace:false,
    		multiple:false,
    		uploadOnSubmit:false,
        	target:null,
        	uploadingMessage:'Uploading...',
        	standbyMessage:'Upload on save',
        	prefix:"",
        	property:"assets",
        	method:'POST',
        	moduleTitle:'Upload Image',
        	allowedTypes: [
		    	'image/jpeg',
		    	'image/png',
		    	'image/jpg',
		    	'image/gif'
    		],
    		createPreviewItem:null
    	},

    	// -------------------------------------
    	makeDefaultPreviewItem: function(options) 
    	{
    		return '<li class="list-group-item list-group-item-info" data-name="'+options.id+'">\
						<div class="media">\
							<div class="media-left media-middle">\
								<a href="" class="lightbox-link">\
									<img width="40px" src="'+(options.src!==undefined?options.src:'')+'" '+(options.src==undefined?'style="display:none"':'')+'>\
								</a>\
							</div>\
							<div class="media-body media-middle">\
								<div class="file-title hidden-md">'+options.title+'</div>\
							</div>\
							<div class="media-right uploading media-middle">\
								<button data-name="'+options.id+'" class="btn btn-default btn-xs remove-filelist-btn">remove</button>\
					    	</div>\
						</div>\
					</li>';
		},	

		// -------------------------------------		
    	makeDeletePreviewItem: function(options) 
    	{
    		options = options || {};
    		options.addDeleteButton = options.addDeleteButton || true;
    		return '<li class="list-group-item list-group-item-info" data-name="'+options.id+'">\
						<div class="media">\
							<div class="media-left media-middle photo">\
								<a href="" class="lightbox-link">\
									<img width="40px" src="'+(options.src!==undefined?options.src:'')+'" '+(options.src==undefined?'style="display:none"':'')+'>\
								</a>\
							</div>\
							<div class="media-body media-middle">\
								<div class="file-title hidden-md">'+(options.title!==undefined?options.title:'')+'</div>\
							</div>\
							<div class="media-right '+(options.addDeleteButton?'remove-photo':'uploading')+' media-middle">\
								<a href="#delete-photo" data-id="'+options.id+'" data-target="#photos-filelist" class="btn btn-default btn-xs delete-photo-btn" '+(options.addDeleteButton?'style="display:none"':'')+'>delete</a>'+(options.addDeleteButton?'<span class="uploading-message">'+this.options.uploadingMessage+'</span>':'')+'\
							</div>\
						</div>\
					</li>';
    	},

    	// -------------------------------------
		_addURLImagePreview: function(options) 
		{
			options = options || {};
			var self = this;
			
			if(self.options.multiple) { 
				options.blob ? self._addBlob(options.blob) : self._addURL(options.src);
				self._itemAdded({blob:options.blob});
				// self._createPreviewItem(options);
				self._createPopoverPreview(options);
			}
			else {
				self._removeAllItems();
				var item = {};
				if(options.blob) {
					self._blobs[0] = options.blob;
					item.blob = options.blob;
				}
				else {
					self._urls[0] = options.src; // not sure what todo here...
					item.url = options.src;
				}

				self._itemAdded(item);
				// self._createPreviewItem(options);
				self._createPopoverPreview(options);	
			}
			
			
		},

    	// -------------------------------------
    	getImages: function() 
    	{
    		var images = {'files':this._files, 'urls':this._urls, 'blobs':this._blobs};
    		// if(this._dropzone) {
    		// 	images.files = this._dropzone.getAcceptedFiles();
    		// }
    		return images;
    	},

    	// -------------------------------------
    	appendImages: function(fd) 
    	{
			if(fd == undefined || fd == null) {
				self._trigger("error", self, {errors:["Missing FormData"]});
				return;
			}
			else {
				var images = this.getImages();
				if(images.urls.length>0) {
					for (var i = 0; i < images.urls.length; i++) {
						fd.append('urls[]', images.urls[i].src);	
					};
				}
				else if(images.blobs.length>0) {
					for (var i = 0; i < images.blobs.length; i++) {
						fd.append('files[]', images.blobs[i], images.blobs[i].name);
					};
				}
				else if(images.files.length>0) {
					for (var i = 0; i < images.files.length; i++) {
						fd.append('files[]', images.files[i]);
					};
				}
			}
			return fd;
    	},

    	// -------------------------------------
    	removeImages: function() {
			this._removeAllItems();
    	},

    	// -------------------------------------
    	_urls:[],
    	_files:[],
		_blobs:[],

    	_dropzone:null,
    	_popover:null,
    	_urlInput:null,
    	_helpBlock:null,
    	_previewTarget:null,
    	_replaceTarget:null,
    	popoverVisible:false,

    	// -------------------------------------
    	closePopover: function() 
    	{
    		if(this._popover) {
				this._urlInput.val('');
				this._popover.popover('hide');
			}
    	},

    	// -------------------------------------
    	_refreshPopoverPosition: function() 
    	{
    		$target = this.element;
    		$popover = this._popover;

    		var h = $popover.height() + 4;
    		var y = $target.offset().top;
    		$popover.css('top', y - h);
    		// console.log(h);
    	},

    	// -------------------------------------
    	_removePopoverImagePreview: function() 
    	{
			var $content = this._popover.find('.upload-popover-content');
    			$content.find('.img-preview').remove();
    			$content.find('form').show();
    			$content.find('.url-input-container').show();
    	},

    	// -------------------------------------
    	_createPopoverPreview: function(options) 
    	{

    		var self = this;

    		this._removePopoverImagePreview();

    		var $content = self._popover.find('.upload-popover-content');

    		var $img = $('<img class="img-preview" src="'+options.src+'">').load(function() {
    			
    			$content.find('.dz-message.info').show();
    			$content.find('.dz-message.loading').hide();

    			$content.prepend($img);

    			$content.find('form').hide();
    			$content.find('.url-input-container').hide();

    			// self._popover.css('top', this.element.position().top);
    			// console.log($content);
				self._refreshPopoverPosition();
			
				$(".submit-image-button").attr('disabled', false);
    		});
    		
    	},

    	// -------------------------------------
    	_createPreviewItem: function(options) 
    	{	
    		if(this._previewTarget.length == 0) return;
    		console.log('Preview Item');
    		options = options || {};
    		options.title = options.title || "No Title";
    		options.id = options.id || options.title;
    		var $previewItem;
    		
    		if(this.options.multiple == false) {
				$previewItem = this._previewTarget.find('li').first();

				if($previewItem.length == 0) {
					$previewItem = this.options.uploadOnAdd ? $(this.makeDeletePreviewItem(options)) : $(this.makeDefaultPreviewItem(options));	
					this._previewTarget.prepend($previewItem);		
				}
    		}
    		else {
				$previewItem = this.options.uploadOnAdd ? $(this.makeDeletePreviewItem(options)) : $(this.makeDefaultPreviewItem(options));	
	    	}

	    	$($previewItem.find('img')).attr('src', options.src);
			$($previewItem.find('.file-title')).html(options.title);

	    	if(this.options.createPreviewItem!==null) {
    			var data  = {
    				image:image,
    				title:title,
    				images:this.getImages(),
    				previewTarget:this._previewTarget,
    				previewItem:$previewItem
    			};
				this._trigger( "createPreviewItem", this, data);
    		}
    		else {
	    		
	    		if(this.options.multiple == true) {
	    			this._previewTarget.prepend($previewItem);		
	    		}

	    		else {

	    			$previewItem.addClass('list-group-item-info');
	    			$previewItem.find('.remove-photo a').hide();
	    			$previewItem.find('.remove-photo').append('<span class="uploading-message">'+this.options.uploadingMessage+'</span>');

					$previewItem.attr('data-name', options.id);
					$previewItem.find('.remove-photo').addClass('uploading');
	    		}
			}

	

			this.closePopover();

    	},

    	// -------------------------------------
    	_showMessage: function(message) { this._helpBlock.fadeIn(300).html(message).delay(2000).fadeOut(200); },

    	// -------------------------------------
    	_canAddFile: function(file) 
    	{
    		for (var i = 0; i < this._files.length; i++) {
				var a = this._files[i].name;
				var b = file.name;
				if(a == b) {
					return false;
				}
			};
			return true;
		},

		// -------------------------------------
    	_canAddBlob: function(file) 
    	{
    		for (var i = 0; i < this._blobs.length; i++) {
				var a = this._blobs[i].name;
				var b = file.name;
				if(a == b) {
					return false;
				}
			};
			return true;
		},

		// -------------------------------------
    	_canAddURL: function(url) 
    	{
    		for (var i = 0; i < this._urls.length; i++) {
				var a = this._urls[i].src;
				var b = url;
				if(a == b) {
					return false;
				}
			};
			return true;
		},

		// -------------------------------------
		_getFileFromName: function(name) 
		{
			for (var i = 0; i < this._dropzone.getAcceptedFiles().length; i++) {
				var a = this._dropzone.getAcceptedFiles()[i];
				if(a.name == name) {
					return a;
				}
			};
			return null;
		},

		// -------------------------------------
		_getURLFromName: function(name) 
		{
			for (var i = 0; i < this._urls.length; i++) {
				var a = this._urls[i];
				if(a == name) {
					return a;
				}
			};
			return null;
		},

		// -------------------------------------
		_removeURL: function(item)
		{
			for(var i = this._urls.length; i--;) {
				if(this._urls[i].src === item) {
					this._urls.splice(i, 1);
				}
			}
		},

		// -------------------------------------
		_removeBlob: function(item)
		{
			for(var i = this._blobs.length; i--;) {
				if(this._blobs[i].name === item) {
					this._blobs.splice(i, 1);
				}
			}
		},

		// -------------------------------------
		_removeFile: function(item) 
		{
			for (var i = 0; i < this._dropzone.getAcceptedFiles().length; i++) {
				var f = this._dropzone.getAcceptedFiles()[i];
				if(f.name === item) {
					this._dropzone.removeFile(f);	
				}
			};

			for(var i = this._files.length; i--;) {
				if(this._files[i].name === item) {
					this._files.splice(i, 1);
				}
			}
			
		},

		// -------------------------------------
		_removeItem: function(item) 
		{
			this._removeURL(item);
			this._removeBlob(item);
			this._removeFile(item);
		},

		// -------------------------------------
		_removeAllItems: function() 
		{
			for (var i = 0; i < this._urls.length; i++) {
				this._removeListItem(this._urls[i]);
			};
			for (var i = 0; i < this._blobs.length; i++) {
				this._removeListItem(this._blobs[i].name);
			};	
			if (this._dropzone) {
				for (var i = 0; i < this._dropzone.getAcceptedFiles().length; i++) {
					var a = this._dropzone.getAcceptedFiles()[i].name;
					this._removeListItem(a);
				};
			};
		},

		// -------------------------------------
		_removeListItem: function(name) 
		{
			this._removeItem(name);
			this._previewTarget.find('li[data-name="'+name+'"]').remove();
		},

		// -------------------------------------
		_isFileAllowed: function(file) { return this.options.allowedTypes.indexOf(file.type) >= 0; },

		// -------------------------------------
		_verifyImageURL: function(url, callback, timeout) 
		{
		    timeout = timeout || 5000;
		    var timedOut = false, timer;
		    var img = new Image();
		    img.onerror = img.onabort = function() {
		        if (!timedOut) {
		            clearTimeout(timer);
		            callback(url, "error");
		        }
		    };
		    img.onload = function() {
		        if (!timedOut) {
		            clearTimeout(timer);
		            callback(url, "success");
		        }
		    };
		    img.src = url;
		    timer = setTimeout(function() {
		        timedOut = true;
		        callback(url, "timeout");
		    }, timeout); 
		},

		// -------------------------------------
		_isDataURL: function(s) {
			var regex = /^\s*data:([a-z]+\/[a-z]+(;[a-z\-]+\=[a-z\-]+)?)?(;base64)?,[a-z0-9\!\$\&\'\,\(\)\*\+\,\;\=\-\.\_\~\:\@\/\?\%\s]*\s*$/i;
    		return !!s.match(regex);
		},

		// -------------------------------------
		_dataURItoBlob: function(dataURI) 
		{
			// convert base64 to raw binary data held in a string
		    // doesn't handle URLEncoded DataURIs
		    var byteString;
		    if (dataURI.split(',')[0].indexOf('base64') >= 0) {
		        byteString = atob(dataURI.split(',')[1]);
		    }
		    else {
		        byteString = unescape(dataURI.split(',')[1]);
		    }

		    // separate out the mime component
		    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

		    // write the bytes of the string to an ArrayBuffer
		    var ab = new ArrayBuffer(byteString.length);
		    var ia = new Uint8Array(ab);
		    for (var i = 0; i < byteString.length; i++) {
		        ia[i] = byteString.charCodeAt(i);
		    }

		    var blob = new Blob([ab], {type: mimeString});
		    	blob.name = "image-" + Date.now() + "." + mimeString.split('/')[1];

		    // write the ArrayBuffer to a blob, and you're done
		    return blob;
		},

		// -------------------------------------
		_handlePastString: function(item) 
		{	
			
			var self = this;
			item.getAsString(function(e) {

	    		var $content = self._popover.find('.upload-popover-content');
    				$content.find('.dz-message.info').hide();
    				$content.find('.dz-message.loading').show();

				if(self._isDataURL(e)) {
					var blob = self._dataURItoBlob(e);
					self._addURLImagePreview({src:e, title:blob.name, id:blob.name, blob:blob});	
				}
				else {
					if(self._canAddURL(e)) {
						self._verifyImageURL(e, function(url, status) {
							
							if(status == 'error') {
								self._trigger("error", this, {errors:["Image not valid"]});
								$content.find('.dz-message.info').show();
    							$content.find('.dz-message.loading').hide();
							}
							else if(status == 'success') {
								self._trigger("imageValid", this, url);	
								//self._addURLImagePreview();
								self._addURL(url);
								self._createPopoverPreview({src:url, title:url});
							}
						});
					}
					else {
						self._trigger("error", self, {errors:["Image already added."]});
						$content.find('.dz-message.info').show();
    					$content.find('.dz-message.loading').hide();
					}
				}
			});
		},

		// -------------------------------------
		_addURL: function(src) 
		{
			this._urls.push({src:src, added:false});
			this._itemAdded({url:src});
		},

		// -------------------------------------
		_addFile: function(file) 
		{	
			file.added = false;
			this._files.push(file);
			this._itemAdded({file:file});
		},

		// -------------------------------------
		_addBlob: function(file) 
		{
			file.added = false;
			this._blobs.push(file);
			this._itemAdded({blob:file});
		},

		// ------------------------------------- Depricated....
		_itemAdded: function(data) 
		{
			return;	
			var self = this;
			if(this.options.uploadOnAdd) {

				var prefix = self.options.prefix;

				var fd = new FormData();
					fd.append('type', this.options.type);
					fd.append('id', this.options.id);
					fd.append('path', this.options.path);
 					fd.append('multiple', this.options.replace);

				if(data.url!==undefined) {
					fd.append(prefix+'urls[]', data.url);
				}
				else if(data.blob!==undefined) {
					fd.append(prefix+'files[]', data.blob, data.blob.name);
				}
				else if(data.file!==undefined) {
					fd.append(prefix+'files[]', data.file);	
				}
				else {
					console.log("Looks like there is not a file to upload");
				}
				
				fd.append('_method', self.options.method);	
				fd.append('property', self.options.property);	

				$.ajax({
					url: this.options.uploadURL,
					type: 'POST',
					dataType: 'json',
					data:fd,
					enctype: 'multipart/form-data',
					processData: false,
					contentType: false,
				})
				.always(function(e) {
					
					console.log(e);

					if(e.response.files!==undefined) {
						
						for (var i = 0; i < e.response.files.length; i++) {
							var name = e.response.files[i].org_filename;
							console.log('Remove Item: '+name);
							self._removeItem(name);
						};

						if(self._replaceTarget!=null && self._replaceTarget.length && self.options.multiple == false && e.response.files.length) {
							var url = self._replaceTarget.attr('src');
							var name = url.substring(url.lastIndexOf('/')+1);
							var file = e.response.files[0];

							self._replaceTarget.fadeTo(200, 0, function() {
								$(this).attr('src', file.base_url+'/'+name+'?'+Math.random()).load(function() {
									$(this).fadeTo(200, 1);
								});				

								
							});
							
						}
						if(self._previewTarget.length) {

							for (var i = 0; i < e.response.files.length; i++) {
								
								var file = e.response.files[i];
								console.log(file);
								var $item = self._previewTarget.find('li[data-name="'+file.org_filename+'"]');
								
								if($item.length) {

									$item.attr('data-id', file.id);
									$item.addClass('list-group-item-success');
									$item.removeClass('list-group-item-info');
									var $up = $item.find('.uploading');
									$item.find('.file-title').html(file.filename);
									$up.removeClass('uploading');
									$up.addClass('remove-photo');
									if(self.options.multiple == true) {
										$up.html('<a href="#delete-photo" data-id="'+file.id+'" data-target="#photos-filelist" class="btn btn-default btn-xs delete-photo-btn">delete</a>');									
									}
									else {
										console.log(self);
										$remove = $item.find('.remove-photo');
										$remove.find('.uploading-message').remove();
										$remove.find('a').show();
										//var $btn = $('<a href="#remove-photo" data-id="'+file.id+'" class="btn btn-default btn-xs" data-preview-target="'+self._previewTarget.selector+'">replace</a>');
										//$up.html(btn);	
									}
									
									
									setTimeout(function() {
										$item.removeClass('list-group-item-success');
									}, 2000);
									
								}
							};
						}
					}
					self._trigger("uploadComplete", self, e);
				});
				
			}

			data.images = this.getImages();
			this._trigger("added", this, data);
		},

		// ------------------------------------- Depricated....
		_handlePastFile: function(file) 
		{
			return;
			var self = this;
			var buttonName = self.element.html();
			
			self.element.html('Loading...');
			self.closePopover();

			
		    var extension = 'png';
		    if (file.name) {
		    	var fileNameMatches = file.name.match(/\.(.+)$/);
		    	if (fileNameMatches) {
		        	extension = fileNameMatches[1];
		    	}
		    }
		    var filename = "image-" + Date.now() + "." + extension;
			file.name = filename;
	
			if(self.options.multiple) {
				self._addBlob(file);
			}
			else {
				self._removeAllItems();
				self._blobs[0] = file; // not sure....
			}
			var reader = new FileReader();
			reader.onloadend = function (e) {
				self._createPreviewItem(reader.result, filename);
				self.element.html(buttonName);	
			}
			reader.readAsDataURL(file);
		},

		// -------------------------------------
		_replaceWithItem: function(options) 
		{
			var self = this;
			var $item = this._previewTarget.find('li').first();
			// $item = self.makeDefaultPreviewItem({src:images.files[i].preview, id:images.files[i].name, title:images.files[i].name, addDeleteButton:false}) );				
			var data = {};
			
			if(options.file !== undefined) {
				data.name = options.file.name;
				data.preview = options.file.preview;
			}

			if($item.length) {
				$item.remove();
			}
		
			$item = $(self.makeDefaultPreviewItem({id:data.name, src:data.preview, title:data.name, addDeleteButton:false}));
			self._previewTarget.prepend( $item );					
		},

		// -------------------------------------
		_submitStandy: function() 
		{	
			var self = this;
			var images = this.getImages();
			var totalImages = 0;
			
			for (var i = 0; i < images.files.length; i++) {
				
				if(images.files[i].added==undefined||images.files[i].added==false) {
					images.files[i].added = true;
					if(!self.options.replace) self._previewTarget.prepend( self.makeDefaultPreviewItem({src:images.files[i].preview, id:images.files[i].name, title:images.files[i].name, addDeleteButton:false}) );
					else {
						self._replaceWithItem({file:images.files[i]});
					}
					totalImages ++;
				}
			};

			for (var i = 0; i < images.urls.length; i++) {
				
				if(images.urls[i].added==undefined||images.urls[i].added==false) {
					images.urls[i].added = true;
					if(!self.options.replace) self._previewTarget.prepend( self.makeDefaultPreviewItem({src:images.urls[i].src, id:images.urls[i].src, title:images.urls[i].src, addDeleteButton:false}) );
					else {
						self._replaceWithItem({url:images.urls[i]});
					}
					totalImages ++;
				}
			};

			for (var i = 0; i < images.blobs.length; i++) {
				
				if(images.blobs[i].added==undefined||images.blobs[i].added==false) {
					images.blobs[i].added = true;
					if(!self.options.replace) self._previewTarget.prepend( self.makeDefaultPreviewItem({src:images.blobs[i].blob.preview, id:images.blobs[i].blob.name, title:images.blobs[i].blob.name, addDeleteButton:false}) );
					else {
						self._replaceWithItem({blob:images.blobs[i]});
					}
					totalImages ++;
				}
			};

			self.closePopover();
			
			console.log("Uploading ("+totalImages+") images");
		},

		// -------------------------------------
		_submit: function() 
		{

			var images = this.getImages();
		

			var self = this;
			var prefix = self.options.prefix;
			var fd = new FormData();
				fd.append('type', this.options.type);
				fd.append('id', this.options.id);
				fd.append('path', this.options.path);
				fd.append('multiple', this.options.multiple);
				fd.append('replace', this.options.replace);
			
			// const ASSET_RIGHTS_USER_OWENED      = 1;//"assets.rights.user.owned";
			// const ASSET_RIGHTS_NOT_USER_OWENED  = 2;//"assets.rights.not.user.owned";
			// const ASSET_RIGHTS_UNKNOWN          = 3;//"assets.rights.unknown";
			var rights = self._popover.find('[name="rights"]').is(':checked') == true ? 1 : 2;
			fd.append('rights', rights);

			var totalImages = 0;
			for (var i = 0; i < images.urls.length; i++) {
				if(!images.urls[i].added) { 
					fd.append(prefix+'urls[]', images.urls[i].src);
					totalImages ++;
					images.urls[i].added = true;
					if(!self.options.replace) {
						self._previewTarget.prepend( self.makeDeletePreviewItem({id:images.urls[i].src, title:images.urls[i].src, addDeleteButton:false}) );
					}
					else {
						self._replaceWithItem({url:images.urls[i]});
					}
				}
			};
			for (var i = 0; i < images.blobs.length; i++) {
				if(!self.options.replace && !images.blobs[i].added) {
					fd.append(prefix+'files[]', images.blobs[i].blob, images.blobs[i].blob.name);
					totalImages ++;
					images.blobs[i].added = true;
					self._previewTarget.prepend( self.makeDeletePreviewItem({id:images.blobs[i].blob.name, title:images.blobs[i].blob.name, addDeleteButton:false}) );
				}
			};
			for (var i = 0; i < images.files.length; i++) {
				if(!images.files[i].added) {
					fd.append(prefix+'files[]', images.files[i]);
					totalImages ++;
					images.files[i].added = true;
					if(!self.options.replace) {
						self._previewTarget.prepend( self.makeDeletePreviewItem({id:images.files[i].name, title:images.files[i].name, addDeleteButton:false}) );
					}
					else {
						self._replaceWithItem({file:images.files[i]});
					}
				}
			};
			
			
			console.log("Uploading ("+totalImages+") images");
			
			fd.append('_method', self.options.method);	
			fd.append('property', self.options.property);	

			// setup preview
			self.closePopover();
		
			/*
			var $listItem;
			if(self.options.replace) {
				$listItem = self._previewTarget.find('li').first();
				// console.log($listItem);
			}

			// then create a preview list item
			else {
				$listItem = $(self.makeDeletePreviewItem());
				self._previewTarget.prepend($listItem);
				//$listItem = self._previewTarget.find('li').first(); 
			}

			console.log($listItem);

			// now we are uploading...
			if($listItem.length) {

				$listItem.addClass('list-group-item-info');
				$right = $listItem.find('.remove-photo a').hide();
				$listItem.find('.uploading-message').remove();
				$listItem.find('.remove-photo').prepend('<span class="uploading-message">Uploading...</span>');
			}*/
			 
			$.ajax({
				url: this.options.uploadURL,
				type: 'POST',
				dataType: 'json',
				data:fd,
				enctype: 'multipart/form-data',
				processData: false,
				contentType: false,
			})
			.always(function(e) {
				
				console.log(e);
				
				if(e.response.files!==undefined) {
					
				
					for (var i = 0; i < e.response.files.length; i++) {

						var file = e.response.files[i];
						var name = file.org_filename;
						// console.log('Remove Item: '+name);

						self._removeItem(name);
						var src = file.base_url+'/s40.'+file.extension+'reaload='+new Date().getTime();
						var $item = self._previewTarget.find('li[data-name="'+name+'"]').first();

						
						if(self.options.replace == true) {
							$item = self._previewTarget.find('li').first();
						}
		
						if($item.length) {
							$item.find('.remove-photo a').show();
							$item.find('.uploading-message').remove();

							// remove the org filename and set the data-id
							$item.removeAttr('data-name');
							$item.attr('data-id', file.id);

							var $removeBtn = $item.find('.uploading');
							$removeBtn.removeClass('uploading');
							$removeBtn.addClass('remove-photo');
							
							$item.find('.delete-photo-btn').attr('data-id', file.id);

							// update the name and data/src
							$item.find('.file-title').html(file.filename);
							$item.find('a[data-name]').attr('data-name', file.filename)
							$item.find('.photo img').fadeTo(200, 0, function() {
								 var $img = $(this);
								 $(this).attr('src', src).load(function() {
								 	$img.fadeTo(200, 1);
								 });
							});
							$item.removeClass('list-group-item-info');
							$item.addClass('list-group-item-success');

							setTimeout(function() {
								$item.removeClass('list-group-item-success');
							}, 2000);

						}	
						
					};
					
					

					
				}

			var images =self.getImages();
				console.log(images.urls.length);

				self._trigger("uploadComplete", self, e);
			});
		
			this._trigger("added", this, images);
		},

		// -------------------------------------
		_setupDropZone: function() 
		{	
			var self = this;

			if(self._dropzone == null) {
				self._dropzone = new Dropzone($dropzone.get(0), {
					url: "/",
					acceptedFiles:'image/*',
					autoProcessQueue:false,
					maxFiles:self.options.multiple?null:1,
					addedfile: function(file) {
						if(self._canAddFile(file)) {
							var reader = new FileReader();
	    						reader.onload = (function(file) { 
    								return function(e) {
    									file.preview = e.target.result;
    									self._addFile(file);
    									self._itemAdded({blob:file});
    									self._createPopoverPreview({src:e.target.result, title:file.name});
    								}
	    						})(file);

	    					reader.readAsDataURL(file);  
						}
						else {
							self._showMessage("This file is already been added");
							self._trigger("error", self, {errors:["Image already added."]});
    					}
				  	}
				});
			}
		},

    	// -------------------------------------
		_create: function() {
			var self = this;

			if(this.element.length == 0) {
				throw new Error('missing target');
				return;
			}
			  			
			this.options.multiple = this.element.data('multiple')!==undefined ? this.element.data('multiple') : this.options.multiple;
			this.options.path = this.element.data('path')!==undefined ? this.element.data('path') : this.options.path;
			this.options.property = this.element.data('property')!==undefined ? this.element.data('property') : this.options.property;
			this.options.id = this.element.data('item-id')!==undefined ? this.element.data('item-id') : this.options.id;
			this.options.type = this.element.data('type')!==undefined ? this.element.data('type') : this.options.type;
			this.options.uploadOnSubmit = this.element.data('upload-on-submit')!==undefined ? this.element.data('upload-on-submit') : this.options.uploadOnSubmit;
			this._replaceTarget = this.element.data('replace-target')!==undefined ? $(this.element.data('replace-target')) : null;
			
			console.log(this.options);
			// -------------------------------------
			// Delete Assets
			// -------------------------------------
			$(document).on('click', '.delete-photo-btn', function(e) {
				e.preventDefault();
				console.log('delete-photo-btn');
				var c = confirm("Are you sure you want to delete this image?");
				if(c) {
					$(this).deleteAsset({
						onComplete:function(e) {
							document.location.reload();
							// if(self._previewTarget.find('li').length == 0) {
								
							// }
							// self._previewTarget.find('li[data-id="'+e.id+'"]').fadeOut(200, function() {
							// 	$(this).remove();
							// });
						},
					});
				}
			});


  			document.onpaste = function(e) {

				if(self.element.is(':focus') || self.popoverVisible) {
					var result = false,
					    clipboardData = e.clipboardData,
					    items;

					if (typeof clipboardData === "object") {
						items = clipboardData.items || clipboardData.files || [];
						
						for (var i = 0; i < items.length; i++) {
					    	var item = items[i];
					    	if(self._isFileAllowed(item)) {
					    		self._handlePastFile(item.getAsFile());
					    	}
					    	else {
					  			self._handlePastString(item);
					    	}
					   	}
					}

					if (result) { e.preventDefault(); }

					return result;
				}
  			};

  			// remove item for x click 
  			$(document).on('click', '.remove-filelist-btn', function(event) {
  				event.preventDefault();
  				var name = $(this).data('name');
  				if(name) {
  					self._removeListItem(name);
  				}
  			});

			// close popovers when clicked outside...
			/*$('body').on('click', function (e) {
				$popover = self._popover;
				if ($popover && !$popover.is(e.target) && $popover.has(e.target).length === 0 && $popover.has(e.target).length === 0 && self.popoverVisible) {
					self._popover.popover('hide');
				}
			});*/


			this._previewTarget = $(this.element.data('preview-target'));
			if(this._previewTarget.length == 0) {
				console.log('*** missing preview targe ***');
			}

			this.element.popover({
				container:'body',
				content:'add image',
				html:true,
				title:function() {
					return self.options.moduleTitle+' <a href="#close-upload-modal"><span class="pull-right glyphicon glyphicon-remove close-upload-modal"></span></a>';
				},
				placement:'top',
				template:'\
						<div class="popover image-upload-popover" role="tooltip">\
							<div class="arrow"></div>\
							<div class="popover-title">\
								<h5>Add Images</h5>\
							</div>\
							<div class="upload-popover-content">\
								<form action="/file-upload" class="dropzone">\
									<div class="fallback">\
										<input name="files" type="file" />\
									</div>\
									<div class="dz-message info">\
										<span class="glyphicon glyphicon-picture">\
										<small class="text-muted">Drop '+(self.options.multiple?'files':'file')+' here or click to browse.</small>\
									</div>\
									<div class="dz-message loading">\
										<small class="text-muted">Loading...</small>\
									</div>\
								</form>\
								<div class="form-group url-input-container">\
									<input type="text" class="form-control url-input" placeholder="http://image.png">\
							      	<small class="help-block text-muted">Tip: just paste URL or Image</small>\
								</div>\
								<div class="form-group submit-container">\
									<div class="checkbox">\
								      <label class="small-label">\
								        <input type="checkbox" name="rights"> Do you own this photo?\
								      </label>\
								    </div>\
								    <button class="form-control submit-image-button">Submit</button>\
								    <p class="help-block text-center"></p>\
								</div>\
							</div>\
						</div>',

			})
			.on('hidden.bs.popover', function() {
				self.popoverVisible = false;
				self._removePopoverImagePreview();
			})
			.on('shown.bs.popover', function () {
				self.popoverVisible = true;
				self._popover = $("#"+$(this).attr('aria-describedby'));
				self._urlInput = self._popover.find('.url-input');
				self._helpBlock = self._popover.find(".help-block");
				$dropzone = self._popover.find('.dropzone');
				
				$webImageBtn = $(".upload-web-image-btn");
				$webImageBtn.click(function(e) {
					e.preventDefault();
					var src = $(this).parent().parent().find('input').val();
					if(src != "" && self._canAddURL(src)) {
						self._addURLImagePreview({image:src, title:src});
					}
				});

				$(".close-upload-modal").click(function(e) {
					e.preventDefault();
					self._popover.popover('hide');
				});

				$(".submit-image-button").attr('disabled', true);
				
				$(".submit-image-button").off('click'); // this is very important else will fire twice.
				$(".submit-image-button").click(function(e) {
					e.preventDefault();
					if(self.options.id == null) {
						self._submitStandy();
					}
					else {
						self._submit();
					}
				});

				self._setupDropZone();
					
			});
    	},
    });

}(jQuery));















