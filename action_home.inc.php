<?php
	if( !isset($_REQUEST['shortname']) )
		$gPageTitle = "All Statuses";
	else
		$gPageTitle = $_REQUEST['shortname']."'s Statuses";
	
	if( !isset($_REQUEST['shortname']) )
		$result = mysql_query ("SELECT * FROM statuses ORDER BY timestamp DESC");
	else
	{
		$userid = userid_from_shortname($_REQUEST['shortname']);
		$result = mysql_query ("SELECT * FROM statuses WHERE user_id='$userid' ORDER BY timestamp DESC");
	}
	
	global $gNewestTimestamp;
	
	$gNewestTimestamp = 0;
	$str = "";
	while( ($row = mysql_fetch_assoc($result)) !== false )
	{
		$str .= make_one_status_message($row);
		if( $row['timestamp'] > $gNewestTimestamp )
			$gNewestTimestamp = $row['timestamp'];
	}
	print_r( mysql_error() );
	
	echo make_header() . $str . make_footer();
?>