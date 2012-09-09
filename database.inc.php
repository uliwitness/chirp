<?php
	function open_database()
	{
		$settings = array();
		$ini_lines = file("settings.ini");
		for( $x = 0; $x < sizeof($ini_lines); $x++ )
		{
			$parts = explode( "=", $ini_lines[$x], 2 );
			$settings[$parts[0]] = trim($parts[1]);
		}
		if( !isset($settings['dbserver']) || !isset($settings['dbuser'])
			|| !isset($settings['dbpassword']) || !isset($settings['dbname']) )
		{
			echo "<b>Setup error. No/invalid ini file.</b>";
			return false;
		}
		
		mysql_connect( $settings['dbserver'], $settings['dbuser'], $settings['dbpassword'] );
		
		mysql_select_db( $settings['dbname'] );
		
		mysql_set_charset("utf8");
		
		return true;
	}
?>