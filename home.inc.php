<?php
require_once("chirp.inc.php");

// -----------------------------------------------------------------------------
//  Display user's twitter home page:
// -----------------------------------------------------------------------------

function chirp_action_home()
{
    global $chirp_htmlusername;
    
    echo "<p>Hello, ".$chirp_htmlusername." what's happening?</p>\n";
    echo "<form method=\"post\" action=\"index.php\">\n";
    echo "<input type=\"hidden\" name=\"do\" value=\"post\">\n";
    echo "<p><input name=\"message\" type=\"text\"></p>\n";
    echo "<p><input type=\"submit\" name=\" Post \"></p>\n";
    echo "</form>\n";
}

?>