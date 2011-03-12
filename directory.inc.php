<?php
require_once("chirp.inc.php");
require_once("home.inc.php");
require_once("authenticate.inc.php");

// -----------------------------------------------------------------------------
//  Authenticate the user:
// -----------------------------------------------------------------------------

function chirp_action_directory()
{
    global $chirp_directoryurl;
    
    $directory = array();
    
    $lines = file( $chirp_directoryurl );
    while( $currline = current($lines) )
    {
        $parts = explode("=",$currline,2);
        $directory[$parts[0]] = trim($parts[1],"\"\n\r ");
        next($lines);
    }
    reset( $directory );
    while( list($key, $val) = each($directory) )
    {
        $htmlkey = htmlentities($key);
        $urlkey = urlencode($key);
        $urlval = urlencode($val);
        echo "<a href=\"$val\">$htmlkey</a> [ <a href=\"?do=follow&name=$urlkey&url=$urlval\">Follow</a> ]<br />\n";   // +++ SANITIZE !
    }
}
?>