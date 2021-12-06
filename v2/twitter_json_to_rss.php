<?php

// Import required libraries
require 'twitter_auth.php';
require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

define('THIS_URL','http://'.$_SERVER['HTTP_HOST']);
define('THIS_PATH',dirname($_SERVER["REQUEST_URI"]));

// If your rss feed is to be parsed by facebook, it will display twitter default meta description as link description text... Not very useful. Set this to true to use a custom page, which will use the tweet's message as description.
define('USE_CUSTOM_TWEET_FILE', true);

//
// Get querystring parameters from URL
//
$list_id = null;
if (isset($_GET['list_id'])) {
	$list_id = $_GET['list_id'];
}
$query = null;
if (isset($_GET['query'])) {
	$query = $_GET['query'];
	$lang = (isset($_GET['lang'])) ? $_GET['lang'] : "en";
}
$user_name = null;
if (isset($_GET['user_name'])) {
	$user_name = $_GET['user_name'];
}

// Check for the minimum required parameters
if (!isset($list_id) && !isset($query) && !isset($user_name)) {
	echo 'Oops! "user_name=", "list_id=", or "query=" URL parameter is required.';
	exit();
}

// Setup OAuth
$connection = new TwitterOAuth($CONSUMER_KEY, $CONSUMER_SECRET, $access_token, $access_token_secret);
$connection->setApiVersion('2');

//
// Execute the API endpoints and associated parameters
//
if (isset($list_id)) { // LIST TWEETS

    $api_url = 'lists/' . $list_id . '/tweets';
	$response = $connection->get($api_url, [
		'tweet.fields'=>'lang,author_id,attachments,entities,created_at',
        'media.fields'=>'preview_image_url',
		'expansions'=>'author_id,attachments.media_keys',
		'user.fields'=>'created_at',
		'max_results'=>50,
	]);

    // Errors? Exit
    if (isset($response->errors)) {
        echo $response->errors[0]->message;
        exit();
    }

    // Get list name for RSS data
    $api_url = 'lists/' . $list_id;
	$list_name_raw = $connection->get($api_url);
    $list_name = $list_name_raw->data->name;

    // Set title and description fields for RSS feed
	$title = $list_name;
	$description = $list_name;

} elseif (isset($query)) { // SEARCH TWEETS

    // optional hashtag parameter (search only tweets matching hashtag)
    $hashtag = $_GET['hashtag'];
	if (isset($hashtag)) {
		$query = '#' . $query;
	}

    // optional language parameter (search only tweets matching a language specified)
    $lang = $_GET['lang'];
    if (isset($lang)) {
        $query .= ' lang:' . $lang;
    }

    // Now get the tweets using the API
    $api_url = 'tweets/search/recent';
	$response = $connection->get($api_url, [
        'query'=>$query,
		'tweet.fields'=>'lang,author_id,attachments,entities,created_at',
        'media.fields'=>'preview_image_url',
		'expansions'=>'author_id,attachments.media_keys',
		'user.fields'=>'created_at',
        'max_results'=>50,
    ]);

    // Errors? Exit
    if (isset($response->errors)) {
        echo $response->errors[0]->message;
        exit();
    } elseif (!isset($response->data)) {
        echo 'No results found for your query.';
        exit();
    }

	// Set title and description fields for RSS feed
	$title = $query;
	$description = $query;

} elseif (isset($user_name)) { // USER TIMELINE TWEETS

    // First get the id of the user
    $api_url = 'users/by/username/' . $user_name;
    $user_data = $connection->get($api_url);
    if (!isset($user_data->data->id)) {
        echo 'User "' . $user_name . '" not found.';
        exit();
    }
    $user_id = $user_data->data->id;

	// Now get the user's timeline tweets using the API
    $api_url = 'users/' . $user_id . '/tweets';
    $response = $connection->get($api_url, [
        'tweet.fields'=>'lang,author_id,attachments,entities,created_at',
        'media.fields'=>'preview_image_url',
		'expansions'=>'author_id,attachments.media_keys',
        'user.fields'=>'created_at',
        'max_results'=>50,
    ]);

    // Set title and description fields for RSS feed
	$title = $user_name;
	$description = $user_name;

}

// Setup user lookup
$users = array();
foreach( $response->includes->users as $user ) {
    $users[$user->id] = $user;
}

// Setup link and publish date fields for RSS feed
$now = date("D, d M Y H:i:s T");
$now = rfc822Date($now);
$link = htmlspecialchars('http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']);

header("Content-Type: application/xml; charset=UTF-8");
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo $title; ?></title>
		<link><?php echo $link; ?></link>
		<atom:link href="<?php echo $link; ?>" rel="self" type="application/rss+xml" />
		<description><?php echo $description; ?></description>
		<pubDate><?php echo $now; ?></pubDate>
		<lastBuildDate><?php echo $now; ?></lastBuildDate>
<?php 
    foreach ($response->data as $data) {
        $title = htmlspecialchars(htmlspecialchars_decode($users[$data->author_id]->name.": ".strip_tags($data->text)));
        $description = htmlspecialchars(htmlspecialchars_decode(strip_tags($data->text)));
        $url = htmlspecialchars("https://twitter.com/".$users[$data->author_id]->username."/statuses/".$data->id);;
        $image = (isset($data->entities->urls[0]->images[0]->url)) ? htmlspecialchars($data->entities->urls[0]->images[0]->url) : null;
        $created_at = rfc822Date($data->created_at);
?>
		<item>
			<title><?php echo $title; ?></title>
			<description>
			<![CDATA[
            <?php echo $description; ?>
            <?php if ($image) { ?>
		        <img src="<?php echo $image; ?>">
            <?php } ?>
            ]]>
            </description>
			<pubDate><?php echo $created_at ?></pubDate>
			<guid><?php echo $url; ?></guid>
			<link><?php echo (USE_CUSTOM_TWEET_FILE) ? THIS_URL.THIS_PATH.'/a_tweet.php?tid='.$data->id : $url; ?></link>
		</item>
<?php } ?>
    </channel>
</rss>
<?php
function rfc822Date($str){
	$timestamp = strtotime($str);
	return date(DATE_RSS, $timestamp);
}
?>