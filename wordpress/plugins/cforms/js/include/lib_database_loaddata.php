<?php

require_once('../../../../../wp-config.php');

if ( !current_user_can('edit_users') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

$f_id   = $_POST['id'];

if ( $f_id<>'' ) {

	$sql="SELECT field_val FROM {$wpdb->cformsdata} WHERE f_id = '$f_id'";
	echo $wpdb->get_var($sql);
	//echo str_replace("\n",'<br />',$newVal);
	
}
?>
