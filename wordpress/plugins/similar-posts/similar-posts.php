<?php
/*
Plugin Name:Similar Posts
Plugin URI: http://rmarsh.com/plugins/similar-posts/
Description: Displays a <a href="options-general.php?page=similar-posts.php">highly configurable</a> list of related posts. Similarity can be based on any combination of word usage in the content, title, or tags. Don't be disturbed if it takes a few moments to complete the installation -- the plugin is indexing your posts. <a href="http://rmarsh.com/plugins/post-options/">Instructions and help online</a>. Requires the latest version of the <a href="http://wordpress.org/extend/plugins/post-plugin-library/">Post-Plugin Library</a> to be installed.
Version: 2.5b23
Author: Rob Marsh, SJ
Author URI: http://rmarsh.com/
*/

/*
Copyright 2008  Rob Marsh, SJ  (http://rmarsh.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details: http://www.gnu.org/licenses/gpl.txt
*/

$similar_posts_version = $similar_posts_feed_version= '2.5b23';

/*
	Template Tag: Displays the posts most similar to the current post.
		e.g.: <?php similar_posts(); ?>
	Full help and instructions at http://rmarsh.com/plugins/post-options/
*/

function similar_posts($args = '') {
	echo SimilarPosts::execute($args);
}

/*

	'innards'
	
*/

if (!defined('DSEP')) define('DSEP', DIRECTORY_SEPARATOR);
if (!defined('POST_PLUGIN_LIBRARY')) SimilarPosts::install_post_plugin_library();

class SimilarPosts {
	
	function execute($args='', $default_output_template='<li>{link}</li>', $option_key='similar-posts'){
		if (!SimilarPosts::check_post_plugin_library('<a href="http://downloads.wordpress.org/plugin/post-plugin-library.zip">'.__('Post-Plugin Library missing').'</a>')) return '';
		global $table_prefix, $wpdb, $wp_version, $post;
		$start_time = ppl_microtime();
		if (defined('POC_CACHE_4')) {
			$cache_key = $option_key.$post->ID.$args;
			$result = poc_cache_fetch($cache_key);
			if ($result !== false) return $result . sprintf("<!-- Similar Posts took %.3f ms (cached) -->", 1000 * (ppl_microtime() - $start_time));
		}
		$table_name = $table_prefix . 'similar_posts';
		// First we process any arguments to see if any defaults have been overridden
		$options = ppl_parse_args($args);
		// Next we retrieve the stored options and use them unless a value has been overridden via the arguments
		$options = ppl_set_options($option_key, $options, $default_output_template);
		if (0 < $options['limit']) {
			$hide_future = true;
			$match_tags = ($options['match_tags'] !== 'false' && $wp_version >= 2.3);
			$exclude_cats = ($options['excluded_cats'] !== '');
			$include_cats = ($options['included_cats'] !== '');
			$exclude_authors = ($options['excluded_authors'] !== '');
			$include_authors = ($options['included_authors'] !== '');
			$exclude_posts = (trim($options['excluded_posts'] !== ''));
			$include_posts = (trim($options['included_posts']) !== '');
			$match_category = ($options['match_cat'] === 'true');
			$use_tag_str = ('' != $options['tag_str'] && $wp_version >= 2.3);
			$omit_current_post = ($options['omit_current_post'] !== 'false' && isset($post) && $post->ID !== 0);
			$hide_pass = ($options['show_private'] === 'false');
			$check_age = ('none' !== $options['age']['direction']);
			$check_custom = (trim($options['custom']['key']) !== '');
			$limit = $options['skip'].', '.$options['limit'];

	 		//get the terms to do the matching
			list( $contentterms, $titleterms, $tagterms) = sp_terms_to_match($post->ID, $options['num_terms']);
			
	 		// these should add up to 1.0
			$weight_content = $options['weight_content'];
			$weight_title = $options['weight_title'];
			$weight_tags = $options['weight_tags'];
			// below a threshold we ignore the weight completely and save some effort
			if ($weight_content < 0.001) $weight_content = (int) 0;
			if ($weight_title < 0.001) $weight_title = (int) 0; 
			if ($weight_tags < 0.001) $weight_tags = (int) 0; 
			
			if ($options['crossmatch'] === 'true') {
				$combinedterms = $contentterms . ' ' . $titleterms . ' ' . $tagterms;
				$contentterms = $combinedterms;
				$titleterms = $combinedterms;
				$tagterms = $combinedterms;
				$count_combined = substr_count($combinedterms, ' ') + 1;
				// the weighting factors here and below are a rough attempt to get the score for
				// a perfect match to be roughly 100 for all combinations of content, title, and tags
				// MySQL fulltext search needs to be normalized by the number of search terms
				// The weighting is more successful when the terms are not combined
				if ($weight_content) $weight_content = 57.0 * $weight_content / $count_combined;
				if ($weight_title) $weight_title = 220.0 * $weight_title / $count_combined;
				if ($weight_tags) $weight_tags = 200.0 * $weight_tags / $count_combined;
			} else {
				$count_content = substr_count($contentterms, ' ') + 1;
				$count_title = substr_count($titleterms, ' ') + 1;
				$count_tags  = substr_count($tagterms, ' ') + 1;
				if ($weight_content) $weight_content = 57.0 * $weight_content / $count_content;
				if ($weight_title) $weight_title = 18.0 * $weight_title / $count_title;
				if ($weight_tags) $weight_tags = 24.0 * $weight_tags / $count_tags;
			}

			// the workhorse...
			$sql = "SELECT *, ";
			$sql .= score_fulltext_match($table_name, $weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms);
		    if ($check_custom) $sql .= "LEFT JOIN $wpdb->postmeta ON post_id = ID ";
			// build the 'WHERE' clause
			$where = array();
			$where[] = where_fulltext_match($weight_title, $titleterms, $weight_content, $contentterms, $weight_tags, $tagterms);
			if ($hide_future) $where[] = where_hide_future();
			if ($match_category) $where[] = where_match_category();
			if ($match_tags) $where[] = where_match_tags($options['match_tags']);
			$where[] = where_show_pages($options['show_pages']);	
			if ($include_cats) $where[] = where_included_cats($options['included_cats']);
			if ($exclude_cats) $where[] = where_excluded_cats($options['excluded_cats']);
			if ($exclude_authors) $where[] = where_excluded_authors($options['excluded_authors']);
			if ($include_authors) $where[] = where_included_authors($options['included_authors']);
			if ($exclude_posts) $where[] = where_excluded_posts(trim($options['excluded_posts']));
			if ($include_posts) $where[] = where_included_posts(trim($options['included_posts']));
			if ($use_tag_str) $where[] = where_tag_str($options['tag_str']);
			if ($omit_current_post) $where[] = where_omit_post();
			if ($hide_pass) $where[] = where_hide_pass();
			if ($check_age) $where[] = where_check_age($options['age']['direction'], $options['age']['length'], $options['age']['duration']);
			if ($check_custom) $where[] = where_check_custom($options['custom']['key'], $options['custom']['op'], $options['custom']['value']);
			$sql .= "WHERE ".implode(' AND ', $where);
			if ($check_custom) $sql .= " GROUP BY $wpdb->posts.ID";
			$sql .= " ORDER BY score DESC LIMIT $limit";
		    $results = $wpdb->get_results($sql);
		} else {
			$results = false;
		}
	    if ($results) {
			$translations = ppl_prepare_template($options['output_template']);
			$output = ''; 
			foreach ($results as $result) {
				$output .= ppl_expand_template($result, $options['output_template'], $translations, $option_key);
			} 
			// If stuff is to be trimmed off the front...
			if ($options['trim_before']!=='' && strpos($output, $options['trim_before']) === 0) {
				$output = substr($output, strlen($options['trim_before']));
			}
			$output = $options['prefix'] . $output . $options['suffix'];
		} else {
			// if we reach here our query has produced no output ... so what next?
			if ($options['no_text'] !== 'false') {
				$output = ''; // we display nothing at all
			} else {
				// we display the blank message, with tags expanded if necessary
				$translations = ppl_prepare_template($options['none_text']);
				$output = $options['prefix'] . ppl_expand_template(array(), $options['none_text'], $translations, $option_key) . $options['suffix'];
			}
		}
		if (defined('POC_CACHE_4')) poc_cache_store($cache_key, $output); 
		return $output . sprintf("<!-- Similar Posts took %.3f ms -->", 1000 * (ppl_microtime() - $start_time));
	}

	// tries to install the post-plugin-library plugin
	function install_post_plugin_library() {
		$plugin_path = 'post-plugin-library/post-plugin-library.php';
		$current = get_option('active_plugins');
		if (!in_array($plugin_path, $current)) {
			$current[] = $plugin_path;
			update_option('active_plugins', $current);
			do_action('activate_'.$plugin_path);
		}
	}

	function check_post_plugin_library($msg) {
		$exists = function_exists('ppl_microtime');
		if (!$exists) echo $msg;
		return $exists;
	}
	
}

global $overusedwords;

function sp_terms_to_match($ID, $num_terms = 20) {
	if (!$ID) return array('', '', '');
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'similar_posts';
	$terms = '';
	$results = $wpdb->get_results("SELECT title, content, tags FROM $table_name WHERE pID=$ID LIMIT 1", ARRAY_A);
	if ($results) {
		$word = strtok($results[0]['content'], ' ');
		$n = 0;
		$wordtable = array();
		while ($word !== false) {
			$wordtable[$word] += 1;
			$word = strtok(' ');
		}
		arsort($wordtable);
		if ($num_terms < 1) $num_terms = 1;
		$wordtable = array_slice($wordtable, 0, $num_terms);
		foreach ($wordtable as $word => $count) {
			$terms .= ' ' . $word;
		}
		
		$res[] = $terms;
		$res[] = $results[0]['title'];	
		$res[] = $results[0]['tags'];
 	}
	return $res;
}


// adapted PageRank algorithm see http://www.cs.unt.edu/~rada/papers/mihalcea.emnlp04.pdf
//and the weighted adaptation http://www.cs.unt.edu/~rada/papers/hassan.ieee07.pdf
function xsp_terms_to_match($ID, $num_terms = 20) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'similar_posts';
	$terms = '';
	$results = $wpdb->get_results("SELECT title, content, tags FROM $table_name WHERE pID=$ID LIMIT 1", ARRAY_A);
	if ($results) {
		// build a directed graph with words as vertices and, as edges, the words which precede them
 		$prev_word = 'aaaaa';
		$graph = array();
		$word = strtok($results[0]['content'], ' ');
		while ($word !== false) {
			$graph[$word][$prev_word] += 1; // list the incoming words and keep a tally of how many times words co-occur
			$out_edges[$prev_word] += 1; // count the number of different words that follow each word
			$prev_word = $word;
			$word = strtok(' ');
		}
 		// initialise the list of PageRanks-- one for each unique word 
		reset($graph);
		while (list($vertex, $in_edges) =  each($graph)) {
			$oldrank[$vertex] = 0.25;
		}
		$n = count($graph);
		$base = 0.15 / $n; 
		$error_margin = $n * 0.005;
		do {
			$error = 0.0;
			// the edge-weighted PageRank calculation
			reset($graph);
			while (list($vertex, $in_edges) =  each($graph)) {
				$r = 0;
				reset($in_edges);
				while (list($edge, $weight) =  each($in_edges)) {
					$r += ($weight * $oldrank[$edge]) / $out_edges[$edge];
				}
				$rank[$vertex] = $base + 0.95 * $r;
				$error += abs($rank[$vertex] - $oldrank[$vertex]);		
			}
			$oldrank = $rank;
			//echo $error . '<br>';
		} while ($error > $error_margin);
		arsort($rank);
		$rank = array_slice($rank, 0, $num_terms);
		foreach ($rank as $vertex => $score) {
			$total += $score * $score;
		}
		$total = sqrt($total);
		foreach ($rank as $vertex => $score) {
			$rank[$vertex] = $score / $total;
		}
		// foreach ($rank as $vertex => $score) {
			// $terms .= ' ' . $vertex;
		// }
		//echo $terms;
		//$res[] = $terms;
		$res[] = $rank;
		// $res[] = $results[0]['title'];	
		// $res[] = $results[0]['tags'];
		$res[] = explode(' ', $results[0]['title']);	
		$res[] = explode(' ', $results[0]['tags']);
 	}
	return $res;
}

// do not try and use this function directly -- it is automatically installed when the option is set to show similar posts in feeds
function similar_posts_for_feed($content) {
	return (is_feed()) ? $content . SimilarPosts::execute('', '<li>{link}</li>', 'similar-posts-feed') : $content;
}

function sp_save_index_entry($postID) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'similar_posts';
	$post = $wpdb->get_row("SELECT post_content, post_title FROM $wpdb->posts WHERE ID = $postID", ARRAY_A);
	//extract its terms
	$options = get_option('similar-posts');
	$utf8 = ($options['utf8'] === 'true');
	$use_stemmer = ($options['use_stemmer'] === 'true');
	$content = sp_get_post_terms($post['post_content'], $utf8, $use_stemmer);
	$title = sp_get_title_terms($post['post_title'], $utf8, $use_stemmer);
	$tags = sp_get_tag_terms($postID, $utf8);
	//check to see if the field is set
	$pid = $wpdb->get_var("SELECT pID FROM $table_name WHERE pID=$postID limit 1");
	//then insert if empty
	if (is_null($pid)) {
		$wpdb->query("INSERT INTO $table_name (pID, content, title, tags) VALUES ($postID, \"$content\", \"$title\", \"$tags\")");
	} else {
		$wpdb->query("UPDATE $table_name SET content=\"$content\", title=\"$title\", tags=\"$tags\" WHERE pID=$postID" );	
	}
	return $postID;
}

function sp_delete_index_entry($postID) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . 'similar_posts';
	$wpdb->query("DELETE FROM $table_name WHERE pID = $postID ");
	return $postID;
}

function sp_clean_words($text) {
	$text = strip_tags($text);
	$text = strtolower($text);
	$text = str_replace("’", "'", $text); // convert MSWord apostrophe
	$text = preg_replace(array('/\[(.*?)\]/', '/&[^\s;]+;/', '/‘|’|—|“|”|–|…/', "/'\W/"), ' ', $text); //anything in [..] or any entities or MS Word droppings
	return $text;
}

function sp_mb_clean_words($text) {
	mb_regex_encoding('UTF-8');
	mb_internal_encoding('UTF-8');
	$text = strip_tags($text);
	$text = mb_strtolower($text);
	$text = str_replace("’", "'", $text); // convert MSWord apostrophe
	$text = preg_replace(array('/\[(.*?)\]/u', '/&[^\s;]+;/u', '/‘|’|—|“|”|–|…/u', "/'\W/u"), ' ', $text); //anything in [..] or any entities
	return 	$text;
}

function sp_mb_str_pad($text, $n, $c) {
	mb_internal_encoding('UTF-8');
	$l = mb_strlen($text);
	if ($l > 0 && $l < $n) {
		$text .= str_repeat($c, $n-$l);
	}
	return $text;
}

function sp_get_post_terms($text, $utf8, $use_stemmer) {
	global $overusedwords;
	if ($utf) {
		if ($use_stemmer) {
			mb_regex_encoding('UTF-8');
			mb_internal_encoding('UTF-8');
			$wordlist = mb_split("\W+", sp_mb_clean_words($text));
			$words = '';
			reset($wordlist);
			while (list($n, $word) =  each($wordlist)) {
				if ( mb_strlen($word) > 3) {
					$stem = sp_mb_str_pad(stem($word), 4, '_');
					if (!isset($overusedwords[$stem])) {
						$words .= $stem . ' ';
					}	
				}	
			}	
			return $words;
		} else {
			mb_regex_encoding('UTF-8');
			mb_internal_encoding('UTF-8');
			$wordlist = mb_split("\W+", sp_mb_clean_words($text));
			$words = ''; 
			reset($wordlist);
			while (list($n, $word) =  each($wordlist)) {
				if ( mb_strlen($word) > 3 && !isset($overusedwords[$word])) {
					$words .= $word . ' ';
				}	
			}	
			return $words;
		}
	} else {
		if ($use_stemmer) {
			$wordlist = str_word_count(sp_clean_words($text), 1);
			$words = ''; 
			reset($wordlist);
			while (list($n, $word) =  each($wordlist)) {
				if ( strlen($word) > 3) {
					$stem = str_pad(stem($word), 4, '_');
					if (!isset($overusedwords[$stem])) {
						$words .= $stem . ' ';
					}	
				}	
			}	
			return $words;
		} else {
			$wordlist = str_word_count(sp_clean_words($text), 1);
			$words = '';
			reset($wordlist);
			while (list($n, $word) =  each($wordlist)) {
				if (strlen($word) > 3 && !isset($overusedwords[$word])) {
					$words .= $word . ' '; 
				}	
			}	
			return $words;
		}
	}
}

$tinywords = array('the' => 1, 'and' => 1, 'of' => 1, 'a' => 1, 'for' => 1, 'on' => 1);

function sp_get_title_terms($text, $utf8, $use_stemmer) {
	global $tinywords;
	if ($utf) {
		if ($use_stemmer) {
			mb_regex_encoding('UTF-8');
			mb_internal_encoding('UTF-8');
			$wordlist = mb_split("\W+", sp_mb_clean_words($text));
			$roots = '';
			foreach ($wordlist as $word) {
				if (!isset($tinywords[$word])) {
					$roots .= sp_mb_str_pad(stem($word), 4, '_') . ' ';
				}	
			}	
			return rtrim($roots);
		} else {
			mb_regex_encoding('UTF-8');
			mb_internal_encoding('UTF-8');
			$wordlist = mb_split("\W+", sp_mb_clean_words($text));
			$words = '';
			foreach ($wordlist as $word) {
				if (!isset($tinywords[$word])) {
					$words .= sp_mb_str_pad($word, 4, '_') . ' ';
				}
			}	
			return rtrim($words);
		}
	} else {
		if ($use_stemmer) {
			$wordlist = str_word_count(sp_clean_words($text), 1);
			$roots = '';
			foreach ($wordlist as $word) {
				if (!isset($tinywords[$word])) {
					$roots .= str_pad(stem($word), 4, '_') . ' ';
				}
			}
			return rtrim($roots);
		} else {
			$wordlist = str_word_count(sp_clean_words($text), 1);
			$words = '';
			foreach ($wordlist as $word) {
				if (!isset($tinywords[$word])) {
					$words .= str_pad($word, 4, '_') . ' ';
				}
			}
			return rtrim($words);
		}
	}
}

function sp_get_tag_terms($ID, $utf8) {
	global $wpdb;
	if (!function_exists('get_object_term_cache')) return ''; 
	$tags = array();
	$query = "SELECT t.name FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy = 'post_tag' AND tr.object_id = '$ID'";
	$tags = $wpdb->get_col($query);
	if (!empty ($tags)) {
		if ($utf8) {
			mb_internal_encoding('UTF-8');
			foreach ($tags as $tag) {
				$newtags[] = sp_mb_str_pad(mb_strtolower($tag), 4, '_');
			}	
		} else {
			foreach ($tags as $tag) {
				$newtags[] = str_pad(strtolower($tag), 4, '_');
			}	
		}	
		$newtags = str_replace(' ', '_', $newtags);	
		$tags = implode (' ', $newtags);
	} else {
		$tags = '';
	}	
	return $tags;		
}

if ( is_admin() ) {
	require(dirname(__FILE__).'/similar-posts-admin.php');
}

function widget_rrm_similar_posts_init() {
	if (! function_exists("register_sidebar_widget")) {
		return;
	}
	function widget_rrm_similar_posts($args) {
		extract($args);
		$options = get_option('widget_rrm_similar_posts');
		$condition = ($options['condition']) ? $options['condition'] : 'true' ;
		$condition = (stristr($condition, "return")) ? $condition : "return ".$condition;
		$condition = rtrim($condition, '; ') . ' || is_admin();'; 
		if (eval($condition)) {
			$title = empty($options['title']) ? __('Similar Posts', 'post_plugins') : $options['title'];
			if ( !$number = (int) $options['number'] )
				$number = 10;
			else if ( $number < 1 )
				$number = 1;
			else if ( $number > 15 )
				$number = 15;
			echo $before_widget;
			echo $before_title.$title.$after_title;
			similar_posts('limit='.$number);
			echo $after_widget;
		}
	}
	function widget_rrm_similar_posts_control() {
		if ( $_POST['widget_rrm_similar_posts_submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['widget_rrm_similar_posts_title']));
			$options['number'] = (int) $_POST["widget_rrm_similar_posts_number"];
			$options['condition'] = stripslashes(trim($_POST["widget_rrm_similar_posts_condition"], '; '));
			update_option("widget_rrm_similar_posts", $options);
		} else {
			$options = get_option('widget_rrm_similar_posts');
		}		
		$title = attribute_escape($options['title']);
		if ( !$number = (int) $options['number'] )
			$number = 5;
		$condition = attribute_escape($options['condition']);
		?>
		<p><label for="widget_rrm_similar_posts_title"> <?php _e('Title:', 'post_plugins'); ?> <input style="width: 200px;" id="widget_rrm_similar_posts_title" name="widget_rrm_similar_posts_title" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="widget_rrm_similar_posts_number"> <?php _e('Number of posts to show:', 'post_plugins'); ?> <input style="width: 25px; text-align: center;" id="widget_rrm_similar_posts_number" name="widget_rrm_similar_posts_number" type="text" value="<?php echo $number; ?>" /></label> <?php _e('(at most 15)', 'post_plugins'); ?> </p>
		<p><label for="widget_rrm_similar_posts_condition"> <?php _e('Show only if page: (e.g., <a href="http://codex.wordpress.org/Conditional_Tags" title="help">is_single()</a>)', 'post_plugins'); ?> <input style="width: 200px;" id="widget_rrm_similar_posts_condition" name="widget_rrm_similar_posts_condition" type="text" value="<?php echo $condition; ?>" /></label></p>
		<input type="hidden" id="widget_rrm_similar_posts_submit" name="widget_rrm_similar_posts_submit" value="1" />
		There are many more <a href="options-general.php?page=similar-posts.php">options</a> available.
		<?php
	}
	register_sidebar_widget(__('Similar Posts +', 'post_plugins'), 'widget_rrm_similar_posts');
	register_widget_control(__('Similar Posts +', 'post_plugins'), 'widget_rrm_similar_posts_control', 300, 100);
}

add_action('plugins_loaded', 'widget_rrm_similar_posts_init');


/*
	now some language specific stuff
*/

//the next lines find the language WordPress is using
$language = substr(WPLANG, 0, 2);
//if no language is specified make it the default which is 'en'
if ($language == '') {
	$language = 'en';
}
$languagedir = dirname(__FILE__).DSEP.'languages'.DSEP.$language.DEP;
//see if the directory exists and if not revert to the default English dir
if (!file_exists($languagedir)) {
	$languagedir = dirname(__FILE__).DSEP.'languages'.DSEP.'en'.DSEP;
}

// import the stemming algorithm ... a single function called 'stem'
require_once($languagedir.'stemmer.php');
require_once($languagedir.'stopwords.php');
$overusedwords = array_flip($overusedwords);

function similar_posts_init () {
	global $overusedwords, $wp_db_version;
	load_plugin_textdomain('post_plugins');
	
	$options = get_option('similar-posts');
	if ($options['content_filter'] === 'true' && function_exists('ppl_register_content_filter')) ppl_register_content_filter('SimilarPosts');
	if ($options['feed_active'] === 'true') add_filter('the_content', 'similar_posts_for_feed');

	//install the actions to keep the index up to date
	add_action('save_post', 'sp_save_index_entry', 1);
	add_action('delete_post', 'sp_delete_index_entry', 1);
	if ($wp_db_version < 3308 ) { 
		add_action('edit_post', 'sp_save_index_entry', 1);
		add_action('publish_post', 'sp_save_index_entry', 1);
	} 
}

add_action ('init', 'similar_posts_init', 1);

?>