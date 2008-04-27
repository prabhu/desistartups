=== Post-Plugin Library ===
Contributors: RobMarsh
Tags: posts, comments, random, recent, similar, related, post-plugins
Requires at least: 1.5
Tested up to: 2.5
Stable tag: 2.5b23

The shared code library for Similar posts, Recent Posts, Random Posts and Recent Comments.

== Description ==

The Post-Plugin Library does nothing by itself but **must** be installed to provide shared code for the [Similar Posts](http://wordpress.org/extend/plugins/similar-posts/), [Recent Posts](http://wordpress.org/extend/plugins/recent-posts-plugin/), [Random Posts](http://wordpress.org/extend/plugins/random-posts-plugin/)>, and [Recent Comments](http://wordpress.org/extend/plugins/recent-comments-plugin/) plugins.

== Installation ==

1. IMPORTANT! If you are upgrading from a previous version first deactivate the plugin, then delete the plugin folder from your server. 

1. Upload the plugin folder to your /wp-content/plugins/ folder.

1. Go to the **Plugins** page and activate the plugin.

[My web site](http://rmarsh.com/) has [full instructions](http://rmarsh.com/plugins/) and [information on customisation](http://rmarsh.com/plugins/post-options/).

== Version History ==

* 2.5b23
	* new option to filter on custom fields
	* proper nesting of braces in {if}  ; also allowed in condition
	* improved bug report feature
	* better way to omit user comments
* 2.5b22
	* show_pages option now possible to show only pages
	* update for {postviews}
	* fix {commenterlink} giving no output
* 2.5b21
	* fix bug in {snippet} stripping
* 2.5b20
	* fix default behaviour of {gravatar}
* 2.5b18
	* new tag {if:condition:true:false}
* 2.5b17
	* enhanced {php}
* 2.5b16
	* fix submenus to work with Lighter Admin Drop Menu
* 2.5b15
	* fix bugs, add 'included posts' setting
* 2.5b14
	* enhanced bug reporter
* 2.5b13
	* added {gravatar}, extended {author}
* 2.5b11
	* fixed problem with bug reporter
* 2.5b9
	* clarifying installation instructions