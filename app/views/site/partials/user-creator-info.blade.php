


<div class="creator-container">
	<div class="creator-image">
		@include('site.partials.user-image', array('user' => $user, 'size'=>isset($size)?$size:'s40'))
	</div>
	<div class="creator-info">
		<ul class="list-unstyled">
			<li>
			
			{{$user->getName()}}
			@if ($user->isSpotter())

			<div class="user-badge {{$user->isEditor()?'editor':''}}" 
				 data-toggle="tooltip"
				 data-title="{{$user->getRoleName()}}">
			{{Helper::svg($user->getBadgeURL(true))}}
			</div>

			
			@endif
			
			</li>
			<li>
				<small>{{$date}}</small>
			</li>
		</ul>
	</div>
</div>