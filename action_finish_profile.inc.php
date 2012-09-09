<?php
		global $gPageTitle;

		$userid = http_authenticated_userid(true);
		if( $userid === false )
			return;
		
		// Create the first admin user:
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$location = mysql_real_escape_string($_REQUEST['location']);
		$homepage = mysql_real_escape_string($_REQUEST['homepage']);
		$biography = mysql_real_escape_string($_REQUEST['biography']);
		$avatarurl = mysql_real_escape_string('http://'.$_SERVER['HTTP_HOST'].'/avatars/'.$_REQUEST['avatarurl']);
		$email = mysql_real_escape_string($_REQUEST['email']);
		$result = mysql_query ("UPDATE users SET fullname='$fullname', location='$location', homepage='$homepage', biography='$biography', avatarurl='$avatarurl', email='$email' WHERE id='$userid'");
		
		$gPageTitle = "User updated";
		
		echo make_header() . "User profile changed." . make_footer();
?>