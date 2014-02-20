twitter-json-to-rss
===================

**Problem**

The problem is that Twitter has removed support for RSS in their APIs. You used to be able to subscribe to your Twitter lists via RSS by simply using this URL convention: 
https://api.twitter.com/1/screen_name/lists/list_name/statuses.rss

Fortunately, they have not removed support for output to JSON. However, RSS readers cannot subscribe to JSON feeds.

**Solution**

*twitter-json-to-rss* is a set of PHP scripts that you install on your public facing server that allows you to get around this problem.

**Installation**

* Make sure you have a web server capable of running PHP 5.3+ scripts.
* Put all the files included here in a web folder of your choice.
* Create a Twitter app from: https://dev.twitter.com/apps/new
* Make note of your: Consumer key, Consumer secret, Access token (user token), Access token secret (user secret)
* Edit the *twitter_auth.php* script to include the Twitter OAuth information from your Twitter App.

**Usage**

On your favorite RSS reader application, you can subscribe to a user's timeline like so:

http://[server_name]/twitter_json_to_rss.php?screen_name=[user_name]

To subscribe to a user's list, simply add the list_name parameter:

http://[server_name]/twitter_json_to_rss.php?screen_name=[user_name]&list_name=[list_name]

To subscribe to a hashtag feed, use a "q" parameter:

http://[server_name]/twitter_json_to_rss.php?q=[searchstring]
Note: if you want to specify a hashtag, url-encode it (= %23 instead of #). For instance, to search on a "#design" hashtag, the searchstring is "%23design".

For example, you can try one of the Twitter lists that I subscribe to:

http://thomasyung.com/twitter_rss/twitter_json_to_rss.php?screen_name=thomasyung&list_name=mobiledev

**Update (June 13, 2013)**
* Twitter removed v1.0 of their API and are forcing all v1.1 API requests via OAuth and SSL. In short, no more public access to the JSON APIs. To get around this, we now have to do some extra setup to get our twitter-json-to-rss PHP script to work. 
* We will now use tmhOAuth PHP scripts to act as an SSL OAuth proxy.

**Update (June 20, 2013)**
* I've now included the tmhOAuth from https://github.com/themattharris/tmhOAuth and included it in this distribution.
* I've also deprecated the *twitter_list_json_to_rss.php* but I have kept it in this distribution in case people are still using it in their RSS subscriptions. Instead, you should be using *twitter_json_to_rss.php*
* The *twitter_list_json.php* and *twitter_json.php* will output JSON in case you need to consume them publicly in another application.
