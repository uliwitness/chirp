<?php
		$userid = http_authenticated_userid(true);
		if( $userid === false )
			return;

		$shortname = mysql_real_escape_string($_REQUEST['shortname']);
		$fullname = $shortname;
		$location = '';
		$homepage = '';
		$biography = '';
		$avatarurl = '';
		$feedurl = mysql_real_escape_string("http://".$_REQUEST['shortname']."/microblog.rss");
		$result = mysql_query( "INSERT INTO users VALUES ( NULL, '$shortname', '$fullname', '$location', '$homepage', '$biography', '$avatarurl', '', '', '$feedurl', 0 )" );
		if( mysql_errno() != 0 )	// Already know this user?
		{
			$result = mysql_query( "SELECT id FROM users WHERE feedurl='$feedurl'" );
			if( mysql_errno() == 0 )
			{
				$row = mysql_fetch_assoc($result);
				$newuserid = $row['id'];
			}
		}
		else
			$newuserid = mysql_insert_id();
		
		$result = mysql_query( "INSERT INTO follows VALUES ( '$userid', '$newuserid' )" );
		print_r( mysql_error() );

		
		$gPageTitle = "Following user";
		
		echo make_header() . "Now following ".htmlentities($_REQUEST['shortname'])."." . make_footer();
?>