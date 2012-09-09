<?php
	$userid = http_authenticated_userid(true);
	if( $userid === false )
		return;

	$str = '<form action="index.php" method="POST">
	<input type="hidden" name="action" value="finish_follow" />
	Follow user: <input type="text" name="shortname" /> <font color="#888888">bird.the-void-software.com</font><br />
	<input type="submit" name="Follow" />
	</form>';
	echo make_header() . $str . make_footer();
?>