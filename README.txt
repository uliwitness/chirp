CHIRP
-----

An RSS-based, distributed Twitter clone. (server-side, not client-side)


INSTALLATION
------------

Upload the contents of this folder to your server. Edit the settings.ini file to contain login information to a MySQL database named "new_bird". Comment out the "return;" statement at the start of the "else if( strcmp($_GET['action'],"init") == 0 )" block in index.php.

Bring up the page index.php?action=init in your browser to set up the database, then uncomment the return statement again.


USAGE
-----

index.php                   -   Write tweets, and they will be added to an RSS feed file.
index.php?do=exchangerss    -   View the RSS feed of your own tweets, which is the URL that others will subscribe to.
index.php?do=directory      -   View the user directory and/or follow someone.
