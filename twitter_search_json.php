<?php
	header("Content-Type: application/json; charset=UTF-8");
	require 'tmhOAuth/tmhOAuth.php';
	require 'twitter_auth.php';
	$tmhOAuth = new tmhOAuth($twitter_auth);
	if (isset($_GET['q'])){
		$search = $_GET['q'];
	}
	$statuses_url = '1.1/search/tweets';
	$options = array(
		'q'=>urlencode($search)
	);
	$code = $tmhOAuth->request('GET', $tmhOAuth->url($statuses_url), $options);
	$return = $tmhOAuth->response['response'];
	echo $return;
?>