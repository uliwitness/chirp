<?php
	function userid_from_shortname( $unsafe_shortname )
	{
		$shortname = mysql_real_escape_string($unsafe_shortname);
		$result = mysql_query ("SELECT id FROM users WHERE shortname='$shortname'");
		print_r( mysql_error() );
		$row = mysql_fetch_assoc($result);
		$userid = mysql_real_escape_string($row['id']);
		return $userid;
	}
	
	
	function userid_for_shortname_password( $unsafe_shortname, $unsafe_password, $mustBeAdmin = false )
	{
		$shortname = mysql_real_escape_string($unsafe_shortname);
		if( strlen($unsafe_password) == 0 )
			return false;
		$passwordhash = mysql_real_escape_string(crypt($unsafe_password,"hellacomplicated"));
		if( strcmp($passwordhash,crypt("","hellacomplicated")) == 0 )
			return false;
		$querystr = "SELECT id FROM users WHERE shortname='$shortname' AND passwordhash='$passwordhash'";
		if( $mustBeAdmin )
			$querystr .= ' AND isAdmin=1';
		$result = mysql_query( $querystr );
		if( $result === false )
			return false;
		$row = mysql_fetch_assoc($result);
		if( $row == false )
			return false;
		$userid = mysql_real_escape_string($row['id']);
		if( $userid == 0 )
			return false;

		print_r( mysql_error() );
		
		return $userid;
	}
	
	
	function userinfo_from_userid( $userid )
	{
		global $gCachedUsers;
		
		if( isset($gCachedUsers[$userid]) )
			return $gCachedUsers[$userid];
		
		$result = mysql_query ("SELECT shortname,fullname,avatarurl,location,homepage,biography,email,feedurl FROM users WHERE id='$userid'");
		print_r( mysql_error() );
		$gCachedUsers[$userid] = $row = mysql_fetch_assoc($result);
		return $row;
	}
	
	
	function http_authenticated_userid( $mustBeAdmin = false )
	{
	    global $chirp_config;
	    
	    if( !isset($_SERVER['PHP_AUTH_USER']) ) // Likely that we need PHP CGI workaround for HTTP AUTH?
	    {
	       list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6))); // Get user/pw from environment and put them in the array.
	        if( strlen($_SERVER['PHP_AUTH_USER']) == 0 || strlen($_SERVER['PHP_AUTH_PW']) == 0 )
	        {
	            unset($_SERVER['PHP_AUTH_USER']);
	            unset($_SERVER['PHP_AUTH_PW']);
	        }
	    }
	    
	    // open a user/pass prompt:
	    if( !isset($_SERVER['PHP_AUTH_USER']) )
	    {
	        header('WWW-Authenticate: Basic realm="Chirp"');
	        header('HTTP/1.0 401 Unauthorized');
	        return false;
	    }
	    else    // Have a username/password? Compare to config file:
	    {
		    $userid = userid_for_shortname_password( strtolower(trim($_SERVER['PHP_AUTH_USER'])), trim($_SERVER['PHP_AUTH_PW']), $mustBeAdmin );
		    if( $userid === false )
		    {
		        header('WWW-Authenticate: Basic realm="Chirp"');
		        header('HTTP/1.0 401 Unauthorized');
		    	return false;
		    }
	    }
	    
	    return $userid;
	}
	
	global $gPageTitle;
	
	$settings = array();
	$ini_lines = file("settings.ini");
	for( $x = 0; $x < sizeof($ini_lines); $x++ )
	{
		$parts = explode( "=", $ini_lines[$x], 2 );
		$settings[$parts[0]] = trim($parts[1]);
	}
	if( !isset($settings['dbserver']) || !isset($settings['dbuser'])
		|| !isset($settings['dbpassword']) )
	{
		echo "<b>Setup error. No/invalid ini file.</b>";
		return;
	}
	
	mysql_connect( $settings['dbserver'], $settings['dbuser'], $settings['dbpassword'] );
	
	mysql_select_db( "new_bird" );
	
	mysql_set_charset("utf8");
	
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
		$result = mysql_query( "SELECT id FROM users" );
		if( mysql_errno() != 0 )	// Don't have a users table?	// +++ What if DB server is down? Wouldn't want to install again!
			$action = 'install';
		else if( mysql_num_rows( $result ) == 0 )	// Or there are no users in it?
			$action = 'install';
	}
	
	require("format_$format.inc.php");
	require("action_$action.inc.php");
?>