<?php
	header("Content-Type: application/xml; charset=UTF-8");
	$screen_name = $_GET['screen_name'];
	$list_name = $_GET['list_name'];
	$statuses_url = 'http://api.twitter.com/1/'.$screen_name.'/lists/'.$list_name.'/statuses.json?per_page=100';
	$fetch_json = file_get_contents($statuses_url);
	$return = json_decode($fetch_json);
	$now = date("D, d M Y H:i:s T");
	$link = htmlspecialchars('http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING']);
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo $list_name; ?></title>
		<link><?php echo $link; ?></link>
		<atom:link href="<?php echo $link; ?>" rel="self" type="application/rss+xml" />
		<description><?php echo $list_name; ?></description>
		<pubDate><?php echo $now; ?></pubDate>
		<lastBuildDate><?php echo $now; ?></lastBuildDate>
	<?php foreach ($return as $line){ ?>
	<item>
		<title><?php echo htmlspecialchars(htmlspecialchars_decode($line->user->screen_name.": ".strip_tags($line->text))); ?></title>
		<description><?php echo htmlspecialchars(htmlspecialchars_decode(strip_tags($line->text))); ?></description>
		<guid><?php echo htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str); ?></guid>
		<link><?php echo htmlspecialchars("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str); ?></link>
	</item>
	<?php } ?>
</channel>
</rss>