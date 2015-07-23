@if ($paginator->getLastPage() > 1)
		<a href="{{ $paginator->getUrl(1) }}" class="item{{ ($paginator->getCurrentPage() == 1) ? ' disabled' : '' }}">Previous</a>
		
		@for ($i = 1; $i <= $paginator->getLastPage(); $i++)
			<a href="{{ $paginator->getUrl($i) }}" class="item{{ ($paginator->getCurrentPage() == $i) ? ' active' : '' }}">{{ $i }}</a>
		@endfor
		
		<a href="{{ $paginator->getUrl($paginator->getCurrentPage()+1) }}" class="item{{ ($paginator->getCurrentPage() == $paginator->getLastPage()) ? ' disabled' : '' }}">Next</a>
@endif

