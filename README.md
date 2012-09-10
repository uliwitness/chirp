Chirp
=====

An RSS-based, distributed Twitter clone. (server-side, not client-side)


Installation
------------

Upload the contents of this folder to your server, so it resides at the root of a domain
or sub-domain. Edit the settings.ini file to contain login information to a MySQL database.
Then bring up the site in a browser and you'll get a form to create your admin user.


Usage
-----

You MUST install this on one domain or sub-domain at root level. This is then equivalent
to one "Twitter account" for one person, and will import statuses from other people whose
RSS feeds you subscribe to ("follow").

To follow someone, use the Follow link *on your server* and type in their domain name
(without the "http://"!) (It will then know to look for an RSS feed on that domain, at
/.well-known/microblog.rss). After that, be sure to immediately click "Refresh Timeline"
so you see new statuses from any accounts you follow.

To set up your own avatar icon, copy a square image into the "avatars" folder on your
server, then you can select it in your profile. Currently there's no interface
for uploading images.


What's this good for?
---------------------

The idea is to lay down a simple, open exchange format for microblogs like App.net
and Twitter (RSS) that can easily be load-balanced via Akamai or similar services.
Everyone who can put an RSS file on a server can have a microblog. Everyone can
follow them, whether they host it themselves or have one at Twitter.

For most users, their ISP or web hoster or one of the big services would host
the feed, but it won't matter, because they're all interoperable. If Warner Bros.
want to host Robert Downey Jr.'s statuses on their server, they can. If a user
wants to use an iPhone client application to post, they can.

Since the user names are domain names, existing services can easily be integrated
by using subdomains. E.g. @uliwitness could map to uliwitness.twitter.com.

To make this more likely, this code is under a very permissive license. Everyone
can grab this code and just use it. Go ahead.


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
	
