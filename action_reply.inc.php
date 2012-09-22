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
	
	$str = "<form action=\"index.php\" method=\"POST\">Reply to <i>".htmlentities($userinfo['fullname'])."</i>:<br />".htmlentities($row['text'])."<br/>\n";
	$str .= "<input type=\"text\" name=\"text\" size=\"60\" value=\"@".htmlentities($userinfo['shortname'])." \" /><input type=\"hidden\" name=\"action\" value=\"newstatus\"><input type=\"hidden\" name=\"statusid\" value=\"".$row['id']."\"></form>";
	
	echo make_header() . $str . make_footer();
?>