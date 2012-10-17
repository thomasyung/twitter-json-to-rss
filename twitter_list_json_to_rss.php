<?php
	$script_name = 'twitter_list_json_to_rss.php';
	$screen_name = $_GET['screen_name'];
	$list_name = $_GET['list_name'];
	$statuses_url = 'http://api.twitter.com/1/'.$screen_name.'/lists/'.$list_name.'/statuses.json?per_page=100';
	$fetch_json = file_get_contents($statuses_url);
	$return = json_decode($fetch_json);
	$now = date("D, d M Y H:i:s T");
	$output = "<?xml version=\"1.0\"?>
		<rss version=\"2.0\">
			<channel>
				<title>".$list_name."</title>
				<link>http://".$_SERVER['SERVER_NAME']."/".$script_name."?screen_name=".$screen_name."&list_name=".$list_name."</link>
				<description>".$list_name."</description>
				<pubDate>$now</pubDate>
				<lastBuildDate>$now</lastBuildDate>
				";
	foreach ($return as $line){
		$output .= "<item><title>".htmlentities($line->user->screen_name.": ".$line->text)."</title>
			<link>".htmlentities("https://twitter.com/".$line->user->screen_name."/statuses/".$line->id_str)."</link>
			<description>".htmlentities(strip_tags($line->text))."</description>
			<author>".$line->user->screen_name."</author>
			</item>";
	}
	$output .= "</channel></rss>";
	header("Content-Type: application/rss+xml");
	echo $output;
?>