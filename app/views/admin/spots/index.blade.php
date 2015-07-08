@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Spots
@stop


@section('scripts')
<script type="text/javascript">
	$(document).ready(function($) {
		if($('#draft-spots tr.success').length) {
			$("#draft-spots tbody").scrollTo($('#draft-spots tr.success'));	
		}
		if($('#publish-spots tr.success').length) {
			$("#publish-spots tbody").scrollTo($('#publish-spots tr.success'));	
		}

		// Javascript to enable link to tab
		var hash = document.location.hash;
		var prefix = "";
		if (hash) {
		    hash = hash.replace(prefix,'');
		    var hashPieces = hash.split('?');
		    activeTab = $('[role="tablist"] a[href=' + hashPieces[0] + ']');
		    activeTab && activeTab.tab('show');
		}

		// Change hash for page-reload
		$('[role="tablist"] a').on('shown.bs.tab', function (e) {
		    window.location.hash = e.target.hash.replace("#", "#" + prefix);
		});
		

		// work on this...
		var arr = document.URL.match(/success=([0-9]+)/)
		if(arr!=null) {
			$target = $('.spots-table tr[data-id="'+arr[1]+'"]');
			if($target.length) {
				setTimeout(function() {
					$(".container-fluid").scrollTo($target);
				}, 500);	
			} 
		}

	});
</script>	
@stop

{{-- Content --}}
@section('content')
	<h2 class="page-header">{{$user->getFirstName()}}'s Spots ({{$user->totalSpots}})</h2>

	<?php $user_spots = array(
				'Published'=>$user->spots,
				'Drafts'=>$user->drafts,
				'Trashed'=>$user->getTrashedSpots());
	?>

	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<?php $first = true; ?>
			@foreach ($user_spots as $key=>$spots)
				<li role="presentation" class="{{$first?'active':''}}"><a href="#{{Str::slug($key)}}" role="tab" data-toggle="tab">{{ $key }} <small>({{$spots->count()}})</small></a></li>
				<?php $first = false; ?>
			@endforeach		
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<?php $first = true; ?>
			@foreach ($user_spots as $key=>$spots)
				<div role="tabpanel" class="tab-pane {{$first?'active':''}}" id="{{Str::slug($key)}}">
					@include('admin.spots.spots-table', ['spots'=>$spots, 'id'=>'{{Str::slug($key)}}-table'])
				</div>
				<?php $first = false; ?>
			@endforeach		
		</div>

	</div>
@stop

