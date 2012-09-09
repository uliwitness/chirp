#!/usr/bin/php
<?php
	require( "usermanagement.inc.php" );
	require( "database.inc.php" );
	require( "importrss.inc.php" );
	
	date_default_timezone_set( "GMT" );
	
	if( !open_database() )
		return;

	$result = mysql_query( "SELECT id FROM users" );
	while( ($row = mysql_fetch_assoc($result)) !== false )
	{
		import_external_user_tweets( $row['id'] );
	}
?>