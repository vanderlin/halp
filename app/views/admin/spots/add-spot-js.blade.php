
<!-- this is the boostrap form validation -->
<link rel="stylesheet" href="{{bower('components-font-awesome/css/font-awesome.min.css')}}" />
<link rel="stylesheet" href="{{bower('bootstrapvalidator/dist/css/bootstrapValidator.min.css')}}"/>
<script type="text/javascript" src="{{bower('twitter-text/twitter-text.js')}}"></script>
<script type="text/javascript" src="{{bower('bootstrapvalidator/dist/js/bootstrapValidator.min.js')}}"></script>

<script type="text/javascript">
	var spot = null;
	var tags = [];
	@if (isset($spot))
		spot = {{$spot->toJson()}};
    	tags = {{$spot->getSelect2Tags()}};
	@endif
</script>


<script type="text/javascript">
	$(document).ready(function($) {
	
		var minFileSize = 786432; 		// 1024 x 768
		var maxFileSize = 256000000;	//7990272;   // 3264 × 2448 (OFF FOR NOW)
	
		// -------------------------------------
		var $charcount   = $(".char-count");
		var $description = $("#spot-description-textarea");
    	var $tagsSearch  = $("#tags-search");
		var $form        = $("#spot-form");
		
		// -------------------------------------
		$("#spot-publish-btn").attr('disabled', !spot);
		$("#spot-save-btn").attr('disabled', false);

	
		// -------------------------------------
		// Google Location Finder
		// -------------------------------------
		$("#location-finder").googleLocationFinder(
		{
			map:"google-map-finder",
			height:400,
			spot:spot,
			showAddButton:false,
			getNoAvialablePaceWindowContent:function(place, spot) {
				console.log(spot);
				var content = '<div class="google-content text-center">\
	                                <h4><a href="'+spot.url+'">'+place.name+'</a></h4>\
	                                <div class="address"><a href="/spots/'+spot.id+'">'+place.name+'</a> has already been created as a spot</div>';
	                content +=      '<div><a href="'+spot.url+'" class="btn btn-default btn-xs">view spot</a></div><br>';
	                content += '</div>';
	            return content;  
			},
			onNotAvailable:function(event, data) {
				$("#location-finder").val('');
			},
			onNewLocation:function(event, data) {
				updateSpotForm(data.place);
			},
		});

		// --------------------------------	
		// Validating the form
		// --------------------------------
	    $('#spot-form').bootstrapValidator({
	        feedbackIcons: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        },
	        fields: {
	        	name: {
	        		message: 'The name is not valid.',
	                validators: {
	                    notEmpty: {
	                        message: 'The name is required and cannot be empty'
	                    },
	                }
	        	},
	        	its_a: {
	        		message: 'This is not a valid entry.',
	                validators: {
	                    notEmpty: {
	                        message: 'This field is required and cannot be empty'
	                    },
	                }
	        	},
	        	description: {
					message: 'This is not a valid entry.',
	                validators: {
	                    notEmpty: {
	                        message: 'This description is required and cannot be empty'
	                    }
	                }
	        	},
	        	'category[]': {
	        		validators: {
	                    choice: {
	                        min: 1,
	                        message: 'Please choose at least one category'
	                    }
	                }
	            },
	            'files[]': {
	                validators: {
	                    file: {
	                    	maxFiles:4,
	                    	minFiles:1,
	                        extension: 'jpeg,png,jpg',
	                        type: 'image/jpeg,image/png',
	                        message: 'The selected file is not valid'
	                    },
	                    /*file: {
	                    	maxSize: maxFileSize,   // 3264 × 2448
	                    	message: 'The image is to large.'
	                    },
	                    file: {
	                    	minSize: minFileSize, // 1024 x 768
	                    	message: 'The image is to small. Min size 1024x768'
	                    }
	                    */

	                }
	            },

	        }
	    })
		.on('error.validator.bv', function(e, data) {
        	console.log("Hide Publish Button");
			$("#spot-publish-btn").attr('disabled', true);
    	})
    	.on('success.validator.bv', function(e, data) {
    		updatePostButtons();
    	})
    	.on('success.field.bv', function(e, data) {
    		
    		if(data.field == 'files[]') {
    			var element = data.element.context;
    			addPreviewImages(element.files);
    		}
            // Remove the field messages
            // $('#errors').find('li[data-field="' + data.field + '"]').remove();
        });

    	// -------------------------------------
    	function updatePostButtons() 
    	{
    		$("#spot-publish-btn").prop('disabled', !canPublishspot());
    		$("#spot-save-btn").prop('disabled', !canSaveSpot());
    	}

    	// -------------------------------------
    	function fillOutForm(place) 
    	{
    		
    		
			var jsonPlace = JSON.stringify(place);
			$("#spot-form input[name='location_name']").attr("value", place.name);
			$("#spot-form input[name='place_id']").attr("value", place.place_id);
			$("#spot-form input[name='location_lat']").attr("value", place.geometry.location.lat());
			$("#spot-form input[name='location_lng']").attr("value", place.geometry.location.lng());
			$("#spot-form input[name='details']").attr("value", jsonPlace);
			$("#spot-form input[name='name']").attr("value", place.name);
			
			if(place.types.length>0) {
				var type = place.types[0].replace("_", " ");
				if(type == 'establishment') type = 'Surf Spot'; 
				$("#spot-form input[name='its_a']").attr("value", type);	
			}
						
		}

    	// -------------------------------------
		function updateSpotForm(place) 
		{
			
			fillOutForm(place);
			$("#spot-save-btn").attr('disabled', !canSaveSpot());
			// if(spot && !e.available) {
			// 	if(spot.id == e.spot.id) e.available = true;
			// }
		}


        // -------------------------------------
        // Description Textarea
        // -------------------------------------
        function updateDescriptionCount() 
        {

        	//255 characters left
			var text = $('<div />', { html: $description.val() }).text();
			/*var totalURLStr = "";
			var urls = twttr.txt.extractUrls(text);

			for (var i = 0; i < urls.length; i++) {
				totalURLStr += urls[i];
			};*/		

			var nChars = text.length;
			var count  = (255-nChars);
			if(count < 0) {
				$charcount.addClass('pink-color');	
				$charcount.removeClass('success');
			}
			else {
				$charcount.removeClass('pink-color');
				$charcount.addClass('success');
			}
			$charcount.html(count+' characters left');      	
        }
        $("#spot-description-textarea").keydown(function(event) 
        {
        	updateDescriptionCount();
		});
		updateDescriptionCount();

		// -------------------------------------
		// Photos
		// -------------------------------------
		$("#photos-upload-btn").imageUpload({
			type:'Spot',
			id:spot?spot.id:null,
			path:'assets/content/spots',
			replace:false,
			error: function(e, data) {
				console.log(data);
				$("#form-status").formStatus(data);
			},
			imageNotValid:function(e, data) {
				$("#form-status").formStatus({errors:["The image is not valid"]});
			},
			/*
			createPreviewItem:function(e, data) {
				var item;

				if(spot) {
					item ='\
					<li class="list-group-item list-group-item-info"" data-id="'+data.title+'">\
						<div class="pull-right uploading">\
		                	<div class="">uploading...</div>\
		                </div>\
		                <div class="file-image">\
	                		<a href="'+data.image+'" class="lightbox-link">\
		                		<img width="40px" src="'+data.image+'">\
		                	</a>\
		                </div>\
						<div class="file-title hidden-md">'+data.title+'</div>\
					</li>';
				}
				else {
					
					item = data.previewItem;
				}
				data.previewTarget.prepend(item);
			},*/
			uploadComplete:function(e, data) {
				$("#form-status").formStatus({notice:["Image Uploaded"]});
			}

			/*
			added:function(e, data) {
				console.log(data);

				var fd = new FormData();
					fd.append('type', 'Spot');
					fd.append('id', spot.id);
					fd.append('path', 'assets/content/spots');

				if(data.url!==undefined) {
					fd.append('urls[]', data.url);	
				}
				else if(data.blob!==undefined) {
					fd.append('files[]', data.blob, data.blob.name);	
				}
				else if(data.file!==undefined) {
					fd.append('files[]', data.file);	
				}
				else {
					console.log("Looks like there is not file to upload");
					return;
				}
				
			
				$.ajax({
					url: '/assets/upload',
					type: 'POST',
					dataType: 'json',
					data:fd,
					enctype: 'multipart/form-data',
					processData: false,
					contentType: false,
				})
				.always(function(e) {
					console.log("complete", e);
				});
				
				
				postSpot({
					reload:false,
					onComplete:function(evt) {
						
						for (var i = 0; i < evt.images.files.length; i++) {
							var file = evt.images.files[i];
							var $item = data.previewTarget.find('li[data-id="'+data.title+'"]');
							if($item.length) {
								$item.attr('data-id', file.id);
								$item.addClass('list-group-item-success');
								var $up = $item.find('.uploading');
								$up.removeClass('uploading');
								$up.addClass('remove-photo');
								$up.html('<a href="#delete-photo" data-id="'+file.id+'" data-target="#photos-filelist" class="pull-right btn btn-default btn-xs file-list-right-btn delete-photo-btn">delete</a>');								
							}
						};
					}
				});
			}*/
		});

    	// -------------------------------------
    	// Tags
    	// -------------------------------------
		$tagsSearch.select2({
			placeholder: "Search for a repository",
    		multiple:true,
			ajax: {
	        	url: function(term) {
	        		console.log(term);
	        		return "/api/search/tags/"+term;
	        	},
        	 	type: "GET",
	        	dataType: 'json',
	        	quietMillis: 250,
	        	results: function (data) {
	        		console.log("data",data);
	        		var results = [];
	        		if(data.status==200 && data.results.length>0) {
	            	
		        		$.each(data.results, function(index, item){
		        			console.log(item);
		        			item.text = item.name;
			            	results.push(item);
				    	});
	        		}
     				return { results: results };
        		},
	        	

    		},

    	});
		$tagsSearch.select2("data", tags);

		// -------------------------------------
		// Can Save a Spot
		// -------------------------------------
		function canSaveSpot() {
			var requiredFileds = [	"name", 
									"place_id", 
									"user_id",
									"location_name",
									"location_lat",
									"location_lng"];

			for (var i = 0; i < requiredFileds.length; i++) {
				if($( "input[name='"+requiredFileds[i]+"']").val().length == 0) return false;
			};
			return true;
		}

		// --------------------------------
		// Can Publish a Spot
		// --------------------------------
		function canPublishspot() {
			var requiredFileds = ["its_a"];
			// if($( "input[name='files[]']").val().length == 0 && urls.length==0)  return false;
			if($( "input[name='category[]']:checked").length == 0)  return false;
			if($( 'textarea[name="description"]').val().length == 0) return false;					

			if(canSaveSpot() == false) return false;						
			for (var i = 0; i < requiredFileds.length; i++) {
				if( $( "input[name='"+requiredFileds[i]+"']").val().length == 0) return false;
			};
			return true;
		}	

		// -------------------------------------
		function postSpot(options) {

			var options = $.extend({
				reload:true
			}, options);


			if(options.element!==undefined) options.element.prop('disabled', true);

			var images = $("#photos-upload-btn").imageUpload('getImages');
			var url = $form.attr('action'); 
			var fd  = new FormData($form[0]);

			$("#photos-upload-btn").imageUpload('appendImages', fd);

			/*var descriptionLength = $description.val().length;
			if(descriptionLength > 255) {
				$("#form-status").formStatus({errors:["The description is too long. Must be less than 255 characters."]});
				options.element.prop('disabled', false);
				return;
			}*/

			if($('#spot-form [name="tags[]"]').val() == null) {
				fd.append('tags[]', null);
			}

			if(options.status!=undefined) {
				fd.append('status', options.status);
			}

			if(options.photo_ids!=undefined) {
				for (var i = 0; i < options.photo_ids.length; i++) {
					fd.append('photo_ids[]', options.photo_ids[i]);
				};
			}
			
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data:fd,
				processData: false,
				contentType: false
			})
			.always(function(e) {
				console.log(e);
				
				$("#form-status").formStatus(e);
				
				if(options.element!==undefined) {
					options.element.prop('disabled', false);
				}
				if(e.status == 200 && e.spot != undefined) {
					if(e.spot.status == 'Publish') {
						if(options.reload == true)  document.location = e.spot.url;	
					}
					else if(e.spot.status == 'Draft') {
						if(options.reload == true) document.location = '/admin/spots?success='+e.spot.id+"#drafts";	
					}
				}
				if(options.onComplete) {
					options.onComplete(e);
				}
			});

			$("#photos-upload-btn").imageUpload('removeImages');
		}

		// -------------------------------------
		function disableButtons() {
			$("#spot-publish-btn").addClass('disabled');
			$("#spot-save-btn").addClass('disabled');
		}

		// -------------------------------------
		function enableButtons() {
			$("#spot-publish-btn").removeClass('disabled');
			$("#spot-save-btn").removeClass('disabled');
		}

		
		// --------------------------------
		// Publish/update Button
		// --------------------------------
		$("#spot-publish-btn").click(function(e) {
			e.preventDefault();
			postSpot({element:$(this), status:'Publish'});
		});
	
		// -------------------------------------
		// Draft Button
		// -------------------------------------
		$("#draft-btn").click(function(e) {
			e.preventDefault();
			postSpot({element:$(this), status:'Draft'});
		});

		// --------------------------------
		// Delete Spot
		// --------------------------------
		$('#spot-delete-btn').click(function(e) {
			e.preventDefault();
			var id = $(this).data('id');
			var really = confirm("Are you sure you want to delete this spot");
			if(really) {
				$.ajax({
					url: '/admin/spots/'+id,
					type: 'POST',
					dataType: 'json',
					data: {_method:'DELETE'},
				})
				.done(function(evt) {
					if(evt.spot && evt.url) {
						document.location = '/admin/spots?success='+id+"#trashed";	
					}
				}).
				fail(function(evt) {
					$("#form-status").formStatus(evt);
				});
			}

		});

		// --------------------------------
		// Preview Spot
		// --------------------------------
		$("#preview-btn").click(function(e) {
			e.preventDefault();
			var id = $(this).data('id');
			postSpot({
				element:$(this), 
				onComplete:function(e) {
					document.location = e.spot.url+"?preview_id="+e.spot.id;
				}
			});
		});
		
		@if (isset($spot))
		// -------------------------------------
		// photos sortable
		// -------------------------------------
		if($("#photos-filelist").length) {
			$("#photos-filelist").sortable({
				axis: 'y',
				stop:function(e, ui) {
		        	var children = $(this).find('li');
				 	var ids = [];
					for (var i = 0; i < children.length; i++) {
						var id = parseInt($(children[i]).attr('data-id'));
						ids.push(id);
					};
					postSpot({element:$(this), photo_ids:ids, reload:false});
		        }
			});	
		}	
		@endif

		// -------------------------------------
	    /*$description.wysihtml5({
	    	toolbar: {
			    "font-styles":  false, 	//Font styling, e.g. h1, h2, etc. Default true
			    "emphasis": 	false, 	//Italics, bold, etc. Default true
			    "lists": 		false, 	//(Un)ordered lists, e.g. Bullets, Numbers. Default true
			    "html": 		true, 	//Button which allows you to edit the generated HTML. Default false
			    "link": 		true, 	//Button to insert a link. Default true
			    "image": 		false, 	//Button to insert an image. Default true,
			    "color": 		false, 	//Button to change color of font  
			    "blockquote": 	false, 	//Blockquote  
			    "size": "sm"			//<buttonsize> //default: none, other options are xs, sm, lg
			},
			events: {
        		change_view: function(e) { 
            		if(e == 'textarea') {
            			$description.addClass('textarea-higlighter');
            		}
            		else {
            			$description.removeClass('textarea-higlighter');	
            		}
        		},
    		}
	    })*/
	    
		// --------------------------------
		// Restore Spot
		// --------------------------------
		$(document).on('click', '#spot-restore-btn', function(e) {
			e.preventDefault();
			postSpot({element:$(this), status:'Draft'});
		});

		// -------------------------------------
		$(".reload-google-details").click(function(event) {
			event.preventDefault();
			var id = $(this).attr('data-id');
			$.ajax({
					url: '/admin/locations/'+id+'/details',
					type: 'POST',
					dataType: 'json',
					data: {_method:'PUT'},
				})
				.done(function(evt) {
					console.log("success", evt);
					showSuccessMessage(["Google Location Data has been reloaded"]);
					setTimeout(function() {
						document.location.reload();
					}, 200);
				}).
				fail(function(evt) {
					console.log("error", evt);
					showErrorMessage(["There was an error updating Google Location Data"]);
				});
		});

		// -------------------------------------
		$('body').on('hidden.bs.modal', '.modal', function () {
		    $(this).removeData('bs.modal');
		});

	});
</script>






























