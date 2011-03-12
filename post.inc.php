<?php
require_once( "chirp.inc.php");
require_once( "authenticate.inc.php");

// -----------------------------------------------------------------------------
//  User wants to post a tweet:
// -----------------------------------------------------------------------------
    
function chirp_action_post()
{
    global $chirp_feedidpath, $chirp_feedpath, $chirp_htmlusername;
    
    if( chirp_authenticate_admin() )
    {
        // Get a new tweet ID and increment our seed in the process:
        $tweetnum = file_exists($chirp_feedidpath) ? (file_get_contents($chirp_feedidpath) +1) : 1;
        $fd = fopen($chirp_feedidpath,"w");
        fwrite($fd,$tweetnum);
        fclose($fd);
        
        // Set up some values we'll need:
        $feedurl = $_SERVER['SCRIPT_URI'];
        $currentdate = date( 'D, d M Y G:i:s O', time() );
        $htmlmessage = htmlentities($_REQUEST['message']);
        
        // Actually post the tweet:
        if( !file_exists( $chirp_feedpath ) )    // First tweet? Make file!
        {
            $feedbody = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<rss version=\"2.0\">

<channel>
<title>$chirp_htmlusername's chirps</title>
<description>This is an RSS feed of small chirps by $chirp_htmlusername</description>
<link>$feedurl?format=rss</link>
<lastBuildDate>$currentdate</lastBuildDate>
<pubDate>$currentdate</pubDate>

<item>
<title></title>
<description>$htmlmessage</description>
<link>$feedurl#$tweetnum</link>
<guid isPermaLink=\"false\">$tweetnum</guid>
<pubDate>$currentdate</pubDate>
</item>

</channel>
</rss>";
            $fd = fopen( $chirp_feedpath, "w" );
            fwrite( $fd, $feedbody );
            fclose( $fd );
        }
        else   // Already have tweets? Insert item at top!
        {
            $tweets = file_get_contents($chirp_feedpath);
            
            $newtweet = "<item>
<title></title>
<description>$htmlmessage</description>
<link>$feedurl#$tweetnum</link>
<guid isPermaLink=\"false\">$tweetnum</guid>
<pubDate>$currentdate</pubDate>
</item>\n\n";
            
            $pos = strpos( $tweets, "<item>" );
            $tweets = substr( $tweets, 0, $pos ).$newtweet.substr( $tweets, $pos );
            
            $fd = fopen( $chirp_feedpath, "w" );
            fwrite( $fd, $tweets );
            fclose( $fd );
        }
        
        chirp_action_home();    // Show home page on success.
    }
}
?>