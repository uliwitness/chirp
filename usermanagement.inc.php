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
	    global $chirp_config, $gCurrentUserID;
	    
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
	    
	    $gCurrentUserID = $userid;
	    
	    return $userid;
	}
?>