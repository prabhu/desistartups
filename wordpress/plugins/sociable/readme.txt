=== Sociable ===
Contributors: joostdevalk
Donate link: http://www.joostdevalk.nl/donate/
Tags: social, bookmark, bookmarks, bookmarking, social bookmarking, social bookmarks
Requires at least: 2.2
Tested up to: 2.5
stable tag: 2.6.4

Automatically add links on your posts to popular social bookmarking sites.

== Description ==
Automatically add links on your posts to popular social bookmarking sites like Facebook, Mixx, StumbleUpon, Digg and many many others.

Special thanks to [Robert Harm](http://www.die-truppe.com/) for coming up with loads of nice ideas.

More info:

* [Sociable](http://www.joostdevalk.nl/wordpress/sociable/).
* Read more about [WordPress SEO](http://www.joostdevalk.nl/wordpress-seo/) so you can get the most out of this plugin.
* Check out the other [Wordpress plugins](http://www.joostdevalk.nl/wordpress/) by the same author.

Changelog

2.5.4 Added HealthRanker, N4G, Meneame, BarraPunto, Laaik.it and E-mail option
2.5.3 Added Global Grind, Salesmarks, Webnews.de, Xerpi, Yigg
2.5.2 Added NuJIJ, eKudos, Sk-rt, Socialogs and MisterWong.de
2.5.1 Swapped Netscape for Propeller

== Installation ==

Download, Upgrading, Installation:

Upgrade

1. First deactivate Sociable
1. Remove the `sociable` directory

Install

1. Unzip the `sociable.zip` file. 
1. Upload the the `sociable` folder (not just the files in it!) to your `wp-contents/plugins` folder. If you're using FTP, use 'binary' mode.

Activate

1. In your WordPress administration, go to the Plugins page
1. Activate the Sociable plugin and a subpage for Sociable will appear
   in your Options menu.

If you find any bugs or have any ideas, please mail me.

Advanced Users

Sociable hooks the_content() and the_excerpt() to display without requiring theme editing. To heavily customize the display, use the admin panel to turn off the display on all pages, then add calls to your theme files:

This is optional extra customization for advanced users:
`<?php if (function_exists('sociable_html')) { print sociable_html(); } ?> // all active sites`
`<?php if (function_exists('sociable_html')) { print sociable_html(Array("Reddit", "del.icio.us")); } ?> // only these sites if they are active`
