work on this later
<div class="table-responsive">
  <table class="table table-striped">
  
    <thead>
      <tr>
        @foreach ($header as $item)
          <th>{{$item}}</th>
        @endforeach
      </tr>
    </thead>

    <tbody>
      
      @foreach ($items as $item)
        
        <tr>
          @foreach ($header as $prop)
          <td>
            
              @if ($item->offsetExists(strtolower($prop)))
                {{$item[strtolower($prop)]}}  
              @endif
              
            

          </td>
          @endforeach
        </tr>

      @endforeach
    </tbody>
  </table>
</div>
