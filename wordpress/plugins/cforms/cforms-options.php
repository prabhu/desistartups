<?php

### 
### please see cforms.php for more information
### 

load_plugin_textdomain('cforms');

$plugindir   = dirname(plugin_basename(__FILE__));
$cforms_root = get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### Check Whether User Can Manage Database
if(!current_user_can('manage_cforms'))
	die(__('Access Denied','cforms'));


### default to 1 & get real #
$FORMCOUNT=get_option('cforms_formcount');

### if all data has been erased quit
if ($FORMCOUNT == ''){
	?>
	<div class="wrap">
	<h2><?php _e('All cforms data has been erased!', 'cforms') ?></h2>
	<p><?php _e('Please go to your <strong>Plugins</strong> tab and either disable the plugin, or toggle its status (disable/enable) to revive cforms!', 'cforms') ?></p>
	</div>
	<?php
	return;
}

if(isset($_REQUEST['addbutton'])){
	require_once(dirname(__FILE__) . '/lib_options_add.php');

} elseif(isset($_REQUEST['dupbutton'])) {
	require_once(dirname(__FILE__) . '/lib_options_dup.php');

} elseif( isset($_REQUEST['uploadcformsdata']) ) {
	require_once(dirname(__FILE__) . '/lib_options_up.php');

} elseif(isset($_REQUEST['delbutton']) && $FORMCOUNT>1 && $_REQUEST['no']<>'1') {
	require_once(dirname(__FILE__) . '/lib_options_del.php');

} else {

	### set paramters to default, if not exists
	$noDISP='1';$no='';
	if( isset($_REQUEST['switchform']) ) { ### only set when hitting form chg buttons
		if( $_REQUEST['switchform']<>'1' )
			$noDISP = $no = $_REQUEST['switchform'];
	}
	else if( isset($_REQUEST['go']) ) { ### only set when hitting form chg buttons
		if( $_REQUEST['pickform']<>'1' )
			$noDISP = $no = $_REQUEST['pickform'];
	}
	else{
		if( isset($_REQUEST['noSub']) && (int)$_REQUEST['noSub']>1 ) ### otherwise stick with the current form
			$noDISP = $no = $_REQUEST['noSub'];
	}
  
}

### PRESETS
if ( isset($_REQUEST['formpresets']) )
	require_once(dirname(__FILE__) . '/lib_options_presets.php');

 
### default: $field_count = what's in the DB
$field_count = get_option('cforms'.$no.'_count_fields');


### check if T-A-F action is required
$alldisabled=false;
$allenabled=0;
if( isset($_REQUEST['addTAF']) || isset($_REQUEST['removeTAF']) )
{
	
	$posts = $wpdb->get_results("SELECT ID FROM $wpdb->posts");

	if ( isset($_REQUEST['addTAF']) ){
	
		foreach($posts as $post) {
			if ( add_post_meta($post->ID, 'tell-a-friend', '1', true) )
				$allenabled++;
		}
		
	} else if ( isset($_REQUEST['removeTAF']) ){
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = 'tell-a-friend'");
		$alldisabled=true;
	}	

}


### new field added (will actually be added below!)
if( isset($_REQUEST['AddField']) && isset($_REQUEST['field_count_submit']) )
{
		$field_count = $_POST['field_count_submit'] + $_POST['AddFieldNo'];
		update_option('cforms'.$no.'_count_fields', $field_count);
}


### set to nothing
$usermsg='&nbsp;';

### Update Settings
if( isset($_REQUEST['Submit1']) || isset($_REQUEST['Submit2']) || isset($_REQUEST['Submit3']) || isset($_REQUEST['Submit4']) || 
    isset($_REQUEST['Submit5']) || isset($_REQUEST['Submit6']) || isset($_REQUEST['Submit7']) || isset($_REQUEST['Submit8']) || 
	isset($_REQUEST['Submit9']) || isset($_REQUEST['AddField']) || array_search("X", $_REQUEST) ) {

	require_once(dirname(__FILE__) . '/lib_options_sub.php');
}


### delete field if we find one and move the rest up
$deletefound = 0;
if(strlen(get_option('cforms'.$no.'_count_field_' . $field_count)) > 0) {

	$temp_count = 1;
	while($temp_count <= $field_count) {
	
		if(isset($_REQUEST['DeleteField' . $temp_count])) {
			$deletefound = 1;
			update_option('cforms'.$no.'_count_fields', ($field_count - 1));
		}
		
		if($deletefound && $temp_count<$field_count) {
			$temp_val = get_option('cforms'.$no.'_count_field_' . ($temp_count+1));
			update_option('cforms'.$no.'_count_field_' . ($temp_count), $temp_val);
		}
		
		$temp_count++;
	} ### while

	if($deletefound == 1) {  ### now delete
	  delete_option('cforms'.$no.'_count_field_' . $field_count);
		$field_count--;
	}

} ### if


### check possible errors
require_once(dirname(__FILE__) . '/lib_options_err.php');


### 
### prep drop down box for form selection
### 
$formlistbox = ' <select id="pickform" name="pickform">';

for ($i=1; $i<=$FORMCOUNT; $i++){

	$j   = ( $i > 1 )?$i:'';
	$sel = ($noDISP==$i)?' selected="selected"':'';

  $formlistbox .= '<option value="'.$i.'" '.$sel.'>'.stripslashes(get_option('cforms'.$j.'_fname')).'</option>';
}
$formlistbox .= '</select><input type="submit" class="allbuttons go" name="go" value="'.__('Go', 'cforms').'"/>';


### make sure at least the default FROM: address is set
if ( get_option('cforms'.$no.'_fromemail') == '' ) 
	update_option('cforms'.$no.'_fromemail', '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>');


### check if HTML needs to be enabled
$fd=get_option('cforms'.$no.'_formdata');
if( strlen($fd)<=2 ) {
	$fd .= ( get_option('cforms'.$no.'_header_html')<>''  )?'1':'0';
	$fd .= ( get_option('cforms'.$no.'_cmsg_html')<>'' )?'1':'0';
	update_option('cforms'.$no.'_formdata',$fd);
}


?>
<div class="wrap" id="top">

	<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p1-title.jpg" alt=""/>

	<form name="chgform" method="post" action="#">
			<div class="chgformbox">
				<div class="chgL" title="<?php _e('Navigate to your other forms.', 'cforms') ?>"><?php echo $formlistbox; ?></div>
				<div class="chgR">
					<input class="allbuttons addbutton" type="submit" name="addbutton" title="<?php _e('adds a new form with default values', 'cforms'); ?>" value="<?php _e('Add New Form', 'cforms'); ?>"/>&nbsp;&nbsp;
			    	<input class="allbuttons dupbutton" type="submit" name="dupbutton" title="<?php _e('clones the current form', 'cforms'); ?>" value="<?php _e('Duplicate This Form', 'cforms'); ?>"/>
			    	<?php
			      if ( (int)$noDISP > 1)
			        echo '<input class="allbuttons deleteall" title="'.__('This will delete the current form - no warning!', 'cforms').'" type="submit" name="delbutton" value="'.__('Delete THIS Form(!)', 'cforms').'"/>';
			      ?>
				</div>
				<div class="chgM">
					<?php
			    	for ($i=1; $i<=$FORMCOUNT; $i++) {
			    		$j   = ( $i > 1 )?$i:'';
			     		echo '<input title="'.stripslashes(get_option('cforms'.$j.'_fname')).'" class="allbuttons chgbutton'.(($i <> $noDISP)?'':'hi').'" type="submit" name="switchform" value="'.$i.'"/>';
		     		}
			    	?>
			 </div>
			</div>	
	  <input type="hidden" name="no" value="<?php echo $noDISP; ?>"/>
	</form>

	<p>
		<?php echo sprintf(__('This plugin allows you <a href="%s" %s>to insert</a> one or more custom designed contact forms, which on submission (preferably via Ajax) will send the visitor info via email and optionally stores the feedback in the database.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#inserting','onclick="setshow(18)"'); ?>
		<?php echo sprintf(__('<a href="%s" %s>Here</a> is a quick step by step quide to get you up and running quickly.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#guide','onclick="setshow(17)"'); ?>
	</p>


	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="#anchorfields">

	<fieldset id="anchorfields">
		<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>" />

		<p class="mainoptions">
			<input type="submit" name="Submit1" class="allbuttons updbutton formupd" value="<?php _e('Update Settings &raquo;', 'cforms') ?>" onclick="javascript:document.mainform.action='#';" />
			<label for="cforms_fname" class="bignumber"><?php _e('Form Name', 'cforms') ?></label>
			<input id="cforms_fname" name="cforms_fname" class="cforms_fname" size="40" value="<?php echo stripslashes(get_option('cforms'.$no.'_fname'));  ?>" title="<?php _e('You may give each form an optional name to better identify incoming emails.', 'cforms') ?>"/>
			<input id="cforms_ajax" type="checkbox" class="cforms_ajax" name="cforms_ajax" <?php if(get_option('cforms'.$no.'_ajax')=="1") echo "checked=\"checked\""; ?>/>
			<label for="cforms_ajax" class="bignumber"><?php _e('Ajax enabled', 'cforms') ?></label>
			<input type="button" class="jqModalInstall allbuttons" name="<?php echo $cforms_root; ?>/js/include/" id="preset" value="<?php _e('Install a form preset', 'cforms'); ?>"/>
		</p>

		<p>
			<?php echo sprintf(__('Please see the <strong>Help!</strong> section for information on how to deploy the various <a href="%s" %s>supported fields</a>,', 'cforms'),'?page='.$plugindir.'/cforms-help.php#fields','onclick="setshow(19)"') . ' ' .
					   sprintf(__('set up forms using <a href="%s" %s>FIELDSETS</a>,', 'cforms'),'?page='.$plugindir.'/cforms-help.php#hfieldsets','onclick="setshow(19)"') . 
					   sprintf(__('use <a href="%s" %s>default values</a> &amp; <a href="%s" %s>regular expressions</a> (<em>specific field validation; check</em> <code>Required</code>!) for single &amp; multi-line fields.', 'cforms'),'?page='.$plugindir.'/cforms-help.php#single','onclick="setshow(19)"','?page='.$plugindir.'/cforms-help.php#regexp','onclick="setshow(19)"') .
					   sprintf(__('Besides the generic success &amp; failure messages below, you can add <a href="%s" %s>custom error messages</a>.', 'cforms'),'?page='.$plugindir.'/cforms-help.php#customerr','onclick="setshow(20)"'); ?>
		</p>

		<p class="ex" id="cformswarning"><?php echo __('Please save the new order of fields (<em>Update Settings</em>)!','cforms'); ?></p>

		<ul class="tableheader">
			<li class="field1th"><?php _e('No.', 'cforms'); ?></li>
			<li class="field2th" title="<?php _e('Can be a simple label or a more complex expression. See Help!', 'cforms'); ?>"><span class="abbr"><?php _e('Field Name', 'cforms'); ?></span></li>
			<li class="field3th" title="<?php _e('Pick one of the supported input field types.', 'cforms'); ?>"><span class="abbr"><?php _e('Type', 'cforms'); ?></span></li>
			<li class="field4th"><span class="abbr"><img src="<?php echo $cforms_root; ?>/images/ic_required.gif" title="<?php _e('Makes an input field required for proper form validation.', 'cforms'); ?>" alt="<?php _e('Required', 'cforms'); ?>" /></span></li>
			<li class="field5th"><span class="abbr"><img src="<?php echo $cforms_root; ?>/images/ic_email.gif" title="<?php _e('Makes the field required and verifies the email address.', 'cforms'); ?>" alt="<?php _e('E-Mail', 'cforms'); ?>" /></span></li>
			<li class="field6th"><span class="abbr"><img src="<?php echo $cforms_root; ?>/images/ic_clear.gif" title="<?php _e('Clears the field (default value) upon focus.', 'cforms'); ?>" alt="<?php _e('Auto Clear', 'cforms'); ?>" /></span></li>
			<li class="field7th"><span class="abbr"><img src="<?php echo $cforms_root; ?>/images/ic_disabled.gif" title="<?php _e('Grey\'s out a form field (field will be completely disabled).', 'cforms'); ?>" alt="<?php _e('Disabled', 'cforms'); ?>" /></span></li>
			<li class="field8th"><span class="abbr"><img src="<?php echo $cforms_root; ?>/images/ic_readonly.gif" title="<?php _e('Form field will be readonly!', 'cforms'); ?>" alt="<?php _e('Read-Only', 'cforms'); ?>" /></span></li>
		</ul>

				
	<div id="allfields" class="groupWrapper">
					
						<?php
						### pre-check for verification field
						$ccboxused=false;
						$emailtoboxused=false;
						$verificationused=false;
						$captchaused=false;
						$fileupload=false; ### only for hide/show options
						
						for($i = 1; $i <= $field_count; $i++) {

								$allfields[$i] = get_option('cforms'.$no.'_count_field_' . $i);

								if ( strpos($allfields[$i],'verification')!==false )	$verificationused = true;
								if ( strpos($allfields[$i],'captcha')!==false )			$captchaused = true;
								if ( strpos($allfields[$i],'emailtobox')!==false )		$emailtoboxused = true;
								if ( strpos($allfields[$i],'ccbox')!==false )			$ccboxused = true;
								if ( strpos($allfields[$i],'upload')!==false )			$fileupload = true;

						}

						$alternate=' ';
						
						for($i = 1; $i <= $field_count; $i++) {
								$field_stat = explode('$#$', $allfields[$i]);
								$field_name = __('New Field', 'cforms');
								$field_type = 'textfield';
								$field_required = '0';
								$field_emailcheck = '0';
								$field_clear = '0';
								$field_disabled = '0';
								$field_readonly = '0';

								if(sizeof($field_stat) >= 3) {
									$field_name = stripslashes(htmlspecialchars($field_stat[0]));
									$field_type = $field_stat[1];
									$field_required = $field_stat[2];
									$field_emailcheck = $field_stat[3];
									$field_clear = $field_stat[4];
									$field_disabled = $field_stat[5];
									$field_readonly = $field_stat[6];
								}
								else if(sizeof($field_stat) == 1){
									add_option('cforms'.$no.'_count_field_' . $i, __('New Field', 'cforms').'$#$textfield$#$0$#$0$#$0$#$0$#$0');
								}

           				 switch ( $field_type ) {
							case 'emailtobox':	$specialclass = 'style="background:#CBDDFE"'; break;
							case 'ccbox':		$specialclass = 'style="background:#D8FFCA"'; break;
							case 'verification':
							case 'captcha':		$specialclass = 'style="background:#FFCDCA"'; break;
							case 'textonly':	$specialclass = 'style="background:#f0f0f0"'; break;
							case 'fieldsetstart':
							case 'fieldsetend':	$specialclass = 'style="background:#ECFEA5"'; break;
							default:			$specialclass = ''; break;
							}
						
						$alternate = ($alternate=='')?' rowalt':''; ?>

						<div id="f<?php echo $i; ?>" class="groupItem<?php echo $alternate; ?>">

							<div class="itemContent">
																
								<span class="itemHeader<?php echo ($alternate<>'')?' altmove':''; ?>" title="<?php _e('Drag me','cforms')?>">&nbsp;</span>						
								<strong class="fieldno"><?php echo (($i<10)?'0':'').$i; ?></strong>
								
								<input title="<?php _e('Please enter field definition', 'cforms'); ?>" class="inpfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_name" id="field_<?php echo($i); ?>_name" size="30" value="<?php echo ($field_type == 'fieldsetend')?'--':$field_name; ?>" /><a href="#" class="jqModal" title="<?php echo $cforms_root.'/js/include/'; ?>"><img class="wrench" src="<?php echo $cforms_root; ?>/images/wrench.gif" alt="<?php _e('edit', 'cforms'); ?>" title="<?php _e('edit', 'cforms'); ?>"/></a><select title="<?php _e('Pick a field type', 'cforms'); ?>" class="fieldtype selfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_type" id="field_<?php echo($i); ?>_type">

									<option value="fieldsetstart" <?php echo($field_type == 'fieldsetstart'?' selected="selected"':''); ?>><?php _e('New Fieldset', 'cforms'); ?></option>
									<option value="textonly" <?php echo($field_type == 'textonly'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Text only (no input)', 'cforms'); ?></option>
									<option value="textfield" <?php echo($field_type == 'textfield'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Single line of text', 'cforms'); ?></option>
									<option value="textarea" <?php echo($field_type == 'textarea'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Multiple lines of text', 'cforms'); ?></option>
									<option value="checkbox" <?php echo($field_type == 'checkbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Check Box', 'cforms'); ?></option>
									<option value="checkboxgroup" <?php echo($field_type == 'checkboxgroup'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Check Box Group', 'cforms'); ?></option>
									<option value="selectbox" <?php echo($field_type == 'selectbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Select Box', 'cforms'); ?></option>
									<option value="multiselectbox" <?php echo($field_type == 'multiselectbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Multi Select Box', 'cforms'); ?></option>
									<option value="radiobuttons" <?php echo($field_type == 'radiobuttons'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Radio Buttons', 'cforms'); ?></option>
									<?php if ( !$ccboxused || $field_type=="ccbox" ) : ?>
									<option value="ccbox" <?php echo($field_type == 'ccbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('CC: option for user', 'cforms'); ?></option>
									<?php	endif; ?>
									<?php if ( !$emailtoboxused || $field_type=="emailtobox" ) : ?>
									<option value="emailtobox" <?php echo($field_type == 'emailtobox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Multiple Recipients', 'cforms'); ?></option>
									<?php	endif; ?>
									<?php if ( !$verificationused || $field_type=="verification" ) : ?>
										<option value="verification" <?php echo($field_type == 'verification'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Visitor verification (Q&amp;A)', 'cforms'); ?></option>
									<?php	endif; ?>
									<?php if ( !$captchaused || $field_type=="captcha" ) : ?>
										<option value="captcha" <?php echo($field_type == 'captcha'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Captcha verification (image)', 'cforms'); ?></option>
									<?php	endif; ?>
									<?php ### if ( !$uploadused || $field_type=="upload" ) : ?>
										<option value="upload" <?php echo($field_type == 'upload'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('File Upload Box', 'cforms'); ?></option>
									<?php ### endif; ?>

									<?php if ( get_option('cforms_datepicker')=='1' ) : ?>
										<option value="datepicker" <?php echo($field_type == 'datepicker'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Date Entry/Dialog', 'cforms'); ?></option>
									<?php endif; ?>
									<option value="pwfield" <?php echo($field_type == 'pwfield'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Password Field', 'cforms'); ?></option>
									<option value="hidden" <?php echo($field_type == 'hidden'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Hidden Field', 'cforms'); ?></option>

									<option value="fieldsetend" <?php echo($field_type == 'fieldsetend'?' selected="selected"':''); ?>><?php _e('End Fieldset', 'cforms'); ?></option>

									<?php if ( substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' ) : ?>
										<option value=""><?php _e('------------ special --------------', 'cforms'); ?></option>
										<option value="yourname" <?php echo($field_type == 'yourname'?' selected="selected"':''); ?>><?php _e('T-A-F * Your Name', 'cforms'); ?></option>
										<option value="youremail" <?php echo($field_type == 'youremail'?' selected="selected"':''); ?>><?php _e('T-A-F * Your Email', 'cforms'); ?></option>
										<option value="friendsname" <?php echo($field_type == 'friendsname'?' selected="selected"':''); ?>><?php _e('T-A-F * Friend\'s Name', 'cforms'); ?></option>
										<option value="friendsemail" <?php echo($field_type == 'friendsemail'?' selected="selected"':''); ?>><?php _e('T-A-F * Friend\'s Email', 'cforms'); ?></option>
									<?php endif; ?>


									<?php if ( get_option('cforms'.$no.'_tellafriend')=='2' ) : ?>
										<option value=""><?php _e('------------ special --------------', 'cforms'); ?></option>
										<option value="cauthor" <?php echo($field_type == 'cauthor'?' selected="selected"':''); ?>><?php _e('Comment Author', 'cforms'); ?></option>
										<option value="email" <?php echo($field_type == 'email'?' selected="selected"':''); ?>><?php _e('Author\'s Email', 'cforms'); ?></option>
										<option value="url" <?php echo($field_type == 'url'?' selected="selected"':''); ?>><?php _e('Author\'s URL', 'cforms'); ?></option>
										<option value="comment" <?php echo($field_type == 'comment'?' selected="selected"':''); ?>><?php _e('Author\'s Comment', 'cforms'); ?></option>
										<option value="send2author" <?php echo($field_type == 'send2author'?' selected="selected"':''); ?>><?php _e('Select: Email/Comment', 'cforms'); ?></option>
									<?php endif; ?>

								</select><?php
										echo '<input '.(($field_count<=1)?'disabled="disabled"':'').' class="'.(($field_count<=1)?'noxbutton':'xbutton').'" type="submit" name="DeleteField'.$i.'" value="" title="'.__('Remove input field', 'cforms').'" alt="'.__('Remove input field', 'cforms').'" onfocus="this.blur()"/>';

										if( in_array($field_type,array('hidden','checkboxgroup', 'radiobuttons','fieldsetstart','fieldsetend','ccbox','captcha','verification','textonly')) )
											echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Not available.', 'cforms').'"/>';
										else
											echo '<input class="fieldisreq chkfld" type="checkbox" name="field_'.($i).'_required" value="required"'.($field_required == '1'?' checked="checked"':'').'/>';
	
	
										if( ! in_array($field_type,array('textfield','youremail','friendsemail','email')) )
											echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Not available.', 'cforms').'"/>';
										else
											echo '<input class="fieldisemail chkfld" type="checkbox" name="field_'.($i).'_emailcheck" value="required"'.($field_emailcheck == '1'?' checked="checked"':'').'/>';
										
										
										if( ! in_array($field_type,array('pwfield','textarea','textfield','datepicker','yourname','youremail','friendsname','friendsemail','email','author','url','comment')) )
											echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Not available.', 'cforms').'"/>';
										else
											echo '<input class="fieldclear chkfld" type="checkbox" name="field_'.($i).'_clear" value="required"'.($field_clear == '1'?' checked="checked"':'').'/>';


										if( ! in_array($field_type,array('pwfield','textarea','textfield','datepicker','checkbox','checkboxgroup','selectbox','multiselectbox','radiobuttons','upload')) )
											echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Not available.', 'cforms').'"/>';
										else
											echo '<input class="fielddisabled chkfld" type="checkbox" name="field_'.($i).'_disabled" value="required"'.($field_disabled == '1'?' checked="checked"':'').'/>';


										if( ! in_array($field_type,array('pwfield','textarea','textfield','datepicker','checkbox','checkboxgroup','selectbox','multiselectbox','radiobuttons','upload')) )
											echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Not available.', 'cforms').'"/>';
										else
											echo '<input class="fieldreadonly chkfld" type="checkbox" name="field_'.($i).'_readonly" value="required"'.($field_readonly == '1'?' checked="checked"':'').'/>';

							?></div> <!--itemContent-->

						</div> <!--groupItem-->
						
				<?php	}	### for  ?>
			</div> <!--groupWrapper-->

		<div class="addfieldbox"><input type="text" name="AddFieldNo" value="1" class="addfieldno"/><input type="submit" name="AddField" class="addfield" title="<?php _e('Add another input field', 'cforms'); ?>" value=" " onfocus="this.blur()" onclick="javascript:document.mainform.action='#anchorfields';" /></div>
		<input type="hidden" name="field_order" value="" />
		<input type="hidden" name="field_count_submit" value="<?php echo($field_count); ?>" />

		<p class="backup">
				<input type="submit" name="savecformsdata" class="allbuttons backupbutton"  value="<?php _e('Backup This Form', 'cforms'); ?>" onclick="javascript:document.mainform.action='#';" />
				<label for="upload"><?php _e(' or restore previously saved settings:', 'cforms'); ?></label>
				<input type="file" id="upload" name="import" size="25" />
				<input type="submit" name="uploadcformsdata" class="allbuttons restorebutton" value="<?php _e('Restore Settings', 'cforms'); ?>" onclick="javascript:document.mainform.action='#';" />
		</p>
		
	</fieldset>


	<fieldset id="fileupload" class="cformsoptions <?php if( !$fileupload) echo "hidden"; ?>">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit5" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#fileupload';" /><a id="b0" class="blindminus" onfocus="this.blur()" onclick="toggleui(0);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('File Upload Settings', 'cforms') ?></p>

			<div id="o0">
				<p>
					<?php echo sprintf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					<?php echo sprintf(__('You may also want to verify the global, file upload specific  <a href="%s" %s>error messages</a>.', 'cforms'),'?page='.$plugindir.'/cforms-global-settings.php#upload','onclick="setshow(11)"'); ?>
				</p>
	
			    <?php
			    $temp = explode( '$#$',stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_dir'))) );
			    $fileuploaddir = $temp[0];
			    $fileuploaddirurl = $temp[1];			    
				if ( !file_exists($fileuploaddir) ) {
			        echo '<div class="updated fade"><p>' . __('Can\'t find the specified <strong>Upload Directory</strong> ! Please verify that it exists!', 'cforms' ) . '</p></div>';
			    }
				?>
				
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_upload_dir"><strong><?php _e('Upload directory', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_upload_dir" name="cforms_upload_dir" value="<?php echo $fileuploaddir; ?>"/> <?php _e('[make sure the dir exists!]', 'cforms') ?></div>
				</div>

				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_upload_dir_url"><strong><?php _e('Upload directory', 'cforms') ?> URL</strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_upload_dir_url" name="cforms_upload_dir_url" value="<?php echo $fileuploaddirurl; ?>"/> <?php _e('[if outside of ../wp-content/..]', 'cforms') ?></div>
				</div>
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_upload_ext"><strong><?php _e('Allowed file extensions', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_upload_ext" name="cforms_upload_ext" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_ext'))); ?>"/> <?php _e('[empty=all files are allowed]', 'cforms') ?></div>
				</div>
	
				<div class="optionsbox" style="margin-top:3px;">
					<div class="optionsboxL"><label for="cforms_upload_size"><strong><?php _e('Maximum file size<br />in kilobyte', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_upload_size" name="cforms_upload_size" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_upload_size'))); ?>"/></div>
				</div>
	
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_noattachments"><strong><?php _e('Do not email attachments', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_noattachments" name="cforms_noattachments" <?php if(get_option('cforms'.$no.'_noattachments')=="1") echo "checked=\"checked\""; ?>/><br /><?php echo sprintf(__('<u>Note</u>: Attachments are stored on the server &amp; can be accessed via the <a href="%s" %s>cforms tracking</a> tables.', 'cforms'),'?page='. $plugindir.'/cforms-global-settings.php#tracking','onclick="setshow(14)"'); ?></div>
				</div>
				
			</div>
		</fieldset>
			

		<fieldset class="cformsoptions" id="anchormessage">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit3" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchormessage';" /><a id="b1" class="blindminus" onfocus="this.blur()" onclick="toggleui(1);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Redirection, Messages, Text and Button Label', 'cforms') ?></p>

			<div id="o1">
				<p><?php echo sprintf(__('These are the messages displayed to the user on successful (or failed) form submission. These messages are form specific, a general message for entering a wrong <strong>visitor verification code</strong> can be found <a href="%s" %s>here</a>.', 'cforms'),'?page='.$plugindir.'/cforms-global-settings.php#visitorv','onclick="setshow(13)"'); ?></p>
	
				<br />
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_submit_text"><strong><?php _e('Submit button text', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input name="cforms_submit_text" id="cforms_submit_text" value="<?php echo (get_option('cforms'.$no.'_submit_text'));  ?>" /></div>
				</div>

				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_working"><strong><?php _e('Waiting message', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input name="cforms_working" id="cforms_working" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_working')));  ?>" /></div>
				</div>
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_required"><strong><?php _e('"required" label', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_required" id="cforms_required" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_required'))); ?>"/></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_emailrequired"><strong><?php _e('"email required" label', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_emailrequired" id="cforms_emailrequired" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_emailrequired'))); ?>"/></div>
				</div>
				
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_success"><?php _e('<strong>Success message</strong><br />form filled out correctly', 'cforms'); ?></label></div>
					<div class="optionsboxR">
						<div id="r1" class="rbox" style="float:left"><textarea rows="80px" cols="200px" name="cforms_success" id="cforms_success"><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_success'))); ?></textarea><div id="rh1"></div></div>
						<div style="float:left"><input type="checkbox" id="cforms_popup1" name="cforms_popup1" <?php if(substr(get_option('cforms'.$no.'_popup'),0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup1"><?php _e('Opt. Popup Msg', 'cforms'); ?></label></div>
					</div>
				</div>
				
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_failure"><?php _e('<strong>Failure message</strong><br />missing fields or wrong field<br />formats (regular expr.)', 'cforms'); ?></label></div>
					<div class="optionsboxR">
						<div id="r2" class="rbox" style="float:left"><textarea rows="80px" cols="200px" name="cforms_failure" id="cforms_failure" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_failure'))); ?></textarea><div id="rh2"></div></div>
						<div style="float:left"><input type="checkbox" id="cforms_popup2" name="cforms_popup2" <?php if(substr(get_option('cforms'.$no.'_popup'),1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup2"><?php _e('Opt. Popup Msg', 'cforms'); ?></label></div>
					</div>
				</div>
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_showposa"><strong><?php _e('Show message<br />(summaries)', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_showposa" name="cforms_showposa" <?php if(substr(get_option('cforms'.$no.'_showpos'),0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposa"><?php _e('Above form', 'cforms'); ?></label><br />
						<input type="checkbox" id="cforms_showposb" name="cforms_showposb" <?php if(substr(get_option('cforms'.$no.'_showpos'),1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposb"><?php _e('Below form', 'cforms'); ?></label>
					</div>
				</div>

				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_jump"><strong><?php _e('Jump to Error', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_jump" name="cforms_jump" <?php if(substr(get_option('cforms'.$no.'_showpos'),4,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_jump"><?php _e('(Only Javascript)', 'cforms'); ?></label>
					</div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_errorLI"><strong><?php _e('Fancy Error messages', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_errorLI" name="cforms_errorLI" <?php if(substr(get_option('cforms'.$no.'_showpos'),2,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_errorLI"><?php _e('Enhanced display of errors (see also new theme CSS declarations)', 'cforms'); ?></label>
					</div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_errorINS"><strong><?php _e('Embedded Custom Error<br />Messages', 'cforms'); ?></strong></label></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_errorINS" name="cforms_errorINS" <?php if(substr(get_option('cforms'.$no.'_showpos'),3,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_errorINS"><?php _e('Field specific error messages will be shown above input field', 'cforms'); ?></label>
					</div>
				</div>

				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><strong><?php _e('Limit', 'cforms'); ?></strong></div>
					<div class="optionsboxR"><input type="text" id="cforms_maxentries" name="cforms_maxentries" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_maxentries'))); ?>"/><label for="cforms_maxentries"><?php _e('<u>total</u> # of form submissions accepted [<strong>empty = no limit</strong>] (tracking must be enabled!)', 'cforms') ?></label></div>
				</div>

				<?php if( get_option('cforms'.$no.'_maxentries') <> '' ) : ?>		
					<div class="optionsbox">
						<div class="optionsboxL"><label for="cforms_limittxt"><strong><?php _e('Limit text', 'cforms'); ?></strong></label></div>
						<div class="optionsboxR">
							<div id="r3" class="rbox"><textarea rows="80px" cols="200px" name="cforms_limittxt" id="cforms_limittxt" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_limittxt'))); ?></textarea><div id="rh3"></div></div>
						</div>
					</div>
				<?php endif; ?>
				
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_redirect"><?php _e('<strong>Redirect</strong><br />options:', 'cforms'); ?></label></div>
					<div class="optionsboxR">
						<input type="radio" id="cforms_redirect"  name="cforms_redirect" value="0" <?php if(get_option('cforms'.$no.'_redirect')==0) echo "checked=\"checked\""; ?>/><label for="cforms_redirect"><?php _e('Disabled (default setting)', 'cforms'); ?></label><br />
						<input type="radio" id="cforms_redirect2" name="cforms_redirect" value="2" <?php if(get_option('cforms'.$no.'_redirect')==2) echo "checked=\"checked\""; ?>/><label for="cforms_redirect2"><?php _e('Hide form after successful submission', 'cforms'); ?></label><br />
						<input type="radio" id="cforms_redirect1" name="cforms_redirect" value="1" <?php if(get_option('cforms'.$no.'_redirect')==1) echo "checked=\"checked\""; ?>/><label for="cforms_redirect1"><?php _e('Enable alternative success page (redirect)', 'cforms'); ?></label><br />
						<input name="cforms_redirect_page" id="cforms_redirect_page" value="<?php echo (get_option('cforms'.$no.'_redirect_page'));  ?>" />
					</div>
				</div>
		
				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_action"><?php _e('<strong>Send form data</strong><br /> to an alternative page:', 'cforms'); ?></label></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_action" name="cforms_action" <?php if(get_option('cforms'.$no.'_action')) echo "checked=\"checked\""; ?>/><label for="cforms_action"><?php _e('Enable alternative form action!', 'cforms'); ?></label><br />
						<input name="cforms_action_page" id="cforms_action_page" value="<?php echo (get_option('cforms'.$no.'_action_page'));  ?>" /> <a class="infobutton" href="#" name="it2"><?php _e('Please read note &raquo;', 'cforms'); ?></a>
					</div>
				</div>

				<p class="infotxt ex" id="it2"><?php _e('If you enable an alternative <strong>form action</strong> you <u>will loose any cforms application logic</u> (spam security, field validation, DB tracking etc.) in non-ajax mode! The settings below are really only for developers that require additional capabilities around forwarding of form data. These settings turn cforms into a front-end only, a "form builder" so to speak.', 'cforms') ?></p>

			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="anchoremail">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit4" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#anchoremail';" /><a id="b2" class="blindminus" onfocus="this.blur()" onclick="toggleui(2);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Core Form Admin / Email Options', 'cforms') ?></p>

			<div id="o2">
				<p><?php echo sprintf(__('These settings will be used for the email sent to you. Both %s and %s formats are valid, but check if your mailserver does accept the format of choice!', 'cforms'),'"<strong>xx@yy.zz</strong>"','"<strong>abc &lt;xx@yy.zz&gt;</strong>"') ?></p>
				<p><?php _e('The default FROM: address is based on your blog\'s name and the WP default address. It can be changed, but I highly recommend you do not, as it may render the plugin useless. If you do change the FROM: address, triple check if all admin emails are being sent/received! ', 'cforms') ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_fromemail"><strong><?php _e('FROM: email address', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_fromemail" id="cforms_fromemail" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_fromemail'))); ?>" /></div>
				</div>
	
				<div class="optionsbox" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_email"><strong><?php _e('Admin email address(es)', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_email" id="cforms_email" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_email'))); ?>" /> <a class="infobutton" href="#" name="it1"><?php _e('More than one "<strong>form admin</strong>"? &raquo;', 'cforms'); ?></a></div>
				</div>
				<p class="infotxt ex" id="it1"><?php echo sprintf(__('Simply add additional email addresses separated by a <strong style="color:red">comma</strong>. &nbsp; <em><u>Note:</u></em> &nbsp; If you want the visitor to choose from any of these per select box, you need to add a corresponding "<code>Multiple Recipients</code>" input field <a href="#anchorfields">above</a> (see the HELP section for <a href="%s" %s>details</a> on the <em>field name</em> format expected!). If <strong>no</strong> "Multiple Recipients" input field is defined above, the submitted form data will go out to <strong>every address listed</strong>!', 'cforms'),'?page='.$plugindir.'/cforms-help.php#multirecipients','onclick="setshow(19)"'); ?></p>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_bcc"><strong><?php _e('BCC', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_bcc" id="cforms_bcc" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_bcc'))); ?>" /></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_subject"><strong><?php _e('Subject admin email', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_subject" id="cforms_subject" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_subject'))); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></div>
				</div>
	
				<?php if( get_option('cforms_showdashboard') == '1' ) : ?>
					<div class="optionsbox" style="margin-top:15px;">
						<div class="optionsboxL"></div>
						<div class="optionsboxR"><input type="checkbox" id="cforms_dashboard" name="cforms_dashboard" <?php if($o=get_option('cforms'.$no.'_dashboard')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_dashboard"><?php _e('Include in <strong>last 5 entries shown</strong> on the dashboard.', 'cforms') ?></label></div>
					</div>
				<?php endif; ?>

				
				<p class="infotxt ex" id="it4"><?php _e('This feature replaces the default NAMEs/IDs (e.g. <strong>cf_field_12</strong>) with <em>custom ones</em>, either derived from the field label you have provided or by specifically declaring it via <strong>[id:XYZ]</strong>,e.g. <em>Your Name[id:the-name]</em>. This will for instance help to feed data to third party systems (requiring certain input field names in the $_POST variable).', 'cforms') ?></p>

				<div class="optionsbox" style="margin-top:20px;">
					<div class="optionsboxL"><label for="cforms_customnames"><strong><?php _e('Use custom input<br />field NAMES &amp; ID\'s', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_customnames" name="cforms_customnames" <?php if(get_option('cforms'.$no.'_customnames')=='1') echo "checked=\"checked\""; ?>/> <a class="infobutton" href="#" name="it4"><?php _e('Please read note &raquo;', 'cforms'); ?></a></div>
				</div>

				<p class="infotxt ex" id="it5">
					<?php echo sprintf(__('There a <a href="%s" %s>three additional</a>, <em>predefined variables</em> that belong to the Tell-A-Friend feature but can be enabled here without actually turning on T-A-F.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#tafvariables','onclick="setshow(23)"'); ?>
					<strong><u><?php _e('Note:','cforms')?></u></strong> <?php _e('This will add two more hidden fields to your form to ensure that all data is available also in AJAX mode.','cforms')?>
				</p>

				<div class="optionsbox" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_taftrick"><strong><?php _e('Extra variables<br />e.g. {Title}', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_taftrick" name="cforms_taftrick" <?php if(get_option('cforms'.$no.'_tellafriend')=='3') echo "checked=\"checked\""; ?>/> <a class="infobutton" href="#" name="it5"><?php _e('Please read note &raquo;', 'cforms'); ?></a></div>
				</div>

			</div>											
		</fieldset>


		<fieldset class="cformsoptions" id="emailoptions">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit6" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#emailoptions';" /><a id="b3" class="blindminus" onfocus="this.blur()" onclick="toggleui(3);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Admin Email Message Options', 'cforms') ?></p>

			<div id="o3">
				<p><?php _e('Generally, EMails sent to the admin and the submitting user can be both in plain text and HTML. The TXT part <strong>is required</strong>, HTML <strong>optional</strong>.', 'cforms'); ?></p>
				<p><?php echo sprintf(__('Below you\'ll find the settings for both the <strong>TXT part</strong> of the admin email as well as the <strong>optional HTML part</strong> of the message. Both areas permit the use of any of the <strong>pre-defined variables</strong> or <strong>data from input fields</strong>. <a href="%s" %s>Please see the documentation on the HELP page</a> (including HTML message examples!).', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></p>
	
				<div class="optionsbox" style="margin-top:30px;">
					<div class="optionsboxL"><label for="cforms_header"><?php _e('<strong>Admin TEXT message</strong> part<br />(header)', 'cforms') ?></label></div>
					<div class="optionsboxRxl">
						<div id="r4" class="rbox"><textarea rows="80px" cols="200px" name="cforms_header" id="cforms_header" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_header'))); ?></textarea><div id="rh4"></div></div>
						<span><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></span>
					</div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_formdata_txt" name="cforms_formdata_txt" <?php if(substr(get_option('cforms'.$no.'_formdata'),0,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_txt"><?php _e('<strong>Include</strong> <em>pre formatted</em> form input at the bottom of the TXT part', 'cforms') ?></label></div>
				</div>
				<div class="optionsbox">
					<div class="optionsboxL">&nbsp;</div>
					<div class="optionsboxR"><input type="text" name="cforms_space" id="cforms_space" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_space'))); ?>" /><label for="cforms_space"> &nbsp; <?php _e('(# characters) : spacing between labels &amp; data, for plain txt version only', 'cforms') ?></label></div>
				</div>
	
				<div class="optionsbox" style="margin-top:20px;">
					<div class="optionsboxL"><label for="cforms_admin_html"><strong><?php _e('Enable HTML', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_admin_html" name="cforms_admin_html" <?php if($o=substr(get_option('cforms'.$no.'_formdata'),2,1)=='1') echo "checked=\"checked\""; ?>/></div>
				</div>
				
				<div class="optionsbox <?php if( !$o=='1' ) echo "hidden"; ?>">
					<div class="optionsboxL"><label for="cforms_header_html"><?php _e('<strong>Admin HTML message</strong> part<br />(header)', 'cforms') ?></label></div>
					<div class="optionsboxRxl">
						<div id="r5" class="rbox"><textarea rows="80px" cols="200px" name="cforms_header_html" id="cforms_header_html" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_header_html'))); ?></textarea><div id="rh5"></div></div>
						<span><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></span>
					</div>
				</div>
				<div class="optionsbox <?php if( !$o=='1' ) echo "hidden"; ?>">
					<div class="optionsboxL"></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_formdata_html" name="cforms_formdata_html" <?php if(substr(get_option('cforms'.$no.'_formdata'),1,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_html"><?php _e('<strong>Include</strong> <em>pre formatted</em> form input at the bottom of the HTML part', 'cforms') ?></label>
						<br style="clear:both" /><br />
						<p><a class="infobutton" href="#" name="it3"><?php _e('\'Don\'t like the default form data block in your admin email?  &raquo;', 'cforms'); ?></a></p>
					</div>					
				</div>
				
				<p class="infotxt ex" id="it3"><strong><u><?php _e('Note:','cforms')?></u></strong> <?php _e('To avoid sending ALL of the submitted user data (especially for very long forms) to the form admin simply <strong>uncheck</strong> "<em>Include pre formatted form input ...</em>" and instead specify the fields you\'d like to receive via the use of <strong>custom variables</strong>.', 'cforms'); ?></p>
						
			</div>
		</fieldset>


		<fieldset class="cformsoptions <?php if( !$ccboxused ) echo "hidden"; ?>" id="cc">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><a id="b4" class="blindminus" onfocus="this.blur()" onclick="toggleui(4);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('CC Settings', 'cforms') ?></p>

			<div id="o4">
				<p><?php _e('This is the subject of the CC email that goes out the user submitting the form and as such requires the <strong>CC:</strong> field in your form definition above.', 'cforms') ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_ccsubject"><strong><?php _e('Subject CC', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_ccsubject" id="cforms_ccsubject" value="<?php $t=explode('$#$',get_option('cforms'.$no.'_csubject')); echo stripslashes(htmlspecialchars($t[1])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></div>
				</div>
			
			</div>
		</fieldset>
		

		<fieldset class="cformsoptions" id="autoconf">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit7" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#autoconf';" /><a id="b5" class="blindminus" onfocus="this.blur()" onclick="toggleui(5);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Auto Confirmation', 'cforms') ?></p>

			<div id="o5">
				<p><?php _e('These settings apply to an auto response/confirmation sent to the visitor. If enabled AND your form contains a "<code>CC me</code>" field <strong>AND</strong> the visitor selected it, no extra confirmation email is sent!', 'cforms') ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR">
						<input type="checkbox" id="cforms_confirm" name="cforms_confirm" <?php if($o=get_option('cforms'.$no.'_confirm')=="1") echo "checked=\"checked\""; ?>/><label for="cforms_confirm"><strong><?php _e('Activate auto confirmation', 'cforms') ?></strong></label>
						<p style="margin:7px 0;"><a class="infobutton" href="#" name="it8"><?php _e('Please read note &raquo;', 'cforms'); ?></a></p>
					</div>
					<p class="infotxt ex" id="it8"><?php _e('For the <em>auto confirmation</em> feature to work, make sure to mark at least one field <code>Email</code>, otherwise <strong>NO</strong> auto confirmation email will be sent out! If multiple fields are checked "Email", only the first in the list will receive a notification.', 'cforms') ?></p>
				</div>
	
	
				<div class="optionsbox <?php if( !$o=="1" ) echo "hidden"; ?>">
					<div class="optionsboxL"><label for="cforms_csubject"><strong><?php _e('Subject auto confirmation', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" name="cforms_csubject" id="cforms_csubject" value="<?php $t=explode('$#$',get_option('cforms'.$no.'_csubject')); echo stripslashes(htmlspecialchars($t[0])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></div>
				</div>
				<div class="optionsbox <?php if( !$o=="1" ) echo "hidden"; ?>" style="margin-top:10px;">
					<div class="optionsboxL"><label for="cforms_cmsg"><?php _e('<strong>TXT message</strong> part', 'cforms') ?></label></div>
					<div class="optionsboxRxl">
						<div id="r6" class="rbox"><textarea rows="80px" cols="200px" name="cforms_cmsg" id="cforms_cmsg" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_cmsg'))); ?></textarea><div id="rh6"></div></div>
						<span><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></span>
 					</div>
				</div>
				<div class="optionsbox <?php if( !$o=="1" ) echo "hidden"; ?>" style="margin-top:15px;">
					<div class="optionsboxL"><label for="cforms_user_html"><strong><?php _e('Enable HTML', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_user_html" name="cforms_user_html" <?php if($o2=substr(get_option('cforms'.$no.'_formdata'),3,1)=='1') echo "checked=\"checked\""; ?>/></div>
				</div>
				<div class="optionsbox <?php if( !$o=="1" || !$o2=="1") echo "hidden"; ?>">
					<div class="optionsboxL"><label for="cforms_cmsg_html"><?php _e('<strong>HTML message</strong> part', 'cforms') ?></label></div>
					<div class="optionsboxRxl">
						<div id="r7" class="rbox"><textarea rows="80px" cols="200px" name="cforms_cmsg_html" id="cforms_cmsg_html" ><?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_cmsg_html'))); ?></textarea><div id="rh7"></div></div>
						<span><?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></span>
					</div>
				</div>

			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="tellafriend">

			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit8" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#tellafriend';" /><a id="b6" class="blindminus" onfocus="this.blur()" onclick="toggleui(6);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('Tell-A-Friend Form Support', 'cforms') ?><span style="font-size:10px; padding-left:20px;"><?php echo sprintf(__('BEFORE turning on this feature, please see the Help section for <a href="%s" %s>more details.</a>', 'cforms'),'?page='. $plugindir.'/cforms-help.php#taf','onclick="setshow(19)"'); ?></span></p>

			<div id="o6">
				<?php 
					if ( $allenabled <> false )
						echo '<div id="tafmessage" class="updated fade"><p>'.$allenabled.' '. __('posts and pages processed and tell-a-friend <strong>enabled</strong>.', 'cforms'). ' </p></div>';
					else if ( $alldisabled )
						echo '<div id="tafmessage" class="updated fade"><p>'. __('All posts &amp; pages processed and tell-a-friend <strong>disabled</strong>.', 'cforms'). ' </p></div>';
				?>
				
				<p><?php _e('If enabled, this forms\' feature set will be extended to cover tell-a-friend requirements, 4 new <em>input field types</em> will be available.', 'cforms'); ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_tellafriend" name="cforms_tellafriend" <?php if( substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_tellafriend"><strong><?php _e('Enable Tell-A-Friend', 'cforms') ?></strong></label></div>
				</div>
				
				<?php if( substr(get_option('cforms'.$no.'_tellafriend'),0,1)=='1' ) : ?>
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_tafdefault" name="cforms_tafdefault" <?php if( substr(get_option('cforms'.$no.'_tellafriend'),1,1)=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_tafdefault"><strong><?php _e('T-A-F enable <strong>new posts/pages</strong> by default', 'cforms') ?></strong></label></div>
				</div>
				
				<div class="optionsbox" style="padding-top:25px;">
					<div class="optionsboxL"><label for="migrate"><?php _e('<strong>T-A-F dis-/enable all</strong> your previous posts.', 'cforms') ?></label></div>
					<div class="optionsboxR">
						<input type="submit" title="<?php _e('This will add a T-A-F custom field per post/page.', 'cforms') ?>" name="addTAF" class="allbuttons" style="width:150px;" value="<?php _e('Enable', 'cforms') ?>" onclick="document.mainform.action='#tellafriend'; return confirm('<?php _e('Do you really want to enable all previous posts and pages for T-A-F?', 'cforms') ?>');"/>
						<input type="submit" title="<?php _e('This will remove the T-A-F custom field on all posts/pages.', 'cforms') ?>" name="removeTAF" class="allbuttons" style="width:150px;" value="<?php _e('Disable', 'cforms') ?>" onclick="document.mainform.action='#tellafriend'; return confirm('<?php _e('Do you really want to disable all previous posts and pages for T-A-F?', 'cforms') ?>');"/>
						<p style="margin:7px 0;"><a class="infobutton" href="#" name="it9"><?php _e('Please read note &raquo;', 'cforms'); ?></a></p>
					</div>
					<p class="infotxt ex" id="it9"><?php echo __('You will find a <strong>cforms Tell-A-Friend</strong> checkbox on your <strong>admin/edit page</strong> (typically under "Post/Author")! <br /><u>Check it</u> if you want to have the form appear for the given post or page.', 'cforms');?></p>
				</div>
	
				<?php endif; ?>

			</div>
		</fieldset>	
		

		<fieldset class="cformsoptions" id="commentrep">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit9" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#commentrep';" /><a id="b7" class="blindminus" onfocus="this.blur()" onclick="toggleui(7);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('WP Comment Feature', 'cforms') ?></p>

			<div id="o7">
			
				<p><?php _e('cforms can be used to replace your <em>default Wordpress comment form</em> (on posts &amp; pages), allowing your readers to either <strong>comment on the post</strong> or <strong>alternatively send the post author a note</strong>!', 'cforms') ?></p>
				<p><?php echo sprintf(__('There will be <strong><u>5</u></strong> additional, comment specific <em>input field types</em> available with this feature turned on, or use the <em>WP comment form preset</em> to get started. <a href="%s" %s>Configuration details.</a>', 'cforms'),'?page='. $plugindir.'/cforms-help.php#commentrep','onclick="setshow(19)"'); ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"></div>
					<div class="optionsboxR"><input type="checkbox" id="cforms_commentrep" name="cforms_commentrep" <?php if( get_option('cforms'.$no.'_tellafriend')=='2' ) echo "checked=\"checked\""; ?>/><label for="cforms_commentrep"><strong><?php _e('Enable this form to optionally (user determined) act as a WP comment form', 'cforms') ?></strong></label>
						<?php if( get_option('cforms'.$no.'_tellafriend')=='2' ) : ?>
							<br />
							<p><a class="infobutton" href="#" name="it6"><?php _e('<em>Tell a friend</em> or <em>WP comment</em>? &raquo;', 'cforms'); ?></a></p>
							<p><a class="infobutton" href="#" name="it7"><?php _e('Important additional configuration requirements &raquo;', 'cforms'); ?></a></p>
						<?php endif; ?>
					</div>
				</div>
				
					<?php if( get_option('cforms'.$no.'_tellafriend')=='2' ) : ?>

							<p class="infotxt ex" id="it6">
							<?php echo sprintf(__('This feature and T-A-F (above) are mutually exclusive. If you need both features, please create a new form for T-A-F.<br />Again, see the <a href="%s" %s>help section</a> on proper use.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#commentrep','onclick="setshow(19)"'); ?>
							</p>
							<p class="infotxt ex" id="it7">
							<?php echo sprintf(__('Please see the extended <a href="%s" %s>WP comment options under <em>Global Settings</em></a> for additional configuration requirements. Especially concerning Ajax comment submission!', 'cforms'),'?page='. $plugindir.'/cforms-global-settings.php#wpcomment','onclick="setshow(19)"'); ?>
							</p>
						
					<?php endif; ?>

			</div>
		</fieldset>	


		<fieldset class="cformsoptions" id="readnotify">
			<p class="cflegend"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="Submit4" class="allbuttons updbutton" value="<?php _e('Update Settings &raquo;', 'cforms'); ?>" onclick="javascript:document.mainform.action='#autoconf';" /><a id="b8" class="blindminus" onfocus="this.blur()" onclick="toggleui(8);return false;" href="#" title="<?php _e('Expand/Collapse', 'cforms') ?>"></a><?php _e('3rd Party Read-Notification Support', 'cforms') ?></p>

			<div id="o8">
				<p><?php echo sprintf(__('If you\'d like to utilize 3rd party email tracking such as %s or %s, add the respective suffix (e.g.: %s) here:', 'cforms'),'<strong>readnotify.com</strong>','<strong>didtheyreadit.com</strong>','<code>.readnotify.com</code>') ?></p>
	
				<div class="optionsbox">
					<div class="optionsboxL"><label for="cforms_tracking"><strong><?php _e('Suffix for email tracking', 'cforms') ?></strong></label></div>
					<div class="optionsboxR"><input type="text" id="cforms_tracking" name="cforms_tracking" value="<?php echo stripslashes(htmlspecialchars(get_option('cforms'.$no.'_tracking'))); ?>"/></div>
				</div>

			</div>
		</fieldset>	
		

		</form>

	<?php cforms_footer(); ?>
</div>

<?php
add_action('admin_footer', 'insert_cfmodal');
function insert_cfmodal(){
	global $cforms_root,$noDISP;
?>
	<div class="jqmWindow" id="cf_editbox">
		<div class="cf_ed_header jqDrag"><?php _e('Input Field Settings','cforms'); ?></div>
		<div class="cf_ed_main">
			<div id="cf_target"></div>
			<div class="controls"><a href="#" id="ok" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('OK', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" id="cancel" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
		</div>
	</div>
	<div class="jqmWindow" id="cf_installbox">
		<div class="cf_ed_header jqDrag"><?php _e('cforms out-of-the-box-form repository','cforms'); ?></div>
		<div class="cf_ed_main">
			<form name="installpreset" method="POST">		
				<div id="cf_installtarget"></div>
				<div class="controls"><a href="#" id="okInstall" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" id="cancelInstall" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
				<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>"/>
			</form>
		</div>
	</div>
<?php
}
?>
