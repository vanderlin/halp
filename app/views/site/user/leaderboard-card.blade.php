<div class="user-card">
	<div class="user-image">
		<a href="{{$user->getProfileURL()}}">
			<img src="{{$user->profileImage->url('s72')}}">
		</a>
	</div>
	<div class="user-details">
		<span class="user-name">
			<h4><a href="{{$user->getProfileURL()}}">{{$user->getName()}}</a></h4>
		</span>
		<hr class="leaderboard-hr">
		<span class="total-task">
		<h2>{{$user->totalClaimed()}}</h2>
		<h6>Claimed Tasks</h6>
		@if (Auth::user()->isAdmin())
			{{$user->claimedTasks->count()}}
		@endif
		</span>
	</div>
</div>
