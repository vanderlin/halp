<?php $success_item_id = Input::get('success', -1); ?>
<table class="table table-striped" id="{{isset($id)?$id:''}}">
  <thead>
    <tr>
      <th>ID#</th>
      <th></th>
      <th></th>
    </tr>
  </thead>
      <tbody>
        @foreach ($itineraries as $itinerary)  

          <tr class="{{$success_item_id==$itinerary->id?'success scroll-to':''}}" data-id="{{$itinerary->id}}">
            <td>{{ $itinerary->id }}</td>
            
            <td>
              <div class="media">
                
                <div class="media-left">
                  <img class="media-object" width="50px" src="{{ $itinerary->heroPhoto->url('s50')}}">
                </div>  

                <div class="media-body media-top">
                  <h5 class="media-heading">
                  {{ link_to($itinerary->getEditURL(), $itinerary->title)}}
                  </h5>
                  @if ($itinerary->isMine()==false)
                    <div>
                      <small class="text-muted">
                      Created by: {{link_to($itinerary->user->getProfileURL(), $itinerary->user->getName())}}
                      </small>
                    </div>
                  @endif
                  <small class="text-muted">Last Updated {{$itinerary->updated_at->diffForHumans()}}</small>
                </div>
              </div>
            </td>

            <td>{{ link_to($itinerary->getEditURL(), 'Edit', ['class'=>'btn btn-xs btn-default action-btn pull-right'])}}</td>
          </tr>
        @endforeach
      </tbody>
</table>

		