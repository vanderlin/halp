<div class="content-container" {{isset($id) ? 'id="'.$id.'"':''}}>
    
    <div class="row">
		<div class="content-title col-md-12">
			<h2>{{$title}}</h2>
		</div>		    
    </div>

    <div class="row spots-masonry">
    	<div class="col-md-12 content">
	    	@if (isset($content))
	    		@include($content, $data)
	    	@endif
   		</div>
    </div>
    
</div>
