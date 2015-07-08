<div class="well">

  @if (isset($office))
  {{Form::open(['url'=>'admin/offices/'.$office->id, 'method'=>'PUT'])}}
  @else
  {{Form::open(['url'=>'admin/offices', 'method'=>'POST'])}}
  @endif


          <div class="form-group">
            <label for="name">Search for office location</label>
            <input type="text" class="form-control" id="office-search" placeholder="ie: Boston, MA">
          </div>

          <div class="form-group place-input">
            <label for="name">Office Name</label>
        		<input type="text" class="form-control" id="name" name="name" value="{{isset($office) ? $office->location->name:''}}">
  	        <input type="text" class="form-control" id="lat" name="lat" value="{{isset($office) ? $office->location->lat:''}}">
  	        <input type="text" class="form-control" id="lng" name="lng" value="{{isset($office) ? $office->location->lng:''}}">
  	        <input type="text" class="form-control" id="place_id" name="place_id" value="{{isset($office) ? $office->location->place_id:''}}">
          </div>

          <div class="form-group">
          	<button type="submit" class="btn btn-default">{{isset($office) ? 'Update':'Add'}} </button>
          </div>

   	
    {{Form::close()}}

</div>    

<div class="form-group text-center">
  @include('site.partials.form-errors')
<div>