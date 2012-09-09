<?php
		$result = mysql_query( "SELECT id FROM users" );
		if( mysql_errno() == 0 )	// Already have a users table?
		{
			if( mysql_num_rows( $result ) != 0 )	// And there are users in it?
				return;	// Don't allow installation!
		}
		
		$result = mysql_query( "CREATE TABLE statuses ( id int NOT NULL PRIMARY KEY AUTO_INCREMENT, user_id int NOT NULL, replyto_id int, text varchar(256), url varchar(140) UNIQUE, timestamp int NOT NULL )");

		print_r( mysql_error() );

		$result = mysql_query( "CREATE TABLE users ( id int NOT NULL PRIMARY KEY AUTO_INCREMENT, shortname varchar(80) NOT NULL UNIQUE, fullname varchar(80), location varchar(80), homepage varchar(140), biography varchar(140), avatarurl varchar(140), passwordhash varchar(140), email varchar(80), feedurl varchar(140), isAdmin int )");
		
		print_r( mysql_error() );
		
		// Create the first admin user:
		$shortname = mysql_real_escape_string($_REQUEST['shortname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$location = mysql_real_escape_string($_REQUEST['location']);
		$homepage = mysql_real_escape_string($_REQUEST['homepage']);
		$biography = mysql_real_escape_string($_REQUEST['biography']);
		$avatarurl = mysql_real_escape_string($_REQUEST['avatarurl']);
		$passwordhash = mysql_real_escape_string(crypt($_REQUEST['password'],"hellacomplicated"));
		$email = mysql_real_escape_string($_REQUEST['email']);
		$result = mysql_query ("INSERT INTO users VALUES ( NULL, '$shortname', '$fullname', '$location', '$homepage', '$biography', '$avatarurl', '$passwordhash', '$email', '', 1 )");
		
		$gPageTitle = "Installation Complete";
		
		echo make_header() . "Installation finished." . make_footer();
?>