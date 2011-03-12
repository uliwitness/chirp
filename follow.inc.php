<?php
require_once("chirp.inc.php");
require_once("home.inc.php");
require_once("authenticate.inc.php");

// -----------------------------------------------------------------------------
//  Authenticate the user:
// -----------------------------------------------------------------------------

function chirp_action_follow()
{
    global $chirp_followeespath;
    
    if( chirp_authenticate_admin() )
    {
        $shortname = $_REQUEST['name']; // +++ SANITIZE INI key!
        $url = $_REQUEST['url'];    // +++ SANITIZE for INI value!
        
        $fd = fopen( $chirp_followeespath, "a" );
        fwrite($fd,"$shortname=\"$url\"\n");
        fclose($fd);
        
        chirp_action_home();
    }
}
?>