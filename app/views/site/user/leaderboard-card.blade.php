<div class="user-card">
	<div class="user-image">
		<img src="{{$user->profileImage->url('s72')}}">
	</div>
	<div class="user-details">
		<span class="user-name">{{$user->getName()}}</span>
		<hr>
		<span class="total-task">
		<h2>{{$user->totalClaimed()}}</h2>
		<p>Completed Tasks</p>
		</span>
	</div>
</div>
