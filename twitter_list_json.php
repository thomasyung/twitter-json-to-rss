<?php
	header("Content-Type: application/json; charset=UTF-8");
	require 'tmhOAuth/tmhOAuth.php';
	require 'twitter_auth.php';
	$tmhOAuth = new tmhOAuth($twitter_auth);
	$screen_name = $_GET['screen_name'];
	$list_name = $_GET['list_name'];
	$statuses_url = '1.1/lists/statuses.json';
	$code = $tmhOAuth->request('GET', $tmhOAuth->url($statuses_url), array(
		'slug'=>$list_name,
		'owner_screen_name'=>$screen_name,
		'count'=>10,
	));
	$return = $tmhOAuth->response;
	echo $return['response'];
?>