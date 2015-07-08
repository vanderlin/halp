<h1>HALP</h1>
@if (Auth::check())
	<ul>
		<li>
			{{ link_to(Auth::user()->getProfileURL(), 'view profile')}}
		</li>

		<li>
			{{ link_to('logout', 'Sign Out')}}
		</li>
	</ul>
@endif