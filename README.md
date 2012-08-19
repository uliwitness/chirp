CHIRP
=====

An RSS-based, distributed Twitter clone. (server-side, not client-side)


INSTALLATION
------------

Upload the contents of this folder to your server. Edit the settings.ini file to contain login information to a MySQL database named "new_bird". Comment out the "return;" statement at the start of the "else if( strcmp($_GET['action'],"init") == 0 )" block in index.php.

Bring up the page in your browser and click "Init Database" to set up the database, then uncomment the return statement again.

