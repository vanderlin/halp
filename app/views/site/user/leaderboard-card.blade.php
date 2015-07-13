<div class="user-card">
	<div class="user-image">
		<a href="{{$user->getProfileURL()}}">
			<img src="{{$user->profileImage->url('s72')}}">
		</a>
	</div>
	<div class="user-details">
		<span class="user-name">
			<a href="{{$user->getProfileURL()}}">{{$user->getName()}}</a>
		</span>
		<hr>
		<span class="total-task">
		<h2>{{$user->totalClaimed()}}</h2>
		<p>Completed Tasks</p>
		</span>
	</div>
</div>
