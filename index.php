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
		    if( strlen(trim($_SERVER['PHP_AUTH_PW'])) == 0 )	// Don't allow empty passwords for logging in. That'd be a user whose feed a real user subscribes to.
		    	return false;
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
	
	
	function unique_tag_name( $basename, $peers )
	{
		$currname = $basename;
		$x = 1;
		while( isset($peers[$currname]) )
		{
			$currname = $basename.$x;
			$x++;
		}
		return $currname;
	}
	
	
	function parse_feed( $reader )
	{
		$tagstack = array(array());
		$tagnamestack = array("root");
		
		while( $reader->read() )
		{
			if( $reader->nodeType == XMLReader::ELEMENT )
			{
				$tagname = unique_tag_name($reader->name,$tagstack[sizeof($tagstack)-1]);
				if( $reader->isEmptyElement )
				{
					$tagstack[sizeof($tagstack)-1][$tagname] = array();
				}
				else
				{
					array_push( $tagnamestack, $tagname );
					array_push( $tagstack, array() );
				}
			}
			else if( $reader->nodeType == XMLReader::END_ELEMENT )
			{
				$currtag = array_pop($tagstack);
				$currtagname = array_pop($tagnamestack);
				$tagstack[sizeof($tagstack)-1][$currtagname] = $currtag;
			}
			else if( $reader->nodeType == XMLReader::TEXT )
			{
				$tagstack[sizeof($tagstack)-1] = $reader->value;
			}
			else if( $reader->nodeType == XMLReader::CDATA )
			{
				$tagstack[sizeof($tagstack)-1] = $reader->value;
			}
			else if( $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE || $reader->nodeType == XMLReader::WHITESPACE )
				;
		}
		
		return $tagstack[0];
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
		$userid = userid_from_shortname( $_REQUEST['shortname'] );
		if( $userid === false )
			return;
		
		$userinfo = userinfo_from_userid( $userid );
		if( strlen($userinfo['feedurl']) == 0 )
			return;	// Don't need to import from one of our local users.
		
		$reader = new XMLReader;
		$reader->open( $userinfo['feedurl'] );
		
		$feed = parse_feed( $reader );
		
		$reader->close();
		
		//print_r( $feed );
		
		$channel = $feed['rss']['channel'];
		$x = 1;
		$itemName = 'item';
		while( true )
		{
			if( !isset($channel[$itemName]) )
				break;
			
			$text = $channel[$itemName]['description'];
			$inreplyto = 0;
			$result = mysql_query ("INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text' )");
		
			print_r( mysql_error() );
			
			$x++;
			$itemName = 'item'.$x;
		}
		
	}
	else if( strcmp($_REQUEST['action'],"addfeed") == 0 )
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
		$feedurl = mysql_real_escape_string($_REQUEST['feedurl']);
		$result = mysql_query ("INSERT INTO users VALUES ( NULL, '$shortname', '$fullname', '$location', '$homepage', '$biography', '$avatarurl', '', '', '$feedurl', 0 )");
		
		print_r( mysql_error() );
	}
	else if( strcmp($_REQUEST['action'],"install") == 0 )
	{
		$result = mysql_query( "SELECT id FROM users" );
		if( mysql_errno() == 0 )	// Already have a users table?
		{
			if( mysql_num_rows( $result ) != 0 )	// And there are users in it?
				return;	// Don't allow installation!
		}

		$gPageTitle = "Install";
		
		print_header();
		
		echo '<form action="index.php" method="POST">
		<input type="hidden" name="action" value="finish_install" />
		To set up Chirp, please create the first administrator user:<br />
		<b>Short Name:</b> <input type="text" name="shortname" /><br />
		<b>Full Name:</b> <input type="text" name="fullname" /><br />
		<b>Location:</b> <input type="text" name="location" /><br />
		<b>Homepage:</b> <input type="text" name="homepage" /><br />
		<b>Bio:</b> <input type="text" name="biography" /><br />
		<b>Avatar:</b><br />
		<select name="avatarurl">
		';
		$dir = opendir('avatars');
		while( false !== ($currfile = readdir($dir)) )
		{
			if( $currfile[0] == '.' )
				continue;
			echo "\t<option value=\"$currfile\">".htmlentities($currfile)."</option>\n";
		}
		echo '</select><br/>
		<b>E-Mail:</b> <input type="text" name="email" /><br />
		<b>Password:</b> <input type="password" name="password" /><br />
		<input type="submit" value="Submit" />
		</form>';
		
		print_footer();
		
		return;
	}
	else if( strcmp($_REQUEST['action'],"finish_install") == 0 )
	{
		$result = mysql_query( "SELECT id FROM users" );
		if( mysql_errno() == 0 )	// Already have a users table?
		{
			if( mysql_num_rows( $result ) != 0 )	// And there are users in it?
				return;	// Don't allow installation!
		}
		
		$result = mysql_query( "CREATE TABLE statuses ( id int NOT NULL PRIMARY KEY AUTO_INCREMENT, user_id int NOT NULL, replyto_id int, text varchar(256) )");

		print_r( mysql_error() );

		$result = mysql_query( "CREATE TABLE users ( id int NOT NULL PRIMARY KEY AUTO_INCREMENT, shortname varchar(80) NOT NULL UNIQUE, fullname varchar(80), location varchar(80), homepage varchar(140), biography varchar(140), avatarurl varchar(140), passwordhash varchar(140), email varchar(80), feedurl varchar(1024), isAdmin int )");
		
		print_r( mysql_error() );
		
		// Create the first admin user:
		$shortname = mysql_real_escape_string($_REQUEST['shortname']);
		$fullname = mysql_real_escape_string($_REQUEST['fullname']);
		$location = mysql_real_escape_string($_REQUEST['location']);
		$homepage = mysql_real_escape_string($_REQUEST['homepage']);
		$biography = mysql_real_escape_string($_REQUEST['biography']);
		$avatarurl = mysql_real_escape_string($_REQUEST['avatarurl']);
		$passwordhash = mysql_real_escape_string(crypt($_REQUEST['password'],"hellacomplicated"));
		$email = mysql_real_escape_string($_REQUEST['email']);
		$result = mysql_query ("INSERT INTO users VALUES ( NULL, '$shortname', '$fullname', '$location', '$homepage', '$biography', '$avatarurl', '$passwordhash', '$email', '', 1 )");
		
		$gPageTitle = "Installation Complete";

		print_header();

		echo "Installation finished.";	
		
		print_footer();

		return;
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