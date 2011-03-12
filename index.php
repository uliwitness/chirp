<?php
// split the user/pass parts
if( !isset($_SERVER['PHP_AUTH_USER']) )
{
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
	if( strlen($_SERVER['PHP_AUTH_USER']) == 0 || strlen($_SERVER['PHP_AUTH_PW']) == 0 )
	{
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);
	}
}

// open a user/pass prompt
if( !isset($_SERVER['PHP_AUTH_USER']) )
{
	header('WWW-Authenticate: Basic realm="My Realm"');
	header('HTTP/1.0 401 Unauthorized');
	echo 'Text to send if user hits Cancel button';
	exit;
}
else
{
	$configpath = dirname($_SERVER['SCRIPT_FILENAME']).'/config/config.ini';
	$feeddirectory = dirname($_SERVER['SCRIPT_FILENAME']).'/tweets';
	$feedpath = $feeddirectory.'/feed.rss';
	
	$config = parse_ini_file( $configpath );
   
	if( strtolower($_SERVER['PHP_AUTH_USER']) != $config['USER']
   		|| strtolower($_SERVER['PHP_AUTH_PW']) != $config['PASS'] )
	{
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Invalid username or password.';
		exit;
	}
}

$htmlusername = htmlentities($_SERVER['PHP_AUTH_USER']);
$feedurl = $_SERVER['SCRIPT_URI'];
$tweetnum = 1;	// +++
$currentdate = date( DATE_RSS, time() );

if( isset($_REQUEST['message']) )
{
	$htmlmessage = htmlentities($_REQUEST['message']);
	
	if( !file_exists( $feedpath ) )
	{
		$feedbody = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<rss version=\"2.0\">

<channel>
<title>$htmlusername's chirps</title>
<description>This is an RSS feed of small chirps by $htmlusername</description>
<link>$feedurl?format=rss</link>
<lastBuildDate>$currentdate</lastBuildDate>
<pubDate>$currentdate</pubDate>

<item>
<title></title>
<description>$htmlmessage</description>
<link>$feedurl#$tweetnum</link>
<guid isPermaLink=\"false\">$tweetnum</guid>
<pubDate>$currentdate</pubDate>
</item>

</channel>
</rss>";
		$fd = fopen( $feedpath, "w" );
		fwrite( $fd, $feedbody );
		fclose( $fd );
	}
	else
	{
		$tweets = file_get_contents($feedpath);
		
		$newtweet = "<item>
<title></title>
<description>$htmlmessage</description>
<link>$feedurl#$tweetnum</link>
<guid isPermaLink=\"false\">$tweetnum</guid>
<pubDate>$currentdate</pubDate>
</item>\n\n";
		
		$pos = strpos( $tweets, "<item>" );
		$tweets = substr( $tweets, 0, $pos ).$newtweet.substr( $tweets, $pos );
		
		echo $tweets;
		
		$fd = fopen( $feedpath, "w" );
		fwrite( $fd, $tweets );
		fclose( $fd );
		
		exit;
	}
}
else if( isset($_REQUEST['format']) && $_REQUEST['format'] == "rss" )
{
	if( file_exists( $feedpath ) )
	{
		$fd = fopen( $feedpath, "r" );
		fpassthru( $fd );
		fclose( $fd );
	}
	exit;
}

echo "<p>Hello, ".htmlentities($_SERVER['PHP_AUTH_USER'])." what's happening?</p>\n";
echo "<form method=\"post\" action=\"index.php\">";
echo "<p><input name=\"message\" type=\"text\"></p>\n";
echo "<p><input type=\"submit\" name=\" Post \"></p>\n";
echo "</form>";
?>