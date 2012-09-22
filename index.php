<?php
	require( "usermanagement.inc.php" );
	require( "database.inc.php" );
		
	global $gPageTitle;
	
	date_default_timezone_set( "GMT" );
	
	$haveDatabase = open_database();
		
	// Determine what format to output in:
	if( !isset($_REQUEST['format']) )
		$format = "html";
	else
		$format = $_REQUEST['format'];
	$format = str_replace("/", "", $format);
	$format = str_replace("\n", "", $format);
	$format = str_replace("\r", "", $format);
	
	// Determine what to do:
	if( !isset($_REQUEST['action']) )
		$action = "home";
	else
		$action = $_REQUEST['action'];
	$action = str_replace("/", "", $action);
	$action = str_replace("\n", "", $action);
	$action = str_replace("\r", "", $action);
	
	// Have we been set up yet?
	if( strcmp($action,"install") != 0 && strcmp($action,"finish_install") != 0 )
	{
		if( $haveDatabase )
		{
			$result = mysql_query( "SELECT id FROM users" );
			if( mysql_errno() != 0 )	// Don't have a users table?	// +++ What if DB server is down? Wouldn't want to install again!
				$action = 'install';
			else if( mysql_num_rows( $result ) == 0 )	// Or there are no users in it?
				$action = 'install';
		}
		else
			$action = "install";
	}
	
	require("format_$format.inc.php");
	require("action_$action.inc.php");
?>