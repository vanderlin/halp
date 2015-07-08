@if (count(Permission::all())==0)
	<div class="text-center text-muted"><i>No Permissions</i></div>
@else
	@foreach (Permission::all() as $perm)
	  <div class="checkbox">
	    
	    <label>
	      <input type="checkbox" name="perms[{{ $perm->id }}]" {{ (isset($role)&&$role->hasPerm($perm->name))?'checked':'' }}>
	      	{{ $perm->display_name }}
		  </label>
	  </div>
	@endforeach
@endif
    


    