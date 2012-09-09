<?php
	$userid = http_authenticated_userid(true);
	if( $userid === false )
		return;

	$userinfo = userinfo_from_userid( $userid );
	
	$str = '<form action="index.php" method="POST">
	<input type="hidden" name="action" value="finish_profile" />
	To set up Chirp, please create the first administrator user:<br />
	<b>Short Name:</b> '.htmlentities($userinfo['shortname']).'<br />
	<b>Full Name:</b> <input type="text" name="fullname" value="'.htmlentities($userinfo['fullname']).'" /><br />
	<b>Location:</b> <input type="text" name="location" value="'.htmlentities($userinfo['location']).'" /><br />
	<b>Homepage:</b> <input type="text" name="homepage" value="'.htmlentities($userinfo['homepage']).'" /><br />
	<b>Bio:</b> <input type="text" name="biography" value="'.htmlentities($userinfo['biography']).'" /><br />
	<b>Avatar:</b><br />
	<select name="avatarurl">
	';
	$dir = opendir('avatars');
	while( false !== ($currfile = readdir($dir)) )
	{
		if( $currfile[0] == '.' )
			continue;
		if( strcmp($currfile, $userinfo['avatarurl']) == 0 )
			$selected = ' selected="selected"';
		else
			$selected = '';
		$str .= "\t<option value=\"$currfile\"$selected>".htmlentities($currfile)."</option>\n";
	}
	$str .= '</select><br/>
	<b>E-Mail:</b> <input type="text" name="email" value="'.htmlentities($userinfo['email']).'" /><br />
	<input type="submit" value="Submit" />
	</form>';
	echo make_header() . $str . make_footer();
?>