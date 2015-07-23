<footer class="footer">
	<div class="footer-bg">
		{{print_svg('assets/img/footer.svg')}}
	</div>
    <div class="container">
        <div class="footer-info">
            <ul class="list-inline site-map halp-list-menu">
                @foreach (get_site_map() as $link)
                    <li><a href="{{URL::to($link->url)}}"><h5>{{$link->name}}</h5></a></li>
                @endforeach
            </ul>
        </div>    
    </div>
</footer>