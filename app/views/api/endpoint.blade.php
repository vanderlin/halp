<?php $data = (object)$data; ?>
<div class="ui container endpoint">
	<h2 class="name" id="{{$data->name}}">{{link_to('#'.$data->name, $data->name)}}</h2>
	<table class="ui celled striped table">
		<thead>
	    	<tr>
	    	<th colspan="1"><code>{{$data->method}}</code></th>
	    	<th colspan="2"><code>{{$data->url}}</code></th>
	  		</tr>
	  	</thead>
	  	<tbody>
	  	<tr>
	  		<td colspan="3">
	  		{{Config::get('config.api_base')}}{{$data->url}}
	  		</td>
	  	</tr>
	  	
	  	<tr>
		  	<td colspan="3">
				<pre><code class="json">{{trim($data->example)}}</code></pre>
			</td>
		</tr>
	    <tr>
	    	<td class="collapsing" colspan="1">
	        	<i class="info icon"></i>
	      	</td>
	      	<td>{{$data->description}}</td>
	    </tr>
		</tbody>
	</table>
</div>