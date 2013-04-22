<?php
	require_once( "database.inc.php" );

	function finish_update( $status, $output, $chirpdir )
	{
		$errmsg = '';
		
		if( open_database() === true )
		{
			$result = mysql_query( "SELECT * FROM statuses LIMIT 1" );
			$row = mysql_fetch_assoc($result);
		}
		else
		{
			$errmsg = "Could not open database.";
			$status = 13764;
		}
		if( !isset($row['original']) )
		{
			$result = mysql_query( "ALTER TABLE statuses ADD original varchar(140)" );
		}
		if( !isset($row['original_user_id']) )
		{
			$result = mysql_query( "ALTER TABLE statuses ADD original_user_id int" );
		}
		
		$result = mysql_query( "ALTER TABLE users ADD priv_key text" );
		
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