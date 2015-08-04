
<div class="user-award {{isset($award)?'filled':'unfilled'}}">
	<img src="{{asset(isset($award)?$type->image:'assets/img/user-award-unfilled.svg')}}">
	<h6>{{$type->title}}</h6>
	@if(isset($award))
	
	@else
		<h4>not earned</h4>
	@endif
	<h4>5/4/15</h4>
</div>
{{-- <div class="user-award filled">
	<img src="{{asset('assets/img/user-award-10.svg')}}">
	<h6>10 Tasks<br>Claimed</h6>
	<h4>5/4/15</h4>
</div>
<div class="user-award filled">
	<img src="{{asset('assets/img/user-award-25.svg')}}">
	<h6>25 Tasks<br>Claimed</h6>
	<h4>5/4/15</h4>
</div>
<div class="user-award unfilled">
	<img src="{{asset('assets/img/user-award-unfilled.svg')}}">
	<h6>50 Tasks<br>Claimed</h6>
	<h4>not earned</h4>
</div>
<div class="user-award unfilled">
	<img src="{{asset('assets/img/user-award-unfilled.svg')}}">
	<h6>75 Tasks<br>Claimed</h6>
	<h4>not earned</h4>
</div>
<div class="user-award unfilled">
	<img src="{{asset('assets/img/user-award-unfilled.svg')}}">
	<h6>100 Tasks<br>Claimed</h6>
	<h4>not earned</h4>
</div> --}}