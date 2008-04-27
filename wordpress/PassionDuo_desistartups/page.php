<?php get_header(); ?>

<div id="wrap">
<div id="page_content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	<?php the_content(__('Read more'));?>

	<!--
	<?php trackback_rdf(); ?>
	-->
	<?php endwhile; else: ?><?php endif; ?>
	
</div>

<!-- The main column ends  -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
