<?php
	function finish_update( $status, $output, $chirpdir )
	{
		$errmsg = '';
		
		$result = mysql_query( "SELECT * FROM statuses WHERE * LIMIT 1" );
		$row = mysql_fetch_assoc($result);
		if( !isset($row['original']) )
		{
			$result = mysql_query( "ALTER TABLE statuses ADD original varchar(140)" );
			if( mysql_errno() != 0 )
			{
				$status = 13762;
				$errmsg = "Could not add new 'origins' field to statuses.";
			}
		}
		if( !isset($row['original_user_id']) )
		{
			$result = mysql_query( "ALTER TABLE statuses ADD original_user_id int" );
			if( mysql_errno() != 0 )
			{
				$status = 13763;
				$errmsg = "Could not add new 'original_user_id' field to statuses.";
			}
		}
		
		echo "<html>\n<head><title>Update Chirp</title>\n</head>\n<body>\n";
		if( $status == 0 )
			echo "<h1>Update Succeeded</h1>\n";
		else if( strlen($errmsg) > 0 )
			echo "<h1>Error during Update: ".htmlentities($errmsg)."</h1>\n";
		else
			echo "<h1>Error $status during Update</h1>\n";
		echo "<pre>".htmlentities($output)."</pre>\n";
		echo "<a href=\"/chirp/\">Back to Chirp</a>\n";
		echo "</body>\n</html>";
	}
?>