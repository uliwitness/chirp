<?php
		$userid = http_authenticated_userid(true);
		if( $userid === false )
		{
			return;
		}

		$statustodelete = $_REQUEST['statusid'];
		$result = mysql_query( "UPDATE statuses SET text='' WHERE user_id = '$userid' AND id='$statustodelete'" );
		print_r( mysql_error() );
		
		require( "action_home.inc.php" );
?>