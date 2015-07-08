**Tools you will need**

<http://www.sublimetext.com/3>		
<http://www.sequelpro.com/>		
<https://www.mamp.info/en/mamp-pro/>			

**You need to have the following installed**			
<https://nodejs.org/>		
<http://bower.io/>		

**Open Terminal:**

**Clone github app**		
`git clone git@github.com:vanderlin/halp.git`		

**Setup MAMP**		
- host: `halp.com:8888`      
- doc root: `/halp/httpdocs`
- Clikc Start Server
	 
**Setup database**		
- open sequelpro		
- host: localhost 		
- connect via socket		
- user: root		
- pass: root		
- top left - click choose database...Then add database, enter `halp`		
	
**install composer**		
`https://getcomposer.org/doc/00-intro.md#globally`

**install bower components**		
`bower install`

**Run composer (this may take sometime)**		
`composer install`

**Run site setup**		
`php artisan site:setup`

**Open Chrome**			
<http://halp.com:8888>