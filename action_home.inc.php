<?php
	global $gPageTitle;

	$querystr = "SELECT * FROM statuses";
	$gPageTitle = "All Statuses";
	if( isset($_REQUEST['statusid']) && strlen($_REQUEST['statusid']) > 0 && is_numeric($_REQUEST['statusid']) )
	{
		$gPageTitle = "Status ID ".$_REQUEST['statusid'];
		$querystr = "SELECT * FROM statuses WHERE id='".mysql_real_escape_string($_REQUEST['statusid'])."'";
	}
	else if( strcmp($_REQUEST['action'],"timeline") == 0 )
	{
		$userid = http_authenticated_userid(true);
		$userinfo = userinfo_from_userid( $userid );
		$gPageTitle = $userinfo['fullname']."'s Timeline";
		$querystr = "SELECT * FROM statuses WHERE user_id IN ( '$userid'";
		$result = mysql_query( "SELECT followee FROM follows WHERE follower='$userid'" );
		print_r( mysql_error() );
		while( ($row = mysql_fetch_assoc($result)) !== false )
			$querystr .= ", '".$row['followee']."'";
		$querystr .= " )";
	}
	else if( isset($_REQUEST['shortname']) )
	{
		$userid = userid_from_shortname( $_REQUEST['shortname'] );
		$userinfo = userinfo_from_userid( $userid );
		$gPageTitle = $userinfo['fullname'];
		$querystr = "SELECT * FROM statuses WHERE user_id='$userid'";
	}
	$result = mysql_query( $querystr." ORDER BY timestamp DESC" );
	print_r( mysql_error() );
	
	global $gNewestTimestamp;
	
	$gNewestTimestamp = 0;
	$str = "";
	while( ($row = mysql_fetch_assoc($result)) !== false )
	{
		$str .= make_one_status_message($row);
		if( $row['timestamp'] > $gNewestTimestamp )
			$gNewestTimestamp = $row['timestamp'];
	}
	
	echo make_header() . make_send_field() . $str . make_footer();
?>