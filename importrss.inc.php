<?php
	function download_data($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		if( $httpCode == 404 )
			return "";
		
		return $data;
	}
	
	
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


	function import_external_user_tweets( $userid )
	{
		$userinfo = userinfo_from_userid( $userid );
		if( strlen($userinfo['feedurl']) == 0 || $userInfo['isAdmin'] == 1 )
			return false;	// Don't need to import from one of our local users.
		
		$xmldata = download_data( $userinfo['feedurl'] );
		if( strlen($xmldata) == 0 )
			return false;
		
		$reader = new XMLReader;
		if( !$reader->xml( $xmldata ) )
			return false;
		
		$feed = parse_feed( $reader );
		
		$reader->close();
		
		$channel = $feed['rss']['channel'];
		$fullname = mysql_real_escape_string($channel['title']);
		$bio = mysql_real_escape_string($channel['description']);
		if( isset($channel['image']) && isset($channel['image']['url']) )
		{
			$avatarurl = $channel['image']['url'];
			$avatarurl = str_replace("<","",$avatarurl);
			$avatarurl = str_replace(">","",$avatarurl);
			$avatarurl = str_replace("\"","",$avatarurl);
			$avatarurl = str_replace("\r","",$avatarurl);
			$avatarurl = str_replace("\n","",$avatarurl);
			$avatarurl = mysql_real_escape_string($avatarurl);
		}
		else
			$avatarurl = "";
		if( isset($channel['link']) )
		{
			$homepage = $channel['link'];
			$homepage = str_replace("<","",$homepage);
			$homepage = str_replace(">","",$homepage);
			$homepage = str_replace("\"","",$homepage);
			$homepage = str_replace("\r","",$homepage);
			$homepage = str_replace("\n","",$homepage);
			$homepage = mysql_real_escape_string($homepage);
		}
		else
			$homepage = "";
		$result = mysql_query( "UPDATE users SET fullname='$fullname', biography='$bio', avatarurl='$avatarurl', homepage='$homepage' WHERE id='$userid'" );
		print_r( mysql_error() );
		
		$x = 1;
		$itemName = 'item';
		while( true )
		{
			if( !isset($channel[$itemName]) )
				break;
			
			$text = html_entity_decode($channel[$itemName]['description']);
			if( preg_match( "/^<a href=\"(.+?)\" rel=\"prev\">@([-A-Za-z.]+)<\\/a>/", $text, $matches ) == 1)
			{
				$text = '@'.$matches[2].substr( $text, strlen($matches[0]) );
				$inreplyto = mysql_real_escape_string(str_replace("\"", "", str_replace("\r", "", str_replace("\n", "", str_replace(">", "", str_replace("<", "", $matches[1]))))));
			}
			else
				$inreplyto = '';
			$text = mysql_real_escape_string($text);
			
			$url = mysql_real_escape_string($channel[$itemName]['link']);
			$timestamp = strtotime($channel[$itemName]['pubDate']);
			$result = mysql_query( "INSERT INTO statuses VALUES ( NULL, '$userid', '$inreplyto', '$text', '$url', '$timestamp' )" );
			if( mysql_errno() != 0 )
				$result = mysql_query( "UPDATE statuses SET text='$text', replytourl='$inreplyto', timestamp='$timestamp' WHERE user_id='$userid' AND url='$url'" );
			
			print_r( mysql_error() );
			
			$x++;
			$itemName = 'item'.$x;
		}
		
		return $x;
	}
?>