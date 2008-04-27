<!-- begin sidebar -->

<div id="sidebar">

<? include(TEMPLATEPATH."/ads.php"); ?>

<div id="sidebar_full">

<div id="tabber" class="tabber">

<div class="tabbertab" title="Recent Posts">
<ul>
<li><ul>
<?php get_archives('postbypost', 8); ?>
</ul></li>
</ul>
</div>

<div class="tabbertab" title="Your say">
<ul><li>
<?php include (TEMPLATEPATH . '/simple_recent_comments.php'); /* recent comments plugin by: www.g-loaded.eu */?>
<?php if (function_exists('src_simple_recent_comments')) { src_simple_recent_comments(8, 60, '', ''); } ?>
</li>
</ul>
</div>

<div class="tabbertab" title="Hot Topics">
<ul><li>
<?php mdv_most_commented(6); ?> 
</li>
</ul>
</div>


</div>

</div>

<div id="sidebar_l">
 <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Left Sidebar') ) : else : ?>

<?php endif; ?>

</div>

<div id="sidebar_r">

<ul>
<li><a href="http://twitter.com/prabhus" rel="alternate"></a>&nbsp;<a href="http://twitter.com/prabhus" rel="alternate"><b>Follow us on Twitter</b></a></li>
<li><a href="http://feeds.feedburner.com/desistartups" rel="alternate"><img src="http://www.feedburner.com/fb/images/pub/feed-icon16x16.png" alt=""></a>&nbsp;<a href="http://feeds.feedburner.com/desistartups" rel="alternate">Subscribe in a reader</a></li>
<li>
<b><a href="http://mippin.com/mippin5703">On your Mobile</a></b>
</li>
<li>
<a href="http://www.feedburner.com/fb/a/emailverifySubmit?feedId=1470196">Via Email</a></li>			
				<li><a href="http://www.google.com/reader/preview/*/feed/http://feeds.feedburner.com/desistartups" title="Subscribe in Google Reader"><img src="<?php bloginfo('template_url'); ?>/images/googleread2.jpg" alt="Subscribe in Google Reader" width="91" height="17" /></a></li>
				<li><a href="http://add.my.yahoo.com/rss?url=http://feeds.feedburner.com/desistartups" title="Add to My Yahoo!" target="subscriptions"><img src="<?php bloginfo('template_url'); ?>/images/addtomyyahoo4.gif" alt="Add to My Yahoo!" /></a></li>
</ul>

<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('Right Sidebar') ) : else : ?>
<?php endif; ?>

</div>


</div>

<!-- end sidebar -->
