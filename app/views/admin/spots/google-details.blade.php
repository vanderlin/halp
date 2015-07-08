<div class="row">
	<div class="col-md-6">
		<ul class="list-unstyled">
			<?php $details = $spot->location->getDetails() ?>
			<li><img src="{{$details->icon}}"></li>
			<li>{{$details->formatted_address}}</li>
			<li>{{$details->formatted_phone_number}}</li>
			<li>{{$details->website}}</li>
			<li>
				@foreach ($details->opening_hours->weekday_text as $day)
					{{$day}}<br>
				@endforeach
			</li>
		</ul>
	</div>

	<div class="col-md-6">
		<ul class="list-inline google-photos-list">
			@foreach ($details->photos as $photo)
				<li><img src="{{$photo->url}}" class="img-responsive thumbnail" width="150px"></li>
			@endforeach
		</ul>
	</div>

</div>