<?php
require_once("chirp.inc.php");
require_once("home.inc.php");

// -----------------------------------------------------------------------------
//  Authenticate the user:
// -----------------------------------------------------------------------------

function chirp_action_default()
{
    chirp_action_home();
}
?>