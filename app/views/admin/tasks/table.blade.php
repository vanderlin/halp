@if ($tasks->count()>0)
	<table class="ui celled table">
	<thead>
		<tr>
			<th>#</th>
			<th>Title</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($tasks as $task)
			<tr class="{{$task->isExpired ? 'negative': ''}}">
				<td>{{$task->id}}</td>
				<td>
					<div class="ui list">
						<div class="item"><b>{{$task->title}}</b></div>
						<div class="item">Created by: {{$task->creator->getName()}}</div>
						<div class="item">Created by: {{$task->claimer?$task->claimer->getName():'<span class="color-red">Not yet claimed</span>'}}</div>
					</div>
				</td>
				<td>
					<code>
						<ul>
							<li>task_date: {{$task->task_date==NULL?'No task_date set':$task->task_date}}</li>
							<li>Due Date: {{$task->date->toDateString()}}</li>
							<li>Expiration Date: {{$task->getExpirationDate()->toDateString()}}</li>
						</ul>
					</code>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>	
@else
<b>No Tasks</b>
@endif