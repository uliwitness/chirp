<?php
// -----------------------------------------------------------------------------
//  Set up some commonly needed stuff:
// -----------------------------------------------------------------------------

$chirp_configpath = dirname($_SERVER['SCRIPT_FILENAME']).'/config/config.ini';
$chirp_feedfolder = dirname($_SERVER['SCRIPT_FILENAME']).'/tweets';
$chirp_feedpath = $chirp_feedfolder.'/feed.rss';
$chirp_feedidpath = $chirp_feedfolder.'/feedid.txt';
$chirp_followeespath = $chirp_feedfolder.'/followees.ini';
$chirp_htmlusername = htmlentities($_SERVER['PHP_AUTH_USER']);

// Read prefs:
$chirp_config = parse_ini_file( $chirp_configpath );
date_default_timezone_set( $chirp_config['TIMEZONE'] );
$chirp_directoryurl = $chirp_config['DIRECTORYURL'];

?>