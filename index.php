<?php
require_once("chirp.inc.php");
require_once("exchangerss.inc.php");
require_once("home.inc.php");
require_once("post.inc.php");
require_once("default.inc.php");

$chirp_action = $_REQUEST['do'];
if( !isset($chirp_action) || strlen($chirp_action) == 0 || !ctype_alpha($chirp_action) )
    chirp_action_default();
else
{
    $actionname = "chirp_action_".$chirp_action;
    //echo $actionname;
    call_user_func( $actionname );
}
?>