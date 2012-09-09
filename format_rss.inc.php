<?php

	function make_header()
	{
		global $gPageTitle, $gNewestTimestamp;
		return '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
<title>'.htmlentities($gPageTitle).'</title>
<description>This is an example of an RSS feed</description>
<link>http://'.htmlentities($_SERVER['HTTP_HOST']).'/</link>
<lastBuildDate>'.date('D, d M Y H:i:s O',$gNewestTimestamp).'</lastBuildDate>
<pubDate>'.date('D, d M Y H:i:s O',$gNewestTimestamp).'</pubDate>
';
	}
	
	function make_one_status_message( $statusdict )
	{
		$text = htmlentities($statusdict['text']);
		$id = $statusdict['id'];
		return '<item>
<title></title>
<description>'.$text.'</description>
<link>'.$statusdict['url'].'</link>
<guid isPermaLink="false">'.$id.'</guid>
<pubDate>'.date('D, d M Y H:i:s O',$statusdict['timestamp']).'</pubDate>
</item>
';
	}

	function make_footer()
	{
		return '
		</channel>
		</rss>';
	}

?>