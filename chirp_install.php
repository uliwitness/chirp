<?php
	
	$mydir = dirname($_SERVER['SCRIPT_FILENAME']);
	$installogfile = $mydir."/installog.log";
	
	if( file_exists($mydir."/.git") )
		$chirpdir = $mydir;
	else
		$chirpdir = $mydir.'/chirp/';
	
	if( !file_exists($chirpdir."/.git") )
	{
		require( $chirpdir."/begininstall.inc.php" );

		mkdir( $chirpdir );
		$cmd = "git clone 'https://github.com/uliwitness/chirp.git' '$chirpdir' >$installogfile 2>&1";
		
		$success = 0;
		system($cmd,$success);
		
		finish_update($success,file_get_contents($installogfile),$chirpdir);
	}
	else
	{
		require( $chirpdir."/upgrade.inc.php" );
		
		chdir($chirpdir);
		$cmd = "git pull . 'remotes/origin/master' >$installogfile 2>&1";
		
		$success = 0;
		system($cmd,$success);
		
		finish_update($success,file_get_contents($installogfile),$chirpdir);
	}
	
	if( file_exists($installogfile) )
		unlink( $installogfile );
?>