<footer class="footer">
  

  <div class="container">
    <div class="footer-info">
        
        <div class="footer-inner">
            <div class="pull-left">
                <div class="logo">{{Helper::svg('assets/content/common/logo.svg')}}</div>
            </div>

            <div class="pull-right">
                <ul class="list-inline site-map">
                    @foreach (get_site_map() as $link)
                        <li><a href="{{URL::to($link->url)}}"><h5>{{$link->name}}</h5></a></li>
                    @endforeach
                </ul>
            </div>
        </div>    
        
    </div>
  </div>

    <div class="footer-bottom">
        <div class="contact-us">
            This is the footer
        </div>
    </div>

</footer>