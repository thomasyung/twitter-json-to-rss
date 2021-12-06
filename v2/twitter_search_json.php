<?php
	require 'twitter_auth.php';
	require "vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;

	// Get querystring parameters from URL
	$search = $_GET['search'];
	if (!isset($search)) {
		echo "search parameter is required.";
		exit(0);
	}
	$hashtag = $_GET['hashtag'];
	if (isset($hashtag)) {
		$search = '#' . $search;
	}
	$lang = $_GET['lang'];
	if (isset($lang)) {
		$search .= ' lang:' . $lang;
	}

	// Setup OAuth
	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);
	$connection->setApiVersion('2');
	
	// Get the latest tweets based on search query
	$api_url = 'tweets/search/recent';
	$response = $connection->get($api_url, [
        'query'=>$search,
		'tweet.fields'=>'created_at,lang,author_id,attachments,entities,created_at',
		'media.fields'=>'preview_image_url',
		'expansions'=>'author_id,attachments.media_keys',
		'user.fields'=>'created_at',
		'max_results'=>50,
    ]);
	
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($response);
?>