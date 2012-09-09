<?php
	$userid = http_authenticated_userid(true);
	if( $userid === false )
		return;

	$str = '<form action="index.php" method="POST">
	<input type="hidden" name="action" value="finish_unfollow" />
	<b>Unfollow user:</b><br /><select name="shortname">';
	
	$result = mysql_query( "SELECT followee FROM follows WHERE follower='$userid'" );
	print_r( mysql_error() );
	while( ($row = mysql_fetch_assoc($result)) !== false )
	{
		$userinfo = userinfo_from_userid( $row['followee'] );
		$str .= "<option value=\"".htmlentities($userinfo['shortname'])."\">".htmlentities($userinfo['fullname'])." (".htmlentities($userinfo['shortname']).")</option>\n";
	}
	
	$str .= '</select><br />
	<input type="submit" name="Unfollow" />
	</form>';
	echo make_header() . $str . make_footer();
?>