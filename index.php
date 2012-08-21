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
		$passwordhash = mysql_real_escape_string(crypt($unsafe_password,"hellacomplicated"));
		$querystr = "SELECT id FROM users WHERE shortname='$shortname' AND passwordhash='$passwordhash'";
		if( $mustBeAdmin )
			$querystr .= ' AND isAdmin=1';
		$result = mysql_query( $querystr );
		$row = mysql_fetch_assoc($result);
		$userid = mysql_real_escape_string($row['id']);

		print_r( mysql_error() );
		
		return $userid;
	}
	
	
	function userinfo_from_userid( $userid )
	{
		global $gCachedUsers;
		
		if( isset($gCachedUsers[$userid]) )
			return $gCachedUsers[$userid];
		
		$result = mysql_query ("SELECT shortname,fullname,avatarurl FROM users WHERE id='$userid'");
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
	    }
	    
	    return $userid;
	}
	
	function indent( $depth )
	{
		$str = "&nbsp;";
		for( $x = 0; $x < $depth; $x++ )
			$str .= "\t";
		return $str;
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
	
	if( strcmp($_REQUEST['format'],"rss") == 0 )
		require("rss_output.php");
	else
		require("html_output.php");
	
	if( strcmp($_REQUEST['action'],"newstatus") == 0 )
	{
		$userid = http_authenticated_userid();
		if( $userid === false )
			return;

		$text = mysql_real_escape_string($_REQUEST['text']);
		$inreplyto=mysql_real_escape_string($_REQUEST['inreplyto']);
		if( !isset($_REQUEST['inreplyto']) || strlen($inreplyto) == 0 || !is_integer($inreplyto) )
			$inreplyto = 0;
		$result = mysql_query ("INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text' )");
		
		print_r( mysql_error() );
	}
	else if( strcmp($_REQUEST['action'],"importrss") == 0 )
	{
		$userid = http_authenticated_userid();
		if( $userid === false )
			return;

		$reader = new XMLReader;
		$reader->open( $_REQUEST['url'] );
		
		$indentLevel = 0;
		
		while( $reader->read() )
		{
			if( $reader->nodeType == XMLReader::ELEMENT )
			{
				$indentLevel++;
				echo indent($indentLevel)."&lt;".$reader->name."&gt;<br />";
			}
			else if( $reader->nodeType == XMLReader::END_ELEMENT )
			{
				$indentLevel--;
				echo indent($indentLevel)."&lt;/".$reader->name."&gt;<br />";
			}
			else if( $reader->nodeType == XMLReader::TEXT )
				echo indent($indentLevel).htmlentities($reader->value)."<br />";
			else if( $reader->nodeType == XMLReader::CDATA )
				echo indent($indentLevel).htmlentities($reader->value)."<br />";
			else if( $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE || $reader->nodeType == XMLReader::WHITESPACE )
				;
			else
				echo indent($indentLevel)."[[".$reader->nodeType.": ".$reader->name.": ".htmlentities($reader->value)."]]<br />";
		}
		$reader->close();
		
		$result = mysql_query ("INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text' )");
		
		print_r( mysql_error() );
	}
	else if( strcmp($_REQUEST['action'],"newuser") == 0 )
	{
		$userid = http_authenticated_userid(true);
		if( $userid === false )
			return;

		$shortname = mysql_real_escape_string($_REQUEST['shortname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$location = mysql_real_escape_string($_REQUEST['location']);
		$homepage = mysql_real_escape_string($_REQUEST['homepage']);
		$biography = mysql_real_escape_string($_REQUEST['biography']);
		$avatarurl = mysql_real_escape_string($_REQUEST['avatarurl']);
		$passwordhash = mysql_real_escape_string(crypt($_REQUEST['password'],"hellacomplicated"));
		$email = mysql_real_escape_string($_REQUEST['email']);
		$result = mysql_query ("INSERT INTO users VALUES ( NULL, '$shortname', '$fullname', '$location', '$homepage', '$biography', '$avatarurl', '$passwordhash', '$email', 0 )");
		
		print_r( mysql_error() );
	}
	else if( strcmp($_REQUEST['action'],"init") == 0 )
	{
		return;	// Comment this line to set up everything.

		$result = mysql_query( "CREATE TABLE statuses ( id int NOT NULL PRIMARY KEY AUTO_INCREMENT, user_id int NOT NULL, replyto_id int, text varchar(140) )");

		print_r( mysql_error() );

		$result = mysql_query( "CREATE TABLE users ( id int NOT NULL PRIMARY KEY AUTO_INCREMENT, shortname varchar(80) NOT NULL UNIQUE, fullname varchar(80), location varchar(80), homepage varchar(140), biography varchar(140), avatarurl varchar(140), passwordhash varchar(140), email varchar(80), isAdmin int )");
		
		print_r( mysql_error() );
	}
	
	if( !isset($_REQUEST['shortname']) )
		$gPageTitle = "All Statuses";
	else
		$gPageTitle = $_REQUEST['shortname']."'s Statuses";
	
	print_header();
	
	if( !isset($_REQUEST['shortname']) )
		$result = mysql_query ("SELECT * FROM statuses ORDER BY id DESC LIMIT 10");
	else
	{
		$userid = userid_from_shortname($_REQUEST['shortname']);
		$result = mysql_query ("SELECT * FROM statuses WHERE user_id='$userid' ORDER BY id DESC LIMIT 10");
	}
	while( ($row = mysql_fetch_assoc($result)) !== false )
		print_one_status_message($row);
	print_r( mysql_error() );
	
	print_footer();
?>