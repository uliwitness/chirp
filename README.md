Chirp
=====

An RSS-based, distributed Twitter clone. (server-side, not client-side)


Installation
------------

Upload the contents of this folder to your server. Edit the settings.ini file to contain
login information to a MySQL database named "new_bird".
Then bring up the site in a browser to initialize the database and you'll get a form to
create your admin user.


Usage
-----

The idea is that you install this on one domain or sub-domain, preferably at root level
so you get a short URL. This is then equivalent to one "Twitter account" for one person,
and will import statuses from other people whose RSS feeds you subscribe to ("follow").


License
-------

	Copyright (c) 2012 by Uli Kusterer
	
	This software is provided 'as-is', without any express or implied
	warranty. In no event will the authors be held liable for any damages
	arising from the use of this software.
	
	Permission is granted to anyone to use this software for any purpose,
	including commercial applications, and to alter it and redistribute it
	freely, subject to the following restrictions:
	
		1. The origin of this software must not be misrepresented; you must not
		claim that you wrote the original software. If you use this software
		in a product, an acknowledgment in the product documentation would be
		appreciated but is not required.
		
		2. Altered source versions must be plainly marked as such, and must not be
		misrepresented as being the original software.
		
		3. This notice may not be removed or altered from any source
		distribution.
	
