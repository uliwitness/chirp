<?php
		$userid = http_authenticated_userid(true);
		if( $userid === false )
		{
			return;
		}

		$text = mysql_real_escape_string($_REQUEST['text']);
		if( isset( $_REQUEST['inreplyto'] ) )
			$inreplyto = mysql_real_escape_string($_REQUEST['inreplyto']);
		else
			$inreplyto = '';
		$time = time();
		$url = "http://".$_SERVER['HTTP_HOST']."/index.php?statusid=";
		$result = mysql_query( "INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text', '$url', $time )" );
		$id = mysql_insert_id();
		$url .= $id;
		$result = mysql_query( "UPDATE statuses SET url='$url' WHERE id='$id'" );
		print_r( mysql_error() );
		
		require( "action_home.inc.php" );
?>