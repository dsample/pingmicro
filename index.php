<?php

if ($_REQUEST['action'] == 'send')
{
	// Replace YOURAPIKEY with your API key from http://www.qaiku.com/settings/api/
	$api['qaiku']['get'] = "http://www.qaiku.com/api/statuses/update.xml?apikey=YOURAPIKEY";
	$api['qaiku']['post'] = "status={status}&lang={lang}";
	$api['qaiku']['regex'] = '<(status)>';
	$api['qaiku']['ok'] = 'status';

	// Just enter your Twitter username and password
	$api['twitter']['get'] = "http://twitter.com/statuses/update.xml";
	$api['twitter']['post'] = "status={status}";
	$api['twitter']['username'] = '';
	$api['twitter']['password'] = '';

	// Just enter your Identi.ca username and password
	$api['identica']['get'] = "http://identi.ca/api/statuses/update.xml";
	$api['identica']['post'] = "status={status}";
	$api['identica']['username'] = '';
	$api['identica']['password'] = '';
	
	// For Jaiku, get your apikey from http://api.jaiku.com/key and put it in the URL with your username
	// Replace YOURUSERNAME with your username
	// Replace YOURAPIKEY with your API key
	$api['jaiku']['get'] = "http://api.jaiku.com/json";
	$api['jaiku']['post'] = "user=YOURUSERNAME&personal_key=YOURAPIKEY&method=presence.send&message={status}";

	// Request an developer API key from http://ping.fm/developers/request/
	// Replace YOURAPIKEY with your application API key
	// Replace YOURUSERAPPKEY with your key from http://ping.fm/key/
	$api['pingfm']['get'] = "http://api.ping.fm/v1/user.post"; 
	$api['pingfm']['post'] = "api_key=YOURAPIKEY&user_app_key=YOURUSERAPPKEY&post_method=default&body={status}";
	$api['pingfm']['regex'] = '<rsp status=\"([a-z]*)\">';
	$api['pingfm']['ok'] = 'OK';

	// Uncomment the services you want to post to
	#$post_to[] = 'qaiku';
	#$post_to[] = 'jaiku';
	#$post_to[] = 'twitter';
	#$post_to[] = 'identica';
	#$post_to[] = 'pingfm';

	foreach($post_to as $this_api)
	{
		$get = str_replace('{status}',urlencode(stripslashes($_REQUEST['status'])),$api[$this_api]['get']);
		$get = str_replace('{lang}',$_REQUEST['lang'],$get);
		if ($api[$this_api]['post'] != null)
		{
			$post = str_replace('{status}',urlencode(stripslashes($_REQUEST['status'])),$api[$this_api]['post']);
			$post = str_replace('{lang}',$_REQUEST['lang'],$post);
			//$post = urlencode($post);
		}
		else
		{
			$post = null;
		}

		// do a cURL request to post the status message
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $get);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		if ($post != null)
		{
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		}

		if ($api[$this_api]['username'] != null && $api[$this_api]['password'] != null)
		{
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, $api[$this_api]['username'] . ":" . $api[$this_api]['password']);
		}

		$api[$this_api]['output'] = curl_exec($curl);

		curl_close($curl);
	}
}

?>
<html>
<head>
<title>PingMicro</title>
</head>
<body>

<?php
if ($_REQUEST['action'] == 'send')
{
	echo "<p>Results</p>";
	echo "<ul>\n";

	foreach($post_to as $this_api)
	{
		if ($api[$this_api]['regex'] != null)
		{
			eregi($api[$this_api]['regex'], $api[$this_api]['output'], $regexResult);
			if (strtolower($regexResult[1]) == strtolower($api[$this_api]['ok']))
			{
				$api[$this_api]['output'] = 'Success';
			}
//			else
//			{
//				$api[$this_api]['output'] = 'Failed';
//			}
		}

		echo "<li>" . $this_api . ": " . htmlentities($api[$this_api]['output']) . "</li>\n";
	}

	echo "</ul>\n";
}
?>

<form method="post">
<label id="statuslabel" for="status">Status</label>: 
<textarea style="width:100%" rows="3" id="status" name="status" onchange="document.getElementById('statuslabel').innerHTML='Status (' + document.getElementById('status').value.length + ')'">
</textarea>
<input style="width:100%" type="submit" id="action" name="action" value="send" />
<input style="width:100%" type="text" id="lang" name="lang" value="en">
</form>

</body>
</html>
