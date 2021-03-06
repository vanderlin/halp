<section class="nav">

    <div class="menu-wrap">
        <nav class="mobile-menu">
          <div class="icon-list">
            <a href="{{URL::to('/')}}">Home</a>
            @if (Auth::check() && Auth::user()->isAdmin())
               {{link_to('admin', 'Admin')}}
            @endif
            {{link_to('leaderboard', 'leaderboard')}}
            @if (Auth::check())
                {{link_to(Auth::user()->getProfileURL(), Auth::user()->getName())}}
                <a href="{{URL::to('logout')}}">Log Out</a>
            @else
               <a href="{{URL::to('login')}}">Log in</a>
            @endif
          </div>
        </nav>
        <button class="close-button" id="close-button">Close Menu</button>
        <div class="morph-shape" id="morph-shape" data-morph-open="M-1,0h101c0,0,0-1,0,395c0,404,0,405,0,405H-1V0z">
          <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 100 800" preserveAspectRatio="none">
            <path d="M-1,0h101c0,0-97.833,153.603-97.833,396.167C2.167,627.579,100,800,100,800H-1V0z"/>
          </svg>
        </div>
  </div>

  <a id="open-button" class="open-menu">Menu</a>

  <div class="logo">
    <a href="{{URL::to('/')}}">
      <img src="{{asset('assets/img/halp.png')}}" height="60px" width="115px"/>
      <span class="logo-text">Halp.</span>
    </a>
  </div>

  <div class="user">
    @if (Auth::check() && Auth::user()->isAdmin())
      <span class="username">{{link_to('admin', 'Admin')}}</span>
    @endif
	  <span class="username">{{link_to('leaderboard', 'leaderboard')}}</span>
    @if (Auth::check())
		  <span class="username">{{link_to(Auth::user()->getProfileURL(), Auth::user()->getName())}}</span>
      <span class="logout"><a href="{{URL::to('logout')}}">Log Out</a></span>  
      <span class="feedback" id="feedback-button"><a href="#Feedback">Feedback <i class="fa fa-comment-o"></i></a></span>
	  @else
	  	<span class="username"></span>
    	<span class="logout"><a href="{{URL::to('login')}}">Log in</a></span>
	  @endif
  </div>

</section>

<script src="{{asset('assets/js/main3.js')}}"></script>
<script src="{{asset('assets/js/classie.js')}}"></script>
