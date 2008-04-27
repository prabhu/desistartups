<?php

/*

	Library for the Recent Posts, Random Posts, Recent Comments, and Similar Posts plugins
	-- provides the routines which evaluate output template tags

*/

define('OT_LIBRARY', true);

// Called by the post plugins to match output tags to the actions that evaluate them
function output_tag_action($tag) {
	return 'otf_'.$tag;
}

/*
	innards
*/


// To add a new output template tag all you need to do is write a tag function like those below.

// All the tag functions must follow the pattern of 'otf_title' below. 
//	the name is the tag name prefixed by 'otf_'
//	the arguments are always $option_key, $result and $ext
//		$option_key	the key to the plugin's options
//		$result		the particular row of the query result
//		$ext			some extra data which a tag may use
//	the return value is the value of the tag as a string  

function otf_postid ($option_key, $result, $ext) {
	return $result->ID;	
}

function otf_title ($option_key, $result, $ext) {
	$value = oth_truncate_text($result->post_title, $ext);
	return apply_filters('the_title', $value);	
}

function otf_url($option_key, $result, $ext) {
	$value = apply_filters('the_permalink', get_permalink($result->ID));
	return oth_truncate_text($value, $ext);
}

function otf_author($option_key, $result, $ext) {
	if ($ext) {
		$s = explode(':', $ext);
		if (count($s) == 1) {
			$type = $s[0];
		}
	}
	switch ($type) {
	case 'display':
		$author = get_author_name($result->post_author);
		break;
	case 'full':
		$auth = get_userdata($result->post_author);
		$author = $auth->first_name.' '.$auth->last_name;
		break;
	case 'reverse':
		$auth = get_userdata($result->post_author);
		$author = $auth->last_name.', '.$auth->first_name;
		break;
	case 'first':
		$auth = get_userdata($result->post_author);
		$author = $auth->first_name;
		break;
	case 'last':
		$auth = get_userdata($result->post_author);
		$author = $auth->last_name;
		break;
	default:	
		$author = get_author_name($result->post_author);
	}
	return $author;
}

function otf_date($option_key, $result, $ext) {
	 return oth_format_date($result->post_date, $ext);
}

function otf_dateedited($option_key, $result, $ext) {
	return oth_format_date($result->post_modified, $ext);
}

function otf_time($option_key, $result, $ext) {
	return oth_format_time($result->post_date, $ext);
}

function otf_timeedited($option_key, $result, $ext) {
	return oth_format_time($result->post_modified, $ext);
}

function otf_excerpt($option_key, $result, $ext) {
	if (!$ext) {
		$len = 55;
		$type = 'a';
	} else {
		$s = explode(':', $ext);
		if (count($s) == 1) {
			$s[] = 'a';
		}
		$len = $s[0];
		$type = $s[1];	
		if ($type === 'b') {
			if (count($s) > 2) {
				$more = $s[2];
			} else {
				$more = ' &hellip;';
			}	
		}
	}
	switch ($type) {
	case 'a':
		$value = trim($result->post_excerpt);
		if ($value == '') $value = $result->post_content;
		$value = oth_trim_excerpt($value, $ext);
		break;
	case 'b':
		$value = trim($result->post_excerpt);
		if ($value === '') {
			$value = $result->post_content;
			$value = convert_smilies($value);
			$value = oth_trim_extract($value, $len, $more);
			$value = apply_filters('get_the_content', $value);
			$value = apply_filters('the_content', $value);
		} else {
			$value = convert_smilies($value);
			$value = apply_filters('get_the_excerpt', $value);
			$value = apply_filters('the_excerpt', $value);
		}
		break;
	default:
		$value = trim($result->post_excerpt);
		if ($value == '') $value = $result->post_content;
		$value = oth_trim_excerpt($value, $len);
		break;
	}
	return $value;
}

function otf_snippet($option_key, $result, $ext) {
	if (!$ext) {
		$len = 100;
		$type = 'char';
	} else {
		$s = explode(':', $ext);
		if (count($s) == 1) {
			$s[] = 'char';
		}
		$len = $s[0];
		$type = $s[1];						
	}
	return oth_format_snippet($result->post_content, $option_key, $type, $len); 
}

function otf_snippetword($option_key, $result, $ext) {
	if (!$ext) $ext = 100;
	return oth_format_snippet($result->post_content, $option_key, 'word', $ext);
}

function otf_fullpost($option_key, $result, $ext) {
	$value = apply_filters('the_content', $result->post_content);
	return str_replace(']]>', ']]&gt;', $value);
}

function otf_commentcount($option_key, $result, $ext) {
	$value = $result->comment_count;
	if ($ext) {
		$s = explode(':', $ext);
		if (count($s) == 3) {
			if ($value == 0) $value = $s[0];
			elseif ($value == 1) $value .= ' ' . $s[1];
			else $value .= ' ' . $s[2];
		}
	}
	return $value;
}

function otf_commentexcerpt($option_key, $result, $ext) {
	return oth_trim_comment_excerpt($result->comment_content, $ext);
}

function otf_commentsnippet($option_key, $result, $ext) {
	if (!$ext) $ext = 100;
	return oth_format_snippet($result->comment_content, $option_key, 'char', $ext);
}

function otf_commentsnippetword($option_key, $result, $ext) {
	if (!$ext) $ext = 100;
	return oth_format_snippet($result->comment_content, $option_key, 'word', $ext);
}

function otf_commentdate($option_key, $result, $ext) {
	return oth_format_date($result->comment_date, $ext);
}

function otf_commenttime($option_key, $result, $ext) {
	return oth_format_time($result->comment_date, $ext);
}

function otf_commentdategmt($option_key, $result, $ext) {
	return oth_format_date($result->comment_date_gmt, $ext);
}

function otf_commenttimegmt($option_key, $result, $ext) {
	return oth_format_time($result->comment_date_gmt, $ext);
}

function otf_commenter($option_key, $result, $ext) {
	$value = $result->comment_author;
	$value = apply_filters('get_comment_author', $value);
	return apply_filters('comment_author', $value);
}

function otf_commenterurl($option_key, $result, $ext) {
	$value = $result->comment_author_url;
	$value = apply_filters('get_comment_author_url', $value);
	return oth_truncate_text($value, $ext);
}

function otf_commenterlink($option_key, $result, $ext) {
	$url = otf_commenterurl($option_key, $result, '');
	$author = otf_commenter($option_key, $result, '');
	$author = oth_truncate_text($author, $ext);
	if (empty($url) || $url == 'http://') $value = $author;
	else $value = "<a href='$url' rel='external nofollow'>$author</a>";
	return $value;
}

function oft_commenterip($option_key, $result, $ext) {
	return $result->comment_author_IP;
}

function otf_commenturl($option_key, $result, $ext) {
	$value = apply_filters('the_permalink', get_permalink($result->ID)) . '#comment-' . $result->comment_ID;
	return oth_truncate_text($value, $ext);
}

function otf_commentlink($option_key, $result, $ext) {
	$ttl = otf_commenter($option_key, $result, '');
	$ttl = '<span class="rc-commenter">' . $ttl . '</span>';
	if (!$ext) $ext = ' commented on ';
	$ttl .= $ext;
	$ttl .= '<span class="rc-title">'.otf_title($option_key, $result, '').'</span>';
	$pml = otf_commenturl($option_key, $result, '');
	$pdt = oth_format_date($result->comment_date_gmt, '');
	$pdt .= __(' at ', 'post_plugins');
	$pdt .= oth_format_time($result->comment_date_gmt, '');
	return "<a href=\"$pml\" rel=\"bookmark\" title=\"$pdt\">$ttl</a>";
}

function otf_commentlink2($option_key, $result, $ext) {
	$commenturl = otf_commenturl($option_key, $result, '');
	$commentdate = otf_commentdate($option_key, $result, '');
	$commenttime = otf_commenttime($option_key, $result, '');
	$title = otf_title($option_key, $result, '');
	$commenter = otf_commenter($option_key, $result, '');
	$commentexcerpt = otf_commentexcerpt($option_key, $result, '10');
	return "<a href=\"$commenturl\" rel=\"bookmark\" title=\"$commentdate at $commenttime on '$title'\">$commenter</a> - $commentexcerpt&hellip;";
}

function otf_catlinks($option_key, $result, $ext) {
return otf_categorylinks($option_key, $result, $ext);
}

function otf_categorylinks($option_key, $result, $ext) {
	$cats = get_the_category($result->ID);
	$value = ''; $n = 0;
	foreach ($cats as $cat) {
		if ($n > 0) $value .= $ext;
		$value .= '<a href="' . get_category_link($cat->cat_ID) . '" title="' . sprintf(__("View all posts in %s", 'post_plugins'), $cat->cat_name) . '" rel="category tag">'.$cat->cat_name.'</a>';
		++$n;
	}
	return $value;
}

function otf_catnames($option_key, $result, $ext) {
	return otf_categorynames($option_key, $result, $ext);
}

function otf_categorynames($option_key, $result, $ext) {
	$cats = get_the_category($result->ID);
	$value = ''; $n = 0;
	foreach ($cats as $cat) {
		if ($n > 0) $value .= $ext;
		$value .= $cat->cat_name;
		++$n;
	}
	return $value;
}

function otf_custom($option_key, $result, $ext) {
	$custom = get_post_custom($result->ID);
	return $custom[$ext][0];
}

function otf_tags($option_key, $result, $ext) {
	$tags = (array) get_the_tags($result->ID);
	$tag_list = array();
	foreach ( $tags as $tag ) {
		$tag_list[] = $tag->name;
	}
	if (!$ext) $ext = ', ';
	$tag_list = join( $ext, $tag_list );
	return $tag_list;
}

function otf_taglinks($option_key, $result, $ext) {
	$tags = (array) get_the_tags($result->ID);
	$tag_list = '';
	$tag_links = array();
	foreach ( $tags as $tag ) {
		$link = get_tag_link($tag->term_id);
		if ( is_wp_error( $link ) )
			return $link;
		$tag_links[] = '<a href="' . $link . '" rel="tag">' . $tag->name . '</a>';
	}
	if (!$ext) $ext = ' ';
	$tag_links = join( $ext, $tag_links );
	$tag_links = apply_filters( 'the_tags', $tag_links );
	$tag_list .= $tag_links;
	return $tag_list;
}

function otf_totalposts($option_key, $result, $ext) {
	global $wpdb;
	$value = '';
	if ($wp_version >= 2.1) {
		$value = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish'");
	} else {
		$value = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish'");
	}
	return $value;
}

function otf_totalpages($option_key, $result, $ext) {
	global $wpdb;
	$value = '';
	if ($wp_version >= 2.1) {
		$value = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish'");
	} else {
		$value = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'static'");	
	}
	return $value;
}

function otf_php($option_key, $result, $ext) {
	global $post;
	$value = '';
	if ($ext) {
		ob_start();
		eval($ext);
		$value = ob_get_contents();
		ob_end_clean();
	}
	return $value;
}

function otf_link($option_key, $result, $ext) {
	$ttl = otf_title($option_key, $result, $ext);
	$pml = otf_url($option_key, $result, null);
	$pdt = otf_date($option_key, $result, null);
	return "<a href=\"$pml\" rel=\"bookmark\" title=\"$pdt\">$ttl</a>";
}

function otf_score($option_key, $result, $ext) {						
	return sprintf("%.0f", $result->score);	
}

// tries to get the number of post views from a few popular plugins if the are installed
function otf_postviews($option_key, $result, $ext) {
	global $wpdb;
	// alex king's popularity contest
	if (class_exists('ak_popularity_contest')) $count = $akpc->get_post_total($result->ID);
	// my own post view count
	else if (function_exists('pvc_view_count')) $count = pvc_view_count($result->ID);
	// lester chan's postviews
	else if (function_exists('the_views')) {
		$count = get_post_custom($result->ID);
		$count = intval($count['views'][0]);
	}	
	// mark ghosh's top10
	else if (function_exists('show_post_count')) {$id = $result->ID; $count = $wpdb->get_var("select cntaccess from mostAccessed WHERE postnumber = $id");}
	// Ivan Djurdjevac's CountPosts
	else if (function_exists('HitThisPost')) {$id = $result->ID; $count = $wpdb->get_var("SELECT post_hits FROM $wpdb->posts WHERE ID=$id");}
	if (!$count) $count	= 0;
	return $count;
}

function otf_image($option_key, $result, $ext) {
	if (!preg_match_all('/<\s*img.+?>/', $result->post_content, $matches)) return '';
	if (!$ext) return $matches[0][0];
	$s = explode(':', $ext);
	$i = $s[0];
	$imgtag = $matches[0][$i];
	if (count($s) === 1) {
		return $imgtag;
	} else if (count($s) === 2) {
		$tsize = $s[1];
		// we try and produce a reduced view by playing with height and width
		preg_match('/\s+src\s*=\s*[\'|\"](.*?)[\'|\"]/s', $imgtag, $matches);
		if (function_exists('getimagesize')) {
			$imagesize = getimagesize($matches[1]);
			$current_width = $imagesize['0'];
			$current_height = $imagesize['1'];
			$width_ratio = $height_ratio = 1.0;
			if ($current_width > $tsize)
				$width_ratio = $tsize / $current_width;
			if ($current_height > $tsize)
				$height_ratio = $tsize / $current_height;
			// the smaller ratio is the one we need to fit it to the constraining box
			$ratio = min( $width_ratio, $height_ratio );
			$twidth = intval($current_width * $ratio);
			$theight = intval($current_height * $ratio);
		} else {
			$twidth = $theight = $tsize;		
		}
	} else if (count($s) === 3) {
		$twidth = $s[1];
		$theight = $s[2];
	}
	// remove height or width if present
	$imgtag = preg_replace('/(width|height)\s*=\s*[\'|\"](.*?)[\'|\"]/s', '', $imgtag);
	// insert the new size
	$imgtag = preg_replace('#/>#s', "height=\"$theight\" width=\"$twidth\" />", $imgtag);
	return $imgtag;
}

function otf_gravatar($option_key, $result, $ext) {
	$size = 96;
	$rating = '';
	$default = "http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=$size"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
	if ($ext) {
		$s = explode(':', $ext);
		if (isset($s[0])) $size = $s[0];
		if (isset($s[1])) $rating = $s[1];
		if (isset($s[3])) {
			$default = 'http:'.$s[3];
		} else {
			if (isset($s[2])) $default = 'http://'.$s[2];
		}	
	}
	$email = '';
	if (isset($result->comment_author_email)) {
		$email = $result->comment_author_email;
	} else {
		$user = get_userdata($result->post_author);
		if ($user) $email = $user->user_email;
	}
	if (!empty($email)) {
		$out = 'http://www.gravatar.com/avatar/';
		$out .= md5(strtolower($email));
		$out .= '?s='.$size;
		$out .= '&amp;d=' . urlencode( $default );
		if ('' !== $rating)
			$out .= "&amp;r={$rating}";
		$avatar = "<img alt='' src='{$out}' class='avatar avatar-{$size}' height='{$size}' width='{$size}' />";
	} else {
		$avatar = "<img alt='' src='{$default}' class='avatar avatar-{$size} avatar-default' height='{$size}' width='{$size}' />";
	}
	return apply_filters('get_avatar', $avatar, $email, $size, $default);
}

// returns the principal category id of a post -- if a cats are hierarchical chooses the most specific -- if multiple cats chooses the first (numerically smallest)
function otf_categoryid($option_key, $result, $ext) {
	$cats = get_the_category($result->ID);
	foreach ($cats as $cat) {
		$parents[] = $cat->category_parent;
	}
	foreach ($cats as $cat) {
		if (!in_array($cat->cat_ID, $parents)) $categories[] = $cat->cat_ID;
	}
	return $categories[0];
}

// fails if parentheses are out of order or nested
function oth_splitapart($subject) {
	$bits = explode(':', $subject);
	$inside = false;
	$newbits = array();
	$acc = '';
	foreach ($bits as $bit) {
		if (false !== strpos($bit, '{')) {
			$inside = true;
			$acc = '';
		}	
		if (false !== strpos($bit, '}')) {
			$inside = false;
			if ($acc !== '') {
				$acc .= ':' . $bit;
			} else {
				$acc = $bit;
			}
		}	
		if ($inside) {
			if ($acc !== '') {
				$acc .= ':' . $bit;
			} else {
				$acc = $bit;
			}
		} else {
			if ($acc !== '') {
				$newbits[] = $acc;
				$acc = '';
			} else {
				$newbits[] = $bit;
			}	
		}
	}
	return $newbits;
}

function otf_if($option_key, $result, $ext) {
	global $post;
	$condition = 'true';
	$true = '';
	$false = '';
	if ($ext) {
		$s = oth_splitapart($ext);
		if (isset($s[0])) $condition = $s[0];
		if (isset($s[1])) $true = $s[1];
		if (isset($s[2])) $false = $s[2];
	}
	if (strpos($condition, '{')!==false) {
		$condition = ppl_expand_template($result, $condition, ppl_prepare_template($condition), $option_key);
	}
	if (eval("return ($condition);")) $tag = $true; else $tag = $false;
	// if the replacement tag contains pseudotags replace them and expand them
	if (strpos($tag, '{')!==false) {
		$tag = ppl_expand_template($result, $tag, ppl_prepare_template($tag), $option_key);
	}
	return $tag;
}

function xotf_if($option_key, $result, $ext) {
	global $post;
	$condition = 'true';
	$true = '';
	$false = '';
	$left = '[';
	$right = ']';
	$colon = '|';
	if ($ext) {
		$s = explode(':', $ext);
		if (isset($s[0])) $condition = $s[0];
		if (isset($s[1])) $true = $s[1];
		if (isset($s[2])) $false = $s[2];
		if (isset($s[3])) $left = $s[3];
		if (isset($s[4])) $right = $s[4];
		if (isset($s[5])) $colon = $s[5];
	}
	if (eval("return ($condition);")) $tag = $true; else $tag = $false;
	// if the replacement tag contains pseudotags replace them and expand them
	if (strpos($tag, $left)!==false) {
		$tag = str_replace(array($left, $right, $colon), array('{', '}', ':'), $tag);
		$tag = ppl_expand_template($result, $tag, ppl_prepare_template($tag), $option_key);
	}
	return $tag;
}


// ****************************** Helper Functions *********************************************

function oth_truncate_text($text, $ext) {
	if (!$ext) {
		return $text;
	}
	$s = explode(':', $ext);
	if (count($s) > 2) {
		return $text;
	}
	if (count($s) == 1) {
		$s[] = 'wrap';
	}
	$length = $s[0];
	$type = $s[1];
	switch ($type) {
	case 'wrap':
		if (!function_exists('mb_detect_encoding')) {
			return wordwrap($text, $length, '<br />', true);
		} else {
			$e = mb_detect_encoding($text);
			$formatted = '';
			$position = -1;
			$prev_position = 0;
			$last_line = -1;
			while($position = mb_stripos($text, " ", ++$position, $e)) {
				if($position > $last_line + $length + 1) {
					$formatted.= mb_substr($text, $last_line + 1, $prev_position - $last_line - 1, $e).'<br />';
					$last_line = $prev_position;
				}
				$prev_position = $position;
			}
			$formatted.= mb_substr($text, $last_line + 1, mb_strlen( $text ), $e);
			return $formatted;
		}	
	case 'chop':
		if (!function_exists('mb_detect_encoding')) {
			 return substr($text, 0, $length);
		} else {
			$e = mb_detect_encoding($text);
			return mb_substr($text, 0, $length, $e);
		}	
	case 'trim':
		if (strlen($text) > $length) {
		} else {
			return $text;
		}	
		if (!function_exists('mb_detect_encoding')) {
			$textlen = strlen($text);
			if ($textlen > $length) {
				$text = substr($text, 0, $length-2);
				return rtrim($text,".").'&hellip;';
			} else {
				return $text;
			}
		} else {
			$e = mb_detect_encoding($text);
			$textlen = mb_strlen($text, $e);
			if ($textlen > $length) {
				$text = mb_substr($text, 0, $length-2, $e);
				return rtrim($text,".").'&hellip;';
			} else {
				return $text;
			}
		}	
	case 'snip':
		if (!function_exists('mb_detect_encoding')) {
			$textlen = strlen($text);
			if ($textlen > $length) {
				$b = floor(($length - 2)/2);
				$l = $textlen - $b - 1;
				return substr($text, 0, $b).'&hellip;'.substr($text, $l);
			} else {
				return $text;
			}
		} else {
			$e = mb_detect_encoding($text);
			$textlen = mb_strlen($text, $e);
			if ($textlen > $length) {
				$b = floor(($length - 2)/2);
				$l = $textlen - $b - 1;
				return mb_substr($text, 0, $b, $e).'&hellip;'.mb_substr($text, $l, 1000, $e);
			} else {
				return $text;
			}
		}	
	default:
		return wordwrap($t, $length, '<br />', true);
	}
}	

function oth_trim_extract($text, $len, $more) {
	$text = str_replace(']]>', ']]&gt;', $text);
	if(strpos($text, '<!--more-->')) {
		$parts = explode('<!--more-->', $text, 2);
		$text = $parts[0];
	} else {
		if ($len > count(preg_split('/[\s]+/', strip_tags($text), -1))) return $text;		
		// remove html entities for now	
		$text = str_replace("\x06", "", $text);
		preg_match_all("/&([a-z\d]{2,7}|#\d{2,5});/i", $text, $ents);
		$text = preg_replace("/&([a-z\d]{2,7}|#\d{2,5});/i", "\x06", $text);
		// now we start counting
		$parts = preg_split('/([\s]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$in_tag = false;
		$num_words = 0;
		$text = '';
		foreach($parts as $part) {
			if(0 < preg_match('/<[^>]*$/s', $part)) {
				$in_tag = true;
			} else if(0 < preg_match('/>[^<]*$/s', $part)) {
				$in_tag = false;
			}
			if(!$in_tag && '' != trim($part) && substr($part, -1, 1) != '>') {
				$num_words++;
			}
			$text .= $part;
			if($num_words >= $len && !$in_tag) break;
		}
		// put back the missing html entities
	    foreach ($ents[0] as $ent) $text = preg_replace("/\x06/", $ent, $text, 1);
	}
	$text = balanceTags($text, true);
	$text = $text . $more;
	return $text;
}

function oth_format_snippet($content, $option_key, $trim, $len) {
	$content = strip_tags($content);
	$p = get_option($option_key);
	$content = oth_strip_special_tags($content, $p['stripcodes']);
	$content = stripslashes($content);
	if (function_exists('mb_detect_encoding')) $enc = mb_detect_encoding($content);
	// grab a maximum number of characters
	if ($enc) {
		mb_internal_encoding($enc);
		$snippet = mb_substr($content, 0, $len);
		if ($trim == 'word' && mb_strlen($snippet) == $len) {
			// trim back to the last full word--NB if our snippet ends on a word
			// boundary we still have to trim back to the non-word character
			// (the final 's' in the pattern makes sure we match newlines)
			preg_match('/^(.*)\W/su', $snippet, $matches);
			//if we can't get a single full word we use the full snippet
			// (we use $matches[1] because we don't want the white-space)
			if ($matches[1]) $snippet = $matches[1];
		}
	} else {
		$snippet = substr($content, 0, $len);
		if ($trim == 'word' && strlen($snippet) == $len) {
			// trim back to the last full word--NB if our snippet ends on a word
			// boundary we still have to trim back to the non-word character
			// (the final 's' in the pattern makes sure we match newlines)
			preg_match('/^(.*)\W/s', $snippet, $matches);
			//if we can't get a single full word we use the full snippet
			// (we use $matches[1] because we don't want the white-space)
			if ($matches[1]) $snippet = $matches[1];
		}
	}
	return $snippet;
}

function oth_strip_special_tags($text, $stripcodes) {
		$numtags = count($stripcodes);
		for ($i = 0; $i < $numtags; $i++) {
			if (!$stripcodes[$i]['start'] || !$stripcodes[$i]['end']) return $text;
			$pattern = '/('. oth_regescape($stripcodes[$i]['start']) . '(.*?)' . oth_regescape($stripcodes[$i]['end']) . ')/i';
			$text = preg_replace($pattern, '', $text);
		}
		return $text;
}

function oth_trim_excerpt($content, $len) {
	// taken from the wp_trim_excerpt filter
	$text = $content;
	$text = apply_filters('the_content', $text);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	if (!$len) $len = 55; 
	$excerpt_length = $len;
	$words = explode(' ', $text, $excerpt_length + 1);
	if (count($words) > $excerpt_length) {
		array_pop($words);
		$text = implode(' ', $words);
	}
	$text = convert_smilies($text);
	return $text;
}

function oth_trim_comment_excerpt($content, $len) {
	// adapted from the wp_trim_excerpt filter
	$text = $content;
	$text = apply_filters('get_comment_text', $text);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	if (!$len) $len = 55; 
	$excerpt_length = $len;
	$words = explode(' ', $text, $excerpt_length + 1);
	if (count($words) > $excerpt_length) {
		array_pop($words);
		$text = implode(' ', $words);
	}
	$text = convert_smilies($text);
	return $text;
}
	
function oth_format_date($date, $fmt) {
		if (!$fmt) $fmt = get_option('date_format');
		$d = mysql2date($fmt, $date);
		$d = apply_filters('get_the_time', $d, $fmt);
		return apply_filters('the_time', $d, $fmt);
}

function oth_format_time($time, $fmt) {
		if (!$fmt) $fmt = get_option('time_format');
		$d = mysql2date($fmt, $time);
		$d = apply_filters('get_the_time', $d, $fmt);
		return apply_filters('the_time', $d, $fmt);
}

function oth_regescape($s) {
		$s = str_replace('\\', '\\\\', $s);
		$s = str_replace('/', '\\/', $s);
		$s = str_replace('[', '\\[', $s);
		$s = str_replace(']', '\\]', $s);
		return $s;
}

?>