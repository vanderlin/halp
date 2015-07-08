@extends('admin.layouts.default')


{{-- Web site Title --}}
@section('title')
  Admin | Itineraries
@stop

@section('scripts')
@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">{{$user->getFirstName()}}'s Itineraries</h2>
 
  <div role="tabpanel">
    
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <?php $first = true; ?>
      @foreach ($user_itineraries as $key=>$itineraries)
        <li role="presentation" class="{{$first?'active':''}}">
          <a href="#{{Str::slug($key)}}" role="tab" data-toggle="tab">
          @if ($key=='Shared')
            <img data-no-retina src="{{common_asset('icons/shared.svg')}}" width="16" style="position:relative;top:-3px;">
          @endif
          {{ $key }} <small>({{$itineraries->count()}})</small>
          </a>
        </li>
        <?php $first = false; ?>
      @endforeach   
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <?php $first = true; ?>
      @foreach ($user_itineraries as $key=>$itineraries)
        <div role="tabpanel" class="tab-pane {{$first?'active':''}}" id="{{Str::slug($key)}}">
          @include('admin.itinerary.itineraries-table', ['itineraries'=>$itineraries, 'id'=>'{{Str::slug($key)}}-table'])
        </div>
        <?php $first = false; ?>
      @endforeach   
    </div>

  </div>

@stop
