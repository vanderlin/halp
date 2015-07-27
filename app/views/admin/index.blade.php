@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
	{{Config::get('config.site_name')}} | Admin | Notifications
@stop

@section('scripts')

@stop




{{-- Content --}}
@section('content')

<section class="content container">
	
	@if ($users->count()>0)
		<table class="ui celled table">
  			<thead>
    			<tr>
    				<th>User</th>
    				<th>Created Tasks</th>
    				<th>Claimed Tasks</th>
    				<th>Roles</th>
    				<th></th>
  				</tr>
  			</thead>
		<tbody>
			@foreach ($users as $user)
			<tr>
		      	<td>
			        <h4 class="ui image header">
			        	<img src="{{$user->profileImage->url('s30')}}" class="ui mini circular image">
			        	<div class="content">
			            	{{link_to($user->getProfileURL(), $user->getName())}}
			            	<div class="sub header">{{$user->email}}</div>
			        	</div>
			      	</h4>
		      	</td>
				
				<td>{{$user->createdTasks->count()}}</td>
				<td>{{$user->claimedTasks->count()}}</td>
			    <td>{{$user->getRoles()}}</td>
				<td style="text-align:center"><a class="mini ui button" href="/admin/users/{{$user->id}}/roles/edit">Edit</a></td>
    		</tr>
			@endforeach
		</tbody>
	</table>	
	<br>

	<div class="text-center">
		{{$users->links()}}
	</div>
	
	@include('site.partials.form-errors')

	@else
		<h3>No Users</h3>
	@endif

</section>
@stop
