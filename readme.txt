=== Hide and Catch Email ===
Contributors: austyfrosty
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F8F3JJ9ERQBYS
Tags: shortcode, replace, email, content, catch-email, hide-content
Requires at least: 3.0
Tested up to: 4.2
Stable tag: trunk

Adds a metabox to post/page and allows you to hide the content, and show only on form submission.

== Description ==

Replace the any public post types content with a form. Any visitor (who is not logged-in and/or set to a certain user role **set by you on a per post basis**) would have to fill out the form to see said content. Right now the form consists of a name field, email field and comment field. The form has a built in spam deterant *honey-pot* method.

There are two options per each post for the cotent replacement. 1) capability => defaults to 'activate_plugins'. 2) text => defualts to empty - (the text you'd like placed before the form). 

There are no furthur options or ways to view who has submitted which form beyond the email you receive. If you would like to keep a copy of all submissions per post please upgrade to the [Pro version](http://extendd.com/plugin/hide-content-catch-email-pro/), including double spam protection and AJAX submissions.

For requests or feedback please leave comments on [Hide &amp; Catch Email](http://austin.passy.co/wordpress-plugins/hide-and-catch-email/).

== Installation ==

Follow the steps below to install the plugin.

1. Upload the `hide-and-catch-email` directory to the /wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Screenshots ==

1. Replacement form
2. Replacement form with error
3. Metabox

== Frequently Asked Questions ==

= Where can I view submissions? =
You'll have to upgade to the [Pro version](http://extendd.com/plugin/hide-content-catch-email-pro/) to view submission on each post.

= Why create this plugin? =
I created this plugin to hide certain content and only reveal it when a user enters there email addresss.

== Changelog ==

= Version 0.4 (12/17/14) =

* Ready for WordPress 4.1
* Change mail to wp_mail

= Version 0.3.5.1 (12/3/12) =

* Removed PHP4 activation.
* Updated dashboard.

= Version 0.3.5 (11/8/11) =

* Feeds updated.
* WordPress 3.3 check.

= Version 0.3.4 (9/8/11) =

* Dashboard fix.

= Version 0.3.3 (6/23/11) =

* [BUG FIX] An error in the dashboard widget is casuing some large images. Sorry. Always escape.

= Version 0.3.2 (3/30/11) =

* Dashboard widget updated.

= Version 0.3.1 (3/15/11) =

* Changed variable where user was getting email instead of site admin.

= Version 0.3 (3/10/11) =

* Complete rewrite of plugin.
* Setting page removed
* Fixed header already sent by (...).
* Removed shortcode.
* Replaces the whole $content. Use included meta box.
* TODO:
** Use AJAX replacement.
** Already 90% written, just needs cross browser testing.

= Version 0.2.3 (2/24/11) =

* Removed javscript link causing hang-ups.

= Version 0.2.2 (2/9/11) =

* Updated the feed parser to comply with deprecated `rss.php` and use `class-simplepie.php`

= Version 0.2.1 =

* Definitions we're all pointing to the incorrect directories.
* Removed unnecessary files.
* Cleaned up option and pointed link to correct address in WordPress

= Version 0.2 =

* Initial Release.

= Version 0.1 =

* First build.


== Upgrade Notice ==

= 0.3 =
Complete rewrite using OOP. Removed shotcodes! Uses full content replacement via metabox.

= 0.2.2 =
Had some issues with SVN, and come files may have been moved, if you have issues, delete and reinstall.

= 0.2 =
First releasem expect bugs, please report to [Hide and Catch Email](http://austinpassy.com/wordpress-plugins/hide-and-catch-email)