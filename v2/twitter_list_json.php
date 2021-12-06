<?php
	require 'twitter_auth.php';
	require "vendor/autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;

	// Get querystring parameters from URL
	$list_id = $_GET['list_id'];
	if (!isset($list_id)) {
		echo "list_id parameter is required.";
		exit(0);
	}

	// Setup OAuth
	$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);
	$connection->setApiVersion('2');
	
	// Get the List tweets
	$api_url = 'lists/' . $list_id . '/tweets';
	$response = $connection->get($api_url, [
		'tweet.fields'=>'lang,author_id,attachments,entities,created_at',
		'media.fields'=>'preview_image_url',
		'expansions'=>'author_id,attachments.media_keys',
		'user.fields'=>'created_at',
		'max_results'=>50,
	]);
	
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($response);
?>