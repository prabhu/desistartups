<?php include(TEMPLATEPATH."/config.inc.php");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="distribution" content="global" />
<meta name="robots" content="follow, all" />
<meta name="language" content="en, sv" />
<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<!-- leave this for stats please -->
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php if($db_feedburner_address) { echo $db_feedburner_address; } else { bloginfo('rss2_url'); } ?>" /><?php /* if you put your feedburner into the theme options, the autodiscover will use that instead of the WP default feed */ ?>
<LINK REL="stylesheet" HREF="<?php bloginfo('template_url'); ?>/css/tabber.css" TYPE="text/css" MEDIA="screen">
<script type="text/javascript">
function chooseStyle(title)
{
  setActiveStyleSheet(title);
}
var tabberOptions = {
  onTabClick:
  function(tabIndex)
  {
    chooseStyle(tabIndex+1);
  }
};
</script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/css/styleswitcher.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/css/tabber.js"></script>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_head(); ?>
<style type="text/css" media="screen">
<!-- @import url( <?php bloginfo('stylesheet_url'); ?> ); -->
</style>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-148132-8");
pageTracker._initData();
pageTracker._trackPageview();
</script>
</head>
<body>
<div class="top"><img src="<?php bloginfo('template_url'); ?>/images/top.gif" alt="" /></div>
<div id="body-container">
<div id="header">
<div class="headline">
<a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('template_url'); ?>/images/desistartups-logo.jpg" alt="Desistartups logo" hspace="6"/></a>
</div>
<div class="TopMenu">
   <ul>
     <li><a href="<?php bloginfo('url'); ?>" title="<?php _e('Home'); ?>" id="home">Home</a></li><?php wp_list_pages('depth=1&sort_column=menu_order&title_li=' . __('') . '' ); ?>
   </ul>
</div>
</div>
