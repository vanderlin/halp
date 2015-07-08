<?php $success_spot_id = Input::get('success', -1); ?>
<table class="table table-striped spots-table" id="{{isset($id)?$id:''}}">
  <thead>
    <tr>
      <th>ID#</th>
      <th></th>
      <th></th>
    </tr>
  </thead>
      <tbody>
        @foreach ($spots as $s)          
          <tr class="{{$success_spot_id==$s->id?'success scroll-to':''}}" data-id="{{$s->id}}">
            <td>{{ $s->id }}</td>
            <td>
              <div class="media">
                <div class="media-left">
                  <img class="media-object" width="50px" src="{{ $s->getThumbnailImage()->url('s50')}}">
                </div>  
                <div class="media-body media-top">
                  <h5 class="media-heading">{{ link_to('admin/spots/'.$s->id.'/edit', $s->name)}}</h5>
                  <small class="text-muted">{{$s->hasLocation() ? $s->location->formattedAddress:'No Address'}}</small>
                </div>
              </div>
            </td>

            <td>{{ link_to('admin/spots/'.$s->id.'/edit', 'Edit', ['class'=>'btn btn-xs btn-default action-btn pull-right'])}}</td>
          </tr>
        @endforeach
      </tbody>
</table>

		