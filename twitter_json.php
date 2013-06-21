<?php
	header("Content-Type: application/json; charset=UTF-8");
	require 'tmhOAuth/tmhOAuth.php';
	require 'twitter_auth.php';
	$tmhOAuth = new tmhOAuth($twitter_auth);
	$screen_name = $_GET['screen_name'];
	$statuses_url = '1.1/statuses/user_timeline.json';
	$code = $tmhOAuth->request('GET', $tmhOAuth->url($statuses_url), array(
		'screen_name'=>$screen_name,
		'count'=>10,
	));
	$return = $tmhOAuth->response;
	echo $return['response'];
?>