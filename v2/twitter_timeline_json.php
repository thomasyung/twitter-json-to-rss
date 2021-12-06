<?php
require 'twitter_auth.php';
require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

// Get querystring parameters from URL
$user_name = $_GET['user_name'];
if (!isset($user_name)) {
	echo 'user_name parameter is required.';
	exit(0);
}

// Setup OAuth
$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);
$connection->setApiVersion('2');

// First get the id of the user
$api_url = 'users/by/username/' . $user_name;
$user_data = $connection->get($api_url);
if (!isset($user_data->data->id)) {
	echo 'user "' . $user_name . '" not found.';
	exit(0);
}
$user_id = $user_data->data->id;

// Now get the user's timeline tweets
$api_url = 'users/' . $user_id . '/tweets';
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