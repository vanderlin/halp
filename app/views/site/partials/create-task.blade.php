{{Form::open(['route'=>'tasks.store'])}}
<section class="content bgcolor">
	<span class="input input--nao">
		<input class="input__field input__field--nao" type="text" id="task-title" placeholder="ex: proofreading" autocomplete="off" name="title" />
		<label class="input__label input__label--nao" for="task-title">
			<span class="input__label-content input__label-content--nao">I need a hand with:</span>
		</label>
		<svg class="graphic graphic--nao" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
			<path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"/>
		</svg>
	</span>
	<span class="input input--nao">
		<input class="input__field input__field--nao" type="text" id="input-2" placeholder="project name" name="project" />
		<label class="input__label input__label--nao" for="input-2">
			<span class="input__label-content input__label-content--nao">For:</span>
		</label>
		<svg class="graphic graphic--nao" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
			<path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"/>
		</svg>
	</span>
	<span class="input input--nao">
		<input class="input__field input__field--nao" type="text" id="input-3" placeholder="mm/dd/yy"/>
		<label class="input__label input__label--nao" for="input-3">
			<span class="input__label-content input__label-content--nao">On:</span>
		</label>
		<svg class="graphic graphic--nao" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
			<path d="M0,56.5c0,0,298.666,0,399.333,0C448.336,56.5,513.994,46,597,46c77.327,0,135,10.5,200.999,10.5c95.996,0,402.001,0,402.001,0"/>
		</svg>
	</span>
	<div class="box">
		<!-- progress button -->
		<div class="progress-button elastic">
			<button><span>Halp Me</span></button>
			<svg class="progress-circle" width="70" height="70"><path d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z"/></svg>
			<svg class="checkmark" width="70" height="70"><path d="m31.5,46.5l15.3,-23.2"/><path d="m31.5,46.5l-8.5,-7.1"/></svg>
			<svg class="cross" width="70" height="70"><path d="m35,35l-9.3,-9.3"/><path d="m35,35l9.3,9.3"/><path d="m35,35l-9.3,9.3"/><path d="m35,35l9.3,-9.3"/></svg>
		</div>
	</div>
</section>
{{Form::close()}}

<script type="text/javascript">
	var data = {{json_encode(Project\Project::all()->lists('title'))}}
	$( 'input[name="project"]' ).autocomplete({
		source: data,
	 	minLength: 0
	})
	.focus(function() {
    	$(this).autocomplete('search', $(this).val())
	});;
</script>