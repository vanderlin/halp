Thanks for helping!
<hr>
<p>
{{$task->creator->firstname}} was notified that you claimed this task and will reach out soon. 
Or you can reach out to {{$task->creator->firstname}}, if you're feeling like a go-getter.
</p>
<div class="progress-button small">
	<button data-id="{{$task->id}}" id="claimed-close-popup-button"><span>OK</span></button>
</div>