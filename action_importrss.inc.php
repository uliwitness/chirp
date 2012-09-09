<?php
	require( "importrss.inc.php" );

	$x = 0;
	$result = mysql_query( "SELECT id FROM users" );
	while( ($row = mysql_fetch_assoc($result)) !== false )
	{
		$n = import_external_user_tweets( $row['id'] );
		if( $n !== false )
			$x += $n;
	}
	
	$gPageTitle = "Statuses imported";

	echo make_header() . "$x items imported/updated." . make_footer();
?>