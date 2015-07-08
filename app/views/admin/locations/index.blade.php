@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Locations 
@stop

@section('scripts')
<style type="text/css">
  .hiddenRow {
    padding: 0 !important;
      max-width: 100%;
  }
</style>


<script type="text/javascript">
  $(document).ready(function($) {

     var hash = window.location.hash ? window.location.hash.substring(1) : null; 
     if(hash) {
      $('tr[data-id="'+hash+'"]').addClass('success');
     }

    function post(options) {
      options = options || {};
      console.log(options);
      $.ajax({
        url: options.url,
        type: 'POST',
        dataType: 'json',
        data: {_method: options._method},
      })
      .always(function(e) {
        console.log(e);
        document.location = document.location+"#"+options.id;
      });
      
    }

    $('a[href="#reload-google-data"]').click(function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      post({
        id:id,
        url:'/admin/locations/'+id+'/details',
        _method:'PUT'
      });
    });
    
  });
</script>
@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">Locations</h2>

  <div class="table-responsive">
    <table class="table table-striped table-condensed">
    
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Location</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        @foreach ($locations as $key=>$location)
          <?php $status = '';

          if($location->locationable_type=='Spot'&&$location->spot_id!==$location->locationable_id) {
            $status = 'danger';  
            if(Spot::find($location->locationable_id) != NULL) {
              $status = 'info';              
            }
          }
          if(isset($location->details->address_components) === false) {
            $status = 'warning';
          }

           ?>
          <tr class="{{$status}}" data-id="{{$location->id}}">
            
            <td>{{ $location->id }}</td>
            
            <td>
              <ul class="list-unstyled">
                <li><b>{{$location->name}}</b> </li>
                <li><small class="text-info"><b>{{$location->locationable_type}}</b> : {{$location->locationable_id}}</small></li>
                <li><small>{{$location->place_id}}</small></li>
                <?php $user = User::find($location->user_id); ?>
                @if ($user)
                  <li><small>Added by: {{$user->getName()}}</small></li>
                @else
                  <li><small>Added by: Unknown</small></li>
                @endif
                
              </ul>
            </td>
            
            <td>
              <ul class="list-unstyled">
                <li>{{$location->latLngString()}}</li>
                <li>City: <b>{{$location->city}}</b></li>
                <li>Country: 
                @if($location->country)
                  <b>{{$location->country->long_name}} / {{$location->country->short_name}}</b>
                @else
                  <b>Missing</b>
                @endif
                </li>
              </ul>
            </td>
            
            <td>
              {{$location->spot_id}}
              <ul class="list-unstyled">
                <li><a href="#loc-{{$location->id}}" data-toggle="collapse" class="accordion-toggle btn btn-default btn-xs">Details</a></li>
                
                <li>
                @if ($location->spot_id)
                  {{link_to($location->spot()->withTrashed()->first()->getURL(), 'View Spot', ['class'=>'btn btn-default btn-xs'])}}
                @endif
                </li>
                
                <li><a href="#reload-google-data" data-id="{{$location->id}}" class="btn btn-default btn-xs">Reload Google Data</a></li>    
              </ul>
              
            </td>

          </tr>
          
          <tr>
            <td class="hiddenRow" colspan="4"> 
              <div class="accordian-body collapse" id="loc-{{$location->id}}">
              <div class="panel-body">
                
                @if ($location->details && isset($location->details->address_components))
                <code>{{json_encode($location->details->address_components)}}</code>
                @endif
                
              </div>
              </div>
            </td>
          </tr>

        @endforeach
      </tbody>
    </table>
    <div class="text-center">
    {{$locations->links()}}
    </div>
  </div>

@stop
