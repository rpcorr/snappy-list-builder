=== Snappy List Builder ===
Contributors: rcorr
Donate link: https://example.com/
Tags: Email, Subscribe, Email List, Email Subscribers, List Builder, Newsletter, MailChimp, CSV, Opt-in, Double Opt-in, Opt-in, Plugin Course, Ultimate
Requires at least: 4.0
Tested up to: 6.0
Stable tag: 1.0.1
Requires PHP: 7.4.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Capture new subscribers. Reward subscribers with custom download upon opt-in. Build unlimited lists. Import and export subscribers easily.

== Description ==

The ultimate email list building plugin for WordPress. Capture new subscribers.  Reward subscribers with a custom download
upon-opt-in. Build unlimited lists. Import and export subscribers easily with .csv

Some of the awesome features include:

*  Create unlimited email lists
*  Capture subscribers with custom forms using a Shortcode
*  Double opt-in for confirming subscriptions
*  User unsubscribe feature with a subscription manager
*  Reward subscribers with an exclusive download when they opt-in
*  Easily export subscribes to  a CSV
*  Easily import subscribes from  a CSV
*  Automatically email subscribers when they sign up and opt-in

== Installation ==

1. Unzip the plugin file
2. Upload the folder `snappy-list-builder` and it's content to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Use the `List Builder` plugin menu to create a new `Email List`
5. Copy the Shortcode (ex. `[slb_form id="123"]`) next to your new list and embed it anywhere in your website
6. You'll also need to create 3 new pages in your WordPress website (see steps 7-9)
7. A page where subscribers will confirm their subscriptions. Include the shortcode `[slb_confirm_subscription]` on this page
8. A page where subscribers will manage subscriptions. Include the shortcode `[slb_manage_subscriptions]` on this page
9. A page where subscribers will retrieve their list rewards. Include the shortcode `[slb_download_reward]` on this page
10. Visit `Plugin Options` in the `List Builder` plugin menu and update the `Manage Subscriber page`, `Opt-In Page`, and `Download Reward Page` to point to the appropriate pages you created in step 6.
11. Happy list building!

== Frequently Asked Questions ==

= Can I learn how to build a WordPress plugin like this? =

Yes. Visit www.wordpressplugincourse.com to learn more.

== Screenshots ==

1. Email Lists page
2. Edit Email List page
3. Subscribers page
4. Edit Subscribers page
5. Import Subscribers page
6. Plugin Option page
7. Plugin Dashboard

== Changelog ==

= 1.0.1 =
* Updated tested versions to include WordPress 6.0
* Improved security


== Upgrade Notice ==

= 1.0.1 = 
This upgrade gets rid of `untested WordPress version` notice on the plugin page for users who've upgraded to WordPress 4.3

== Learn how to build this plugin ==

This plugin is the result of what you'll learn in [The Ultimate WordPress Course](http://wordpressplugincourse.com/ "The Ultimate WordPress Plugin Course"):
An online video course to teach you how to become a WordPress plugin developer today.  Head over to [www.wordpressplugincourse.com](http://wordpressplugincourse.com/ "The Ultimate WordPress Plugin Course") to learn more!

== Other info ==

This course also uses:

 - WP Mail Logging plugin (https://wordpress.org/plugins/wp-mail-logging/) to record email testing while developing plugin.
 - Advanced Custom Fields (https://en-ca.wordpress.org/plugins/advanced-custom-fields/) for creating fields bofore integrating to custom plugin.