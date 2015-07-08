@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | {{$office->location->name}} 
@stop


@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
<script type="text/javascript">
	
	function initialize() {

		// Create the autocomplete object, restricting the search
		// to geographical location types.
		var autocomplete = new google.maps.places.Autocomplete((document.getElementById('office-search')));
		// When the user selects an address from the dropdown,
		// populate the address fields in the form.
		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			console.log(place);
			$('.place-input input[id="name"]').attr('value', place.address_components[0].short_name);
			$('.place-input input[id="lat"]').attr('value', place.geometry.location.lat());
			$('.place-input input[id="lng"]').attr('value', place.geometry.location.lng());
			$('.place-input input[id="place_id"]').attr('value', place.place_id);

		});
	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
@stop

{{-- Content --}}
@section('content')
  
  <h1 class="page-header">{{$office->location->name}}</h1>

  @include('admin.offices.form', array('office' => $office))

@stop
