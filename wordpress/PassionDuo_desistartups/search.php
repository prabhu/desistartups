<?php get_header(); ?>

<div id="wrap">
<div id="page_content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	<?php the_content(__('Read more'));?>

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