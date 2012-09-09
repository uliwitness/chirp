<?php
	function make_header()
	{
		global $gPageTitle;
		
		$str = "<html>\n<head>\n<title>".htmlentities($gPageTitle)."</title>\n";
		$str .= "<link rel=\"stylesheet\" href=\"styles.css\" type=\"text/css\" />\n";
		$feedurl = "feed://".$_SERVER['HTTP_HOST']."/index.php?format=rss";
		if( isset($_REQUEST['shortname']) )
		{
			$str .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"All Statuses\" href=\"$feedurl\" />\n";
			$username = rawurlencode($_REQUEST['shortname']);
			$feedurl = "feed://".$_SERVER['HTTP_HOST']."/index.php?shortname=$username&format=rss";
		}
		
		$str .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".htmlentities($gPageTitle)."\" href=\"$feedurl\" />\n";
		$str .= "</head>\n<body>";
		$str .= "<div class=\"actions\">";
		$str .= "<a href=\"index.php?action=home\">Home</a><br />";
		$str .= "<a href=\"$feedurl\">RSS Feed</a><br />";
		$str .= "<a href=\"index.php?action=addfeed&shortname=zathrasdeblog&fullname=Zathras.de%20Blog&avatarurl=ElectricSheep.png&feedurl=http://orangejuiceliberationfront.com/feed/\">Add external user (Feed) Test</a><br />";
		if( isset($_REQUEST['shortname']) )
		{
			$username = rawurlencode($_REQUEST['shortname']);
			$str .= "<a href=\"index.php?action=importrss&shortname=$username\">Import new messages for this user</a><br />";
		}
		$str .= "</div>";
		$str .= "<div class=\"postfield\"><form action=\"index.php\" method=\"POST\">What are you doing?<br /><input type=\"text\" name=\"text\" size=\"60\" /><input type=\"hidden\" name=\"action\" value=\"newstatus\"></form></div>";
		
		return $str;
	}
	
	function make_one_status_message( $statusdict )
	{
		$userid = $statusdict['user_id'];
		$row = userinfo_from_userid($userid);
		$shortname = $row['shortname'];
		$fullname = $row['fullname'];
		$avatarurl = $row['avatarurl'];
		return "<div class=\"tweet\"><a href=\"index.php?shortname=".urlencode($shortname)."\" class=\"statussender\">".((strlen($avatarurl) > 0) ? "<img src=\"avatars/".rawurlencode($avatarurl)."\" width=\"48\" height=\"48\" align=\"left\" style=\"padding-right: 8pt;\" />" : "<div class=\"avatarplaceholder\"></div>").htmlentities($fullname)."</a> ".htmlentities($statusdict['text'])."</div><br/>";
	}

	function make_footer()
	{
		return "</body></html>";
	}
	
?>