<?php
/*
	fetches a given tweet, and renders an html page using the tweet's content. Useful to control how facebook publishes a link to the tweet (if just using the twitter status url, the description is Twitter's default description meta tag, yuk!).
*/

error_reporting(E_WARNING | E_ERROR);
ini_set('display_errors',1);

$tid = null;

if(!isset($_GET['tid'])){
	die("nope, sorry, no tweet specified");
}

$tid = trim($_GET['tid']);

require 'tmhOAuth/tmhOAuth.php';
require 'twitter_auth.php';

$tmhOAuth = new tmhOAuth($twitter_auth);

$url = "/1.1/statuses/show.json";
$options = array(
	'id'=> $tid,
);

// DO !

$code = $tmhOAuth->request('GET', $tmhOAuth->url($url), $options);
$tweet = json_decode($tmhOAuth->response['response'], false);


$title= htmlspecialchars(htmlspecialchars_decode($tweet->user->name.": ".strip_tags($tweet->text)));
$description= htmlspecialchars(htmlspecialchars_decode(strip_tags($tweet->text)));
$description_html = find_hashtags(find_links($description));

$url = htmlspecialchars("https://twitter.com/".$tweet->user->screen_name."/statuses/".$tweet->id_str);;
$image = (strlen($tweet->entities->media[0]->media_url)>0) ? htmlspecialchars($tweet->entities->media[0]->media_url) : null;
$datetime = new DateTime($tweet->created_at);
$datetime->setTimezone(new DateTimeZone('Europe/Brussels'));
$created_at = $datetime->format(DATE_RFC822);
$profile_url= $tweet->user->profile_image_url;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title ?></title>

		<!-- Meta Information starts -->
		<meta name="description" content="<?php echo $description ?>">

		<!-- Open graph starts -->
		<meta property="og:title" content="<?php echo $title ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo $_SERVER['PHP_SELF'] ?>">
		<meta property="og:image" content="<?php echo $image ?>">
		<meta property="og:description" content="<?php echo $description ?>">
		<!-- Open graph ends -->
		<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.2.1/pure-min.css">
		<style type="text/css">
		body{background:#DDD;}
		h1{font-size:100%;}
		article{background:white;max-width:25em;margin:2em auto; padding:2em;border:1px solid #CCC;border-radius:1em}
		img{max-width: 100%;border:1px solid #CCC;padding:4px;vertical-align: text-bottom;}

		</style>
	</head>

	<body>
	<article>
<h1><?php echo '<img src="'.$profile_url.'"> '. $description_html ?></h1>
<p><?php echo ($image !='') ? '<img src="'.$image.'">':'';?></p>
<p>Source: <a href="<?php echo $url ?>" rel="nofollow">twitter</a></p>
	</article>
	</body>
</html>
<?php
// helpers
function find_links($text){
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	// make the urls hyper links
	return  (preg_match($reg_exUrl, $text, $url)) ? preg_replace($reg_exUrl, '<a href="'.$url[0].'">'.$url[0].'</a> ', $text) : $text;
}

function find_hashtags($text){
// https://twitter.com/search?q=%23dwmaj&src=hash
	return preg_replace("/#(\w+)/i", "<a href=\"http://twitter.com/search?q=%23$1\">#$1</a>", $text);
}
?>