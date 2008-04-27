=== Similar Posts ===
Contributors: RobMarsh
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate%40rmarsh%2ecom&item_name=Rob%20Marsh%27s%20WordPress%20Plugins&item_number=Similar%20Posts&no_shipping=1&cn=Any%20Comments&tax=0&currency_code=GBP&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: posts, related, similar, related posts, similar posts, tags, post-plugins
Requires at least: 1.5
Tested up to: 2.5
Stable tag: 2.5b23
Displays a list of posts similar to the current one based on content, title and/or tags.

== Description ==

Similar Posts displays a list of posts that are similar or related to the current posts. The list can be customised in *many* ways. Similarity is judged according to a post's title, content, and tags and you can adjust the balance of factors to fit your own blog.

This plugin **requires** the latest version of the *Post-Plugin Library:* [download it now](http://downloads.wordpress.org/plugin/post-plugin-library.zip).

== Installation ==

1. IMPORTANT! If you are upgrading from a previous version first deactivate the plugin, then delete the plugin folder from your server.

1. If you have the *Similar Posts Feed* plugin installed you must deactivate it before installing Similar Posts (which now does the same job).

1. Upload the plugin folder to your /wp-content/plugins/ folder. If you haven't already you should also install the [Post-Plugin Library](http://wordpress.org/extend/plugins/post-plugin-library/)></a>.

1. Go to the **Plugins** page and activate the plugin.

1. Put `<?php similar_posts(); ?>` at the place in your template where you want the list of related posts to appear or use the plugin as a widget.

1. Use the **Options/Settings** page to adjust the behaviour of the plugin.

[My web site](http://rmarsh.com/) has [full instructions](http://rmarsh.com/plugins/similar-posts/) and [information on customisation](http://rmarsh.com/plugins/post-options/).

== Version History ==

* 2.5b23
	* new option to filter on custom fields
	* nested braces in {if}; condition now taggable
	* improved bug report feature
	* better way to omit user comments
* 2.5b22
	* restored automatic indexing on installation
	* moved indexing menu under settings
	* show_pages option can now show only pages
	* fix for upgraders who had utf8 selected but no mbstring
* 2.5b20
	* optimised indexing for speed and memory use
* 2.5b19
	* fixing some extended character issues
* 2.5b18
	* fix output filter bug
	* add conditional tag {if:condition:yes:no}
* 2.5b16
	* fix for {php}
* 2.5b15
	* fix more or less obscure bugs, add 'include posts' setting
* 2.5b14
	* fix file-encoding, installation error, etc.
* 2.5b12
	* fix serious bug for WP < 2.3
* 2.5b11
	* some widget fixes
* 2.5b10
	* fix for non-creation of table
* 2.5b9
	* clarifying installation instructions

* [previous versions](http://rmarsh.com/plugins/similar-posts/)
