<?php
	
	$mydir = dirname($_SERVER['SCRIPT_FILENAME']);
	$installogfile = $mydir."/installog.log";
	
	if( file_exists($mydir."/.git") )
		$chirpdir = $mydir;
	else
		$chirpdir = $mydir.'/chirp/';
	
	if( !file_exists($chirpdir."/.git") )
	{
		mkdir( $chirpdir );
		$cmd = "git clone 'https://github.com/uliwitness/chirp.git' '$chirpdir' >$installogfile 2>&1";
		
		$success = 0;
		system($cmd,$success);
		
		if( $success != 0 )
			echo "<html><head><title>Download Failed</title></head><body><h1>Download Failed</h1><pre>".htmlentities(file_get_contents($installogfile))."</pre></body></html>";
		else
		{
			require( $chirpdir."/begininstall.inc.php" );
				
			finish_update($success,file_get_contents($installogfile),$chirpdir);
		}
	}
	else
	{
		chdir($chirpdir);
		$cmd = "git pull . 'remotes/origin/master' >$installogfile 2>&1";
		
		$success = 0;
		system($cmd,$success);
		
		require( $chirpdir."/upgrade.inc.php" );
		finish_update($success,file_get_contents($installogfile),$chirpdir);
	}
	
	if( file_exists($installogfile) )
		unlink( $installogfile );
?>