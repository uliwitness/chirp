<?php
	function print_header()
	{
		global $gPageTitle;
		echo "<html><head><title>".htmlentities($gPageTitle)."</title><link rel=\"stylesheet\" href=\"styles.css\" type=\"text/css\" /></head><body>";
		echo "<div class=\"actions\">";
		echo "<a href=\"index.php?action=newuser&shortname=testuser&fullname=Test+User&location=The+Net&homepage=&biography=I+did+something&avatarurl=&password=1234&email=testemail\">New Test User</a><br />";
		echo "<a href=\"index.php?action=importrss&url=http://orangejuiceliberationfront.com/feed/\">Import a Feed</a><br />";
		if( isset($_REQUEST['shortname']) )
		{
			$username = rawurlencode($_REQUEST['shortname']);
			echo "<a href=\"feed://".$_SERVER['HTTP_HOST']."/index.php?shortname=$username&format=rss\">RSS Feed</a><br />";
		}
		else
			echo "<a href=\"feed://".$_SERVER['HTTP_HOST']."/index.php?format=rss\">RSS Feed</a><br />";
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