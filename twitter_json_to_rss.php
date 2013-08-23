<?php

require 'tmhOAuth/tmhOAuth.php';
require 'twitter_auth.php';

define('THIS_URL', 'http://'.$_SERVER['HTTP_HOST']);

define('THIS_PATH',dirname($_SERVER["REQUEST_URI"]));

// If your rss feed is to be parsed by facebook, it will display twitter default meta description as link description text... Not very useful. Set this to true to use a custom page, which will use the tweet's message as description.

define('USE_CUSTOM_TWEET_FILE', false);

// RUNTIME

$tmhOAuth = new tmhOAuth($twitter_auth);

$screen_name = null;
if (isset($_GET['screen_name'])){
	$screen_name = $_GET['screen_name'];
}
$list_name = null;
if (isset($_GET['list_name'])){
	$list_name = $_GET['list_name'];
}
$search = null;
if (isset($_GET['q'])){
	$search = $_GET['q'];
}
if (!isset($screen_name) && !isset($search)){
	echo "Oops! screen_name parameter or hashtag is required.";
	exit();
}
if (isset($list_name)){
	$statuses_url = '1.1/lists/statuses.json';
	$options = array(
		'slug'=>$list_name,
		'owner_screen_name'=>$screen_name,
		'count'=>100,
	);
	$title = $list_name;
	$description = $list_name;

} elseif(isset($search)){
	$usage = 'hashtag';
	$statuses_url = '1.1/search/tweets';
	$options = array(
		'q'=>urlencode($search)
	);
	$title = $search;
	$description = $search;

} else {
	$statuses_url = '1.1/statuses/user_timeline.json';
	$options = array(
		'screen_name'=>$screen_name,
		'count'=>100,
	);
	$title = $screen_name;
	$description = $screen_name;
}

$code = $tmhOAuth->request('GET', $tmhOAuth->url($statuses_url), $options);

$return = json_decode($tmhOAuth->response['response'], false);
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
$tweets = ($usage == 'hashtag') ? $return->statuses : $return;
foreach ($tweets as $line){
	$title= htmlspecialchars(htmlspecialchars_decode($line->user->name.": ".strip_tags($line->text)));
	$description= htmlspecialchars(htmlspecialchars_decode(strip_tags($line->text)));
	$url = htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str);;
	$image = (strlen($line->entities->media[0]->media_url)>0) ? htmlspecialchars($line->entities->media[0]->media_url) : null;
	$created_at = rfc822Date($line->created_at);

?>
		<item>
			<title><?php echo $title; ?></title>
			<description>
			<![CDATA[
			<?php
	echo $description;
	if (strlen($line->entities->media[0]->media_url)>0) { ?>
				<img src="<?php echo $image; ?>">
			<?php
	}
	?>	]]></description>
			<pubDate><?php echo $created_at ?></pubDate>
			<guid><?php echo $url; ?></guid>
			<link><?php echo (USE_CUSTOM_TWEET_FILE) ? THIS_URL.THIS_PATH.'/a_tweet.php?tid='.$line->id_str : $url; ?></link>
		</item>
<?php
}
?>
</channel>
</rss>
<?php
function rfc822Date($str){
	$timestamp = strtotime($str);
	return date(DATE_RSS, $timestamp);
}
?>