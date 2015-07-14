@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Notifications
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')

<section class="content admin">
	@if ($users->count()>0)
		<table style="width:900px;">
		<thead>
			<tr>
				<td>#</td>
				<td>Name</td>
				<td>Email</td>
				<td>Role</td>
				<td></td>
			</tr>
		</thead>
		<tbody>
			@foreach ($users as $user)
				<tr>
					<td>{{$user->id}}</td>
					<td>
					<img class="circle-img" src="{{$user->profileImage->url('s30')}}">
					{{$user->getName()}}
					</td>
					<td>{{$user->email}}</td>
					<td>{{$user->getRoles()}}</td>
					<td><a href="/admin/users/{{$user->id}}/roles/edit">Edit</a></td>
				</tr>
			@endforeach
		</tbody>
	</table>	
	<br>
	<div class="progress-button small">
		{{Form::open(['url'=>'notifications/send'])}}
		<button><span>Send Notifications</span></button>
		{{Form::close()}}
	</div>
	
	@include('site.partials.form-errors')

	@else
		<h3>No Users</h3>
	@endif

</section>
@stop
