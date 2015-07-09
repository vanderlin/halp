<p align="center">
  <img width="230" height="120" src="https://www.dropbox.com/s/vt6jju9zcg16nyj/halp.png?dl=1">
</p>

## Tools you will need		
<http://www.sublimetext.com/>				
<http://www.sequelpro.com/>			
<https://www.mamp.info>			

## Requirements
**You need to have the following installed**			

- **Node**		
<https://nodejs.org/>		
In Terminal type: `npm -v`		

- **Bower**		
<http://bower.io/>		
In Terminal type: `bower -v`		

- **Composer**		
In Terminal type: `composer -v`		 
To install:		
	- `curl -sS https://getcomposer.org/installer | php`			     
	- `mv composer.phar /usr/local/bin/composer`			

- **php 5.4.x**		
In Terminal type: `php -v`		

- **php mcrypt extension**			
In Terminal type: `php -i | grep mcrypt`	
You should see `mcrypt support => enabled` in the output. You may need to alias your version of php to point to the version in MAMP. 			

- Fix mcrypt			
	In Terminal:						
	`open ~/.bash_profile`If this file does not exist you need to create it. `touch ~/.bash_profile`		

	Find out what version of **php** you are running in **MAMP**. Click on MAMP, then the gear icon (preferences), then the PHP tab copy the version you see ie: `5.6.7`			

	Add the following line to the bottom of the `.bash_profile` file. 			
	`alias php='/Applications/MAMP/bin/php/php5.6.7/bin/php'`			

	Reload your bash profile			
	`. ~/.bash_profile`			

	Run `php -i | grep mcrypt` and see if you fixed it.

## Installation			

- **Open Terminal:**		
	- `/Applications/Utilities/Terminal.app`			

- **Clone Github App**		
	- `cd "A director that you want to install the app ie: ~/Sites"`			 
	- `git clone git@github.com:vanderlin/halp.git`		

- **Setup MAMP**		
	- doc root: `(app location)/halp/httpdocs`			
	- Click Start Server
	 
- **Setup Local Database**		
	- Open **sequelpro**		
	- Host: **localhost** 		
	- Click: **Connect via socket**		
	- User: **root**		
	- Password: **root**		
	- Top left - **click choose database**...**Then add database**, enter `halp`		
	
- **install bower components**		
	- `bower install`

- **Run composer (this may take sometime)**		
	- `composer install`

- **Run Site Setup**		
	- `php artisan site:setup`

- **Open Chrome**			
	- <http://localhost:8888>