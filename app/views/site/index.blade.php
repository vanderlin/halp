@extends('site.layouts.default', ['use_navigation'=>true, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}}
@stop

@section('head')
@stop

@section('scripts')
@stop


@section('content')

	@include('site.partials.create-task')
	
	
			<section class="content">
				<div class="task">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Todd V.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">Proofreading</span>
						<hr>
						<span class="project-name">For NASDAQ</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Kim M.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">UI Design</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Danny D.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">Deck Design</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Ashley H.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">Sound Design</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Nick D.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">3D Modeling</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Todd V.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">Printing Help</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Todd V.</div>
				</div>
				<div class="task">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
						<div class="progress-button small">
							<button><span>Claim task</span></button>
						</div>
					</div>
					<div class="posted-by">Posted by Todd V.</div>
				</div>

				<div class="turtle-break">
					<div class="turtle-line"></div>
					<img src="img/happy-turtle.png" width="111px" height="58px" />
					<div class="turtle-line"></div>
				</div>

				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>
				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>

				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>

				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>
				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>
				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>

				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>

				<div class="task claimed">
					<div class="task-details">
						<span class="task-name">Animation</span>
						<hr>
						<span class="project-name">For Turf Wars</span>
						<span class="date">July 3, 2015</span>
					</div>
					<div class="claimed-by">Claimed by Todd V.</div>
				</div>

			</section>
@stop
    
{{--


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
--}}