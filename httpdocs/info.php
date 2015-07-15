<?php opcache_reset(); ?>
<pre>

<?php

print_r(

	[	
		'this'=>dirname(__FILE__),
		['file'=>__DIR__.'/vars.php', 'data'=>require_once __DIR__.'/vars.php'],
		['file'=>__DIR__.'/../sub.php', 'data'=>require_once __DIR__.'/../sub.php'],
		['file'=>__DIR__.'/../bootstrap/paths.php', 'data'=>require_once __DIR__.'/../bootstrap/paths.php'],
		['file'=>__DIR__.'/../bootstrap/boot.php', 'data'=>require_once __DIR__.'/../bootstrap/boot.php'],
		'$_SERVER'=>$_SERVER,
	]
	);

?>

</pre>