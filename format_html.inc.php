<?php
	function make_header()
	{
		global $gPageTitle;
		
		$str = "<html>\n<head>\n<title>".htmlentities($gPageTitle)."</title>\n";
		$str .= "<base href=\"/chirp/\" />\n";
		$str .= "<link rel=\"stylesheet\" href=\"styles.css\" type=\"text/css\" />\n";
		$feedurl = "feed://".$_SERVER['HTTP_HOST']."/chirp/index.php?format=rss";
		if( isset($_REQUEST['shortname']) )
		{
			$str .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"All Statuses\" href=\"$feedurl\" />\n";
			$username = rawurlencode($_REQUEST['shortname']);
			$feedurl = "feed://".$_SERVER['HTTP_HOST']."/index.php?shortname=$username&format=rss";
		}
		
		$str .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".htmlentities($gPageTitle)."\" href=\"$feedurl\" />\n";
		$str .= "</head>\n<body>\n";
		$str .= "<div class=\"actions\">\n";
		$str .= "<a href=\"index.php?action=timeline\">My Timeline</a><br />\n";
		$str .= "<a href=\"index.php?action=home\">Global Timeline</a><br />\n";
		$str .= "<a href=\"$feedurl\">RSS Feed</a><br />\n";
		$str .= "<a href=\"index.php?action=follow\">Follow</a><br />\n";
		$str .= "<a href=\"index.php?action=unfollow\">Unfollow</a><br />\n";
		$str .= "<a href=\"index.php?action=profile\">My Profile</a><br />\n";
		$str .= "<a href=\"index.php?action=importrss\">Refresh Timeline</a><br />\n";
		$str .= "</div>\n";
		
		return $str;
	}
	
	
	function make_send_field()
	{
		return "<div class=\"postfield\">\n<form action=\"index.php\" method=\"POST\">\nWhat are you doing?<br />\n<input type=\"text\" name=\"text\" size=\"60\" />\n<input type=\"hidden\" name=\"action\" value=\"newstatus\">\n</form>\n</div>\n";
	}
	
	function make_one_status_message( $statusdict )
	{
		$userid = $statusdict['user_id'];
		$row = userinfo_from_userid($userid);
		if( isset( $statusdict['original'] ) && strlen($statusdict['original']) > 0 )
		{
			$reposter = $row['fullname'];
			$userid = $statusdict['original_user_id'];
			$row = userinfo_from_userid( $statusdict['original_user_id'] );
		}
		else
			$reposter = '';
		$shortname = $row['shortname'];
		$fullname = $row['fullname'];
		$avatarurl = $row['avatarurl'];
		$str = "<div class=\"status\">";
		$str .= "<a href=\"index.php?shortname=".urlencode($shortname)."\" class=\"statussender\">".((strlen($avatarurl) > 0) ? "<img src=\"".$avatarurl."\" width=\"48\" height=\"48\" align=\"left\" style=\"padding-right: 8pt;\" />" : "<div class=\"avatarplaceholder\"></div>").htmlentities($fullname)."</a> ".htmlentities($statusdict['text'])."<div class=\"statuscontrols\"><a href=\"index.php?action=reply&statusid=".$statusdict['id']."\">&#8617;</a> <a href=\"index.php?action=repost&statusid=".$statusdict['id']."\">&#9851;</a>";
		if( isset($statusdict['replytourl']) && strlen($statusdict['replytourl']) > 1 )
			$str .= " <a href=\"".htmlentities($statusdict['replytourl'])."\">&#128172;</a>";
		if( isset($statusdict['original']) && strlen($statusdict['original']) > 1 )
			$str .= " <a href=\"".htmlentities($statusdict['original'])."\">&#128172;</a>";
		if( strlen($reposter) > 0 )
			$str .= " <i>via ".htmlentities($reposter)."</i>";
		$str .= "</div></div><br/>";
		return $str;
	}

	function make_footer()
	{
		return "</body></html>";
	}
	
?>