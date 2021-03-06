<?php

// Admin stuff for Similar Posts Plugin, Version 2.5b23

function similar_posts_option_menu() {
	add_options_page(__('Similar Posts Options', 'post_plugins'), __('Similar Posts', 'post_plugins'), 8, 'similar-posts', 'similar_posts_options_page');
}

add_action('admin_menu', 'similar_posts_option_menu', 1);

function similar_posts_for_feed_option_menu() {
	add_options_page(__('Similar Posts Feed Options', 'post_plugins'), __('Similar Posts Feed', 'post_plugins'), 8, 'similar-posts-feed', 'similar_posts_for_feed_options_page');
}

// this sneaky piece of work lets the similar posts feed menu appear and disappear
function juggle_similar_posts_menus() {
	if (isset($_POST['feed_active'])) {
		$active = ($_POST['feed_active'] === 'true');
	} else {
		$options = get_option('similar-posts');
		$active = ($options['feed_active'] === 'true');
	}
	if ($active) {
		add_action('admin_menu', 'similar_posts_for_feed_option_menu', 2);
	} else {
		remove_action('admin_menu', 'similar_posts_for_feed_option_menu');
	}
}

add_action('plugins_loaded', 'juggle_similar_posts_menus');

function similar_posts_options_page(){
	echo '<div class="wrap"><h2>';
	_e('Similar Posts ', 'post_plugins'); 
	echo '<a href="http://rmarsh.com/plugins/post-options/" style="font-size: 0.8em;">';
	_e('help and instructions'); 
	echo '</a></h2></div>';
	if (!SimilarPosts::check_post_plugin_library(__('<h1>Please install the <a href="http://downloads.wordpress.org/plugin/post-plugin-library.zip">Post Plugin Library</a> plugin.</h1>'))) return;
	$m = new admin_subpages();
	$m->add_subpage('General', 'general', 'similar_posts_general_options_subpage');
	$m->add_subpage('Output', 'output', 'similar_posts_output_options_subpage');
	$m->add_subpage('Filter', 'filter', 'similar_posts_filter_options_subpage');
	$m->add_subpage('Other', 'other', 'similar_posts_other_options_subpage');
	$m->add_subpage('Manage the Index', 'index', 'similar_posts_index_options_subpage');
	$m->add_subpage('Report a Bug', 'bug', 'similar_posts_bug_subpage');
	$m->add_subpage('Remove this Plugin', 'remove', 'similar_posts_remove_subpage');
	$m->display();
}

function similar_posts_general_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('limit', 'skip', 'show_private', 'show_pages', 'age', 'omit_current_post', 'match_cat', 'match_tags'));
		update_option('similar-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('General Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save General Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_limit($options['limit']); 
				ppl_display_skip($options['skip']); 
				ppl_display_show_private($options['show_private']); 
				ppl_display_show_pages($options['show_pages']); 
				ppl_display_age($options['age']);
				ppl_display_omit_current_post($options['omit_current_post']); 
				ppl_display_match_cat($options['match_cat']); 
				ppl_display_match_tags($options['match_tags']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save General Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_output_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('output_template', 'prefix', 'suffix', 'none_text', 'no_text', 'trim_before'));
		update_option('similar-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Output Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Output Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<tr>
			<td>
			<table>
			<?php 
				ppl_display_output_template($options['output_template']); 
				ppl_display_prefix($options['prefix']); 
				ppl_display_suffix($options['suffix']); 
				ppl_display_none_text($options['none_text']); 
				ppl_display_no_text($options['no_text']); 
				ppl_display_trim_before($options['trim_before']); 
			?>
			</table>
			</td>
			<td>
			<?php ppl_display_available_tags('similar-posts'); ?>
			</td></tr>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Output Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_filter_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('excluded_posts', 'included_posts', 'excluded_authors', 'included_authors', 'excluded_cats', 'included_cats', 'tag_str', 'custom'));
		update_option('similar-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Filter Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Filter Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_excluded_posts($options['excluded_posts']); 
				ppl_display_included_posts($options['included_posts']); 
				ppl_display_authors($options['excluded_authors'], $options['included_authors']); 
				ppl_display_cats($options['excluded_cats'], $options['included_cats']); 
				ppl_display_tag_str($options['tag_str']); 
				ppl_display_custom($options['custom']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Filter Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_other_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('content_filter', 'stripcodes', 'feed_active', 'crossmatch', 'num_terms', 'weight_title', 'weight_content', 'weight_tags'));
		$wcontent = $options['weight_content'] + 0.0001; 
		$wtitle = $options['weight_title'] + 0.0001;
		$wtags = $options['weight_tags'] + 0.0001;
		$wcombined = $wcontent + $wtitle + $wtags;
		$options['weight_content'] = $wcontent / $wcombined; 
		$options['weight_title'] = $wtitle / $wcombined; 
		$options['weight_tags'] = $wtags / $wcombined; 
		update_option('similar-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Other Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Other Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_weights($options); 
				ppl_display_num_terms($options['num_terms']); 
				//ppl_display_crossmatch($options['crossmatch']); 
				ppl_display_feed_active($options['feed_active']); 
				ppl_display_content_filter($options['content_filter']);
				ppl_display_stripcodes($options['stripcodes']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Other Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_index_options_subpage(){
	if (isset($_POST['reindex_all'])) {
		check_admin_referer('similar-posts-manage-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		$options = get_option('similar-posts');
		$options['utf8'] = $_POST['utf8'];
		if (!function_exists('mb_split')) {
			$options['utf8'] = 'false';
		}
		$options['use_stemmer'] = $_POST['use_stemmer'];
		$options['batch'] = ppl_check_cardinal($_POST['batch']);
		if ($options['batch'] === 0) $options['batch'] = 100;
		flush();
		$termcount = save_index_entries (($options['utf8']==='true'), ($options['use_stemmer']==='true'), $options['batch']);
		update_option('similar-posts', $options);
		//show a message
		printf(__('<div class="updated fade"><p>Indexed %d posts.</p></div>'), $termcount);
	} else {
		$options = get_option('similar-posts');
	}
	?>
    <div class="wrap"> 
		<?php 
		_e('<h2>Manage Index</h2>', 'post_plugins'); 
		_e('<p><strong>Similar Posts</strong> maintains a special index to help search for related posts. 
			The index is created when the plugin is activated and then kept up-to-date 
			automatically when posts are added, edited, or deleted.</p>
			<p>The two options that affect the index can be set below.</p>', 'post_plugins');
		_e('<p>If you are using a language other than english you may find that the plugin 
			mangles some characters since PHP is normally blind to multibyte characters. You 
			can force the plugin to interpret extended characters as UTF-8 at the expense 
			of a little speed. <em>This facility is only available if your 
			installation of PHP supports the mbstring functions.</em></p>', 'post_plugins');
		_e('<p>Some related word forms should really be counted together, e.g., "follow", 
			"follows", and "following". The plugin can use a <em>stemming</em> algorithm to
			reduce related forms to their root stem. It is worth experimenting to see if this
			improves the similarity of posts in your particular circumstances. Stemming algorithms are provided
			for english, german, spanish, french and italian but stemmers for other languages 
			can be created: see the help for instructions. <em>Stemming slows down the 
			indexing more than a little.</em></p>', 'post_plugins'); 
		_e('<p>The indexing routine processes posts in batches of 100 by default. If you run into
			problems with limited memory you can opt to make the batches smaller.</p>', 'post_plugins'); 
		_e('<p><strong>Note</strong>: The process of indexing may take a little while. On my
			modest machine 500 posts take between 5 seconds and 20 seconds (with stemming and
			utf-8 support). <strong>Don\'t worry if the screen fails to update until finished</strong>.</p>', 'post_plugins'); 
		?>
		<form method="post" action="">		
		<table class="optiontable form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Handle extended characters?', 'post_plugins') ?></th>
				<td>
					<select name="utf8" id="utf8" <?php if (!function_exists('mb_split')) echo 'disabled="true"'; ?> >
					<option <?php if($options['utf8'] == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
					<option <?php if($options['utf8'] == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
					</select>
				</td> 
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Use a stemming algorithm?', 'post_plugins') ?></th>
				<td>
					<select name="use_stemmer" id="use_stemmer">
					<option <?php if($options['use_stemmer'] == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
					<option <?php if($options['use_stemmer'] == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
					</select>
				</td> 
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Batch size:', 'post_plugins') ?></th>
				<td><input name="batch" type="text" id="batch" value="<?php echo $options['batch']; ?>" size="3" /></td>
			</tr>
		</table>
		<div class="submit">
		<input type="submit" name="reindex_all" value="<?php _e('Recreate Index', 'post_plugins') ?>" />
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-manage-update-options'); ?>
		</div>   
		</form>       
    </div>
	<?php
}


function similar_posts_bug_subpage(){
	ppl_bug_form('similar-posts'); 
}

function similar_posts_remove_subpage(){
	function eradicate() {
		global $wpdb, $table_prefix;
		delete_option('similar-posts');
		delete_option('similar-posts-feed');
		delete_option('widget_rrm_similar_posts');
		$table_name = $table_prefix . 'similar_posts';
		$wpdb->query("DROP TABLE `$table_name`");
	}
	ppl_plugin_eradicate_form('eradicate', str_replace('-admin', '', __FILE__)); 
}	

function similar_posts_for_feed_options_page(){
	echo '<div class="wrap"><h2>';
	_e('Similar Posts Feed ', 'post_plugins'); 
	echo '<a href="http://rmarsh.com/plugins/post-options/" style="font-size: 0.8em;">';
	_e('help and instructions'); 
	echo '</a></h2></div>';
	$m = new admin_subpages();
	$m->add_subpage('General', 'general', 'similar_posts_feed_general_options_subpage');
	$m->add_subpage('Output', 'output', 'similar_posts_feed_output_options_subpage');
	$m->add_subpage('Filter', 'filter', 'similar_posts_feed_filter_options_subpage');
	$m->add_subpage('Other', 'other', 'similar_posts_feed_other_options_subpage');
	$m->add_subpage('Report a Bug', 'bug', 'similar_posts_feed_bug_subpage');
	$m->add_subpage('Remove this Plugin', 'remove', 'similar_posts_feed_remove_subpage');
	$m->display();
}

function similar_posts_feed_general_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts-feed');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-feed-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('limit', 'skip', 'show_private', 'show_pages', 'age', 'omit_current_post', 'match_cat', 'match_tags'));
		update_option('similar-posts-feed', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('General Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save General Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_limit($options['limit']); 
				ppl_display_skip($options['skip']); 
				ppl_display_show_private($options['show_private']); 
				ppl_display_show_pages($options['show_pages']); 
				ppl_display_age($options['age']);
				ppl_display_omit_current_post($options['omit_current_post']); 
				ppl_display_match_cat($options['match_cat']); 
				ppl_display_match_tags($options['match_tags']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save General Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-feed-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_feed_output_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts-feed');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-feed-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('output_template', 'prefix', 'suffix', 'none_text', 'no_text', 'trim_before'));
		update_option('similar-posts-feed', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Output Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Output Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<tr>
			<td>
			<table>
			<?php 
				ppl_display_output_template($options['output_template']); 
				ppl_display_prefix($options['prefix']); 
				ppl_display_suffix($options['suffix']); 
				ppl_display_none_text($options['none_text']); 
				ppl_display_no_text($options['no_text']); 
				ppl_display_trim_before($options['trim_before']); 
			?>
			</table>
			</td>
			<td>
			<?php ppl_display_available_tags('similar-posts'); ?>
			</td></tr>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Output Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-feed-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_feed_filter_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts-feed');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-feed-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('excluded_posts', 'included_posts', 'excluded_authors', 'included_authors', 'excluded_cats', 'included_cats', 'tag_str', 'custom'));
		update_option('similar-posts-feed', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Filter Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Filter Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_excluded_posts($options['excluded_posts']); 
				ppl_display_included_posts($options['included_posts']); 
				ppl_display_authors($options['excluded_authors'], $options['included_authors']); 
				ppl_display_cats($options['excluded_cats'], $options['included_cats']); 
				ppl_display_tag_str($options['tag_str']); 
				ppl_display_custom($options['custom']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Filter Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-feed-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_feed_other_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('similar-posts-feed');
	if (isset($_POST['update_options'])) {
		check_admin_referer('similar-posts-feed-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('stripcodes', 'crossmatch', 'num_terms', 'weight_title', 'weight_content', 'weight_tags'));
		$wcontent = $options['weight_content'] + 0.0001; 
		$wtitle = $options['weight_title'] + 0.0001;
		$wtags = $options['weight_tags'] + 0.0001;
		$wcombined = $wcontent + $wtitle + $wtags;
		$options['weight_content'] = $wcontent / $wcombined; 
		$options['weight_title'] = $wtitle / $wcombined; 
		$options['weight_tags'] = $wtags / $wcombined; 
		update_option('similar-posts-feed', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'post_plugins') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Other Settings', 'post_plugins'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Other Settings', 'post_plugins') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_weights($options); 
				ppl_display_num_terms($options['num_terms']); 
				//ppl_display_crossmatch($options['crossmatch']); 
				ppl_display_stripcodes($options['stripcodes']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Other Settings', 'post_plugins') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('similar-posts-feed-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function similar_posts_feed_bug_subpage(){
	ppl_bug_form('similar-posts-feed'); 
}

function similar_posts_feed_remove_subpage(){
	function eradicate() {
		global $wpdb, $table_prefix;
		delete_option('similar-posts');
		delete_option('similar-posts-feed');
		$table_name = $table_prefix . 'similar_posts_feed';
		$wpdb->query("DROP TABLE `$table_name`");
	}
	ppl_plugin_eradicate_form('eradicate', str_replace('-admin', '', __FILE__)); 
}	

// sets up the index for the blog
function save_index_entries ($utf8=false, $use_stemmer=false, $batch=100) {
	global $wpdb, $table_prefix;
	// empty the index table
	$table_name = $table_prefix.'similar_posts';
	//$start_time = ppl_microtime();
	$wpdb->query("TRUNCATE `$table_name`");
	$termcount = 0;
	$start = 0;
	// in batches to conserve memory
	while ($posts = $wpdb->get_results("SELECT `ID`, `post_title`, `post_content` FROM $wpdb->posts LIMIT $start, $batch", ARRAY_A)) {
		reset($posts);
		while (list($dummy, $post) = each($posts)) {
			$content = sp_get_post_terms($post['post_content'], $utf, $use_stemmer);
			$title = sp_get_title_terms($post['post_title'], $utf, $use_stemmer);
			$tags = sp_get_tag_terms($postID, $utf);
			$postID = $post['ID'];
			$wpdb->query("INSERT INTO `$table_name` (pID, content, title, tags) VALUES ($postID, \"$content\", \"$title\", \"$tags\")");
			$termcount = $termcount + 1;
		}
		$start += $batch;
		set_time_limit(30);
	}
	//echo (ppl_microtime() - $start_time);
	unset($posts);
	return $termcount;
}

// this function gets called when the plugin is installed to set up the index and default options
function similar_posts_install() {
   	global $wpdb, $table_prefix;
	
	$table_name = $table_prefix . 'similar_posts';
	$errorlevel = error_reporting(0);
	$suppress = $wpdb->hide_errors();
	$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`pID` bigint( 20 ) unsigned NOT NULL ,
			`content` longtext NOT NULL ,
			`title` text NOT NULL ,
			`tags` text NOT NULL ,
			FULLTEXT KEY `title` ( `title` ) ,
			FULLTEXT KEY `content` ( `content` ) ,
			FULLTEXT KEY `tags` ( `tags` )
			) ENGINE = MyISAM CHARSET = utf8;";
	$wpdb->query($sql);
	// MySQL before 4.1 doesn't recognise the character set properly, so if there's an error we can try without
	if ($wpdb->last_error !== '') {
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
				`pID` bigint( 20 ) unsigned NOT NULL ,
				`content` longtext NOT NULL ,
				`title` text NOT NULL ,
				`tags` text NOT NULL ,
				FULLTEXT KEY `title` ( `title` ) ,
				FULLTEXT KEY `content` ( `content` ) ,
				FULLTEXT KEY `tags` ( `tags` )
				) ENGINE = MyISAM;";
		$wpdb->query($sql);
	}
	$options = (array) get_option('similar-posts-feed');
	// check each of the option values and, if empty, assign a default (doing it this long way
	// lets us add new options in later versions)
	if (!isset($options['limit'])) $options['limit'] = 5;
	if (!isset($options['skip'])) $options['skip'] = 0;
	if (!isset($options['age'])) {$options['age']['direction'] = 'none'; $options['age']['length'] = '0'; $options['age']['duration'] = 'month';}
	if (!isset($options['trim_before'])) $options['trim_before'] = '';
	if (!isset($options['omit_current_post'])) $options['omit_current_post'] = 'true';
	if (!isset($options['show_private'])) $options['show_private'] = 'false';
	if (!isset($options['show_pages'])) $options['show_pages'] = 'false';
	// show_static is now show_pages
	if ( isset($options['show_static'])) {$options['show_pages'] = $options['show_static']; unset($options['show_static']);};
	if (!isset($options['none_text'])) $options['none_text'] = __('None Found', 'post_plugins');
	if (!isset($options['no_text'])) $options['no_text'] = 'false';
	if (!isset($options['tag_str'])) $options['tag_str'] = '';
	if (!isset($options['excluded_cats'])) $options['excluded_cats'] = '';
	if ($options['excluded_cats'] === '9999') $options['excluded_cats'] = '';
	if (!isset($options['included_cats'])) $options['included_cats'] = '';
	if ($options['included_cats'] === '9999') $options['included_cats'] = '';
	if (!isset($options['excluded_authors'])) $options['excluded_authors'] = '';
	if ($options['excluded_authors'] === '9999') $options['excluded_authors'] = '';
	if (!isset($options['included_authors'])) $options['included_authors'] = '';
	if ($options['included_authors'] === '9999') $options['included_authors'] = '';
	if (!isset($options['included_posts'])) $options['included_posts'] = '';
	if (!isset($options['excluded_posts'])) $options['excluded_posts'] = '';
	if ($options['excluded_posts'] === '9999') $options['excluded_posts'] = '';
	if (!isset($options['stripcodes'])) $options['stripcodes'] = array(array());
	if (!isset($options['prefix'])) $options['prefix'] = 'Similar Posts:<ul>';
	if (!isset($options['suffix'])) $options['suffix'] = '</ul>';
	if (!isset($options['output_template'])) $options['output_template'] = '<li>{link}</li>';
	if (!isset($options['match_cat'])) $options['match_cat'] = 'false';
	if (!isset($options['match_tags'])) $options['match_tags'] = 'false';
	if (!isset($options['custom'])) {$options['custom']['key'] = ''; $options['custom']['op'] = '='; $options['custom']['value'] = '';}
	if (!isset($options['weight_content'])) $options['weight_content'] = 0.9;
	if (!isset($options['weight_title'])) $options['weight_title'] = 0.1;
	if (!isset($options['weight_tags'])) $options['weight_tags'] = 0.0;	
	if (!isset($options['num_terms'])) $options['num_terms'] = 20;
	update_option('similar-posts-feed', $options);
	
	$options = (array) get_option('similar-posts');
	// check each of the option values and, if empty, assign a default (doing it this long way
	// lets us add new options in later versions)
	if (!isset($options['feed_active'])) $options['feed_active'] = 'false';
	if (!isset($options['limit'])) $options['limit'] = 5;
	if (!isset($options['skip'])) $options['skip'] = 0;
	if (!isset($options['age'])) {$options['age']['direction'] = 'none'; $options['age']['length'] = '0'; $options['age']['duration'] = 'month';}
	if (!isset($options['trim_before'])) $options['trim_before'] = '';
	if (!isset($options['omit_current_post'])) $options['omit_current_post'] = 'true';
	if (!isset($options['show_private'])) $options['show_private'] = 'false';
	if (!isset($options['show_pages'])) $options['show_pages'] = 'false';
	// show_static is now show_pages
	if ( isset($options['show_static'])) {$options['show_pages'] = $options['show_static']; unset($options['show_static']);};
	if (!isset($options['none_text'])) $options['none_text'] = __('None Found', 'post_plugins');
	if (!isset($options['no_text'])) $options['no_text'] = 'false';
	if (!isset($options['tag_str'])) $options['tag_str'] = '';
	if (!isset($options['excluded_cats'])) $options['excluded_cats'] = '';
	if ($options['excluded_cats'] === '9999') $options['excluded_cats'] = '';
	if (!isset($options['included_cats'])) $options['included_cats'] = '';
	if ($options['included_cats'] === '9999') $options['included_cats'] = '';
	if (!isset($options['excluded_authors'])) $options['excluded_authors'] = '';
	if ($options['excluded_authors'] === '9999') $options['excluded_authors'] = '';
	if (!isset($options['included_authors'])) $options['included_authors'] = '';
	if ($options['included_authors'] === '9999') $options['included_authors'] = '';
	if (!isset($options['included_posts'])) $options['included_posts'] = '';
	if (!isset($options['excluded_posts'])) $options['excluded_posts'] = '';
	if ($options['excluded_posts'] === '9999') $options['excluded_posts'] = '';
	if (!isset($options['stripcodes'])) $options['stripcodes'] = array(array());
	if (!isset($options['prefix'])) $options['prefix'] = '<ul>';
	if (!isset($options['suffix'])) $options['suffix'] = '</ul>';
	if (!isset($options['output_template'])) $options['output_template'] = '<li>{link}</li>';
	if (!isset($options['match_cat'])) $options['match_cat'] = 'false';
	if (!isset($options['match_tags'])) $options['match_tags'] = 'false';
	if (!isset($options['content_filter'])) $options['content_filter'] = 'false';
	if (!isset($options['custom'])) {$options['custom']['key'] = ''; $options['custom']['op'] = '='; $options['custom']['value'] = '';}
	if (!isset($options['weight_content'])) $options['weight_content'] = 0.9;
	if (!isset($options['weight_title'])) $options['weight_title'] = 0.1;
	if (!isset($options['weight_tags'])) $options['weight_tags'] = 0.0;	
	if (!isset($options['num_terms'])) $options['num_terms'] = 20;
	if (!isset($options['utf8'])) $options['utf8'] = 'false';
	if (!function_exists('mb_internal_encoding')) $options['utf8'] = 'false';
	if (!isset($options['use_stemmer'])) $options['use_stemmer'] = 'false';
	if (!isset($options['batch'])) $options['batch'] = '100';
	
	update_option('similar-posts', $options);

 	// initial creation of the index, if the table is empty
	$num_index_posts = $wpdb->get_var("SELECT COUNT(*) FROM `$table_name`");
	if ($num_index_posts == 0) save_index_entries (($options['utf8'] === 'true'), false);	

	// deactivate legacy Similar Posts Feed if present
	$current = get_option('active_plugins');
	if (in_array('Similar_Posts_Feed/similar-posts-feed.php', $current)) {
		array_splice($current, array_search('Similar_Posts_Feed/similar-posts-feed.php', $current), 1); 
		update_option('active_plugins', $current);	
	}
	unset($current);
	
 	// clear legacy custom fields
	$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = 'similarterms'");
	
	// clear legacy index
	$indices = $wpdb->get_results("SHOW INDEX FROM $wpdb->posts", ARRAY_A);
	foreach ($indices as $index) {
		if ($index['Key_name'] === 'post_similar') {
			$wpdb->query("ALTER TABLE $wpdb->posts DROP INDEX post_similar");
			break;
		}	
	}
	
	$wpdb->show_errors($suppress);
	error_reporting($errorlevel);
}

add_action('activate_'.str_replace('-admin', '', plugin_basename(__FILE__)), 'similar_posts_install');

?>