<?php
/*
Plugin Name: Most Commented
Plugin URI: http://mtdewvirus.com/code/wordpress-plugins/
Description: Retrieves a list of the posts with the most comments. Modified for Last X days -- by DJ Chuang www.djchuang.com 
Version: 1.4
Author: Nick Momrik
Author URI: http://mtdewvirus.com/
*/

function mdv_most_commented($no_posts = 5, $before = '<li>', $after = '</li>', $show_pass_post = false, $duration='') {
    global $wpdb;
	$request = "SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS 'comment_count' FROM $wpdb->posts, $wpdb->comments";
	$request .= " WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish'";
	if(!$show_pass_post) $request .= " AND post_password =''";

        if($duration !="") { $request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
}

	$request .= " GROUP BY $wpdb->comments.comment_post_ID ORDER BY comment_count DESC LIMIT $no_posts";
    $posts = $wpdb->get_results($request);
    $output = '';
	if ($posts) {
		foreach ($posts as $post) {
			$post_title = stripslashes($post->post_title);
			$comment_count = $post->comment_count;
			$permalink = get_permalink($post->ID);
			$output .= $before . '<a href="' . $permalink . '" title="' . $post_title.'">' . $post_title . '</a> (' . $comment_count.')' . $after;
		}
	} else {
		$output .= $before . "None found" . $after;
	}
    echo $output;
}
?>