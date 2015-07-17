<?php $data = (object)$data; ?>
<table class="ui celled striped table">
	<thead>
    	<tr>
    	<th colspan="1"><code>{{$data->method}}</code></th>
    	<th colspan="2"><code>{{$data->url}}</code></th>
  		</tr>
  	</thead>
  	<tbody>
  	<tr>
  		<td colspan="3">{{Config::get('config.api_base')}}/projects</td>
  	</tr>
  	
  	<tr>
	  	<td colspan="3">
			<pre><code class="json"></code></pre>
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