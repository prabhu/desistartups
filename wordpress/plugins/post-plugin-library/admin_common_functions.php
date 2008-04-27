<?php

/*

	Library for the Recent Posts, Random Posts, Recent Comments, and Similar Posts plugins
	-- provides the admin routines which the plugins share

*/

define('ACF_LIBRARY', true);

function ppl_options_from_post($options, $args) {
	foreach ($args as $arg) {
		switch ($arg) {
		case 'limit':
		case 'skip':
		    $options[$arg] = ppl_check_cardinal($_POST[$arg]);
			break;
		case 'excluded_cats':
		case 'included_cats':
			if (isset($_POST[$arg])) {	
				// get the subcategories too
				if (function_exists('get_term_children')) {
					$catarray = $_POST[$arg];
					foreach ($catarray as $cat) {
						$catarray = array_merge($catarray, get_term_children($cat, 'category'));
					}
					$_POST[$arg] = array_unique($catarray);
				}
				$options[$arg] = implode(',', $_POST[$arg]);
			} else {
				$options[$arg] = '';
			}	
			break;
		case 'excluded_authors':
		case 'included_authors':
			if (isset($_POST[$arg])) {
				$options[$arg] = implode(',', $_POST[$arg]);
			} else {
				$options[$arg] = '';
			}	
			break;
		case 'excluded_posts':
		case 'included_posts':
			$check = explode(',', rtrim($_POST[$arg]));
			$ids = array();
			foreach ($check as $id) {
				$id = ppl_check_cardinal($id);
				if ($id !== 0) $ids[] = $id;
			}
			$options[$arg] = implode(',', array_unique($ids));
			break;
		case 'stripcodes':
			$st = explode("\n", trim($_POST['starttags']));
			$se = explode("\n", trim($_POST['endtags']));
			if (count($st) != count($se)) {
				$options['stripcodes'] = array(array());
			} else {
				$num = count($st);
				for ($i = 0; $i < $num; $i++) {
					$options['stripcodes'][$i]['start'] = $st[$i];
					$options['stripcodes'][$i]['end'] = $se[$i];
				}
			}
			break;
		case 'age':
			$options['age']['direction'] = $_POST['age-direction'];
			$options['age']['length'] = ppl_check_cardinal($_POST['age-length']);
			$options['age']['duration'] = $_POST['age-duration'];
			break;
		case 'custom':
			$options['custom']['key'] = $_POST['custom-key'];
			$options['custom']['op'] = $_POST['custom-op'];
			$options['custom']['value'] = $_POST['custom-value'];
			break;
		case 'num_terms':
			$options['num_terms'] = $_POST['num_terms'];
			if ($options['num_terms'] < 1) $options['num_terms'] = 20;
			break;
		default:
			$options[$arg] = $_POST[$arg];
		}
	}
	return $options;
}

function ppl_check_cardinal($string) {
	$value = intval($string);
	return ($value > 0) ? $value : 0;
}

function ppl_display_available_tags($plugin_name) {
	?>
		<h3><?php _e('Available Tags', 'post_plugins'); ?></h3>
		<ul style="list-style-type: none;">
		<li title="">{author}</li>
		<li title="">{categoryid}</li>
		<li title="">{categorylinks}</li>
		<li title="">{categorynames}</li>
		<li title="">{commentcount}</li>
		<li title="">{custom}</li>
		<li title="">{date}</li>
		<li title="">{dateedited}</li>
		<li title="">{excerpt}</li>
		<li title="">{fullpost}</li>
		<li title="">{gravatar}</li>
		<li title="">{if}</li>
		<li title="">{image}</li>
		<li title="">{link}</li>
		<li title="">{php}</li>
		<li title="">{postid}</li>
		<li title="">{postviews}</li>
		<?php if ($plugin_name === 'similar-posts') { ?>
			<li title="">{score}</li>
		<?php } ?>
		<li title="">{snippet}</li>
		<li title="">{tags}</li>
		<li title="">{taglinks}</li>
		<li title="">{title}</li>
		<li title="">{time}</li>
		<li title="">{timeedited}</li>
		<li title="">{totalpages}</li>
		<li title="">{totalposts}</li>
		<li title="">{url}</li>
		</ul>
	<?php
}

function ppl_display_available_comment_tags() {
	?>
		<ul style="list-style-type: none;">
		<li title="">{commentexcerpt}</li>
		<li title="">{commentsnippet}</li>
		<li title="">{commentsnippetword}</li>
		<li title="">{commentdate}</li>
		<li title="">{commenttime}</li>
		<li title="">{commentdategmt}</li>
		<li title="">{commenttimegmt}</li>
		<li title="">{commenter}</li>
		<li title="">{commenterip}</li>
		<li title="">{commenterurl}</li>
		<li title="">{commenterlink}</li>
		<li title="">{commenturl}</li>
		<li title="">{commentlink}</li>
		<li title="">{commentlink2}</li>
		</ul>
	<?php
}

/*

	inserts a form button to submit a bug report to my web site
	
*/
function get_plugin_version($prefix) {
	$plugin_version = str_replace('-', '_', $prefix) . '_version';
	global $$plugin_version;
	return ${$plugin_version};
}

function ppl_bug_form($options_key) {
	global $wp_version;
	$template_name = basename(get_bloginfo('template_url'));
	$options = get_option($options_key);	
	$options['mbstring'] = intval(function_exists('mb_internal_encoding'));
	$woptions = get_option('widget_rrm_'.str_replace('-', '_', $options_key)); 
	?>
	<div class="wrap">
	<h2>Report a Bug</h2>
	<form method="post" action="http://rmarsh.com/report-a-bug/">
	<p><?php _e('This option takes you to my site where you can inform me of any issues 
	you are having with this plugin. It also passes along useful debugging information such as 
	which versions of WordPress, PHP, and MySQL you are using, as well as the current 
	plugin settings.', 'post_plugins'); ?></p>
	<div class="submit"><input type="submit" name="report_bug" value="<?php _e('File Report', 'post_plugins') ?>"  /></div>
	<input type="hidden" name="plugin" value="<?php echo $options_key; ?>" />
	<input type="hidden" name="plugin_version" value="<?php echo get_plugin_version($options_key); ?>" />	
	<input type="hidden" name="wp_version" value="<?php echo $wp_version; ?>" />
	<input type="hidden" name="php_version" value="<?php echo PHP_VERSION; ?>" />
	<input type="hidden" name="mysql_version" value="<?php echo mysql_get_client_info(); ?>" />
	<input type="hidden" name="wp_language" value='<?php echo WPLANG; ?>' />
	<input type="hidden" name="template" value='<?php echo $template_name; ?>' />
	<input type="hidden" name="options_set" value='<?php echo serialize($options); ?>' />
	<input type="hidden" name="widget_options_set" value='<?php echo serialize($woptions); ?>' />
	</form>
	</div>
	<?php
}

/*

	inserts a form button to completely remove the plugin and all its options etc.

*/
function ppl_plugin_eradicate_form($eradicate_action, $plugin_file) {
	if (isset($_POST['eradicate-plugin'])) {
		check_admin_referer('eradicate-plugin'); 
		if (ppl_confirm_eradicate()) {
			if (defined('POC_CACHE_4')) poc_cache_flush();
			$eradicate_action();
			ppl_deactivate_plugin($plugin_file);
			echo '<div class="updated fade"><p>' . __('The plugin and all its settings have been completely removed', 'post_plugins') . '</p></div>';
			exit;
		} 
	}
	?>
	<div class="wrap">
	<h2>Remove this Plugin</h2>
	<form method="post" action="">
	<p><?php _e('Deactivating a plugin from the Plugins page usually leaves all the plugin\'s
	settings intact. Often this is the desired behaviour as you can then choose to reactivate the plugin 
	and all your settings will still be in place. If, however, you want to remove this plugin 
	completely, along with all its settings and tables, you can do so by pressing the button below.', 'post_plugins'); ?></p>
	<div class="submit">
	<p><label for="eradicate-check"><input type="checkbox" name="eradicate-check" value="yes" /> check this box to confirm your intention</label></p>	
	<input type="submit" name="eradicate-plugin" id="eradicate-plugin" value="<?php _e('Remove Plugin', 'post_plugins') ?>"  />
	</div>
	<?php if (function_exists('wp_nonce_field')) wp_nonce_field('eradicate-plugin'); ?>
	</form>
	</div>
	<?php

}

function ppl_confirm_eradicate() {
 return (isset($_POST['eradicate-check']) && 'yes'===$_POST['eradicate-check']);
}

function ppl_deactivate_plugin($plugin_file) {
	$current = get_option('active_plugins');
	$plugin_file = substr($plugin_file, strlen(ABSPATH.PLUGINDIR)+1);
	$plugin_file = str_replace('\\', '/', $plugin_file);
	if (in_array($plugin_file, $current)) {
		array_splice($current, array_search($plugin_file, $current), 1); 
		update_option('active_plugins', $current);
	}
}


/*

	For the display of the option pages

*/

function ppl_display_limit($limit) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Number of posts to show:', 'post_plugins') ?></th>
		<td><input name="limit" type="text" id="limit" value="<?php echo $limit; ?>" size="2" /></td>
	</tr>
	<?php
}

function ppl_display_skip($skip) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Number of posts to skip:', 'post_plugins') ?></th>
		<td><input name="skip" type="text" id="skip" value="<?php echo $skip; ?>" size="2" /></td>
	</tr>
	<?php
}

function ppl_display_omit_current_post($omit_current_post) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Omit the current post?', 'post_plugins') ?></th>
		<td>
		<select name="omit_current_post" id="omit_current_post" >
		<option <?php if($omit_current_post == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
		<option <?php if($omit_current_post == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
		</select> 
		</td>
	</tr>
	<?php
}

function ppl_display_show_private($show_private) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Show password-protected posts?', 'post_plugins') ?></th>
		<td>
		<select name="show_private" id="show_private">
		<option <?php if($show_private == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
		<option <?php if($show_private == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
		</select> 
		</td>
	</tr>
	<?php
}

function ppl_display_show_pages($show_pages) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Show static pages?', 'post_plugins') ?></th>
		<td>
			<select name="show_pages" id="show_pages">
			<option <?php if($show_pages == 'false') { echo 'selected="selected"'; } ?> value="false">No pages, just posts</option>
			<option <?php if($show_pages == 'true') { echo 'selected="selected"'; } ?> value="true">Both pages and posts</option>
			<option <?php if($show_pages == 'but') { echo 'selected="selected"'; } ?> value="but">Pages but no posts</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_match_cat($match_cat) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Match the current post\'s category?', 'post_plugins') ?></th>
		<td>
			<select name="match_cat" id="match_cat">
			<option <?php if($match_cat == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($match_cat == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_match_tags($match_tags) {
	global $wp_version;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Match the current post\'s tags?', 'post_plugins') ?></th>
		<td>
			<select name="match_tags" id="match_tags" <?php if ($wp_version < 2.3) echo 'disabled="true"'; ?> >
			<option <?php if($match_tags == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($match_tags == 'any') { echo 'selected="selected"'; } ?> value="any">Any tag</option>
			<option <?php if($match_tags == 'all') { echo 'selected="selected"'; } ?> value="all">Every tag</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_none_text($none_text) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Default display if no matches:', 'post_plugins') ?></th>
		<td><input name="none_text" type="text" id="none_text" value="<?php echo htmlspecialchars(stripslashes($none_text)); ?>" size="40" /></td>
	</tr>
	<?php
}

function ppl_display_no_text($no_text) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Show nothing if no matches?', 'post_plugins') ?></th>
		<td>
			<select name="no_text" id="no_text">
			<option <?php if($no_text == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($no_text == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_prefix($prefix) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Text and codes before the list:', 'post_plugins') ?></th>
		<td><input name="prefix" type="text" id="prefix" value="<?php echo htmlspecialchars(stripslashes($prefix)); ?>" size="40" /></td>
	</tr>
	<?php
}

function ppl_display_suffix($suffix) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Text and codes after the list:', 'post_plugins') ?></th>
		<td><input name="suffix" type="text" id="suffix" value="<?php echo htmlspecialchars(stripslashes($suffix)); ?>" size="40" /></td>
	</tr>
	<?php
}

function ppl_display_output_template($output_template) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Output template:', 'post_plugins') ?></th>
		<td><textarea name="output_template" id="output_template" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes($output_template)); ?></textarea></td>
	</tr>
	<?php
}

function ppl_display_trim_before($trim_before) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Text to trim at start:', 'post_plugins') ?></th>
		<td><input name="trim_before" type="text" id="trim_before" value="<?php echo $trim_before; ?>" size="40" /></td>
	</tr>
	<?php
}

function ppl_display_tag_str($tag_str) {
	global $wp_version;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Match posts with tags:<br />(a,b matches posts with either tag, a+b only matches posts with both tags)', 'post_plugins') ?></th>
		<td><input name="tag_str" type="text" id="tag_str" value="<?php echo $tag_str; ?>" <?php if ($wp_version < 2.3) echo 'disabled="true"'; ?> size="40" /></td>
	</tr>
	<?php
}

function ppl_display_excluded_posts($excluded_posts) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Posts to exclude:', 'post_plugins') ?></th>
		<td><input name="excluded_posts" type="text" id="excluded_posts" value="<?php echo $excluded_posts; ?>" size="40" /> <?php _e('comma-separated IDs', 'post_plugins'); ?></td>
	</tr>
	<?php
}

function ppl_display_included_posts($included_posts) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Posts to include:', 'post_plugins') ?></th>
		<td><input name="included_posts" type="text" id="included_posts" value="<?php echo $included_posts; ?>" size="40" /> <?php _e('comma-separated IDs', 'post_plugins'); ?></td>
	</tr>
	<?php
}

function ppl_display_authors($excluded_authors, $included_authors) {
	global $wpdb;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Authors to exclude/include:', 'post_plugins') ?></th>
		<td>
			<table border="1">	
			<?php 
				$users = $wpdb->get_results("SELECT ID, user_login FROM $wpdb->users ORDER BY user_login");
				if ($users) {
					$excluded = explode(',', $excluded_authors);
					$included = explode(',', $included_authors);
					echo "\n\t<tr valign=\"top\"><td style=\"border-bottom-width: 0px;\" ><strong>Author</strong></td><td style=\"border-bottom-width: 0px;\">Exclude</td><td style=\"border-bottom-width: 0px;\">Include</td></tr>";
					foreach ($users as $user) {
						if (false === in_array($user->ID, $excluded)) {
							$ex_ischecked = '';
						} else {
							$ex_ischecked = 'checked';
						}
						if (false === in_array($user->ID, $included)) {
							$in_ischecked = '';
						} else {
							$in_ischecked = 'checked';
						}
						echo "\n\t<tr valign=\"top\"><td style=\"border-bottom-width: 0px;\">$user->user_login</td><td style=\"border-bottom-width: 0px;\"><input type=\"checkbox\" name=\"excluded_authors[]\" value=\"$user->ID\" $ex_ischecked /></td><td style=\"border-bottom-width: 0px;\"><input type=\"checkbox\" name=\"included_authors[]\" value=\"$user->ID\" $in_ischecked /></td></tr>";
					}
				}	
			?>
			</table>
		</td> 
	</tr>
	<?php
}

function ppl_display_cats($excluded_cats, $included_cats) {
	global $wpdb;
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Categories to exclude/include:', 'post_plugins') ?></th>
		<td>
			<table border="1">	
			<?php 
				if (function_exists("get_categories")) {
					$categories = get_categories('&hide_empty=1');
				} else {
					$categories = $wpdb->get_results("SELECT * FROM $wpdb->categories WHERE category_count <> 0 ORDER BY cat_name");
				}
				if ($categories) {
					echo "\n\t<tr valign=\"top\"><td style=\"border-bottom-width: 0px;\"><strong>Category</strong></td><td style=\"border-bottom-width: 0px;\">Exclude</td><td style=\"border-bottom-width: 0px;\">Include</td></tr>";
					$excluded = explode(',', $excluded_cats);
					$included = explode(',', $included_cats);
					$level = 0;
					$cats_added = array();
					$last_parent = 0;
					$cat_parent = 0;
					foreach ($categories as $category) {
						$category->cat_name = wp_specialchars($category->cat_name);
						if (false === in_array($category->cat_ID, $excluded)) {
							$ex_ischecked = '';
						} else {
							$ex_ischecked = 'checked';
						}
						if (false === in_array($category->cat_ID, $included)) {
							$in_ischecked = '';
						} else {
							$in_ischecked = 'checked';
						}
						$last_parent = $cat_parent;
						$cat_parent = $category->category_parent;
						if ($cat_parent == 0) {
							$level = 0;
						} elseif ($last_parent != $cat_parent) {
							if (in_array($cat_parent, $cats_added)) {
								$level = $level - 1;
							} else {
								$level = $level + 1;
							}
							$cats_added[] = $cat_parent;
						}
						$pad = str_repeat('&nbsp;', 3*$level);
						echo "\n\t<tr valign=\"top\"><td style=\"border-bottom-width: 0px;\">$pad$category->cat_name</td><td style=\"border-bottom-width: 0px;\"><input type=\"checkbox\" name=\"excluded_cats[]\" value=\"$category->cat_ID\" $ex_ischecked /></td><td style=\"border-bottom-width: 0px;\"><input type=\"checkbox\" name=\"included_cats[]\" value=\"$category->cat_ID\" $in_ischecked /></td></tr>";
					}
				}
			?>
			</table>
		</td> 
	</tr>
	<?php
}

function ppl_display_stripcodes($stripcodes) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Other plugins\' tags to remove from snippet:', 'post_plugins') ?></th>
		<td>
			<table>	
			<tr><td style="border-bottom-width: 0"><?php _e('opening', 'post_plugins') ?></td><td style="border-bottom-width: 0"><?php _e('closing', 'post_plugins') ?></td></tr>
			<tr valign="top"><td style="border-bottom-width: 0"><textarea name="starttags" id="starttags" rows="4" cols="10"><?php foreach ($stripcodes as $tag) echo htmlspecialchars(stripslashes($tag['start']))."\n"; ?></textarea></td><td style="border-bottom-width: 0"><textarea name="endtags" id="endtags" rows="4" cols="10"><?php foreach ($stripcodes as $tag) echo htmlspecialchars(stripslashes($tag['end']))."\n"; ?></textarea></td></tr>
			</table>
		</td> 
	</tr>
	<?php
}

function ppl_display_age($age) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Ignore posts :', 'post_plugins') ?></th>
		<td>
			<table><tr>
			<td style="border-bottom-width: 0">
				<select name="age-direction" id="age-direction">
				<option <?php if($age['direction'] == 'before') { echo 'selected="selected"'; } ?> value="before">less than</option>
				<option <?php if($age['direction'] == 'after') { echo 'selected="selected"'; } ?> value="after">more than</option>
				<option <?php if($age['direction'] == 'none') { echo 'selected="selected"'; } ?> value="none">-----</option>
				</select>
			</td>
			<td style="border-bottom-width: 0"><input name="age-length" type="text" id="age-length" value="<?php echo $age['length']; ?>" size="4" /></td>
			<td style="border-bottom-width: 0">
				<select name="age-duration" id="age-duration">
				<option <?php if($age['duration'] == 'day') { echo 'selected="selected"'; } ?> value="day">day(s)</option>
				<option <?php if($age['duration'] == 'month') { echo 'selected="selected"'; } ?> value="month">month(s)</option>
				<option <?php if($age['duration'] == 'year') { echo 'selected="selected"'; } ?> value="year">year(s)</option>
				</select>
				old
			</td>
			</tr></table>
		</td>
	</tr>
	<?php
}

function ppl_display_custom($custom) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Match posts by custom field:', 'post_plugins') ?></th>
		<td>
			<table>
			<tr><td style="border-bottom-width: 0">Field Name</td><td style="border-bottom-width: 0"></td><td style="border-bottom-width: 0">Field Value</td></tr>
			<tr>
			<td style="border-bottom-width: 0"><input name="custom-key" type="text" id="custom-key" value="<?php echo $custom['key']; ?>" size="20" /></td>
			<td style="border-bottom-width: 0">
				<select name="custom-op" id="custom-op">
				<option <?php if($custom['op'] == '=') { echo 'selected="selected"'; } ?> value="=">=</option>
				<option <?php if($custom['op'] == '!=') { echo 'selected="selected"'; } ?> value="!=">!=</option>
				<option <?php if($custom['op'] == '>') { echo 'selected="selected"'; } ?> value=">">></option>
				<option <?php if($custom['op'] == '>=') { echo 'selected="selected"'; } ?> value=">=">>=</option>
				<option <?php if($custom['op'] == '<') { echo 'selected="selected"'; } ?> value="<"><</option>
				<option <?php if($custom['op'] == '<=') { echo 'selected="selected"'; } ?> value="<="><=</option>
				<option <?php if($custom['op'] == 'LIKE') { echo 'selected="selected"'; } ?> value="LIKE">LIKE</option>
				<option <?php if($custom['op'] == 'NOT LIKE') { echo 'selected="selected"'; } ?> value="NOT LIKE">NOT LIKE</option>
				<option <?php if($custom['op'] == 'REGEXP') { echo 'selected="selected"'; } ?> value="REGEXP">REGEXP</option>
				<option <?php if($custom['op'] == 'EXISTS') { echo 'selected="selected"'; } ?> value="EXISTS">EXISTS</option>			
				</select>
			</td>
			<td style="border-bottom-width: 0"><input name="custom-value" type="text" id="custom-value" value="<?php echo $custom['value']; ?>" size="20" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<?php
}

function ppl_display_content_filter($content_filter) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Replace special tags in content?', 'post_plugins') ?></th>
		<td>
			<select name="content_filter" id="content_filter">
			<option <?php if($content_filter == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($content_filter == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

// now for similar_posts

function ppl_display_num_terms($num_terms) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Maximum number of words to use for match:', 'post_plugins') ?></th>
		<td><input name="num_terms" type="text" id="num_terms" value="<?php echo $num_terms; ?>" size="3" /></td>
	</tr>
	<?php
}

function ppl_display_crossmatch($crossmatch) {
	?>
	<tr valign="top">
		<th scope="row" title=""><?php _e('Crossmatch terms?', 'post_plugins') ?></th>
		<td>
			<select name="crossmatch" id="crossmatch">
			<option <?php if($crossmatch == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($crossmatch == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_weights($options) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Relative importance of:', 'post_plugins') ?></th>
		<td>
			<table><tr>
			<td style="border-bottom-width: 0"><label for="weight_content" style="float:left;">content:  </label><input name="weight_content" type="text" id="weight_content" value="<?php echo round(100 * $options['weight_content']); ?>" size="3" /> % </td>
			<td style="border-bottom-width: 0"><label for="weight_title" style="float:left;">title:  </label><input name="weight_title" type="text" id="weight_title" value="<?php echo round(100 * $options['weight_title']); ?>" size="3" /> % </td>
			<td style="border-bottom-width: 0"><label for="weight_tags" style="float:left;">tags:  </label><input name="weight_tags" type="text" id="weight_tags" value="<?php echo round(100 * $options['weight_tags']); ?>" size="3" /> % ( adds up to 100% )</td>
			</tr></table>
		</td>
	</tr>
	<?php
}

function ppl_display_feed_active($feed_active) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Add Similar Posts to feeds? (configured from own options page)', 'post_plugins') ?></th>
		<td>
		<select name="feed_active" id="feed_active">
		<option <?php if($feed_active == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
		<option <?php if($feed_active == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
		</select> 
		</td>
	</tr>
	<?php
}

// now for recent_comments

function ppl_display_show_type($show_type) {
	?>
	<tr valign="top">
		<th scope="row" title=""><?php _e('Type of comment to show:', 'post_plugins') ?></th>
		<td>
			<select name="show_type" id="show_type">
			<option <?php if($show_type == 'all') { echo 'selected="selected"'; } ?> value="all">All kinds of comment</option>
			<option <?php if($show_type == 'comments') { echo 'selected="selected"'; } ?> value="comments">Just plain comments</option>
			<option <?php if($show_type == 'trackbacks') { echo 'selected="selected"'; } ?> value="trackbacks">Just trackbacks and pingbacks</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_group_by($group_by) {
	?>
	<tr valign="top">
		<th scope="row" title=""><?php _e('Type of grouping:', 'post_plugins') ?></th>
		<td>
			<select name="group_by" id="group_by">
			<option <?php if($group_by == 'post') { echo 'selected="selected"'; } ?> value="post">By Post</option>
			<option <?php if($group_by == 'none') { echo 'selected="selected"'; } ?> value="none">Ungrouped</option>
			<option <?php if($group_by == 'author') { echo 'selected="selected"'; } ?> value="author">By Commenter</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_group_template($group_template) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Group title template:', 'post_plugins') ?></th>
		<td><textarea name="group_template" id="group_template" rows="4" cols="38"><?php echo htmlspecialchars(stripslashes($group_template)); ?></textarea></td>
	</tr>
	<?php
}

function ppl_display_no_author_comments($no_author_comments) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Omit comments by the post author?', 'post_plugins') ?></th>
		<td>
			<select name="no_author_comments" id="no_author_comments">
			<option <?php if($no_author_comments == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($no_author_comments == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

function ppl_display_no_user_comments($no_user_comments) {
	?>
	<tr valign="top">
		<th scope="row"><?php _e('Omit comments by registered users?', 'post_plugins') ?></th>
		<td>
			<select name="no_user_comments" id="no_user_comments">
			<option <?php if($no_user_comments == 'false') { echo 'selected="selected"'; } ?> value="false">No</option>
			<option <?php if($no_user_comments == 'true') { echo 'selected="selected"'; } ?> value="true">Yes</option>
			</select>
		</td> 
	</tr>
	<?php
}

?>