
@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | Edit Categories
@stop

@section('scripts')
<script type="text/javascript">
  $(document).ready(function($) {
    
    var search = $("#spot-name-search");
    search.select2({
        minimumInputLength: 1,
        multiple:true,
      ajax: {
            url: function(term) {
              return "/api/search/spots/"+term;
            },
            type: "GET",
            dataType: 'json',
            quietMillis: 250,
            results: function (data) {
              console.log("data",data);
              var results = [];
              if(data.status==200 && data.results.length>0) {
                
                $.each(data.results, function(index, item){
                  console.log(item);
                  item.text = item.name;
                  results.push(item);
              });
              }
            return { results: results };
            },
            

        },

    })
    .on('select2-selecting', function(e) {
      
    });

    $("#add-spots-button").click(function(e) {
      e.preventDefault();
      var data = search.select2('data');
      var spots = [];
      for (var i = 0; i < data.length; i++) {
        spots.push(data[i].id);
      }

      $.ajax({url: '/admin/categories/{{$category->id}}',
          type: 'POST',
          dataType: 'json',
          data:{_method:'PUT', spots:spots}
        })
        .done(function(evt) {
          console.log("success", evt);
          
        })
        .fail(function(evt) {
          console.log("error", evt.responseText);
      });

      for (var i = 0; i < data.length; i++) {
          var choice = data[i];
          if($('.examples-table tbody tr[data-id="'+choice.id+'"]').length==0) {
            var tr = '<tr data-id="'+choice.id+'">\
                <td>\
                  <img alt="'+choice.name+'" width="40" height="40" src="'+choice.thumbnail_base+'/s50" class="img-circle">\
                </td>\
                <td>'+choice.name+'</td>\
                <td><a class="pull-right itinerary-remove-user btn btn-danger btn-xs" href="#remove-user" data-id="'+choice.id+'">Remove</a></td>\
                </tr>';
          $(".examples-table tbody").append(tr);
        }
      }
      
      search.select2("val", "");

    });
    
    // -------------------------------------
    $(".category-remove-spots").click(function(e) {
      e.preventDefault(); 
      var spot_id = $(this).attr('data-id');
    
      $.ajax({
          url: '/admin/categories/{{$category->id}}',
          type: 'POST',
          dataType: 'json',
          data:{_method:'PUT', remove_spots:[spot_id]}
        })
        .done(function(evt) {
          console.log("success", evt);
          if(evt.status == 200) {
            $('.examples-table tr[data-id="'+spot_id+'"').fadeOut(200, function() {
              $(this).remove();
            });
          }
        })
        .fail(function(evt) {
          console.log("error", evt);
        });
    });


  });
</script> 
@stop


{{-- Content --}}
@section('content')

  <div class="page-header">
    <h2 class="inline">
      {{ $category->name }}
    </h2>
    <h5 class="inline"><a href="{{$category->getURL()}}">view</a></h5>
  </div>
  
  <div class="well">

      {{Form::open(['url'=>'admin/categories/'.$category->id, 
                    'method'=>'PUT', 
                    'role'=>"form",
                    'files'=>true])}}

      <div class="row">
        

        
        <div class="col-sm-6">
          
          <!-- Name -->
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{$category->name}}">
          </div>

          <!-- Description HTML -->
          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="textarea-editor form-control" rows="10">{{$category->description}}</textarea>
          </div>

          <!-- Spot Example  -->
          <div class="form-group">
            <label for="name">Spot Examples</label>
            <div class="input-group">
              <input multiple="false" type="hidden" class="form-control" id="spot-name-search" placeholder="ie: 1369 Coffee Shop">
              <span class="input-group-btn">
                <button class="btn btn-default" id="add-spots-button" type="button">Add</button>
              </span>
            </div>
          
            <table class="table table-striped examples-table">
              <thead>
                <tr>
                  <th></th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>

              <tbody>
              @foreach ($category->examples as $spot)
                <tr class="item" data-id="{{$spot->id}}">
                  <td><img width="30" src="{{ $spot->getThumbnail()->url('s30') }}"></td>
                  <td>{{ $spot->name }}</td>
                  <td class="action-td"><a class="category-remove-spots btn btn-danger btn-xs" href="#remove-spot" data-id="{{$spot->id}}">Remove</a></td>
                </tr>
              @endforeach
              </tbody>
            </table>

          </div>






        </div>





        <div class="col-sm-6">

            <!-- Icon -->
            <div class="form-group">
              <label for="description">Icon</label>
              <ul class="list-unstyled">
                @if ($category->icon)
                  <li><img class="thumbnail" src="{{$category->icon->url()}}"></li>
                @endif
                <li><input type="file" name="icon"></li>
              </ul>
            </div>      

            <!-- Photos -->    
            <div class="form-group">
              <label for="description">Hero Photos</label>
              @if ($category->photos->count()>0)
                @include('admin.partials.photos-list-group', array('photos'=>$category->photos))
              @else
                <div class="text-center text-muted no-photos">
                  <small><i>No Photos...</i></small>
                </div>
              @endif
              <input type="file" name="files[]" id="files-input" accept="image/*" multiple>
            </div>

        </div>


      </div>

      
      <div class="form-group">
        <div class="text-right">
          <button type="submit" class="btn btn-default">Update</button>
        </div>
      </div>

    </form>

    <div class="form-group text-center">
      @include('site.partials.form-errors')
    <div>


  </div>

@stop
