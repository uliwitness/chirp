<?php
	function print_header()
	{
		global $gPageTitle;
		
		echo "<html>\n<head>\n<title>".htmlentities($gPageTitle)."</title>\n";
		echo "<link rel=\"stylesheet\" href=\"styles.css\" type=\"text/css\" />\n";
		$feedurl = "feed://".$_SERVER['HTTP_HOST']."/index.php?format=rss";
		if( isset($_REQUEST['shortname']) )
		{
			echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"All Statuses\" href=\"$feedurl\" />\n";
			$username = rawurlencode($_REQUEST['shortname']);
			$feedurl = "feed://".$_SERVER['HTTP_HOST']."/index.php?shortname=$username&format=rss";
		}
		
		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".htmlentities($gPageTitle)."\" href=\"$feedurl\" />\n";
		echo "</head>\n<body>";
		echo "<div class=\"actions\">";
		echo "<a href=\"$feedurl\">RSS Feed</a><br />";
		echo "<a href=\"index.php?action=addfeed&shortname=zathrasdeblog&fullname=Zathras.de%20Blog&avatarurl=ElectricSheep.png&feedurl=http://orangejuiceliberationfront.com/feed/\">Add external user (Feed) Test</a><br />";
		if( isset($_REQUEST['shortname']) )
		{
			$username = rawurlencode($_REQUEST['shortname']);
			echo "<a href=\"index.php?action=importrss&shortname=$username\">Import new messages for this user</a><br />";
		}
		echo "</div>";
		echo "<div class=\"postfield\"><form action=\"index.php\" method=\"POST\">What are you doing?<br /><input type=\"text\" name=\"text\" size=\"60\" /><input type=\"hidden\" name=\"action\" value=\"newstatus\"></form></div>";
	}
	
	function print_one_status_message( $statusdict )
	{
		$userid = $statusdict['user_id'];
		$row = userinfo_from_userid($userid);
		$shortname = $row['shortname'];
		$fullname = $row['fullname'];
		$avatarurl = $row['avatarurl'];
		echo "<div class=\"tweet\"><a href=\"index.php?shortname=".urlencode($shortname)."\" class=\"statussender\">".((strlen($avatarurl) > 0) ? "<img src=\"avatars/".rawurlencode($avatarurl)."\" width=\"48\" height=\"48\" align=\"left\" style=\"padding-right: 8pt;\" />" : "<div class=\"avatarplaceholder\"></div>").htmlentities($fullname)."</a> ".htmlentities($statusdict['text'])."</div><br/>";
	}

	function print_footer()
	{
		echo "</body></html>";
	}
	
?>