<?php

$userconfirm = get_option('cforms'.$no.'_confirmerr');

// check URI
if (!isset($_SERVER['REQUEST_URI'])){
    if(isset($_SERVER['SCRIPT_NAME']))
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
    else
        $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
}

$uriprefix='';
if ( ($x=strpos($_SERVER['REQUEST_URI'],'/wp-admin/')) > 0 )
	$uriprefix = substr($_SERVER['REQUEST_URI'],0,$x);


echo '<form name="errmessages" action="#" method="post"><input type="hidden" name="switchform" value="'.$noDISP.'"/>';

if ( get_option('cforms'.$no.'_showpos')=='' && (($userconfirm&1)==0) ) {
		$text = sprintf(__('please check the <a href="%s" %s>success/failure message settings</a> and >>Show messages<< options below!', 'cforms'),'#anchormessage','onclick="setshow(1)"');
		showmesssage(1);
} 


if ( get_option('cforms'.$no.'_upload_dir')=='' && (($userconfirm&2)==0) ) {
		$text = sprintf(__('please check the new <a href="%s" %s>file upload/attachment</a> relevant settings below! You can ignore the message if you\'re not using any file upload field(s).', 'cforms'),'#fileupload','onclick="setshow(0)"');
		showmesssage(2);
} 


// check for set email header
if ( get_option('cforms'.$no.'_header')=='' && (($userconfirm&4)==0) ) {
		$text = sprintf(__('please check the <a href="%s" %s>email header settings</a> below!', 'cforms'),'#anchoremail','onclick="setshow(2)"');
		showmesssage(4);
} 

// check for URI prefix
if ( $uriprefix<>'' && (($userconfirm&8)==0) ) {
		$text = sprintf(__('It seems that your ROOT directory for Wordpress is %s. <strong>cforms</strong> tried to auto-adjust its settings accordingly, however if you still encounter issues with Ajax (form submission & CAPTCHA reset) please open the file %s in your cforms plugin folder and check the %s variable. (After changing the file, please emtpy your browser cache!)', 'cforms'),"<strong>$uriprefix</strong>",'<strong>js/cforms.js</strong>','<strong>sajax_uri</strong>');
		showmesssage(8);
} 

// check for TAF 
if ( substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' && (($userconfirm&16)==0) ) {
		$text = __('You have enabled the <strong>Tell a Friend</strong> feature for this form, please make sure you follow the guidelines on the HELP! page <strong>otherwise your form may not show</strong>!', 'cforms');
		showmesssage(16);
}

// 32 taken in global settings!

// set fancy errors by default
$tmp = get_option('cforms'.$no.'_showpos');
if ( strlen($tmp)<=2 ) {
	update_option('cforms'.$no.'_showpos', $tmp.'yy' );
	$text = __('Please note that the <strong>fancy error</strong> feature has been enabled. You can turn it off in the <em>Redirection, Messages...</em> section below.', 'cforms');
	showmesssage(64);
}

echo '</form>';

function showmesssage($confirm){
	global $no, $userconfirm, $text;
	
	if ( $confirm<8 )
		$text = __('It seems that you have recently upgraded cforms','cforms').' '.$text;
	
	if ( isset($_POST['confirm'.$confirm]) )
		update_option('cforms'.$no.'_confirmerr',($userconfirm|$confirm));
	else
		echo '<div id="message'.$confirm.'" class="updated fade"><p>'.$text.'</p><p><input class="rm_button" type="submit" name="confirm'.$confirm.'" value="'.__('Remove Message','cforms').'"></p></div>';
}

?>
