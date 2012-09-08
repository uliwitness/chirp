<?php
		$userid = http_authenticated_userid(true);
		if( $userid === false )
			return;

		$shortname = mysql_real_escape_string($_REQUEST['shortname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$location = mysql_real_escape_string($_REQUEST['location']);
		$homepage = mysql_real_escape_string($_REQUEST['homepage']);
		$biography = mysql_real_escape_string($_REQUEST['biography']);
		$avatarurl = mysql_real_escape_string($_REQUEST['avatarurl']);
		$feedurl = mysql_real_escape_string($_REQUEST['feedurl']);
		$result = mysql_query ("INSERT INTO users VALUES ( NULL, '$shortname', '$fullname', '$location', '$homepage', '$biography', '$avatarurl', '', '', '$feedurl', 0 )");
		
		print_r( mysql_error() );
?>