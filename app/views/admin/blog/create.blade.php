@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Create New Blog Post
@stop



@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {

		$(document).on('keydown', '#post-textarea', function(e) {
			
			var keyCode = e.keyCode || e.which; 
			if (keyCode == 9) { 
			    e.preventDefault(); 
			    var start = $(this).get(0).selectionStart;
			    var end = $(this).get(0).selectionEnd;

			    // set textarea value to: text before caret + tab + text after caret
			    $(this).val($(this).val().substring(0, start)
			                + "\t"
			                + $(this).val().substring(end));
			    // put caret at right position again
			    $(this).get(0).selectionStart = 
			    $(this).get(0).selectionEnd = start + 1;
			} 

		});

		$form = $("#blog-form");
		

		// -------------------------------------
		var users = {{$users->toJson()}};
		$("#author-selector").select2();

		// -------------------------------------
		$(".hero-image-upload").imageUpload({
			multiple:false,
			dataType:'Post',
			dataID:{{isset($post) ? $post->id:'null'}}, 
			dataPath:'assets/content/blog',
			uploadOnAdd:{{isset($post) ? 'true':'false'}},
			error: function(e, data) {
				$("#form-status").formStatus(data);
			},
		});
		
		// -------------------------------------
		function createPost(options) {
			options.element.prop('disabled', true);
			var images = $(".hero-image-upload").imageUpload('getImages');
			
			console.log(images);

			var fd  = new FormData($form[0]);

			if(images.urls.length>0) {
				fd.append('hero_file_url', images.urls[0]);
			}
			else if(images.blobs.length>0) {
				fd.append('hero_file', images.blobs[0], images.blobs[0].name);
			}
			else if(images.files.length>0) {
				fd.append('hero_file', images.files[0]);
			}
			
			if(options.status!=undefined) {
				fd.append('status', options.status);
			}

			var date = $("#post-date").data('date-edit');
			if(date!==undefined) {
				fd.append('created_at', date);	
			}
			
			
			var url = $form.attr('action'); 
			$.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data:fd,
				processData: false,
				contentType: false
			})
			.always(function(e) {
				$("#form-status").formStatus(e);
				if(options.element.attr('id')=='post-restore-btn') {
					document.location = '/admin/blog/post/'+e.post.id;
				}
				options.element.prop('disabled', false);
				if(options.onComplete) {
					options.onComplete(e);
				}
			});
		}

		// -------------------------------------
		$("#publish-btn").click(function(e) {
			e.preventDefault();
			var $self = $(this);
			createPost({
				element:$(this), 
				status:'Publish', 
				onComplete:function(e) {
					document.location = e.url;
				}
			});
		});
		$("#draft-btn").click(function(e) {
			e.preventDefault();
			createPost({
				element:$(this), 
				status:'Draft',
				onComplete:function(e) {
					document.location = '/admin/blog/post/'+e.post.id;
				}
			});
		});
		$("#post-restore-btn").click(function(e) {
			e.preventDefault();
			var id = $(this).attr('data-id');
			var really = confirm("Are you sure you want to restore this blog post");
			if(really) {
				createPost({element:$(this), status:'Draft'});
			}

		});

		$("#preview-btn").click(function(e) {
			e.preventDefault();
			var id = $(this).data('id');
			createPost({
				status:'Preview',
				element:$(this), 
				onComplete:function(e) {
					document.location = e.url+"?preview_id="+e.post.id;
				}
			});
		});

		$("#delete-button").click(function(e) {
			e.preventDefault();

			if(confirm('Are you sure you ?')) {
				var id = $(this).data('id');
				$.ajax({
					url: '/admin/blog/post/'+id,
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(e) {
					if(e.status == 200) {
						document.location = "/admin/blog";
					}
				})
				.fail(function(e) {
					console.log("error", e);
				})	
			}
		});

		// -------------------------------------
	    $('#post-textarea').wysihtml5({
	    	toolbar: {
			    "font-styles": true, 	//Font styling, e.g. h1, h2, etc. Default true
			    "emphasis": true, 		//Italics, bold, etc. Default true
			    "lists": true, 			//(Un)ordered lists, e.g. Bullets, Numbers. Default true
			    "html": true, 			//Button which allows you to edit the generated HTML. Default false
			    "link": true, 			//Button to insert a link. Default true
			    "image": false, 		//Button to insert an image. Default true,
			    "color": false, 		//Button to change color of font  
			    "blockquote": true, 	//Blockquote  
			    "size": "sm"			//<buttonsize> //default: none, other options are xs, sm, lg
			},
			parserRules: {
				classes: {
					"img-responsive":1,
				},
				tags: {
					"a":  {
                		check_attributes: {
                        'href': 'alt', // important to avoid XSS
                        'target': 'alt',
                        'rel': 'alt'
                    	}
                	},
                },
			},
			events: {
        		change_view: function(e) { 
            		if(e == 'textarea') {
            			$("#post-textarea").addClass('textarea-higlighter');
            		}
            		else {
            			$("#post-textarea").removeClass('textarea-higlighter');	
            		}
        		},
    		}
	    })

	    // -------------------------------------
	    $("#post-date").datetimepicker({
	    	defaultDate:"{{isset($post) ? $post->created_at : Carbon\Carbon::now()}}",	
	    	showTodayButton:true,
	    });
	    
	    $("#post-date").datetimepicker().on("dp.change",function (e) {
	    	$("#post-date").attr('data-date-edit', e.date.format('YYYY-MM-DD HH:mm:ss'));
        });
	    

	});
</script>	
@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">
  {{-- Carbon\Carbon::createFromFormat('m/dd/yyyy g:i A', '02/28/2015 6:19 AM'); --}}
  {{ isset($post)?$post->title:'Create new post'}}</h2>

	{{ Form::open(['route'=>isset($post)?['admin.blog.update', $post->id]:'admin.blog.store', 'id'=>'blog-form', 'method'=>isset($post)?'PUT':'POST']) }}
	<div class="row">

		<div class="col-md-8">
			<div class="form-group">
		  		<input class="form-control" placeholder="Post Title" name="title" value="{{isset($post)?$post->title:''}}">
		  	</div>

		  	<div class="form-group">
		  		<textarea class="form-control textarea-editor" id="post-textarea" type="text" name="body" rows="10" placeholder="Write your post here...">{{isset($post)?$post->body:''}}</textarea>
		  	</div>

		  	<div class="form-group">
		  		<label>Excerpt</label>
		  		<textarea class="form-control textarea-editor" type="text" name="excerpt" rows="3" placeholder="Optional">{{isset($post)?$post->excerpt:''}}</textarea>
		  	</div>
			
			
			<div class="text-center" id="form-status">
				@include('site.partials.form-errors')
			</div>

		</div>

		<div class="col-md-4">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Publish</h3>
				</div>
				<div class="panel-body" id="tags-panel">
					<div class="row">
						<div class="col-md-12">
							<div class="pull-left">
								<button class="btn btn-default" id="draft-btn">{{isset($post)&&$post->status!='Draft'?'Save as Draft':'Save Draft'}}</button>
							</div>
							<div class="pull-right">
								<button class="btn btn-default" id="preview-btn" data-id="{{isset($post)?$post->id:''}}">Preview</button>
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="pull-left">
								@if (isset($post) && $post->isTrashed())
									<a id="post-restore-btn" data-id="{{$post->id}}" class="btn btn-info btn-default" data-id="{{isset($post)?$post->id:''}}">Remove from Trash</a>
								@else 
									<a class="btn btn-default btn-danger" {{isset($post)?'':'disabled'}} id="delete-button" data-id="{{isset($post)?$post->id:''}}">Delete</a>
								@endif
							</div>
							<div class="pull-right">
								<button class="btn btn-default btn-success" id="publish-btn">{{(isset($post)&&$post->status!='Draft') ? 'Update' : 'Publish'}}</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Date</h3>
				</div>

				<div class="panel-body" id="date-panel">
					<div class="form-group">
		                <div class='input-group date' id='post-date'>
		                    <input type='text' class="form-control"/>
		                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                    </span>
		                </div>
            		</div>
				</div>
			</div>	

			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Post Type</h3>
				</div>
				<div class="panel-body" id="tags-panel">
					<select class="form-control" name="post_type">
					@foreach ($postTypes as $type)
						<option value="{{$type->id}}" {{isset($post)&&$post->post_type_id==$type->id?'selected':''}}>{{ ucfirst($type->name) }}</option>
					@endforeach
					</select>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Tags</h3>
				</div>

				<div class="panel-body" id="tags-panel">
					<?php $tagids = isset($post) ? $post->tagIds() : [];?>
		  			<select class="form-control auto-select" name="tags[]" multiple="multiple">
						@foreach ($tags as $tag)
							<option value="{{$tag->id}}" {{in_array($tag->id, $tagids)?'selected':''}}>{{$tag->name}}</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Hero Image</h3>
				</div>
				
				@if (empty($post) || (isset($post) && $post->hasHero()==false))
					<div class="panel-body" id="hero-panel">
						<button class="btn btn-default hero-image-upload" type="button" data-preview-target="#hero-filelist">Add Image</button>
					</div>
				@endif


				<!-- List group -->
				<ul class="list-group files-list" id="hero-filelist">
					@if (isset($post) && $post->hasHero())
						{{-- //#hero-filelist --}}
						@include('admin.partials.photo-list-item', array('id' => $post->id, 'image'=>$post->heroPhoto, 'title'=>$post->heroPhoto->getName(), 'multiple'=>false, 'target'=>'hero-image-upload', 'preview'=>'#hero-filelist'))
						{{--<li class="list-group-item">
							<div class="pull-right remove-photo">
			                	<a href="#remove-photo" data-name="{{$post->heroPhoto->filename}}" class="pull-right btn btn-default btn-xs hero-image-upload file-list-right-btn" data-preview-target="#hero-filelist">replace</a>
			                </div>
			                <div class="file-image">
		                	<a href="{{$post->heroPhoto->url('w1024', false)}}" class="lightbox-link">
			                		<img width="40px" src="{{$post->heroPhoto->url('s40')}}">
			                	</a>
			                </div>
							<div class="file-title hidden-md">{{$post->heroPhoto->filename}}</div>
						</li>--}}
					@endif
				</ul>
				
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Hero URL</h3>
				</div>

				<div class="panel-body" id="hero-link-panel">
					<div class="form-group">
		  				<input class="form-control" placeholder="http://localsonly.ideo.com/spot" name="hero_url" value="{{isset($post)?$post->hero_url:''}}">
		  			</div>					
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					 <h3 class="panel-title">Author</h3>
				</div>

				<div class="panel-body" id="author-panel">
					<div class="form-group">
						<select class="form-control" id="author-selector" name="user">
							@foreach ($users as $user)
								@if (isset($post))
									<option value="{{$user->id}}" {{$post->user_id == $user->id?'selected':''}}>{{$user->getName()}}</option>
								@else
									<option value="{{$user->id}}" {{Auth::user()->id == $user->id?'selected':''}}>{{$user->getName()}}</option>						
								@endif
							@endforeach
						</select>
		  				{{-- <input class="form-control" placeholder="http://localsonly.ideo.com/spot" name="hero_url" value="{{isset($post)?$post->hero_url:''}}"> --}}
		  			</div>					
				</div>
			</div>

		</div>
  </div>
  {{ Form::close() }}
@stop
