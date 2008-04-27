<?php get_header(); ?>

<div id="wrap">
<div id="page_content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>

	<div style="display:block;float:left;">
	<div class="date">By <span style="text-transform:capitalize"><?php the_author_posts_link(); ?></span> | <span style="text-transform:uppercase"><?php the_time('M j, Y'); ?></span></div>
	<div class="postinfo"></div>
	</div>

	<?php the_content(__('Read more'));?>
	<!--
	<?php trackback_rdf(); ?>
	-->
	<?php comments_template(); // Get wp-comments.php template ?>
	<?php endwhile; else: ?><?php endif; ?>
	
<div class="prevnext">

					<div class="alignleft">
						<?php next_posts_link('&laquo; Previous Entries') ?>
					</div>

					<div class="alignright">
						<?php previous_posts_link('Next Entries &raquo;') ?>
					</div>

	</div>

	
</div>

<!-- The main column ends  -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
