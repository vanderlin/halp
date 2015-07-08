@extends('admin.layouts.default')

<?php $assets = Asset::orderBy('created_at', 'DESC')->paginate(25); ?>
{{-- Web site Title --}}
@section('title')
  Admin | Assets 
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">Assets ({{$assets->getTotal()}})</h2>

  <div class="row">
    <div class="col-md-6">
      
      <h3>Add Asset</h3>
      {{Form::open(['url'=>'/assets/create', 'method'=>'POST', 'files'=>true])}}
      
      <div class="form-group">
        <label for="filename">Filename</label>
        <div class="input-group">
          <input type="text" class="form-control" id="filename" placeholder="ie: assets/content/file.png">
          <span class="input-group-btn">
            <button class="btn btn-default btn-file" type="button">
              <span id="file-icon">Browse</span>
              <input type="file" name="file" id="file" accept="image/*">
            </button>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" placeholder="optional">
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-default btn-success">Create</button>
      </div>

      <div class="text-center">
        
          @include('site.partials.form-errors')
        
      </div>

      {{Form::close()}}
      

    </div>
  </div>




  <div class="table-responsive">
    <h3>Assets</h3>
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
          
          <tr class="{{$asset->fileExists()?'':'danger'}}">
            <td>{{ $asset->id }}</td>
            <td>
            	<img width="30" height="30" src="{{$asset->url('s30')}}" class="thumbnail">
            </td>
            <td>
            {{ link_to('admin/assets/'.$asset->id.'/edit', $asset->getName()) }}
            {{ $asset->shared?' <small class="text-warning">(Shared)</small>':'' }}
            </td>
            <td>{{$asset->assetable_type?$asset->assetable_type:'None'}}</td>
            <td>{{ link_to('admin/assets/'.$asset->id.'/edit', 'Edit', ['class'=>'btn btn-default btn-xs pull-right'])}}</td>
          </tr>

        @endforeach
      </tbody>
    </table>
  </div>

  <div class="row text-center">
  {{$assets->links()}}
  </div>

@stop
