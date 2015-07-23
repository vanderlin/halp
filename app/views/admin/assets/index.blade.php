@extends('admin.layouts.default', ['use_footer'=>false])

<?php $assets = Asset::orderBy('created_at', 'DESC')->paginate(12); ?>

{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | Admin | Projects
@stop

@section('scripts')
<script type="text/javascript">
    jQuery(document).ready(function($) {

        $('.edit-asset').click(function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.magnificPopup.open({
                tLoading: 'Loading...',
                closeOnContentClick: false,
                closeOnBgClick:false,
                mainClass: 'mfp-fade',
                items: {
                    src: url,
                    type: 'ajax',
                },
                callbacks: {
                    parseAjax: function(mfpResponse) {
                        mfpResponse.data = $(mfpResponse.xhr.responseText);
                    },
                    ajaxContentAdded: function() {
                  
                    }
                }
            });    
        });

        // -------------------------------------
        $(".delete-asset").click(function(e) {
          e.preventDefault();
          var c = confirm('Are you sure?');
          if(c) {
            $.ajax({
              url: $(this).attr('href'),
              type: 'POST',
              dataType: 'json',
              data: {_method: 'DELETE'},
            })
            .done(function(evt) {
              console.log("success", evt);
              if(evt.status == 200) {
                document.location = '/admin/assets';
              }
            })
            .fail(function(evt) {
              console.log("error", evt);
            });
          }
        });

        // -------------------------------------   
        $('.add-asset').click(function(e) {
            e.preventDefault();
            $.magnificPopup.open({
                tLoading: 'Loading...',
                closeOnContentClick: false,
                closeOnBgClick:false,
                mainClass: 'mfp-fade',
                items: {
                    src: '/admin/assets/upload',
                    type: 'ajax',
                },
                callbacks: {
                    parseAjax: function(mfpResponse) {
                        mfpResponse.data = $(mfpResponse.xhr.responseText);
                    },
                    ajaxContentAdded: function() {
                  
                    }
                }
            });    
        });
    });
</script> 
@stop


{{-- Content --}}
@section('content')

<section class="content admin">
  @if ($assets->count()>0)
    <table class="ui celled table">
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
        <tr class="{{$asset->fileExists()?'':'danger'}}">
          <td class="center aligned">{{ $asset->id }}</td>
          <td class="center aligned">
            <img width="40" height="40" src="{{$asset->url('s40')}}" class="thumbnail">
          </td>
          <td>
          {{ link_to('admin/assets/'.$asset->id.'/edit', $asset->getName()) }}
          {{ $asset->shared?' <small class="text-warning">(Shared)</small>':'' }}
          </td>
          <td>{{$asset->assetable_type?$asset->assetable_type:'None'}}</td>
          <td class="center aligned">
          <a href="/assets/{{$asset->id}}" class="delete-asset ui red button" data-id="{{$asset->id}}"><i class="trash icon"></i></a>
          {{ link_to('admin/assets/'.$asset->id.'/edit', 'Edit', ['class'=>'edit-asset ui button'])}}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>  
  
  <div class="ui pagination menu">
    @include('admin.partials.paginator', ['paginator'=>$assets])
    <div class="icon item"></div>
    <a class="icon item add-asset">
      <i class="plus icon"></i>
    </a>
  </div>

  <div class="text-center">@include('site.partials.form-errors')</div>
 
  @endif

</section>
@stop
