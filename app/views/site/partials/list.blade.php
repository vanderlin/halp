@foreach ($items as $item)
	@include($view, array($item_name=>$item))
@endforeach