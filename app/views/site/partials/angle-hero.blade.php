{{-- ---------------------------------------- --}}
<div class="jumbotron profile-jumbotron">
	

	<div class="container-fluid jumbo-inner">
		

		<!-- angle background -->

		<!-- The photo carousel -->
		<!-- div class="profile-info-container">
			
			<div class="col-md-8">
			test
		    </div>
		
	    </div> -->
	    <!-- End photo carousel -->


	    <!-- Banner Info -->
	    <div class="baner-container">
	    	
	    	<div class="background"></div>

			<div class="container profile-info-cointainer">
				<div class="row">
					<div class="col-md-8">
						@include('site.profile.info', array('user' => $user))
					</div>			
				</div>
			</div>
		</div>
		<!-- End Banner Info -->


		<!-- User Circles Info -->
		<div class="circles-container">
			<div class="container">
				<div class="row">
					<div class="col-md-8">
						@include('site.profile.info-circles', array('user' => $user))
					</div>			
				</div>
			</div>
		</div>
		<!-- End User Circles Info -->



	    <!-- The Spot Map -->
	    <div class="hero-info-container">
			
			
	    	<div class="info container">
		    		<div class="row">
		    			<div class="col-md-offset-8 col-md-4 col-sm-offset-9 col-sm-3 hidden-xs info-col-wrapper">
					    	<div class="inner">
						    		
			    				<div class="row">
					    			<div class="google-map-container  ">
					    				<div id="user-map" class="sidebar-map"></div>
					    			</div>
				    			</div>

					    	</div>
				    	</div>
			    	</div>
				
	    	</div>
	    </div>
	    <!-- End the spot map -->
	
	</div>

</div>
{{-- ---------------------------------------- --}}

