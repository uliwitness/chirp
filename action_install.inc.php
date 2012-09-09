<?php
		$result = mysql_query( "SELECT id FROM users" );
		if( mysql_errno() == 0 )	// Already have a users table?
		{
			if( mysql_num_rows( $result ) != 0 )	// And there are users in it?
				return;	// Don't allow installation!
		}

		$gPageTitle = "Install";
		
		$str = '<form action="index.php" method="POST">
		<input type="hidden" name="action" value="finish_install" />
		To set up Chirp, please create the first administrator user:<br />
		<b>Short Name:</b> '.htmlentities($_SERVER['HTTP_HOST']).'<input type="hidden" name="shortname" value="'.$_SERVER['HTTP_HOST'].'" /><br />
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
			$str .= "\t<option value=\"$currfile\">".htmlentities($currfile)."</option>\n";
		}
		$str .= '</select><br/>
		<b>E-Mail:</b> <input type="text" name="email" /><br />
		<b>Password:</b> <input type="password" name="password" /><br />
		<input type="submit" value="Submit" />
		</form>';
		
		echo make_header() . $str . make_footer();
?>