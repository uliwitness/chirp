<?php
	function finish_update( $status, $output, $chirpdir )
	{
		echo "<html>\n<head><title>Update Chirp</title>\n</head>\n<body>\n";
		if( $status == 0 )
			echo "<h1>Update Succeeded</h1>\n";
		else
			echo "<h1>Error $status during Update</h1>\n";
		echo "<pre>".htmlentities($output)."</pre>\n";
		echo "<a href=\"/chirp/\">Back to Chirp</a>\n";
		echo "</body>\n</html>";
	}
?>