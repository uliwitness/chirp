<?php
	/*
		THIS SCRIPT IS RUN BY THE chirp_install.php SCRIPT
		ONE LEVEL UP!
	*/
	
	$mydir = dirname($_SERVER['SCRIPT_FILENAME']);
	
	$htaccesspath = $mydir."/.htaccess";
	if( file_exists($htaccesspath) )
	{
		echo "<br /><br />Error: .htaccess already exists, Creating _htaccess instead. Please merge its contents with .htaccess.";
		$htaccesspath = $mydir."/_htaccess";
	}
	
	$fd = fopen( $htaccesspath, "w" );
	fwrite( $fd, "RewriteEngine on\nRewriteRule   ^\.well-known/microblog\.rss$   	/chirp/index.php?action=microblog&format=rss\n" );
	fclose( $fd );
?>