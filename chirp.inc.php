<?php
// -----------------------------------------------------------------------------
//  Set up some commonly needed stuff:
// -----------------------------------------------------------------------------

$chirp_configpath = dirname($_SERVER['SCRIPT_FILENAME']).'/config/config.ini';
$chirp_feeddirectory = dirname($_SERVER['SCRIPT_FILENAME']).'/tweets';
$chirp_feedpath = $chirp_feeddirectory.'/feed.rss';
$chirp_feedidpath = $chirp_feeddirectory.'/feedid.txt';
$chirp_htmlusername = htmlentities($_SERVER['PHP_AUTH_USER']);

// Read prefs:
$chirp_config = parse_ini_file( $chirp_configpath );
date_default_timezone_set( $chirp_config['TIMEZONE'] );

?>