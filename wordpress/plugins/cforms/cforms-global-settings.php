<?php
/* 
please see cforms.php for more information
*/
load_plugin_textdomain('cforms');

@session_start();

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';


// SMPT sever configured?
$smtpsettings=explode('$#$',get_option('cforms_smtp'));

### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms')) {
	die(__('Access Denied','cforms'));
}



// if all data has been erased quit
if ( get_option('cforms_formcount') == '' ){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>
	<?php
	return;
}


if( isset($_REQUEST['deleteall']) ) {  // erase all cforms data

	$alloptions =  $wpdb->query("DELETE FROM `$wpdb->options` WHERE option_name LIKE 'cforms%'");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

	?>
	<div id="message" class="updated fade"><p><strong><?php _e('All cforms related data has been deleted.', 'cforms') ?></strong></p></div>

	<div class="wrap">
	<h2><?php _e('Thank you for using cforms.', 'cforms') ?></h2>
	<p><?php _e('You can go straight to your <strong>Plugins</strong> tab and disable the plugin now!', 'cforms') ?></p>
	</div>
	<?php
	
	die;


} else if ( isset($_REQUEST['deletetables']) ) {

	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
	$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

	update_option('cforms_database', '0');

	?>
	<div id="message" class="updated fade">
		<p>
		<strong><?php echo sprintf (__('cforms tracking tables %s have been deleted.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong>
			<br />		
			<?php _e('Please backup/clean-up your upload directory, chances are that when you turn tracking back on, existing (older) attachments may be <u>overwritten</u>!') ?>
			<br />
			<small><?php _e('(only of course, if your form includes a file upload field)') ?></small>
		</p>
	</div>
	<?php

}



// Update Settings
if( isset($_REQUEST['Submit1']) || isset($_REQUEST['Submit2']) || isset($_REQUEST['Submit3']) || 
	isset($_REQUEST['Submit4']) || isset($_REQUEST['Submit5']) || isset($_REQUEST['Submit6']) ||
	isset($_REQUEST['Submit7']) || isset($_REQUEST['Submit8']) || isset($_REQUEST['Submit9']) ) {

//	update_option('cforms_linklove', $_REQUEST['cforms_linklove']?'1':'0');
	update_option('cforms_show_quicktag', 	$_REQUEST['cforms_show_quicktag']?'1':'0');
	update_option('cforms_sec_qa', 			$_REQUEST['cforms_sec_qa'] );
	update_option('cforms_codeerr', 		$_REQUEST['cforms_codeerr']);
	update_option('cforms_database', 		$_REQUEST['cforms_database']?'1':'0');
	update_option('cforms_showdashboard', 	$_REQUEST['cforms_showdashboard']?'1':'0');
	update_option('cforms_datepicker', 		$_REQUEST['cforms_datepicker']?'1':'0');
	update_option('cforms_dp_date', 		$_REQUEST['cforms_dp_date']);
	update_option('cforms_dp_days', 		$_REQUEST['cforms_dp_days']);
	update_option('cforms_dp_start', 		$_REQUEST['cforms_dp_start']);
	update_option('cforms_dp_months', 		$_REQUEST['cforms_dp_months']);

	$nav=array();
	$nav[0]=$_REQUEST['cforms_dp_prevY'];
	$nav[1]=$_REQUEST['cforms_dp_prevM'];
	$nav[2]=$_REQUEST['cforms_dp_nextY'];
	$nav[3]=$_REQUEST['cforms_dp_nextM'];
	$nav[4]=$_REQUEST['cforms_dp_close'];
	$nav[5]=$_REQUEST['cforms_dp_choose'];	
	update_option('cforms_dp_nav', 			$nav);

	update_option('cforms_include', 		$_REQUEST['cforms_include']);

	update_option('cforms_commentsuccess',	$_REQUEST['cforms_commentsuccess']);
	update_option('cforms_commentWait',		$_REQUEST['cforms_commentWait']);
	update_option('cforms_commentParent',	$_REQUEST['cforms_commentParent']);
	update_option('cforms_commentHTML',		$_REQUEST['cforms_commentHTML']);
	update_option('cforms_commentInMod',	$_REQUEST['cforms_commentInMod']);

	$smtpsettings[0]=$_REQUEST['cforms_smtp_onoff']?'1':'0';
	$smtpsettings[1]=$_REQUEST['cforms_smtp_host'];
	$smtpsettings[2]=$_REQUEST['cforms_smtp_user'];
	if ( !preg_match('/^\*+$/',$_REQUEST['cforms_smtp_pass']) ) {
		$smtpsettings[3]=$_REQUEST['cforms_smtp_pass'];
		}
	$smtpsettings[4]=$_REQUEST['cforms_smtp_ssltls'];
	$smtpsettings[5]=$_REQUEST['cforms_smtp_port'];

	update_option('cforms_smtp', implode('$#$',$smtpsettings) );

	update_option('cforms_upload_err1', $_REQUEST['cforms_upload_err1']);
	update_option('cforms_upload_err2', $_REQUEST['cforms_upload_err2']);
	update_option('cforms_upload_err3', $_REQUEST['cforms_upload_err3']);
	update_option('cforms_upload_err4', $_REQUEST['cforms_upload_err4']);
	update_option('cforms_upload_err5', $_REQUEST['cforms_upload_err5']);

	$captcha['w'] = $_REQUEST['cforms_cap_w'];
	$captcha['h'] = $_REQUEST['cforms_cap_h'];
	$captcha['c'] = $_REQUEST['cforms_cap_c'];
	$captcha['l'] = $_REQUEST['cforms_cap_l'];
	$captcha['bg']= $_REQUEST['cforms_cap_b'];
	$captcha['f'] = $_REQUEST['cforms_cap_f'];
	$captcha['f1']= $_REQUEST['cforms_cap_f1'];
	$captcha['f2']= $_REQUEST['cforms_cap_f2'];
	$captcha['a1']= $_REQUEST['cforms_cap_a1'];
	$captcha['a2']= $_REQUEST['cforms_cap_a2'];
	$captcha['c1']= $_REQUEST['cforms_cap_c1'];
	$captcha['c2']= $_REQUEST['cforms_cap_c2'];
	$captcha['ac']= $_REQUEST['cforms_cap_ac'];
	update_option('cforms_captcha_def', $captcha);

	// Setup database tables ?
	if ( isset($_REQUEST['cforms_database']) && $_REQUEST['cforms_database_new']=='true' ) {
	
		if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ){
			
			$sql = "CREATE TABLE " . $wpdb->cformssubmissions . " (
					  id int(11) unsigned auto_increment,
					  form_id varchar(3) default '',
					  sub_date timestamp,
					  email varchar(40) default '', 
					  ip varchar(15) default '', 
					  PRIMARY KEY  (id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			
			$sql = "CREATE TABLE " . $wpdb->cformsdata . " (
					  f_id int(11) unsigned auto_increment primary key, 
					  sub_id int(11) unsigned NOT NULL, 
					  field_name varchar(100) NOT NULL default '', 
					  field_val text) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);

			?>
			<div id="message" class="updated fade">
				<p><strong><?php echo sprintf(__('cforms tracking tables %s have been created.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong></p>
			</div>
			<?php
			
		} else {

			$sets = $wpdb->get_var("SELECT count(id) FROM $wpdb->cformssubmissions");
			?>
			<div id="message" class="updated fade">
				<p><strong><?php echo sprintf(__('Found existing cforms tracking tables with %s records!', 'cforms'),$sets) ?></strong></p>
			</div>
			<?php	
		}
	}
	
}
?>

<div class="wrap" id="top">
<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p2-title.jpg" alt=""/>

	<p><?php _e('All settings and configuration options on this page apply to all forms.', 'cforms') ?></p>

	<form id="cformsdata" name="mainform" method="post" action="">
	 <input type="hidden" name="cforms_database_new" value="<?php if(get_option('cforms_database')=="0") echo 'true'; ?>"/>

		<fieldset id="wpcomment" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit9" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#wpcomment';"/><a id="b28" class="blindminus" onfocus="this.blur()" onclick="toggleui(28);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('WP Comment Feature Settings', 'cforms') ?></p>

			<div id="o28">
				<p><?php _e('Find below the additional settings for cforms WP comment feature.', 'cforms') ?></p>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_commentsuccess"><strong><?php _e('Comment Success Message', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r16" class="rbox"><textarea rows="80px" cols="200px" name="cforms_commentsuccess" id="cforms_commentsuccess"><?php echo stripslashes(htmlspecialchars(get_option('cforms_commentsuccess'))); ?></textarea><div id="rh16"></div></div>
					</div>
				</div>
			
				<div class="optionsbox" style="margin-top:10px;margin-bottom:5px;">					
					<div class="optionsboxL"></div><div class="optionsboxR"><strong><?php _e('Ajax Settings', 'cforms'); ?></strong></div>
				</div>

				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_commentWait"><strong><?php _e('Wait time for new comments (in seconds)', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_commentWait" name="cforms_commentWait" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_commentWait') )); ?>"/></div>
				</div>
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_commentParent"><strong><?php _e('Parent Comment Container', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_commentParent" name="cforms_commentParent" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_commentParent') )); ?>"/><a class="infobutton" href="#" name="it8"><?php _e('Note &raquo;', 'cforms'); ?></a></div>
					<p class="infotxt ex" id="it8"><?php _e('The HTML <strong>element ID</strong> of the parent element containing<br />all comments, for example:', 'cforms'); ?><br />
						<code>
						&lt;/h2&gt;<br />
						&lt;ol id="<u style="color:#f37891">commentlist</u>"&gt;<br />
						&nbsp;&nbsp;&lt;li id="comment-126"&gt;<br />
						</code>
					</p>
				</div>
				
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_commentInMod"><strong><?php _e('Comment in moderation', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_commentInMod" name="cforms_commentInMod" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_commentInMod') )); ?>"/></div>
				</div>
				
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_commentHTML"><strong><?php _e('New comment HTML template', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r17" class="rbox"><textarea rows="80px" cols="200px" name="cforms_commentHTML" id="cforms_commentHTML"><?php echo stripslashes(htmlspecialchars(get_option('cforms_commentHTML'))); ?></textarea><div id="rh17"></div></div><a class="infobutton" href="#" name="it9"><?php _e('Supported Variables &raquo;', 'cforms'); ?></a>
						<br />
					</div>
					<div class="infotxt ex" id="it9">
						<table class="hf">
							<tr><td class="bleft">{moderation}</td><td class="bright"><em><?php _e('Comment in moderation', 'cforms'); ?></em></td></tr>
							<tr><td class="bleft">{id}</td><td class="bright"><?php _e('New comment ID', 'cforms'); ?></td></tr>
							<tr><td class="bleft">{usercomment}</td><td class="bright"><?php _e('Comment Text', 'cforms'); ?></td></tr>
							<tr><td class="bleft">{author}</td><td class="bright"><?php _e('Comment Author', 'cforms'); ?></td></tr>
							<tr><td class="bleft">{url}</td><td class="bright"><?php _e('The author\'s website', 'cforms'); ?></td></tr>
							<tr><td class="bleft">{date}</td><td class="bright"><?php _e('Current date.', 'cforms'); ?></td></tr>
							<tr><td class="bleft">{time}</td><td class="bright"><?php _e('Current time.', 'cforms'); ?></td></tr>
						</table></div>
				</div>
			</div>
		</fieldset>

		<fieldset id="inandexclude" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit8" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#popupdate';"/><a id="b27" class="blindminus" onfocus="this.blur()" onclick="toggleui(27);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Enabling cforms for specific pages', 'cforms') ?></p>

			<div id="o27">
				<p><?php _e('Specific the ID(s) of <strong>pages or posts</strong> separated by comma on which you\'d like to show cforms. The cforms header will only be included specifically on those pages, helping to maintain all other pages neat.', 'cforms') ?></p>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_include"><strong><?php _e('Page/Post ID(s)', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_include" name="cforms_include" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_include') )); ?>"/><br /><?php _e('Leave empty to enable for your entire blog', 'cforms') ?></div>
				</div>
			</div>
		</fieldset>

		<fieldset id="popupdate" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit6" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#popupdate';"/><a id="b9" class="blindminus" onfocus="this.blur()" onclick="toggleui(9);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Popup Date Picker', 'cforms') ?></p>

			<div id="o9">
				<p><?php echo sprintf(__('If you\'d like to offer a Javascript based date picker for more convenient date entry, enable this feature here. This will add a <strong>new input field</strong> for you to add to your form. See <a href="%s" %s>Help!</a> for more info and <strong>date formats</strong>.', 'cforms'),'?page='.$plugindir.'/cforms-help.php#datepicker','onclick="setshow(19)"') ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_datepicker" name="cforms_datepicker" <?php if(get_option('cforms_datepicker')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_datepicker"><strong><?php _e('Enable Javascript date picker', 'cforms') ?></strong></label> ** <a class="infobutton" href="#" name="it10"><?php _e('Note &raquo;', 'cforms'); ?></a></div>
					<p class="infotxt ex" id="it10"><?php _e('Note that turning on this feature will result in loading an additional Javascript file to support the date picker.', 'cforms') ?></p>
				</div>				
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_dp_date"><strong><?php _e('Date Format', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_date" name="cforms_dp_date" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_date') )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_days"><strong><?php _e('Days (Columns)', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_days" name="cforms_dp_days" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_days') )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_months"><strong><?php _e('Months', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_months" name="cforms_dp_months" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_months') )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<?php $nav = get_option('cforms_dp_nav'); ?>
					<div class="optionsboxL"><label for="cforms_dp_prevY"><strong><?php _e('Previous Year', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_prevY" name="cforms_dp_prevY" value="<?php echo stripslashes(htmlspecialchars( $nav[0] )); ?>"/></div>
					<div class="optionsboxL"><label for="cforms_dp_prevM"><strong><?php _e('Previous Month', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_prevM" name="cforms_dp_prevM" value="<?php echo stripslashes(htmlspecialchars( $nav[1] )); ?>"/></div>
					<div class="optionsboxL"><label for="cforms_dp_nextY"><strong><?php _e('Next Year', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_nextY" name="cforms_dp_nextY" value="<?php echo stripslashes(htmlspecialchars( $nav[2] )); ?>"/></div>
					<div class="optionsboxL"><label for="cforms_dp_nextM"><strong><?php _e('Next Month', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_nextM" name="cforms_dp_nextM" value="<?php echo stripslashes(htmlspecialchars( $nav[3] )); ?>"/></div>
					<div class="optionsboxL"><label for="cforms_dp_close"><strong><?php _e('Close', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_close" name="cforms_dp_close" value="<?php echo stripslashes(htmlspecialchars( $nav[4] )); ?>"/></div>
					<div class="optionsboxL"><label for="cforms_dp_choose"><strong><?php _e('Choose Date', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_choose" name="cforms_dp_choose" value="<?php echo stripslashes(htmlspecialchars( $nav[5] )); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_dp_start"><strong><?php _e('Week start day', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_dp_start" name="cforms_dp_start" value="<?php echo stripslashes(htmlspecialchars( get_option('cforms_dp_start') )); ?>"/> <?php _e('0=Sunday, 1=Monday, etc.', 'cforms'); ?></div>
				</div>
			
			</div>
		</fieldset>
		
		
		<fieldset id="smtp" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#smtp';"/><a id="b10" class="blindminus" onfocus="this.blur()" onclick="toggleui(10);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('SMTP Server Settings', 'cforms') ?><span style="font-size:10px; margin-left:10px;"><?php _e('In a normal WP environment you do not need to configure these settings!', 'cforms') ?></span></p>

			<div id="o10">
				<p><img style="vertical-align:middle;margin-right:10px;" src="<?php echo $cforms_root; ?>/images/phpmailer.png" alt="phpmailerV2"/><?php _e('In case your web hosting provider doesn\'t support the <strong>native PHP mail()</strong> command feel free to configure <strong>cforms</strong> to utilize an external <strong>SMTP mail server</strong> to deliver the emails.', 'cforms') ?></p>
				<?php
				$userconfirm = get_option('cforms_confirmerr');
				if ( $smtpsettings[0]=='1' && $smtpsettings[4]<>'' && ($userconfirm&32)==0 ){
					if ( isset($_POST['confirm32']) )
						update_option('cforms_confirmerr',($userconfirm|32));
					else {
						$text = '<strong>'.__('Important:','cforms').'</strong> '.__('If you require SSL / TLS make sure your webserver/PHP environment permits it! If in doubt, check with your web hosting company regarding <strong>openssl</strong> support.', 'cforms');
						echo '<div id="message32" class="updated fade"><p>'.$text.'</p><p><input class="rm_button" type="submit" name="confirm32" value="'.__('Remove Message','cforms').'"></p></div>';
					}
				}
				?>	
					
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_smtp_onoff" name="cforms_smtp_onoff" <?php if($smtpsettings[0]=="1") echo "checked=\"checked\""; ?>/><label for="cforms_smtp_onoff"><strong><?php _e('Enable an external SMTP server', 'cforms') ?></strong></label> ** <a class="infobutton" href="#" name="it11"><?php _e('Note &raquo;', 'cforms'); ?></a></div>
					<p class="infotxt ex" id="it11"><?php echo sprintf(__('To avoid additional sources of error, cformsII v6.4 and beyond includes the PHPmailer 2.0 scripts, now <strong>supporting</strong> both <strong>SSL</strong> and <strong>TLS</strong> for authentication.','cforms')); ?></p>
				</div>
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_smtp_host"><strong><?php _e('SMTP server address', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_host" name="cforms_smtp_host" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[1])); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_smtp_ssl"><strong><?php _e('Secure Connection', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">						
						<input type="radio" id="cforms_smtp_none" value="" name="cforms_smtp_ssltls" <?php echo ($smtpsettings[4]=='')?'checked="checked"':''; ?>/><label for="cforms_smtp_none"><strong><?php _e('No', 'cforms'); ?></strong></label><br />
						<input type="radio" id="cforms_smtp_ssl" value="ssl" name="cforms_smtp_ssltls" <?php echo ($smtpsettings[4]=='ssl')?'checked="checked"':''; ?>/><label for="cforms_smtp_ssl"><strong><?php _e('SSL (e.g. gmail)', 'cforms'); ?></strong></label><br />
						<input type="radio" id="cforms_smtp_tls" value="tls" name="cforms_smtp_ssltls" <?php echo ($smtpsettings[4]=='tls')?'checked="checked"':''; ?>/><label for="cforms_smtp_tls"><strong><?php _e('TLS', 'cforms'); ?></strong></label>
					</div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_smtp_port"><strong><?php _e('Port', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_port" name="cforms_smtp_port" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[5])); ?>"/> <?php _e('Usually 465 (e.g. gmail) or 587', 'cforms'); ?></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_smtp_user"><strong><?php _e('Username', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_user" name="cforms_smtp_user" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[2])); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_smtp_pass"><strong><?php _e('Password', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_smtp_pass" name="cforms_smtp_pass" value="<?php echo str_repeat('*',strlen($smtpsettings[3])); ?>"/></div>
				</div>
			
			</div>
		</fieldset>


		<fieldset id="upload" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a>
			<input type="submit" name="Submit3" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#upload';"/><a id="b11" class="blindminus" onfocus="this.blur()" onclick="toggleui(11);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Global File Upload Settings', 'cforms') ?></p>

			<div id="o11">
				<p>
					<?php echo sprintf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					<?php echo sprintf(__('Form specific settings (directory path etc.) have been moved to <a href="%s" %s>here</a>.', 'cforms'),'?page='.$plugindir.'/cforms-options.php#fileupload','onclick="setshow(0)"'); ?>
				</p>
	
				<p class="ex"><?php _e('Also, note that by adding a <em>File Upload Box</em> to your form, the Ajax (if enabled) submission method will (automatically) <strong>gracefully degrade</strong> to the standard method, due to general HTML limitations.', 'cforms') ?></p>
	
				<p style="padding-top:15px;"><?php _e('Specify error messages shown in case something goes awry:', 'cforms') ?></p>
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_upload_err5"><strong><?php _e('File type not allowed', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r8" class="rbox"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err5" id="cforms_upload_err5" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err5'))); ?></textarea><div id="rh8"></div></div>
					</div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err1"><strong><?php _e('Generic (unknown) error', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r9" class="rbox"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err1" id="cforms_upload_err1" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err1'))); ?></textarea><div id="rh9"></div></div>
					</div>
				</div>
				
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err2"><strong><?php _e('File is empty', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r10" class="rbox"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err2" id="cforms_upload_err2" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err2'))); ?></textarea><div id="rh10"></div></div>
					</div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err3"><strong><?php _e('File size too big', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r11" class="rbox"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err3" id="cforms_upload_err3" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err3'))); ?></textarea><div id="rh11"></div></div>
					</div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_err4"><strong><?php _e('Error during upload', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<div id="r12" class="rbox"><textarea rows="80px" cols="280px" class="errmsgbox" name="cforms_upload_err4" id="cforms_upload_err4" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_upload_err4'))); ?></textarea><div id="rh12"></div></div>
					</div>
				</div>

			</div>
		</fieldset>


		<fieldset id="wpeditor" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#wpeditor';"/><a id="b12" class="blindminus" onfocus="this.blur()" onclick="toggleui(12);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('WP Editor Button support', 'cforms') ?></p>

			<div id="o12">
				<p><?php _e('If you would like to use editor buttons to insert your cforms please enable them below.', 'cforms') ?></p>
		
				<div class="optionsbox">
					<div class="optionsboxL"><img src="<?php echo $cforms_root; ?>/images/button.gif" alt=""/></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_show_quicktag" name="cforms_show_quicktag" <?php if(get_option('cforms_show_quicktag')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_show_quicktag"><strong><?php _e('Enable TinyMCE', 'cforms') ?></strong> <?php _e('&amp; Code editor buttons', 'cforms') ?></label></div>
				</div>
			</div>
		</fieldset>

		<fieldset id="captcha" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit7" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#captcha';"/><a id="b26" class="blindminus" onfocus="this.blur()" onclick="toggleui(26);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('CAPTCHA Image Settings', 'cforms') ?></p>

			<div id="o26">						   
				<p><?php _e('Below you can find a few switches and options to change the default look of the captcha image. Feel free to upload your own backgrounds and fonts to the respective directories (<em>cforms/captchabg/</em> &amp; <em>cforms/captchafonts/</em>).', 'cforms') ?></p>
	
				<?php 
					$captcha = get_option('cforms_captcha_def'); 
					$h = ( $captcha['h']<>'' ) ? stripslashes(htmlspecialchars( $captcha['h'] )) : 25;
					$w = ( $captcha['w']<>'' ) ? stripslashes(htmlspecialchars( $captcha['w'] )) : 115;
					$c = ( $captcha['c']<>'' ) ? stripslashes(htmlspecialchars( $captcha['c'] )) : '000066';
					$l = ( $captcha['l']<>'' ) ? stripslashes(htmlspecialchars( $captcha['l'] )) : '000066';
					$f = ( $captcha['f']<>'' ) ? stripslashes(htmlspecialchars( $captcha['f'] )) : 'font4.ttf';
					$a1 = ( $captcha['a1']<>'' ) ? stripslashes(htmlspecialchars( $captcha['a1'] )) : -12;
					$a2 = ( $captcha['a2']<>'' ) ? stripslashes(htmlspecialchars( $captcha['a2'] )) : 12;
					$f1 = ( $captcha['f1']<>'' ) ? stripslashes(htmlspecialchars( $captcha['f1'] )) : 17;
					$f2 = ( $captcha['f2']<>'' ) ? stripslashes(htmlspecialchars( $captcha['f2'] )) : 19;
					$bg = ( $captcha['bg']<>'' ) ? stripslashes(htmlspecialchars( $captcha['bg'] )) : '1.gif';
					$c1 = ( $captcha['c1']<>'' ) ? stripslashes(htmlspecialchars( $captcha['c1'] )) : 4;
					$c2 = ( $captcha['c2']<>'' ) ? stripslashes(htmlspecialchars( $captcha['c2'] )) : 5;
					$ac = ( $captcha['ac']<>'' ) ? stripslashes(htmlspecialchars( $captcha['ac'] )) : 'abcdefghijkmnpqrstuvwxyz23456789';

					$_SESSION['turing_string_test'] = rc();

					$img = "ts=test&amp;w={$w}&amp;h={$h}&amp;c={$c}&amp;l={$l}&amp;f={$f}&amp;a1={$a1}&amp;a2={$a2}&amp;f1={$f1}&amp;f2={$f2}&amp;b={$bg}";
					
					$fonts = '<select name="cforms_cap_f" id="cforms_cap_f">'.cf_get_files('captchafonts',$f).'</select>';
					$backgrounds = '<select name="cforms_cap_b" id="cforms_cap_b">'.cf_get_files('captchabg',$bg).'</select>';

				?>

				<div class="optionsbox" style="margin:20px auto">
					<div class="optionsboxL"><strong><?php _e('Preview Image', 'cforms') ?></strong></div>
					<div class="optionsboxR">
						<img src="<?php echo $cforms_root; ?>/cforms-captcha.php?<?php echo $img; ?>" alt="<?php _e('Captcha Preview', 'cforms') ?>" title="<?php _e('Captcha Preview', 'cforms') ?>"/>
					</div>
				</div>


				<div style="position:absolute; z-index:9999;">
				<div id="mini" onmousedown="coreXY('mini',event)" style="top:0px; left:10px; display:none; margin-left:5%;">
					<div class="north"><span id="mHEX">FFFFFF</span><div onmousedown="$cfS('mini').display='none';">x</div></div>
					<div class="south" id="mSpec" style="HEIGHT: 128px; WIDTH: 128px;" onmousedown="coreXY('mCur',event)">
						<div id="mCur" style="TOP: 86px; LEFT: 68px;"></div>
						<img src="<?php echo $cforms_root; ?>/images/circle.png" onmousedown="return false;" ondrag="return false;" onselectstart="return false;" alt=""/>
						<img src="<?php echo $cforms_root; ?>/images/resize.gif" id="mSize" onmousedown="coreXY('mSize',event); return false;" ondrag="return false;" onselectstart="return false;" alt=""/>
					</div>
				</div>
				</div>
				
				<div class="optionsbox">

					<div class="optionsboxL"><label for="cforms_cap_w"><strong><?php _e('Width', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<input class="cap" type="text" id="cforms_cap_w" name="cforms_cap_w" value="<?php echo $w; ?>"/>
						<label for="cforms_cap_h" class="second-l"><strong><?php _e('Height', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_h" name="cforms_cap_h" value="<?php echo $h; ?>"/>
					</div>
					
					<div class="optionsboxL"><label for="inputID1"><strong><?php _e('Border Color', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<input class="cap" type="text" id="inputID1" name="cforms_cap_l" onclick="javascript:currentEL=1;" value="<?php echo $l; ?>"/><input type="button" name="col-border" class="colorswatch" style="background-color:#<?php echo $l; ?>" id="plugID1" onclick="this.blur(); currentEL=1; $cfS('mini').display='block';"/>
					</div>
					
					<div class="optionsboxL"><label for="cforms_cap_b"><strong><?php _e('Background Image', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<?php echo $backgrounds; ?>
					</div>
				</div>

				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_cap_f"><strong><?php _e('Font Type', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<?php echo $fonts; ?>
					</div>

					<div class="optionsboxL"><label for="cforms_cap_f1"><strong><?php _e('Min Size', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<input class="cap" type="text" id="cforms_cap_f1" name="cforms_cap_f1" value="<?php echo $f1; ?>"/>
						<label for="cforms_cap_f2" class="second-l"><strong><?php _e('Max Size', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_f2" name="cforms_cap_f2" value="<?php echo $f2; ?>"/>
					</div>

					<div class="optionsboxL"><label for="cforms_cap_a1"><strong><?php _e('Min Angle', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<input class="cap" type="text" id="cforms_cap_a1" name="cforms_cap_a1" value="<?php echo $a1; ?>"/>
						<label for="cforms_cap_a2" class="second-l"><strong><?php _e('Max Angle', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_a2" name="cforms_cap_a2" value="<?php echo $a2; ?>"/>
					</div>

					<div class="optionsboxL"><label for="inputID2"><strong><?php _e('Color', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<input class="cap" type="text" id="inputID2" name="cforms_cap_c" onclick="javascript:currentEL=2;" value="<?php echo $c; ?>"/><input type="button" name="col-border" class="colorswatch" style="background-color:#<?php echo $c; ?>" id="plugID2" onclick="this.blur(); currentEL=2; $cfS('mini').display='block';"/>
					</div>
				</div>

				<div class="optionsbox" style="margin-top:10px;">
					<strong style="padding:0 0 0 176px;"><?php _e('Number of shown characters', 'cforms') ?></strong><br />
					<div class="optionsboxL"><label for="cforms_cap_c1"><strong><?php _e('Minimum', 'cforms') ?></strong></label></div>
					<div class="optionsboxR">
						<input class="cap" type="text" id="cforms_cap_c1" name="cforms_cap_c1" value="<?php echo $c1; ?>"/>
						<label for="cforms_cap_c2" class="second-l"><strong><?php _e('Maximum', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_c2" name="cforms_cap_c2" value="<?php echo $c2; ?>"/>
					</div>

					<div class="optionsboxL"><label for="cforms_cap_ac"><strong><?php _e('Allowed Characters', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_cap_ac" name="cforms_cap_ac" value="<?php echo $ac; ?>"/></div>
				</div>
		
			</div>
		</fieldset>


		<fieldset id="visitorv" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit1" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#visitorv';"/><a id="b13" class="blindminus" onfocus="this.blur()" onclick="toggleui(13);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Visitor Verification Settings (Q&amp;A)', 'cforms') ?></p>

			<div id="o13">
				<p><?php _e('Getting a lot of <strong>SPAM</strong>? Use these Q&amp;A\'s to counteract spam and ensure it\'s a human submitting the form. To use in your form, add the corresponding input field "<code>Visitor verification</code>" preferably in its own FIELDSET!', 'cforms') ?></p>
		
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><a class="infobutton" href="#" name="it12"><?php _e('Note &raquo;', 'cforms'); ?></a></div>
						<p class="infotxt ex" id="it12">
							<?php _e('The below error/failure message is also used for <strong>captcha</strong> verification!', 'cforms') ?><br />
							<?php echo sprintf(__('Depending on your personal preferences and level of SPAM security you intend to put in place, you can also use <a href="%s" %s>cforms\' CAPTCHA feature</a>!', 'cforms'),'?page='.$plugindir.'/cforms-help.php#captcha','onclick="setshow(19)"'); ?>
						</p>
				</div>
					
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_codeerr"><?php _e('<strong>Failure message</strong><br />(for a wrong answer)', 'cforms'); ?></label></div>
					<div class="optionsboxR">
						<div id="r13" class="rbox"><textarea rows="80px" cols="280px" name="cforms_codeerr" id="cforms_codeerr" ><?php echo stripslashes(htmlspecialchars(get_option('cforms_codeerr'))); ?></textarea><div id="rh13"></div></div>
					</div>
				</div>
	
				<?php $qa = stripslashes(htmlspecialchars(get_option('cforms_sec_qa'))); ?>
		
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_sec_qa"><?php _e('<strong>Questions &amp; Answers</strong><br />format: Q=A', 'cforms') ?></label></div>
					<div class="optionsboxR">
						<div id="r14" class="rbox"><textarea rows="80px" cols="280px" name="cforms_sec_qa" id="cforms_sec_qa" ><?php echo $qa; ?></textarea><div id="rh14"></div></div>
					</div>
				</div>
		
			</div>
		</fieldset>


		<fieldset id="tracking" class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit2" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#tracking';"/><a id="b14" class="blindminus" onfocus="this.blur()" onclick="toggleui(14);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Database Input Tracking', 'cforms') ?></p>

			<div id="o14">
				<p><?php _e('If you like to track your form submissions also via the database, please enable this feature below. If required, this will create two new tables and you\'ll see a new sub tab "<strong>Tracking</strong>" under the cforms menu.', 'cforms') ?></p>
		
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><a class="infobutton" href="#" name="it13"><?php _e('Note &raquo;', 'cforms'); ?></a></div>
						<p class="infotxt ex" id="it13"><?php echo sprintf(__('If you\'ve enabled the <a href="%s" %s>auto confirmation message</a> feature or have included a <code>CC: me</code> input field, you can optionally configure the subject line/message of the email to include the form tracking ID by using the variable <code>{ID}</code>.', 'cforms'),'?page=' . $plugindir . '/cforms-options.php#autoconf','onclick="setshow(5)"'); ?></p>
				</div>
		
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_database" name="cforms_database" <?php if(get_option('cforms_database')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_database"><span class="abbr" title="<?php _e('Will create two new tables in your WP database.', 'cforms') ?>"><strong><?php _e('Enable Database Tracking', 'cforms') ?></strong></span></label></div>
				</div>

				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_showdashboard" name="cforms_showdashboard" <?php if(get_option('cforms_showdashboard')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_showdashboard"><span class="abbr" title="<?php _e('Make sure to enable your forms individually as well!', 'cforms') ?>"><strong><?php _e('Show last form submissions on dashboard.', 'cforms') ?></strong></span></label></div>
				</div>
				
				<?php if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions ) :?>
				<div class="optionsbox" style="margin-top:25px;">
					<div class="optionsboxL"><label for="deletetables"><?php _e('<strong>Wipe out</strong> all collected cforms submission data and drop tables.', 'cforms') ?></label></div>
					<div class="optionsboxR"><input id="deletetables" type="submit" title="<?php _e('Be careful with this one!', 'cforms') ?>" name="deletetables" class="allbuttons deleteall" value="<?php _e('Delete cforms Tracking Tables', 'cforms') ?>" onclick="return confirm('<?php _e('Do you really want to erase all collected data?', 'cforms') ?>');"/></div>
				</div>
				<?php endif; ?>

			</div>
		</fieldset>


		<fieldset class="cformsoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><a id="b15" class="blindminus" onfocus="this.blur()" onclick="toggleui(15);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Uninstalling / Removing cforms', 'cforms') ?></p>

			<div id="o15">
				<p><?php _e('Generally, deactivating this plugin does <strong>not</strong> erase any of its data, if you like to quit using cforms for good, please erase all data before deactivating the plugin.', 'cforms') ?></p>

				<p><?php _e('This erases <strong>all</strong> cforms data (form &amp; plugin settings). <strong>This is irrevocable!</strong> Be careful.', 'cforms') ?>&nbsp;&nbsp;&nbsp;
					 <input type="submit" name="deleteall" title="<?php _e('Are you sure you want to do this?!', 'cforms') ?>" class="allbuttons deleteall" value="<?php _e('DELETE *ALL* CFORMS DATA', 'cforms') ?>" onclick="return confirm('<?php _e('Do you really want to erase all of the plugin config data?', 'cforms') ?>');"/>
				</p>
			</div>
		</fieldset>


	</form>

	<?php cforms_footer(); ?>
</div>

<?php
function cf_get_files($dir,$currentfile){
	$fullplugindir	= ABSPATH . 'wp-content/plugins/' . dirname(plugin_basename(__FILE__));
	$presetsdir		= $fullplugindir.'/'.$dir.'/';
	$list 			= '';
	$allfiles		= array();
	
	if ($handle = opendir($presetsdir)) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." && $file != ".." && filesize($presetsdir.$file) > 0)
				array_push($allfiles,$file);
	    }
	    closedir($handle);
	}
	sort($allfiles);
	foreach( $allfiles as $file )
		$list .= '<option value="'.$file.'"'.(($file==$currentfile)?' selected="selected"':'').'>' .$file. '</option>';
		
    return ($list=='')?'<li>'.__('Not available','cforms').'</li>':$list;
}
?>
