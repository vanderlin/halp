@foreach (Role::all() as $role)
  <div class="checkbox">
    <label>
      <input type="checkbox" value="" name="roles[{{$role->id}}]" {{ (isset($user)&&$user->hasRole($role->name))?'checked':''}}>
      {{ $role->display_name }} &nbsp; <small class="text-muted">({{ $role->name }})</small>
      </label>
  </div>
@endforeach

    


    