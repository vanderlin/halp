<div class="grouped fields">
@foreach (Role::all() as $role)
	<div class="field">
		<div class="ui {{(isset($user)&&$user->hasRole($role->name))?'checked':''}} checkbox">
	  		<input name="roles[{{$role->id}}]" type="checkbox" {{(isset($user)&&$user->hasRole($role->name))?'checked':''}}>
	  		<label>{{ $role->display_name }} &nbsp; ({{ $role->name }})</label>
		</div>
	</div>
@endforeach
</div>
    


