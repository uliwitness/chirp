<?php
	function load_settings()
	{
		global $gSettings;
		
		$settingspath = dirname($_SERVER['SCRIPT_FILENAME'])."/settings.ini";
		if( !file_exists($settingspath) )
			$settingspath = dirname($_SERVER['SCRIPT_FILENAME'])."/chirp/settings.ini";
		
		$gSettings = array();
		$ini_lines = file($settingspath);
		for( $x = 0; $x < sizeof($ini_lines); $x++ )
		{
			$parts = explode( "=", $ini_lines[$x], 2 );
			$gSettings[$parts[0]] = trim($parts[1]);
		}
		if( !isset($gSettings['dbserver']) || !isset($gSettings['dbuser'])
			|| !isset($gSettings['dbpassword']) || !isset($gSettings['dbname']) )
		{
			echo "<b>Setup error. No/invalid ini file.</b>";
			return false;
		}
		if( strcmp($gSettings['dbserver'], "mysql.example.com") == 0 )	// Still have dummy info in .ini file?
			return false;
		
		return true;
	}

	function open_database()
	{
		global $gSettings;

		if( !load_settings() )
			return false;
		
		mysql_connect( $gSettings['dbserver'], $gSettings['dbuser'], $gSettings['dbpassword'] );
		
		mysql_select_db( $gSettings['dbname'] );
		
		mysql_set_charset("utf8");
		
		return true;
	}
?>