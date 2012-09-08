<?php

	function print_header()
	{
		global $gPageTitle;
		echo '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>'.htmlentities($gPageTitle).'</title>
<description>This is an example of an RSS feed</description>
<link>http://'.htmlentities($_SERVER['HTTP_HOST']).'/</link>
<lastBuildDate>Mon, 28 Aug 2006 11:12:55 -0400 </lastBuildDate>
<pubDate>Tue, 29 Aug 2006 09:00:00 -0400</pubDate>
';
	}
	
	function print_one_status_message( $statusdict )
	{
		$text = htmlentities($statusdict['text']);
		$id = $statusdict['id'];
		echo '<item>
<title></title>
<description>'.$text.'</description>
<link>http://'.htmlentities($_SERVER['HTTP_HOST']).'/index.php?statusid='.$id.'</link>
<guid isPermaLink="false">'.$id.'</guid>
<pubDate>Tue, 29 Aug 2006 09:00:00 -0400</pubDate>
</item>
';
	}

	function print_footer()
	{
		echo '
		</channel>
		</rss>';
	}

?>