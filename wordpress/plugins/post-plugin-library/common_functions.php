<?php

/*

	Library for the Recent Posts, Random Posts, Recent Comments, and Similar Posts plugins
	-- provides the routines which the plugins share

*/

define('CF_LIBRARY', true);

function ppl_parse_args($args) {
	// 	$args is of the form 'key1=val1&key2=val2'
	//	The code copes with null values, e.g., 'key1=&key2=val2' 
	//	and arguments with embedded '=', e.g. 'output_template=<li class="stuff">{...}</li>'.
	if($args){
		// the default separator is '&' but you may wish to include the character in a title, say,
		// so you can specify an alternative separator by making the first character of $args
		// '&' and the second character your new separator...
		if (substr($args, 0, 1) === '&') {
			$s = substr($args, 1, 1);
			$args = substr($args, 2);
		} else {
			$s = '&';
		}
		// separate the arguments into key=value pairs
		$arguments = explode($s, $args);
		foreach($arguments as $arg){
			if($arg){
				// find the position of the first '='
				$i = strpos($arg, '=');
				// if not a valid format ('key=value) we ignore it
				if ($i){
					$key = substr($arg, 0, $i);
					$val = substr($arg, $i+1); 
					$result[$key]=$val;
				}
			}
		}
	}
	return $result;
}

function ppl_set_options($option_key, $arg, $default_output_template) {
	$options = get_option($option_key);
	// deal with compound options
	if (isset($arg['custom-key'])) {$arg['custom']['key'] = $arg['custom-key']; unset($arg['custom-key']);}
	if (isset($arg['custom-op'])) {$arg['custom']['op'] = $arg['custom-op']; unset($arg['custom-op']);}
	if (isset($arg['custom-value'])) {$arg['custom']['value'] = $arg['custom-value']; unset($arg['custom-value']);}
	if (isset($arg['age-direction'])) {$arg['age']['direction'] = $arg['age-direction']; unset($arg['age-direction']);}
	if (isset($arg['age-length'])) {$arg['age']['length'] = $arg['age-length']; unset($arg['age-length']);}
	if (isset($arg['age-duration'])) {$arg['age']['duration'] = $arg['age-duration']; unset($arg['age-duration']);}
	// then fill in the defaults
	if (!isset($arg['limit'])) $arg['limit'] = stripslashes($options['limit']);
	if (!isset($arg['skip'])) $arg['skip'] = stripslashes($options['skip']);
	if (!isset($arg['trim_before'])) $arg['trim_before'] = stripslashes($options['trim_before']);
	if (!isset($arg['omit_current_post'])) $arg['omit_current_post'] = $options['omit_current_post'];
	if (!isset($arg['show_private'])) $arg['show_private'] = $options['show_private'];
	if (!isset($arg['show_pages'])) $arg['show_pages'] = $options['show_pages'];
	if (!isset($arg['none_text'])) $arg['none_text'] = stripslashes($options['none_text']);
	if (!isset($arg['no_text'])) $arg['no_text'] = $options['no_text'];
	if (!isset($arg['tag_str'])) $arg['tag_str'] = stripslashes($options['tag_str']);
	if (!isset($arg['excluded_cats'])) $arg['excluded_cats'] = stripslashes($options['excluded_cats']);
	if (!isset($arg['included_cats'])) $arg['included_cats'] = stripslashes($options['included_cats']);
	if (!isset($arg['excluded_authors'])) $arg['excluded_authors'] = stripslashes($options['excluded_authors']);
	if (!isset($arg['included_authors'])) $arg['included_authors'] = stripslashes($options['included_authors']);
	if (!isset($arg['excluded_posts'])) $arg['excluded_posts'] = stripslashes($options['excluded_posts']);
	if (!isset($arg['included_posts'])) $arg['included_posts'] = stripslashes($options['included_posts']);
	if (!isset($arg['stripcodes'])) $arg['stripcodes'] = $options['stripcodes'];
	if (!isset($arg['prefix'])) $arg['prefix'] = stripslashes($options['prefix']);
	if (!isset($arg['suffix'])) $arg['suffix'] = stripslashes($options['suffix']);
	if (!isset($arg['output_template'])) $arg['output_template'] = stripslashes($options['output_template']);
	// an empty output_template makes no sense so we fall back to the default
	if ($arg['output_template'] == '') $arg['output_template'] = $default_output_template;
	if (!isset($arg['match_cat'])) $arg['match_cat'] = $options['match_cat'];
	if (!isset($arg['match_tags'])) $arg['match_tags'] = $options['match_tags'];
	if (!isset($arg['age'])) $arg['age'] = $options['age'];
	if (!isset($arg['custom'])) $arg['custom'] = $options['custom'];
	
	// just for recent_comments
	if (!isset($arg['group_by'])) $arg['group_by'] = $options['group_by'];
	if (!isset($arg['group_template'])) $arg['group_template'] = stripslashes($options['group_template']);
	if (!isset($arg['show_type'])) $arg['show_type'] = $options['show_type'];
	if (!isset($arg['no_author_comments'])) $arg['no_author_comments'] = $options['no_author_comments'];
	if (!isset($arg['no_user_comments'])) $arg['no_user_comments'] = $options['no_user_comments'];

	// just for similar_posts[feed]
	if (!isset($arg['combine'])) $arg['combine'] = $options['crossmatch'];
	if (!isset($arg['weight_content'])) $arg['weight_content'] = $options['weight_content'];
	if (!isset($arg['weight_title'])) $arg['weight_title'] = $options['weight_title'];
	if (!isset($arg['weight_tags'])) $arg['weight_tags'] = $options['weight_tags'];
	if (!isset($arg['num_terms'])) $arg['num_terms'] = stripslashes($options['num_terms']);
	// the last options cannot be set via arguments
	$arg['stripcodes'] = $options['stripcodes'];
	$arg['utf8'] = $options['utf8'];
	$arg['use_stemmer'] = $options['use_stemmer'];
	$arg['batch'] = $options['batch'];
	//$arg['use_freq'] = $options['use_freq'];
	$arg['content_filter'] = $options['content_filter'];
	
	return $arg;
}

function ppl_prepare_template($template) {
	// Now we process the output_template to find the embedded tags which are to be replaced
	// with values taken from the database.
	// A tag is of the form, {tag:ext}, where the tag part will be evaluated and replaced 
	// and the optional ext part provides extra data pertinent to that tag
	preg_match_all('/{((?:[^{}]|{[^{}]*})*)}/', $template, $matches);	
	foreach ($matches[1] as $match) {
		list($tag, $ext) = explode(':', $match, 2);
		$action = output_tag_action($tag);
		if (function_exists($action)) {
			// store the action that instantiates the tag
			$translations['acts'][] = $action;		
			// add the tag in a form ready to use in translation later
			$translations['fulltags'][] = '{'.$match.'}';
			// the extra data if any
			$translations['exts'][] = $ext;
		}
	}	
	return $translations;
}
		
function xppl_prepare_template($template) {
	// Now we process the output_template to find the embedded tags which are to be replaced
	// with values taken from the database.
	// A tag is of the form, {tag:ext}, where the tag part will be evaluated and replaced 
	// and the optional ext part provides extra data pertinent to that tag
	preg_match_all('/{(.+?)}/', $template, $matches);	
	foreach ($matches[0] as $match) {
		list($tag, $ext) = explode(':', trim($match, '{}'), 2);
		$action = output_tag_action($tag);
		if (function_exists($action)) {
			// store the action that instantiates the tag
			$translations['acts'][] = $action;		
			// add the tag in a form ready to use in translation later
			$translations['fulltags'][] = $match;
			// the extra data if any
			$translations['exts'][] = $ext;
		}
	}	
	return $translations;
}
		
function ppl_expand_template($result, $template, $translations, $option_key) {
	global $wpdb, $wp_version;
	$replacements = array();
	$numtags = count($translations['fulltags']);
	for ($i = 0; $i < $numtags; $i++) {
		$fulltag = $translations['fulltags'][$i];
		$act = $translations['acts'][$i];
		$ext = $translations['exts'][$i];
		$replacements[$fulltag] = $act($option_key, $result, $ext);
	}
	// Replace every valid tag with its value
	$tmp = strtr($template, $replacements)."\n";
	return $tmp;
}

/*

	Functions to fill in the WHERE part of the workhorse SQL
	
*/

function where_match_tags($match_tags) {
	global $wpdb, $wp_version, $post;
	$args = array('fields' => 'ids');
	$tag_ids = wp_get_object_terms($post->ID, 'post_tag', $args);
	if ( is_array($tag_ids) && count($tag_ids) > 0 )  {
		if ($match_tags === 'any') {
			$ids = get_objects_in_term($tag_ids, 'post_tag');
		} else {
			$ids = array();
			foreach ($tag_ids as $tag_id){
				if (count($ids) > 0) {
					$ids = array_intersect($ids, get_objects_in_term($tag_id, 'post_tag'));
				} else {
					$ids = get_objects_in_term($tag_id, 'post_tag');
				}	
			}	
		}
		if ( is_array($ids) && count($ids) > 0 ) {
			$ids = array_unique($ids);
			$out_posts = "'" . implode("', '", $ids) . "'";
			$sql = "$wpdb->posts.ID IN ($out_posts)";
		} 
	} else {
		$sql = "1 = 2";
	}
	return $sql;
}

function where_show_pages($show_pages) {
	global $wp_version;
	if ($wp_version < 2.1) {
		if ($show_pages === 'true') $sql = "post_status IN ('publish', 'static')";
		else if ($show_pages === 'false') $sql = "post_status = 'publish'";
		else if ($show_pages === 'but') $sql = "post_status = 'static'";
	} else {
		if ($show_pages === 'true') $sql = "post_status='publish' AND post_type IN ('page', 'post')";
		else if ($show_pages === 'false') $sql = "post_status='publish' AND post_type='post'";
		else if ($show_pages === 'but') $sql = "post_status='publish' AND post_type='page'";
	}
	return $sql;
}

// a replacement, for WP < 2.3, ONLY category children
if (!function_exists('get_term_children')) {
	function get_term_children($term, $taxonomy) {
		if ($taxonomies !== 'category') return array();
		return get_categories('child_of='.$term);
	}
}


// a replacement, for WP < 2.3, ONLY to get posts with given category IDs
if (!function_exists('get_objects_in_term')) {
	function get_objects_in_term($terms, $taxonomies) {
		global $wpdb;
		if ($taxonomies !== 'category') return array();
		$terms = "'" . implode("', '", $terms) . "'";
		$object_ids = $wpdb->get_col("SELECT post_id FROM $wpdb->post2cat WHERE category_id IN ($terms)");
		if (!$object_ids) return array();
		return $object_ids;
	}
}

function where_match_category() {
	global $wpdb, $wp_version;
	$cat_ids = '';
	foreach(get_the_category() as $cat) {
		if ($cat->cat_ID) $cat_ids .= $cat->cat_ID . ',';
	}
	$cat_ids = rtrim($cat_ids, ',');
	$catarray = explode(',', $cat_ids);
	foreach ( $catarray as $cat ) {
		$catarray = array_merge($catarray, get_term_children($cat, 'category'));
	}
	$catarray = array_unique($catarray);
	$ids = get_objects_in_term($catarray, 'category');
	$ids = array_unique($ids);
	if ( is_array($ids) && count($ids) > 0 ) {
		$out_posts = "'" . implode("', '", $ids) . "'";
		$sql = "$wpdb->posts.ID IN ($out_posts)";
	} else {
		$sql = "1 = 2";
	}
	return $sql;
}

function where_included_cats($included_cats) {
	global $wpdb, $wp_version;
	$catarray = explode(',', $included_cats);
	foreach ( $catarray as $cat ) {
		$catarray = array_merge($catarray, get_term_children($cat, 'category'));
	}
	$catarray = array_unique($catarray);
	$ids = get_objects_in_term($catarray, 'category');
	if ( is_array($ids) && count($ids) > 0 ) {
		$ids = array_unique($ids);
		$in_posts = "'" . implode("', '", $ids) . "'";
		$sql = "$wpdb->posts.ID IN ($in_posts)";
	}
	return $sql;	
}

function where_excluded_cats($excluded_cats) {
	global $wpdb, $wp_version;
	$catarray = explode(',', $excluded_cats);
	foreach ( $catarray as $cat ) {
		$catarray = array_merge($catarray, get_term_children($cat, 'category'));
	}
	$catarray = array_unique($catarray);
	$ids = get_objects_in_term($catarray, 'category');
	if ( is_array($ids) && count($ids) > 0 ) {
		$out_posts = "'" . implode("', '", $ids) . "'";
		$sql = "$wpdb->posts.ID NOT IN ($out_posts)";
	}
	return $sql;
} 

function where_excluded_authors($excluded_authors){
	return "post_author NOT IN ( $excluded_authors )";
}

function where_included_authors($included_authors){
	return "post_author IN ( $included_authors )";
}

function where_excluded_posts($excluded_posts) {
	return "ID NOT IN ( $excluded_posts )";
}

function where_included_posts($included_posts) {
	return "ID IN ( $included_posts )";
}

function where_tag_str($tag_str) {
	global $wpdb;
	if ( strpos($tag_str, ',') !== false ) {
		$intags = explode(',', $tag_str);
		foreach ( (array) $intags as $tag ) {
			$tags[] = sanitize_term_field('name', $tag, 0, 'post_tag', 'db');
		}
		$tag_type = 'any';
	} else if ( strpos($tag_str, '+') !== false ) {
		$intags = explode('+', $tag_str);
		foreach ( (array) $intags as $tag ) {
			$tags[] = sanitize_term_field('name', $tag, 0, 'post_tag', 'db');
		}
		$tag_type = 'all';
	} else {
		$tags[] = sanitize_term_field('name', $tag_str, 0, 'post_tag', 'db');
		$tag_type = 'any';
	}
	$ids = array();
	if ($tag_type == 'any') {
		foreach ($tags as $tag){
			if (is_term($tag, 'post_tag')) {
				$t = get_term_by('name', $tag, 'post_tag');
				$ids = array_merge($ids, get_objects_in_term($t->term_id, 'post_tag'));
			}	
		}	
	} else {
		foreach ($tags as $tag){
			if (is_term($tag, 'post_tag')) {
				$t = get_term_by('name', $tag, 'post_tag');
				if (count($ids) > 0) {
					$ids = array_intersect($ids, get_objects_in_term($t->term_id, 'post_tag'));
				} else {
					$ids = get_objects_in_term($t->term_id, 'post_tag');
				}
			}	
		}	
	}
	if ( is_array($ids) && count($ids) > 0 ) {
		$ids = array_unique($ids);
		$out_posts = "'" . implode("', '", $ids) . "'";
		$sql .= "$wpdb->posts.ID IN ($out_posts)";
	} else $sql .= "1 = 2";
	return $sql;
}

function where_omit_post() {	
	global $post, $ID;	
	if (isset($post) && isset($post->ID)) {
		$postid = $post->ID;
	} else if (isset($ID)) {
		$postid = $ID;
	} else {
		$postid = -1;
	}
	return "ID != $postid";
}

function where_hide_pass() {
	return "post_password =''";
}

function where_hide_future() {
	global $wp_version;
	if ($wp_version < 2.1) {
		$time_difference = get_option('gmt_offset');
		$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));
		$sql = "post_date <= '$now'";
	} else {
		$sql = "post_status != 'future'";
	}
	return $sql;
}

function where_fulltext_match($weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms) {
	$wsql = array();
	if ($weight_title) $wsql[] = "MATCH (`title`) AGAINST ( \"$titleterms\" )";
	if ($weight_content) $wsql[] = "MATCH (`content`) AGAINST ( \"$contentterms\" )";
	if ($weight_tags) $wsql[] = "MATCH (`tags`) AGAINST ( \"$tagterms\" )";
	return '(' . implode(' OR ', $wsql) . ') ' ;
}

function xwhere_fulltext_match($weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms) {
	$wsql = array();
	if ($weight_title) $wsql[] = "MATCH (`title`) AGAINST ( \"$titleterms\" )";
	$contentterms = implode(' ', array_keys($contentterms));
	if ($weight_content) $wsql[] = "MATCH (`content`) AGAINST ( \"$contentterms\" )";
	if ($weight_tags) $wsql[] = "MATCH (`tags`) AGAINST ( \"$tagterms\" )";
	return '(' . implode(' OR ', $wsql) . ') ' ;
}

function where_author_comments() {
	$author_email = get_the_author_email();
	return "'$author_email' != comment_author_email";
}

function where_user_comments() {
	return "user_id = 0";
}

function score_fulltext_match($table_name, $weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms) {
	global $wpdb;
	$wsql = array();
	if ($weight_title) $wsql[] = "($weight_title * (MATCH (`title`) AGAINST ( \"$titleterms\" )))";
	if ($weight_content) $wsql[] = "($weight_content * (MATCH (`content`) AGAINST ( \"$contentterms\" )))";
	if ($weight_tags) $wsql[] = "($weight_tags * (MATCH (`tags`) AGAINST ( \"$tagterms\" )))";
	return '(' . implode(' + ', $wsql) . " ) as score FROM `$table_name` LEFT JOIN `$wpdb->posts` ON `pID` = `ID` "; 
}

function xscore_fulltext_match($table_name, $weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms) {
	global $wpdb;
	$wsql = array();
	if ($weight_title) $wsql[] = "($weight_title * (MATCH (`title`) AGAINST ( \"$titleterms\" )))";
	if ($weight_content) {
		foreach($contentterms as $word => $score) {
			$weight = $weight_content * sqrt($score);
			$wsql[] = "($weight * (MATCH (`content`) AGAINST ( \"$word\" )))";
		}
	}
	if ($weight_tags) $wsql[] = "($weight_tags * (MATCH (`tags`) AGAINST ( \"$tagterms\" )))";
	return '(' . implode(' + ', $wsql) . " ) as score FROM `$table_name` LEFT JOIN `$wpdb->posts` ON `pID` = `ID` "; 
}

function where_comment_type($comment_type) {
	if ($comment_type === 'comments') $sql = "comment_type = ''";
	elseif ($comment_type === 'trackbacks') $sql = "comment_type != ''";
	return $sql;
}

function where_check_age($direction, $length, $duration) {
	global $wp_version;
	if ('none' === $direction) return '';
	$age = "DATE_SUB(CURDATE(), INTERVAL $length $duration)";
	// we only filter out posts based on age, not pages
	if ('before' === $direction) {
		if ($wp_version < 2.1) {
			return "(post_date <= $age OR post_status='static')";
		} else {
			return "(post_date <= $age OR post_type='page')";
		}
	} else {
		if ($wp_version < 2.1) {
			return "(post_date >= $age OR post_status='static')";
		} else {
			return "(post_date >= $age OR post_type='page')";
		}
	}
}
			//"(meta_key = '_edit_last' && meta_value = '2')";

function where_check_custom($key, $op, $value) {
	if ($op === 'EXISTS') {
		return "meta_key = '$key'";
	} else {
		return "(meta_key = '$key' && meta_value $op '$value')";
	}
}

/*

	End of SQL functions

*/

function ppl_microtime() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec); 
} 

/*

	Now some routines to handle content filtering

*/

// the '|'-separated list of valid content filter tags
global $ppl_filter_tags;

// each plugin calls this on startup to have content scanned for its own tag
function ppl_register_content_filter($tag) {
	global $ppl_filter_tags;
	if (!$ppl_filter_tags) {
		$ppl_filter_tags = $tag;
	} else {
		$tags = explode('|', $ppl_filter_tags);
		$tags[] = $tag;
		$tags = array_unique($tags);
		$ppl_filter_tags = implode('|', $tags);
	}
}

function ppl_content_filter($content) {
	global $ppl_filter_tags;
	// replaces every instance of "<!--RecentPosts-->", for example, with the output of the plugin
	$content = preg_replace("/(<!\s*--\s*)($ppl_filter_tags)(\s*--\s*>)/e", "\\2::execute()", $content);
	return $content;
}

function ppl_content_filter_init() {
	global $ppl_filter_tags;
	if (!$ppl_filter_tags) return;
	add_filter( 'the_content',     'ppl_content_filter', 5 );
	add_filter( 'the_content_rss', 'ppl_content_filter', 5 );
	add_filter( 'the_excerpt',     'ppl_content_filter', 5 );
	add_filter( 'the_excerpt_rss', 'ppl_content_filter', 5 );
	add_filter( 'widget_text',     'ppl_content_filter', 5 );
}

// watch out that the registration fucntion are called earlier
add_action ('init', 'ppl_content_filter_init');

?>