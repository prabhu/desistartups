<?php 
/*
 Plugin Name: WordPress XML Export
 Plugin URI: http://www.technosailor.com
 Description: This will generate a WordPress XML export for import into other WordPress blogs. Based on code provided in 2.1+ versions of WordPress and originally written by <a href="http://www.photomatt.net">Matt Mullenweg</a>.
 Version: 2.0
 Author: Aaron Brazell
 Author URI: http://www.technosailor.com/
 */

if ( ( '2.0' == substr( get_bloginfo('version'), 0, 3 ) ) || ( '1.5' == substr( get_bloginfo('version'), 0, 3 ) ) )
{
	if(!function_exists('wp_exporter_subpanel'))
	{
		function wp_exporter_subpanel()
		{
?>
<div class="wrap">
<h2><?php _e('Export'); ?></h2>
<p><?php _e('This will allow you to download an export of your WordPress posts and comments in an XML format usable in WordPress.com. It can also be used to merge or move content to a self-hosted WordPress blog. To import this file to a self-hosted WordPress blog, upload the wordpress.php file that was bundled with this plugin to <code>wp-admin/import/</code> folder <em>in the blog you are moving to!</em>  Once uploaded, you can find the Importer under the Import tab.'); ?></p>

<p><strong>WARNING:</strong> Some shared hosts limit the size of a file upload. To get around this, please consult with them about adjusting the upload limit. Alternatively, you can break the XML file into smaller pieces.</p>
<p><strong>WARNING #2:</strong> The WordPress Importer is a beta-level importer supported through WordPress. Issues with Importing should be logged with them, or on the <a href="http://www.wordpress.org/support">support forum</a>.</p>

<p>Please choose the categories you wish to export.</p>
<?php $cats = export_cat_list(); ?>
<form action="" method="post">
<ul style="list-style:none;">
<?php
	foreach($cats as $cat)
		{
			echo'<li><input type="checkbox" id="catexport[]" name="catexport[]" checked="checked" value="'.$cat->cat_ID.'" /> '.$cat->cat_name.'</li>'."\n";
		}
?>
</ul>

<p class="submit"><input type="submit" name="submit" value="<?php _e('Download Export File'); ?> &raquo;" />
<input type="hidden" name="wpexportdownload" value="true" />
</p>
</form>
</div>
<?php
		}
	}
	
	if(!function_exists('wp_export_navitem'))
	{
		function wp_export_navitem()
		{
		if (function_exists('add_management_page')) 
			{
				add_management_page('WP Export', 'WP Export', 1, basename(__FILE__), 'wp_exporter_subpanel');
			}
		}
	}

	if(!function_exists('wp_export'))
		{
		function export_wp($cats) 
			{
			global $wpdb, $posts, $post;
			$filename = 'wordpress.' . date('Y-m-d') . '.xml';
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header("Content-Disposition: attachment; filename=$filename");
			header('Content-type: text/xml; charset=' . get_settings('blog_charset'), true);
			$posts = query_posts('');
			$cats = implode(',', $cats);
			
			$postswithcats = $wpdb->get_col("SELECT DISTINCT(post_id) FROM ".$wpdb->post2cat." WHERE category_id IN ($cats)");
			$postcats = implode(',', $postswithcats);
			$posts = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID IN ($postcats) ORDER BY post_date_gmt ASC");
?>
<!-- generator="wordpress/<?php bloginfo_rss('version') ?>" created="<?php echo date('Y-m-d H:m'); ?>"-->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:wp="http://wordpress.org/export/1.0/"
>

<channel>
	<title><?php bloginfo_rss('name'); ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php bloginfo_rss('version'); ?></generator>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('rss2_head'); ?>
<?php 
			if ($posts) 
				{ foreach ($posts as $post) 
					{ start_wp(); ?>
<item>
<title><?php the_title_rss() ?></title>
<link><?php permalink_single_rss() ?></link>
<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
<dc:creator><?php the_author() ?></dc:creator>
<?php the_category_rss() ?>

<guid isPermaLink="false"><?php the_guid(); ?></guid>
<description></description>
<wp:excerpt><![CDATA[<?php echo $post->post_excerpt ?>]]></wp:excerpt>
<content:encoded><![CDATA[<?php echo $post->post_content ?>]]></content:encoded>
<wp:post_date><?php echo $post->post_date; ?></wp:post_date>
<wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
<wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
<wp:ping_status><?php echo $post->ping_status; ?></wp:ping_status>
<wp:post_name><?php echo $post->post_name; ?></wp:post_name>
<wp:status><?php echo $post->post_status; ?></wp:status>
<wp:post_parent><?php echo $post->post_parent; ?></wp:post_parent>
<wp:post_type><?php echo $post->post_type; ?></wp:post_type>
<?php
		$postmeta = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = $post->ID"); 
					if ( $postmeta ) 
						{
?>
<?php 
						foreach( $postmeta as $meta ) 
							{ 
?>	
<wp:postmeta>
<wp:meta_key><?php echo $meta->meta_key; ?></wp:meta_key>
<wp:meta_value><?Php echo $meta->meta_value; ?></wp:meta_value>
</wp:postmeta>
<?php 						} 
						} 
					$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID"); 
					if ( $comments ) { foreach ( $comments as $c ) 
						{ 
?>
<wp:comment>
<wp:comment_author><?php echo htmlent2numeric($c->comment_author); ?></wp:comment_author>
<wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
<wp:comment_author_url><?php echo $c->comment_author_url; ?></wp:comment_author_url>
<wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
<wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
<wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
<wp:comment_content><?php echo htmlent2numeric($c->comment_content); ?></wp:comment_content>
<wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
<wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
<wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
</wp:comment>
<?php 					} 
					} 
?>
	</item>
<?php 
				} 
			} 
?>
</channel>
</rss>
<?php
exit;
wp_redirect('admin.php');
		}
	}
	
	if(!function_exists('wpexport_init'))
	{
		function wpexport_init()
		{
			
			if($_POST['wpexportdownload'])
			{
				$catexport = $_POST['catexport'];
				export_wp($_POST['catexport']);
			}
		}
	}
	
	if(!function_exists('htmlent2numeric'))
	{
		function htmlent2numeric($string)
		{
			$conversion = array(
			'&amp;' => '&#38;',
			'&raquo;' => '&#187;',
			'&endash;' => '&#8211;',
			'&emdash;' => '&#8212;',
			'&quot;' => '&#34;',
			'&' => '&#38;'
			);
			
			foreach($conversion as $oldchar => $newchar)
				$string = str_replace($oldchar, $newchar, $string);
			return $string;
		}
	}
	if(!function_exists('export_cat_list'))
	{
		function export_cat_list()
		{
			global $wpdb;
			$cats = $wpdb->get_results("SELECT cat_ID, cat_name, category_nicename, category_description, category_parent, category_count
                        FROM $wpdb->categories
                        WHERE cat_ID > 0 
                        ORDER BY cat_name");
			return $cats;
		}
	}

	add_action('init', 'wpexport_init');
	add_action('admin_menu', 'wp_export_navitem');
}
?>