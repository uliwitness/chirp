<?php
		$userid = http_authenticated_userid(true);
		if( $userid === false )
		{
			return;
		}

		$text = mysql_real_escape_string($_REQUEST['text']);
		
		$inreplyto = $_REQUEST['statusid'];
		$original = $_REQUEST['originalstatusid'];
		if( isset($inreplyto) && strlen($inreplyto) > 0 && is_numeric($inreplyto) )
		{
			$result = mysql_query( "SELECT * FROM statuses WHERE id='$inreplyto'" );
			print_r( mysql_error() );
			$row = mysql_fetch_assoc($result);
			
			$inreplyto = mysql_real_escape_string($row['url']);
			if( strlen($inreplyto) == 0 )
				$inreplyto = mysql_real_escape_string("http://".$_SERVER['HTTP_HOST']."/chirp/index.php?statusid=".$_REQUEST['statusid']);
		}
		else
		if( isset($original) && strlen($original) > 0 && is_numeric($original) )
		{
			$result = mysql_query( "SELECT * FROM statuses WHERE id='$original'" );
			print_r( mysql_error() );
			$row = mysql_fetch_assoc($result);
			
			$originaluserid = mysql_real_escape_string($row['original_user_id']);
			$inreplyto = mysql_real_escape_string($row['url']);
			if( strlen($original) == 0 )
				$original = mysql_real_escape_string("http://".$_SERVER['HTTP_HOST']."/chirp/index.php?statusid=".$_REQUEST['statusid']);
		}
		else
		{
			$inreplyto = '';
			$original = '';
			$originaluserid = 0;
		}
		$time = time();
		$url = "http://".$_SERVER['HTTP_HOST']."/chirp/index.php?statusid=";
		$result = mysql_query( "INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text', '$url', $time, '$original', '$originaluserid' )" );
		$id = mysql_insert_id();
		$url .= $id;
		$result = mysql_query( "UPDATE statuses SET url='$url' WHERE id='$id'" );
		print_r( mysql_error() );
		
		require( "action_home.inc.php" );
?>