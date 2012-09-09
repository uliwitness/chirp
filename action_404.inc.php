<?php
	@header("Status: 404 Not Found");

	global $gPageTitle;
	$gPageTitle = "File not Found";
	echo make_header()."Couldn't find file <i>\"".htmlentities($_SERVER['REQUEST_URI'])."\"</i>.".make_footer();
?>