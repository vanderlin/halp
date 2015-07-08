<nav class="main-navbar navbar navbar-fixed-top">
    <div class="container">

      {{-- -------------------------------- --}}
      <div class="navbar-header">
         
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
          
          <a class="navbar-brand" href="{{URL::to('/')}}">
            <img data-no-retina src="{{ asset('assets/content/common/logo.svg') }}" />
          </a>
          
      </div>
      {{-- -------------------------------- --}}


      <div id="navbar" class="navbar-collapse collapse">
        
          
        
        {{-- -------------------------------- --}}
        <form class="navbar-form navbar-left search-form hidden-sm" role="search">
          <div class="form-group">
            <input type="text" class="form-control nav-search-bar" placeholder="SEARCH">
          </div>
        </form>
        <div class="search-results">
          <ul class="search-menu list-group">
          
          </ul>
        </div>
        {{-- -------------------------------- --}}


        {{-- -------------------------------- --}}
        <ul class="site-nav nav navbar-nav navbar-right">
          

          @if (Auth::check())
          <li class="dropdown user-dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" role="button" aria-expanded="false">
              <img width="40" height="40" src="{{ Auth::getUser()->profileImage->url('w40') }}" class="nav-profile-image img-circle">
            </a>

            <ul class="dropdown-menu user-menu" role="menu">
              <li><a href="{{URL::to('users/'.Auth::user()->id)}}">View Profile</a></li>
                
                @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Editor'))
                  <li><a href="{{URL::to('admin')}}">Admin</a></li>
                @endif
                <li><a href="{{URL::to('admin/itinerary')}}">My Itineraries</a></li>
                @if (Auth::user()->can('create_spots'))
                  <li><a href="{{URL::to('admin/spots')}}">My Spots</a></li>
                @endif

                <li><a href="{{URL::to('users/logout')}}">Logout</a></li>
            </ul>
          </li>
          @else 
          <li><a href="{{URL::to('users/login')}}">Sign In</a></li>
          @endif

          <li><a href="{{URL::to('spots')}}">Spots</a></li>

          <li><a href="{{URL::to('itineraries')}}">Itineraries</a></li>

          <li><a href="{{URL::to('locals')}}">Locals</a></li>

          <li><a href="{{URL::to('about')}}">About Us</a></li>

          @if (Auth::check())
            <li class="add-spot">
              <a href="{{Auth::user()->can('create_spots')?Spot::addURL():URL::to('become-a-spotter')}}">
                <img data-no-retina src="{{ asset('assets/content/common/add-spot-btn.svg') }}">
              </a>
            </li>
          @endif

        </ul>
        {{-- -------------------------------- --}}

      </div>
    </div>
  
  {{-- site message   --}}
  <?php 
  $notifcations = Notification\Notification::siteNotifications()->forUser(Auth::user())->get(); 
  ?>
  @if (isset($notifcations))
  <div class="alert alert-warning site-notification">
    <div class="container">
      
      @foreach ($notifcations as $notice)
        <div class="row">
        <div class="col-md-10">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong>{{$notice->parent->title}}</strong> {{$notice->parent->body}}
        </div>
        </div>
      @endforeach

    </div>
  </div>
  @endif
</nav>
