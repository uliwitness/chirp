CHIRP
-----

An RSS-based, distributed Twitter clone. (server-side, not client-side)


INSTALLATION
------------

Upload the contents of this folder to your server. Make sure the script has write access to the "tweet" folder. Edit the config/config.ini file to contain your username, password and time zone.


USAGE
-----

index.php                   -   Write tweets, and they will be added to an RSS feed file.
index.php?do=exchangerss    -   View the RSS feed of your own tweets, which is the URL that others will subscribe to.
index.php?do=directory      -   View the user directory and/or follow someone.
