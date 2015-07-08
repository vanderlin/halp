@extends('site.layouts.default')

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Users
@stop

@section('scripts')
<script type="text/javascript">
	jQuery(document).ready(function($) {
		
		$('.office-filter li a').click(function(e) {
			e.preventDefault();
			var slug = $(this).data('slug');
			$(this).toggleClass('active');

			var filter = $('.office-filter li a.active').map(function() {return $(this).data('slug')}).get().join();
				
			document.location = 'locals?filter='+filter;
			

		});


	});
</script>
@stop




{{-- Content --}}
@section('content')

<div class="jumbotron locals-jumbotron">
	<div class="container">
		<img data-no-retina class="img-responsive" src="{{common_asset('locals/locals-hero.png')}}">
	</div>
</div>


<div class="container content-container users-container">
	
	<div class="row">
		<div class="col-md-12">
			<ul class="office-filter list-inline">
				@foreach (Office::all() as $office)
					<li><a href="#{{$office->slug}}" data-slug="{{$office->slug}}" class="{{(isset($filter) && in_array($office->slug, $filter))?'active':''}}">{{$office->name}}</a></li>
				@endforeach
			</ul>
		</div>
	</div>

	<div class="row">
		<div class="content-title col-md-12">
			<h2>Spotters ({{$spotters->count()}})</h2>
		</div>
	</div>

	@foreach ($spotters->chunk(3) as $row)
		<div class="row">
		@foreach ($row as $user)
			<div class="col-sm-4">
				@include('site.user.spotter-block', array('user' => $user, 'show_badge'=>true))
			</div>
		@endforeach
		</div>
	@endforeach


	<div class="row">
		<div class="content-title col-md-12">
			<h2>Locals ({{$locals->count()}})</h2>
		</div>
	</div>

	@foreach ($locals->chunk(3) as $row)
		<div class="row">
		@foreach ($row as $user)
			<div class="col-sm-4">
				@include('site.user.spotter-block', array('user' => $user, 'show_badge'=>true))
			</div>
		@endforeach
		</div>
	@endforeach
	

</div>

@stop
