<?php

### Common HTML message information
$styles="<HEAD>\n<style><!--\n".
		".fs-td { font:bold 1.2em Arial; letter-spacing:2px; border-bottom:2px solid #7babfb; padding:10px 0 5px; text-align:center; background:#ddecff;}\n".
		".data-td { font-weight:bold; padding-right:20px; vertical-align:top; }\n".
		".datablock { background:#c1ddff; width:90%; padding:2px;}\n".
		".cforms { font:normal 10px Arial; color:#777;}\n".
		"--></style>\n</HEAD>";

### SMPT sever configured?
$smtpsettings=explode('$#$',get_option('cforms_smtp'));

if ( $smtpsettings[0]=='1' ) {
	if ( file_exists(dirname(__FILE__) . '/phpmailer/class.phpmailer.php') )
		require_once(dirname(__FILE__) . '/phpmailer/cforms_phpmailer.php');
	else
		$smtpsettings[0]=='0';
}

### other global stuff
$track = array(); 
$Ajaxpid = '';
$AjaxURL = '';

### prep captcha get call
function get_captcha_uri() {
	$captcha = get_option('cforms_captcha_def'); 
	$h = prep( $captcha['h'],25 );
	$w = prep( $captcha['w'],115 );
	$c = prep( $captcha['c'],'000066' );
	$l = prep( $captcha['l'],'000066' );
	$f = prep( $captcha['f'],'font4.ttf' );
	$a1 = prep( $captcha['a1'],-12 );
	$a2 = prep( $captcha['a2'],12 );
	$f1 = prep( $captcha['f1'],17 );
	$f2 = prep( $captcha['f2'],19 );
	$bg = prep( $captcha['bg'],'1.gif');
	return "&amp;w={$w}&amp;h={$h}&amp;c={$c}&amp;l={$l}&amp;f={$f}&amp;a1={$a1}&amp;a2={$a2}&amp;f1={$f1}&amp;f2={$f2}&amp;b={$bg}";
}


### captcha random code
function rc() {
	$captcha = get_option('cforms_captcha_def'); 
	$min = prep( $captcha['c1'],4 );
	$max = prep( $captcha['c2'],5 );
	$src = prep( $captcha['ac'], 'abcdefghijkmnpqrstuvwxyz23456789');

	$srclen = strlen($src)-1;	
	$length = mt_rand($min,$max);
	$Code = '';
	for($i=0; $i<$length; $i++) 
		$Code .= substr($src, mt_rand(0, $srclen), 1);
	
	return $Code;
}

### strip stuff
function prep($v,$d) {
	return ($v<>'') ? stripslashes(htmlspecialchars($v)) : $d;
}

### Special Character Suppoer in subject lines
function encode_header ($str) {
	$x = preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
	
	if ($x == 0)
		return ($str);

	$maxlen = 75 - 7 - strlen( get_option('blog_charset') );
	
	$encoded = base64_encode($str);
	$maxlen -= $maxlen % 4;
	$encoded = trim(chunk_split($encoded, $maxlen, "\n"));
	
	$encoded = preg_replace('/^(.*)$/m', " =?".get_option('blog_charset')."?B?\\1?=", $encoded);
	$encoded = trim($encoded);
	
	return $encoded;
}

### write DB record
function write_tracking_record($no,$field_email){
		global $wpdb, $track;

		if ( get_option('cforms_database') == '1'  ) {

			$page = (get_option('cforms'.$no.'_tellafriend')=='2')?$_POST['cforms_pl'.$no]:get_current_page(); // WP comment fix

			$wpdb->query("INSERT INTO $wpdb->cformssubmissions (form_id,email,ip,sub_date) VALUES ".
						 "('" . $no . "', '" . $field_email . "', '" . cf_getip() . "', '".gmdate('Y-m-d H:i:s', current_time('timestamp'))."');");
	
    		$subID = $wpdb->get_row("select LAST_INSERT_ID() as number from $wpdb->cformssubmissions;");
    		$subID = ($subID->number=='')?'1':$subID->number;

			$sql = "INSERT INTO $wpdb->cformsdata (sub_id,field_name,field_val) VALUES " .
						 "('$subID','page','$page'),";

			foreach ( array_keys($track) as $key ){
				if( !preg_match('/^\$\$\$/',$key) )
					$sql .= "('$subID','".addslashes($key)."','".addslashes($track[$key])."'),";
			}
			
			$wpdb->query(substr($sql,0,-1));			
		}
		else
			$subID = 'noid';
			
	return $subID;
}

### replace standard & custom variables in message/subject text
function get_current_page(){
	global $Ajaxpid;

	if ( strpos($_SERVER['REQUEST_URI'],'?')>0 )
		$page = substr( $_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'?')-1);
	else
		$page = $_SERVER['REQUEST_URI'];
	
	$page = (trim($page)=='' || strpos($page,'lib_ajax.php')!==false )?$_SERVER['HTTP_REFERER']:trim($page); // for ajax
	return $page;
	
}

### look for default/system variables
function check_default_vars($m,$no) {
		global $subID, $Ajaxpid, $AjaxURL, $post, $wpdb, $wp_db_version;

		if ( $_POST['comment_post_ID'.$no] )
			$pid = $_POST['comment_post_ID'.$no];
		else if ( $Ajaxpid<>'' )
			$pid = $Ajaxpid;
		else if ( function_exists('get_the_ID') )
			$pid = get_the_ID();

		if ( $_POST['cforms_pl'.$no] )
			$permalink = $_POST['cforms_pl'.$no];
		else if ( $Ajaxpid<>'' )
			$permalink = $AjaxURL;
		else if ( function_exists('get_permalink') && function_exists('get_userdata') )
			$permalink = get_permalink($pid);
		
		###		
		### if the "month" is not spelled correctly, try the commented out line instead of the one after
		###
		### $date = utf8_encode(html_entity_decode( mysql2date(get_option('date_format'), current_time('mysql')) ));
		$date = mysql2date(get_option('date_format'), current_time('mysql'));
		
		$time = gmdate(get_option('time_format'), current_time('timestamp'));
		$page = get_current_page();
				
		if ( get_option('cforms'.$no.'_tellafriend')=='2' ) // WP comment fix
			$page = $permalink;

		$find = $wpdb->get_row("SELECT p.post_title, p.post_excerpt, u.display_name FROM $wpdb->posts AS p LEFT JOIN ($wpdb->users AS u) ON p.post_author = u.ID WHERE p.ID='$pid'");

		if ( $wp_db_version >= 3440 ) //&& function_exists( 'wp_get_current_user' )
			$CurrUser = wp_get_current_user();
		
		$m 	= str_replace( '{Form Name}',	get_option('cforms'.$no.'_fname'), $m );
		$m 	= str_replace( '{Page}',		$page, $m );
		$m 	= str_replace( '{Date}',		$date, $m );
		$m 	= str_replace( '{Author}',		$find->display_name, $m );
		$m 	= str_replace( '{Time}',		$time, $m );
		$m 	= str_replace( '{IP}',			cf_getip(), $m );
		$m 	= str_replace( '{BLOGNAME}',	get_option('blogname'), $m );

		$m 	= str_replace( '{CurUserID}',	$CurrUser->ID, $m );
		$m 	= str_replace( '{CurUserName}',	$CurrUser->display_name, $m );
		$m 	= str_replace( '{CurUserEmail}',$CurrUser->user_email, $m );
		
		$m 	= str_replace( '{Permalink}',	$permalink, $m );
		$m 	= str_replace( '{Title}',		$find->post_title, $m );
		$m 	= str_replace( '{Excerpt}',		$find->post_excerpt, $m );

		$m 	= preg_replace( "/\r\n\./", "\r\n", $m );			
		
		if  ( get_option('cforms_database') && $subID<>'' )
			$m 	= str_replace( '{ID}', $subID, $m );
							 
		return $m;
}

### look for custom variables
function check_cust_vars($m,$t,$no) {

	preg_match_all('/\\{([^\\{]+)\\}/',$m,$findall);

	if ( count($findall[1]) > 0 ) {
		$allvars = array_keys($t);

		foreach ( $findall[1] as $fvar ) {

			$fTrackedVar = $fvar;
			
			### convert _fieldXYZ to actual label name tracked...
			if ( strpos($fvar,'_field')!==false ){
				$fNo = substr($fvar,6);
				if ( $allvars[$fNo]<>'' )
					$fTrackedVar = $t['$$$'.$fNo];  ### reset $fvar to actual label name and continue
			}

			### convert if alt [id:] used
			if ( in_array( '$$$'.$fTrackedVar, $allvars ) ){
				if ( $t['$$$'.$fTrackedVar]<>'' )
					$fTrackedVar = $t['$$$'.$fTrackedVar];  ### reset $fvar to actual label name and continue
			}
			
			### check if label name is tracked...
			if( in_array( $fTrackedVar,$allvars ) )
				$m = str_replace('{'.$fvar.'}', $t[$fTrackedVar], $m);

		}
	}
	return $m;
}

### Can't use WP's function here, so lets use our own
if ( !function_exists('cf_getip') ) :
function cf_getip() {
	if (isset($_SERVER)) {
 		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
 		elseif (isset($_SERVER["HTTP_CLIENT_IP"]))	$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
 		else										$ip_addr = $_SERVER["REMOTE_ADDR"];
	} else {
 		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) 	$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
 		elseif ( getenv( 'HTTP_CLIENT_IP' ) )	  	$ip_addr = getenv( 'HTTP_CLIENT_IP' );
 		else										$ip_addr = getenv( 'REMOTE_ADDR' );
	}
	return $ip_addr;
}
endif;
?>
