














<div class="ui selection dropdown user-selector" tabindex="0">
    <input type="hidden" name="{{$form_name}}" value="{{$default==NULL?'NULL':$default->id}}">
    
    <div class="text">
    @if ($default==NULL)
        NULL
    @else
        <img class="ui mini avatar image" src="{{$default->profileImage->url('s28')}}">
        {{$default->getName()}}
    @endif
    </div>

    <i class="dropdown icon"></i>

    <div class="menu transition hidden" tabindex="-1">
        @if($default==NULL)
            <div class="item" data-value="NULL">
                NULL
            </div>
        @endif
        
        @foreach ($users as $user)
            <div class="item" data-value="{{$user->id}}" >
                <img class="ui mini avatar image" src="{{$user->profileImage->url('s28')}}">
                {{$user->getName()}}
            </div>
        @endforeach
    </div>
</div>




