<div class="white-popup claimed-popup animated fadeIn">
    <div class="popup-content">

        {{Form::open(['route'=>['assets.update', $asset->id], 'method'=>'PUT', 'files'=>true, 'class'=>'ui form'])}}
        	
        	<input type="hidden" value="{{Auth::user()->id}}" name="user_id">

	        <!-- Name -->
	        <div class="field">
	          	<label for="name">Name</label>
	        	<input id="name" name="name" autocomplete="off" placeholder="optional" class="form-control" value="{{$asset->name}}">
	        </div>

	        <!-- FileName -->
	        <div class="field">
	          	<label for="filename">Filename</label>
	        	<input disabled name="filename" class="form-control" value="{{$asset->filename}}">
	        </div>

	        <!-- Path -->
	        <div class="field">
	          	<label for="path">Path</label>
	        	<input disabled name="path" class="form-control" value="{{$asset->path}}">
	        </div>

          	<!-- tag -->
	        <div class="field">
	          	<label for="tag">Tag</label>
	        	<input name="tag" class="form-control" value="{{$asset->tag}}">
	        </div>


	        <!-- file -->
	        <div class="field">
	          	<img src="{{$asset->url('w100')}}"><br>
	          	<input type="file" name="file" id="file-input" accept="image/*" multiple>	
	        </div>


	         <!-- shared -->
	        <div class="field">
	        	<div class="checkbox">
					<label>
			    		<input type="checkbox" name="shared" {{(isset($asset->shared)&&$asset->shared==1)?'checked':''}}> Shared
			    	</label>
			  	</div>
	        </div>

	         <!-- Info -->
	        <div class="field">
	          	<label for="path">File Exists: {{$asset->fileExists()?'True':'False'}}</label>
	          	<br>
	          	@foreach ($asset->getMissingReleationship() as $missing)
	          		<b>{{$missing[0]}}:</b> {{$missing[1]}} - {{$missing[2]}}<br>
	          	@endforeach
	        </div>


			{{--
            <div class="field">
                <label for="name" class="text-left">Name (optional)</label>
                <input type="text" id="name" name="name" placeholder="My awesome asset" value="{{$asset->name}}">
            </div>
            <div class="field">
                <label for="tag" class="text-left">Tag (optional)</label>
                <input type="text" id="tag" name="tag" placeholder="some_asset_tag" value="{{$asset->tag}}">
            </div>
            <div class="field">
                <label for="file" class="ui icon button">Browse</label>
                <input type="file" id="file" name="file" style="display:none">
            </div>
            --}}

            <div class="field">
                <button type="submit" class="ui button">Save</button>
            </div>

            <div class="text-center">@include('site.partials.form-errors')</div>

        {{Form::close()}}

    </div>
</div>
