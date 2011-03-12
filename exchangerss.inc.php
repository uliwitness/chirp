<?php
// -----------------------------------------------------------------------------
//  Show RSS feed that contains all tweets, if desired (this is what subscribers poll):
// -----------------------------------------------------------------------------

function chirp_action_exchangerss()
{
    global $chirp_feedpath;
    
    //echo "//$chirp_feedpath//";
    
    if( file_exists( $chirp_feedpath ) )
    {
        $fd = fopen( $chirp_feedpath, "r" );
        fpassthru( $fd );
        fclose( $fd );
    }
}

?>