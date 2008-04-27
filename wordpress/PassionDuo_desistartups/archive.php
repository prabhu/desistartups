<?php get_header(); ?>

<div id="wrap">
<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<div class="postpacker">

	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	
	<div>
	<div class="date">By <span style="text-transform:capitalize"><?php the_author_posts_link(); ?></span> | <span style="text-transform:uppercase"><?php the_time('M j, Y'); ?></span></div>
	<div class="postinfo"><img src="<?php bloginfo('template_url'); ?>/images/i_com.gif" align="top" alt="" /> <span class="koment"><?php comments_popup_link('Comment', '1 Comment', '% Comments'); ?></span></div>
	</div>

<div class="clearer"></div>

	<?php the_excerpt(__('Read more'));?>

</div>


	<!--
	<?php trackback_rdf(); ?>
	-->

	
	<?php endwhile; else: ?>
	<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>
	<p><?php posts_nav_link(' &#8212; ', __('&laquo; Previous Page'), __('Next Page &raquo;')); ?></p>
	
</div>

<!-- The main column ends  -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>