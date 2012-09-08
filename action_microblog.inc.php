<?php
	$result = mysql_query ("SELECT shortname FROM users WHERE isAdmin=1 LIMIT 1");
	print_r( mysql_error() );
	$row = mysql_fetch_assoc($result);
	$_REQUEST['shortname'] = $row['shortname'];
	
	require("action_home.inc.php");
?>