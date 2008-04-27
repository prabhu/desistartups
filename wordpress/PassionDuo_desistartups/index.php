<?php get_header(); ?>

<div id="wrap">
<div id="content">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	

	<div class="postpacker">

	<h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	
	<div>	
	<div class="date">By <span style="text-transform:capitalize"><?php the_author_posts_link(); ?></span> | <span style="text-transform:uppercase"><?php the_time('M j, Y'); ?></span></div>
	<div class="postinfo"><?php the_category(', ') ?> | <span class="koment"><?php comments_popup_link('Comment', '1 Comment', '% Comments'); ?></span></div>
	</div>
	
	<div class="clearer"></div>
	
	<?php the_content(__('Continue Reading >>'));?>


	</div>

	<!--
	<?php trackback_rdf(); ?>
	-->
	
	<?php comments_template(); // Get wp-comments.php template ?>
	<?php endwhile; else: ?><?php endif; ?>

	
<div class="prevnext">

					<div class="alignleft">
						<?php next_posts_link('Previous Posts') ?>
					</div>

					<div class="alignright">
						<?php previous_posts_link('Next Posts') ?>
					</div>

                	</div>

	
</div>

<!-- The main column ends  -->

<?php get_sidebar(); ?>
</div>
<?php get_footer(); ?>
