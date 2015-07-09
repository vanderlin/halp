<section class="nav">
  <div class="logo">
    <img src="{{asset('assets/img/halp.png')}}" height="60px" width="115px"/>
    <span class="logo-text">Halp.</span>
  </div>
  <div class="user">
	  @if (Auth::check())
		<span class="username">{{link_to(Auth::user()->getProfileURL(), Auth::user()->getName())}}</span>
    	<span class="logout"><a href="{{URL::to('logout')}}">Log Out</a></span>  
	  @else
	  	<span class="username"></span>
    	<span class="logout"><a href="{{URL::to('login')}}">Log in</a></span>
	  @endif
  </div>
</section>
