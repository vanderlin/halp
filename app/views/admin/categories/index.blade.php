@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Categories 
@stop


@section('scripts')

@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">Categories</h2>

  <div class="table">
    <table class="table table-striped">
    
      <thead>
        <tr>
          <th class="table-id-col">#</th>
          <th></th>
          <th>Name</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        @foreach (Category::all() as $cat)
          
          <tr>
            <td>{{ $cat->id }}</td>
            <td><img src="{{ $cat->icon->url('s50') }}" class="thumbnail purple-background"></td>
            <td>{{ link_to('admin/categories/'.$cat->id.'/edit', $cat->name) }}</td>
            <td>{{ link_to('admin/categories/'.$cat->id.'/edit', 'Edit', ['class'=>'btn btn-default btn-xs pull-right'])}}</td>
          </tr>

        @endforeach
      </tbody>
    </table>
  </div>

@stop
