twitter-json-to-rss
===================

**Update (June 13, 2013)**

Twitter removed v1.0 of their API and are forcing all v1.1 API requests via OAuth and SSL. 
In short, no more public access to the JSON APIs. 
To get around this, we now have to do some extra setup to get our twitter-json-to-rss PHP script to work. 
* Please download tmhOAuth from: https://github.com/themattharris/tmhOAuth
* Those set of PHP scripts will act as an SSL OAuth proxy.
* Create a Twitter app from: https://dev.twitter.com/apps/new
* Make note of your: Consumer key, Consumer secret, Access token (user token), Access token secret (user secret)

**Problem**

The problem is that Twitter has removed support for RSS in their APIs. You used to be able to subscribe to your Twitter lists via RSS by simply using this URL convention: 
https://api.twitter.com/1/screen_name/lists/list_name/statuses.rss

Fortunately, they have not removed support for output to JSON. However, RSS readers cannot subscribe to JSON feeds.

**Solution**

twitter_list_json_to_rss.php is a PHP script that you install on your public facing server that allows you to get around this problem.

**Installation**

* Make sure you have a web server capable of running PHP scripts.
* Put twitter_list_json_to_rss.php in a web folder of your choice.
* Edit the script to include the Twitter OAuth information that you made note of earlier.
* In the same web folder, create a tmhOAuth folder. Put the tmhOAuth files there.

**Usage**

On your favorite, RSS reader application, simply enter the URL to your PHP script and provide your list_name and screen_name parameters.
http://server_name/twitter_list_json_to_rss.php?list_name=list_name&screen_name=your_name

For example, you can try one of the Twitter lists that I subscribe to: 
http://thomasyung.com/twitter_list_json_to_rss.php?list_name=mobiledev&screen_name=thomasyung

