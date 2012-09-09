<?php
	global $gPageTitle;
	
	$userid = http_authenticated_userid(true);
	if( $userid === false )
		return;
	$unfollow_userid = userid_from_shortname( $_REQUEST['shortname'] );
	
	$result = mysql_query( "DELETE FROM follows WHERE follower='$userid' AND followee='$unfollow_userid'" );
	print_r( mysql_error() );
	
	$gPageTitle = "Unfollow ".$_REQUEST['shortname'];
	echo make_header() . "Unfollowed ".htmlentities($_REQUEST['shortname']) . make_footer();
?>