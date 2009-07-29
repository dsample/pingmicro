<?php

if ($_REQUEST['action'] == 'send')
{
	// Replace YOURAPIKEY with your API key from http://www.qaiku.com/settings/api/
	$api['qaiku']['get'] = "http://www.qaiku.com/api/statuses/update.xml?apikey=YOURAPIKEY";
	$api['qaiku']['post'] = "status={status}";

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

	// Uncomment the services you want to post to
	#$post_to[] = 'qaiku';
	#$post_to[] = 'jaiku';
	#$post_to[] = 'twitter';
	#$post_to[] = 'identica';
	#$post_to[] = 'pingfm';

	foreach($post_to as $this_api)
	{
		$get = str_replace('{status}',$_REQUEST['status'],$api[$this_api]['get']);
		if ($api[$this_api]['post'] != null)
		{
			$post = str_replace('{status}',$_REQUEST['status'],$api[$this_api]['post']);
			//$post = urlencode($post);
			echo "posting: " . $post;
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

		$output[$this_api] = curl_exec($curl);

		curl_close($curl);
	}
}

?>
<html>
<head>
<title>Multipost</title>
</head>
<body>

<?php
if ($_REQUEST['action'] == 'send')
{
	echo "<p>Results</p>";

	foreach ($output as $this_output)
	{
		echo "<p><pre>" . $this_output . "</pre></p>\n";
	}
}
?>

<form method="post">
<label for="status">Status</label>: <input type="text" id="status" name="status" />
<input type="submit" id="action" name="action" value="send" />
<label for="lang">Lang</label>: <input type="text" id="lang" name="lang" value="en">
</form>

</body>
</html>
