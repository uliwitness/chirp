<?php
		$userid = http_authenticated_userid();
		if( $userid === false )
			return;

		$text = mysql_real_escape_string($_REQUEST['text']);
		$inreplyto = mysql_real_escape_string($_REQUEST['inreplyto']);
		if( !isset($_REQUEST['inreplyto']) || strlen($inreplyto) == 0 || !is_integer($inreplyto) )
			$inreplyto = 0;
		$time = time();
		$url = "http://".$_SERVER['HTTP_HOST']."/index.php?statusid=";
		$result = mysql_query( "INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text', '$url', $time )" );
		$id = mysql_insert_id();
		$url .= $id;
		$result = mysql_query( "UPDATE statuses SET url='$url' WHERE id='$id'" );
		print_r( mysql_error() );
		
		require( "action_home.inc.php" );
?>