<?php

require 'tmhOAuth/tmhOAuth.php';
require 'twitter_auth.php';

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
foreach ($tweets as $line){ ?>
		<item>
			<title><?php echo htmlspecialchars(htmlspecialchars_decode($line->user->name.": ".strip_tags($line->text))); ?></title>
			<description><?php echo htmlspecialchars(htmlspecialchars_decode(strip_tags($line->text))); ?></description>
			<pubDate><?php echo $line->created_at ?></pubDate>
			<guid><?php echo htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str); ?></guid>
			<link><?php echo htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str); ?></link>
<?php echo (strlen($line->entities->media[0]->media_url)>0) ? '<image>'.htmlspecialchars($line->entities->media[0]->media_url).'</image>'."\n": ''; ?>
		</item>
<?php
}
?>
</channel>
</rss>