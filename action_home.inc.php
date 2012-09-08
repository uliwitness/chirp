<?php
	if( !isset($_REQUEST['shortname']) )
		$gPageTitle = "All Statuses";
	else
		$gPageTitle = $_REQUEST['shortname']."'s Statuses";
	
	print_header();
	
	if( !isset($_REQUEST['shortname']) )
		$result = mysql_query ("SELECT * FROM statuses ORDER BY id DESC LIMIT 10");
	else
	{
		$userid = userid_from_shortname($_REQUEST['shortname']);
		$result = mysql_query ("SELECT * FROM statuses WHERE user_id='$userid' ORDER BY id DESC LIMIT 10");
	}
	while( ($row = mysql_fetch_assoc($result)) !== false )
		print_one_status_message($row);
	print_r( mysql_error() );
	
	print_footer();
?>