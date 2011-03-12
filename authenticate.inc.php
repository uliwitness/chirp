<?php
require_once("chirp.inc.php");

// -----------------------------------------------------------------------------
//  Authenticate the user:
// -----------------------------------------------------------------------------

function chirp_authenticate_admin()
{
    global $chirp_config;
    
    if( !isset($_SERVER['PHP_AUTH_USER']) ) // Likely that we need PHP CGI workaround for HTTP AUTH?
    {
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6))); // Get user/pw from environment and put them in the array.
        if( strlen($_SERVER['PHP_AUTH_USER']) == 0 || strlen($_SERVER['PHP_AUTH_PW']) == 0 )
        {
            unset($_SERVER['PHP_AUTH_USER']);
            unset($_SERVER['PHP_AUTH_PW']);
        }
    }
    
    // open a user/pass prompt:
    if( !isset($_SERVER['PHP_AUTH_USER']) )
    {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Text to send if user hits Cancel button';
        return false;
    }
    else    // Have a username/password? Compare to config file:
    {
        if( strtolower(trim($_SERVER['PHP_AUTH_USER'])) != strtolower($chirp_config['USER'])
               || trim($_SERVER['PHP_AUTH_PW']) != $chirp_config['PASS'] )
        {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Invalid username or password.';
            return false;
        }
    }
    
    return true;
}
?>