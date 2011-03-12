<?php
require_once("chirp.inc.php");


function    chirp_tweets_from_rss_to_array( $feed, &$tweets, $name, $url )
{
    $pos = strpos( $feed, "<item>" );
    $descriptions = array();
    preg_match_all( "/\<description\>(.*)\<\/description\>/i", $feed, $descriptions, PREG_PATTERN_ORDER, $pos );
    $links = array();
    preg_match_all( "/\<link\>(.*)\<\/link\>/i", $feed, $links, PREG_PATTERN_ORDER, $pos );
    
    $pubDates = array();
    preg_match_all( "/\<pubDate\>(.*)\<\/pubDate\>/i", $feed, $pubDates, PREG_PATTERN_ORDER, $pos );
    
    while( ($currdesc = current($descriptions[1])) && ($currlink = current($links[1])) && ($currPubDate = current($pubDates[1])) )
    {
        $timestamp = strtotime($currPubDate);
                
        array_push($tweets,array( "username" => $name, "userurl" => $url, "link" => $currlink, "description" => $currdesc, "pubdate" => $currPubDate, "pubdatets" => $timestamp ));
        
        next($descriptions[1]); next($links[1]); next($pubDates[1]);
    }
}


function compare_tweet_times( $a, $b )
{
    return ( $b['pubdatets'] - $a['pubdatets'] );
}


// -----------------------------------------------------------------------------
//  Display user's twitter home page:
// -----------------------------------------------------------------------------

function chirp_action_home()
{
    global $chirp_htmlusername, $chirp_followeespath, $chirp_feedpath, $chirp_username;
    
    echo "Home | <a href=\"?do=directory\">Directory</a> | <a href=\"?do=exchangerss\">RSS</a><br/>\n<br />\n";
    
	if( file_exists($chirp_followeespath) )
    	$followees = parse_ini_file($chirp_followeespath);
    else
    	$followees = array();
    $tweets = array();
    while( list($name, $url) = each($followees) )
    {
        $feed = file_get_contents( $url );
        chirp_tweets_from_rss_to_array($feed,$tweets,$name,$url);
    }
    
    if( file_exists($chirp_feedpath) )
    	$feed = file_get_contents( $chirp_feedpath );
    else
    	$feed = "";
    chirp_tweets_from_rss_to_array($feed,$tweets,$chirp_username,"index.php?do=exchangerss");
    
    usort( $tweets, "compare_tweet_times" );
    
    echo "<p>Hello ".$chirp_htmlusername.", what's happening?</p>\n";
    echo "<form method=\"post\" action=\"index.php\">\n";
    echo "<input type=\"hidden\" name=\"do\" value=\"post\">\n";
    echo "<p><input name=\"message\" type=\"text\"></p>\n";
    echo "<p><input type=\"submit\" name=\" Post \"></p>\n";
    echo "</form>\n";
    
    while( $currtweet = current($tweets) )
    {
        echo "<a href=\"".$currtweet['userurl']."\"><b>".$currtweet['username'].":</b></a> ".htmlentities($currtweet['description'])." <a href=\"".$currtweet['link']."\">".htmlentities($currtweet['pubdate'])."</a><br />";
        next($tweets);
    }
}

?>