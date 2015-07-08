@extends('admin.layouts.default')

<?php 


$lost_spots = Location::where('locationable_type', 'Spot')->whereDoesntHave('spot', function($q) {

})->get();





$assets = Asset::all()->filter(function($a) {
	if($a->fileExists()==false && $a->missingReleationship()==false) {
		return $a;
	}
});

$spotsMissingData = Spot::all()->filter(function($s) {
  if(empty($s->location) || empty($s->user_id)) return $s;
});


?>

{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | Edit Users
@stop

{{-- Content --}}
@section('content')
  
  <h2>Spots Missing Data</h2>
    <table class="table table-striped assets-table">
    
      <thead>
        <tr>
          <th>#</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        @foreach ($spotsMissingData as $spot)
          
          <tr>
            <td>{{ $spot->id }}</td>
            <td>{{ link_to('admin/spots/'.$spot->id.'/edit', 'Edit', ['class'=>'btn btn-default btn-xs pull-right'])}}</td>
          </tr>

        @endforeach
      </tbody>
    </table>
  



	<h2>Assets</h2>
    <table class="table table-striped assets-table">
    
      <thead>
        <tr>
          <th>#</th>
          <th>File</th>
          <th>Name</th>
          <th>Type</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        @foreach ($assets as $asset)
          
          <tr class="{{$asset->fileExists() ? '' : 'danger'}}">
            <td>{{ $asset->id }}</td>
            <td>
            	<img width="30" height="30" src="{{$asset->url('s30')}}" class="thumbnail">
            </td>
            <td>
            <ul class="list-unstyled">
	           <li> {{ link_to('admin/assets/'.$asset->id.'/edit', $asset->getName()) }}</li>
	            <li><small>{{$asset->relativeURL()}}</small></li>
	            <li><small>Missing: {{$asset->missingReleationship()?'True':'False'}}</small></li>
            </ul>
            </td>
            <td>{{$asset->assetable_type?$asset->assetable_type:'None'}}</td>
            <td>{{ link_to('admin/assets/'.$asset->id.'/edit', 'Edit', ['class'=>'btn btn-default btn-xs pull-right'])}}</td>
          </tr>

        @endforeach
      </tbody>
    </table>
  



  <h2 class="page-header">Lost Spots</h2>
  
  <div class="col-md-6">

    <div class="row">
	  <table class="table table-striped">
	  
	    <thead>
	      <tr>
	          <th>ID</th>
	          <th>Name</th>
	          <th>Spot</th>
	          <th></th>
	      </tr>
	    </thead>

	    <tbody>
	    	@foreach ($lost_spots as $loc)
	        	<tr>
	          		<td>{{$loc->id}}</td>
	          		<td>{{$loc->name}}</td>
	          		<td>
	          		{{$loc->locationable_id}}
	          		</td>
	          		<td class="text-right"><a href="/spots/{{$loc->locationable_id}}" class="btn btn-default btn-xs">Check</a></td>
	        	</tr>
			@endforeach
	    </tbody>
	  </table>
	</div>


    <div class="text-center">
        @if (Session::get('error'))
            <div class="alert alert-error">{{{ Session::get('error') }}}</div>
        @endif

        @if (Session::get('notice'))
            <div class="alert">{{{ Session::get('notice') }}}</div>
        @endif
    </div>

  </div>

@stop
