<?php
	$userid = http_authenticated_userid(true);
	if( $userid === false )
		return;
	
	$statusid = $_REQUEST['statusid'];
	if( !isset($statusid) || strlen($statusid) == 0 || !is_numeric($statusid) )
		return;
	$result = mysql_query( "SELECT * FROM statuses WHERE id='$statusid'" );
	print_r( mysql_error() );

	$row = mysql_fetch_assoc($result);
	
	$userinfo = userinfo_from_userid( $row['user_id'] );
	
	$str = "<form action=\"index.php\" method=\"POST\">Repost <i>".htmlentities($userinfo['fullname'])."</i>:<br />"."RP @".htmlentities($userinfo['shortname'])." ".htmlentities($row['text'])."<br/>\n";
	$str .= "<input type=\"hidden\" name=\"text\" size=\"60\" value=\"".rawurlencode($row['text'])." \" />\n<input type=\"hidden\" name=\"action\" value=\"newstatus\">\n<input type=\"hidden\" name=\"originalstatusid\" value=\"".$row['id']."\"><br />\n<input type=\"submit\" name=\"submit\" />\n</form>";
	
	echo make_header() . $str . make_footer();
?>