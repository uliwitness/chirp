<?php
	function set_up_htaccess( $chirpdir )
	{
		$mydir = dirname($chirpdir);
		
		$htaccesspath = $mydir."/.htaccess";
		if( file_exists($htaccesspath) )
		{
			echo "<br /><br />Error: .htaccess already exists, Creating _htaccess instead. Please merge its contents with .htaccess.";
			$htaccesspath = $mydir."/_htaccess";
		}
		
		$fd = fopen( $htaccesspath, "w" );
		fwrite( $fd, "RewriteEngine on\nRewriteRule   ^\.well-known/microblog\.rss$   	/chirp/index.php?action=microblog&format=rss\n" );
		fclose( $fd );
	}
	
	function finish_update( $status, $output, $chirpdir )
	{
		echo "<html>\n<head><title>Install Chirp</title>\n</head>\n<body>\n";
		if( $status == 0 )
		{
			echo "<h1>Installation Succeeded</h1>\n";
			set_up_htaccess( $chirpdir );
		}
		else
			echo "<h1>Error $status during Installation</h1>\n";
		echo "<pre>".htmlentities($output)."</pre>\n";
		echo "<a href=\"/chirp/\">Go to Setup</a>\n";
		echo "</body>\n</html>";
	}
?>