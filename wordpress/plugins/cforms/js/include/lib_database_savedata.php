<?php

require_once('../../../../../wp-config.php');

if ( !current_user_can('edit_users') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$f_id   = $_POST['id'];
$newVal = stripslashes($_POST['value']);

if ( $f_id<>'' ) {

	$sql="UPDATE {$wpdb->cformsdata} SET field_val='$newVal' WHERE f_id = '$f_id'";
	$entries = $wpdb->get_results($sql);
	echo str_replace("\n",'<br />',$newVal);
	
}
?>
