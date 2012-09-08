<?php
	function unique_tag_name( $basename, $peers )
	{
		$currname = $basename;
		$x = 1;
		while( isset($peers[$currname]) )
		{
			$currname = $basename.$x;
			$x++;
		}
		return $currname;
	}
	
	
	function parse_feed( $reader )
	{
		$tagstack = array(array());
		$tagnamestack = array("root");
		
		while( $reader->read() )
		{
			if( $reader->nodeType == XMLReader::ELEMENT )
			{
				$tagname = unique_tag_name($reader->name,$tagstack[sizeof($tagstack)-1]);
				if( $reader->isEmptyElement )
				{
					$tagstack[sizeof($tagstack)-1][$tagname] = array();
				}
				else
				{
					array_push( $tagnamestack, $tagname );
					array_push( $tagstack, array() );
				}
			}
			else if( $reader->nodeType == XMLReader::END_ELEMENT )
			{
				$currtag = array_pop($tagstack);
				$currtagname = array_pop($tagnamestack);
				$tagstack[sizeof($tagstack)-1][$currtagname] = $currtag;
			}
			else if( $reader->nodeType == XMLReader::TEXT )
			{
				$tagstack[sizeof($tagstack)-1] = $reader->value;
			}
			else if( $reader->nodeType == XMLReader::CDATA )
			{
				$tagstack[sizeof($tagstack)-1] = $reader->value;
			}
			else if( $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE || $reader->nodeType == XMLReader::WHITESPACE )
				;
		}
		
		return $tagstack[0];
	}

	$userid = userid_from_shortname( $_REQUEST['shortname'] );
	if( $userid === false )
		return;
	
	$userinfo = userinfo_from_userid( $userid );
	if( strlen($userinfo['feedurl']) == 0 )
		return;	// Don't need to import from one of our local users.
	
	$reader = new XMLReader;
	$reader->open( $userinfo['feedurl'] );
	
	$feed = parse_feed( $reader );
	
	$reader->close();
	
	$channel = $feed['rss']['channel'];
	$x = 1;
	$itemName = 'item';
	while( true )
	{
		if( !isset($channel[$itemName]) )
			break;
		
		$text = $channel[$itemName]['description'];
		$inreplyto = 0;
		$result = mysql_query ("INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text' )");
	
		print_r( mysql_error() );
		
		$x++;
		$itemName = 'item'.$x;
	}

?>