<?php
	@header('Content-Type: application/rss+xml');
	
	function make_header()
	{
		global $gPageTitle, $gNewestTimestamp;
		
		$userid = userid_from_shortname( $_REQUEST['shortname'] );
		$userinfo = userinfo_from_userid( $userid );
		
		$str = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:microblog="http://microblog.reallysimple.org/">
<channel>
	<title>'.htmlentities($gPageTitle).'</title>
	<description>'.htmlentities($userinfo['biography']).'</description>
	<link>'.htmlentities($userinfo['homepage']).'</link>
	<lastBuildDate>'.date('D, d M Y H:i:s O',$gNewestTimestamp).'</lastBuildDate>
	<pubDate>'.date('D, d M Y H:i:s O',$gNewestTimestamp).'</pubDate>
';
		if( isset($userinfo['avatarurl']) && strlen($userinfo['avatarurl']) > 0 )
		{
			$str .= "\t<image>\n\t\t<url>".htmlentities($userinfo['avatarurl'])."</url>\n\t\t<link>".htmlentities($userinfo['homepage'])."</link>\n\t\t<title>".htmlentities($userinfo['fullname'])."</title>\n\t</image>\n";
			$str .= "<microblog:avatar>".htmlentities($userinfo['avatarurl'])."</microblog:avatar>\n";
		}
		
		return $str;
	}

	function make_send_field()
	{
		return "";
	}
	
	function make_one_status_message( $statusdict )
	{
		$text = htmlentities($statusdict['text']);
		$id = $statusdict['id'];
		
		$userid = $statusdict['user_id'];
		$userinfo = userinfo_from_userid( $userid );
		if( isset($statusdict['replytourl']) && strlen($statusdict['replytourl']) > 0 )
		{
			$text = preg_replace( "/^@([-A-Za-z.\\pL]+)/", "&lt;a href=\"".$statusdict['replytourl']."\" rel=\"prev\"&gt;@$1&lt;/a&gt;", $text );
		}
		if( isset($statusdict['original']) && strlen($statusdict['original']) > 0 )
		{
			$originaluserid = $statusdict['original_user_id'];
			$originaluserinfo = userinfo_from_userid( $originaluserid );
			$text = "&lt;a href=\"".$statusdict['original']."\" rel=\"original\"&gt;RP @".htmlentities($originaluserinfo['shortname'])."&lt;/a&gt; ".$text;
		}
		return '	<item>
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