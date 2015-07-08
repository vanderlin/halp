@if(isset($errors)) 
	@if (is_array($errors))
		@foreach ($errors as $err)
	        <div class="alert alert-error alert-danger">
                @if (is_array($err))
                    {{$err[0]}}
                @else
                    {{$err}}
                @endif
            </div>
		@endforeach
    @elseif(!is_object($errors))
        <div class="alert alert-error alert-danger">{{$errors}}</div>
	@endif
@endif

@if(isset($error)) 
    @if (is_array($error))
        @foreach ($error as $err)
            <div class="alert alert-error alert-danger">
                @if (is_array($err))
                    {{$err[0]}}
                @else
                    {{$err}}
                @endif
            </div>
        @endforeach
    @else
        <div class="alert alert-error alert-danger">{{$error}}</div>
    @endif
@endif

@if (Session::get('error'))
    <div class="alert alert-error alert-danger">
        @if (is_array(Session::get('error')))
            {{ head(Session::get('error')) }}
    	@else 
		{{ Session::get('error') }}
        @endif
    </div>
@endif

@if (Session::get('notice'))
    <div class="alert bg-success">{{ Session::get('notice') }}</div>
@endif