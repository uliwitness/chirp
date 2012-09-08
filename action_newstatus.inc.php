<?php
		$userid = http_authenticated_userid();
		if( $userid === false )
			return;

		$text = mysql_real_escape_string($_REQUEST['text']);
		$inreplyto=mysql_real_escape_string($_REQUEST['inreplyto']);
		if( !isset($_REQUEST['inreplyto']) || strlen($inreplyto) == 0 || !is_integer($inreplyto) )
			$inreplyto = 0;
		$result = mysql_query ("INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text' )");
		
		print_r( mysql_error() );
?>