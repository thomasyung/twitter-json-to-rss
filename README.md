twitter-json-to-rss
===================

**Problem**

The problem is that Twitter has removed support for RSS in their APIs. You used to be able to subscribe to your Twitter lists via RSS by simply using this URL convention: 
https://api.twitter.com/1/screen_name/lists/list_name/statuses.rss

Fortunately, they have not removed support for output to JSON. Therefore, you can still use: 
https://api.twitter.com/1/screen_name/lists/list_name/statuses.json

However, RSS readers cannot subscribe to JSON feeds. At least, not yet. :-)

**Solution**

twitter_list_json_to_rss.php is a quick and dirty PHP script that you install on your public facing server that allows you to get around this problem.

**Installation**

* Make sure you have a web server capable of running PHP scripts.
* Put this script in the root web folder. Otherwise, you'll need to change the $script_name variable to point to correct path.

**Usage**

On your favorite, RSS reader application, simply enter the URL to your PHP script and provide your list_name and screen_name parameters.
http://server_name/twitter_list_json_to_rss.php?list_name=list_name&screen_name=your_name

For example, you can try one of the Twitter lists that I subscribe to: 
http://thomasyung.com/twitter_list_json_to_rss.php?list_name=mobiledev&screen_name=thomasyung

