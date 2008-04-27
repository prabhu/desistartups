<?php

/*
please see cforms.php for more information
*/

load_plugin_textdomain('cforms');

$plugindir		= dirname(plugin_basename(__FILE__));
$fullplugindir	= ABSPATH . 'wp-content/plugins/' . $plugindir;
$cforms_root	= get_settings('siteurl') . '/wp-content/plugins/'.$plugindir;

### db settings
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### CSS styles
$style		= get_option('cforms_css');           
$stylefile	= $fullplugindir.'/styling/'.$style;

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
	die;
}

//
// Enable/Disable LabelIDs ?
//

if(isset($_POST['label-ids'])){
	update_option( 'cforms_labelID', get_option('cforms_labelID')?'0':'1' );
}
else if(isset($_POST['li-ids'])){
	update_option( 'cforms_liID', get_option('cforms_liID')?'0':'1' );
}
else if(isset($_POST['no-css'])){
	update_option( 'cforms_no_css', get_option('cforms_no_css')?'0':'1' );
}

//
// Select new CSS?
//

if(!empty($_POST['save_css'])){

	    $newcss = stripslashes($_POST['csseditor']);
		   	
		if(is_writeable($stylefile)) {

		    $f = fopen($stylefile, 'w+');
		    fwrite($f, $newcss);
			fclose($f);
		
		    echo ' <div id="message" class="updated fade"><p><strong>'. __('The stylesheet has been updated.', 'cforms') .'</strong></p></div>'."\n";

		} else

		    echo ' <div id="message" class="updated fade"><p><strong>'. __('Write Error! Please verify write permissions on the style file.', 'cforms') .'</strong></p></div>'."\n";

} else if ( !empty($_POST['chg_css']) ){

		    update_option('cforms_css', $_POST['style']);
			$style = get_option('cforms_css');           
			$stylefile	= $fullplugindir.'/styling/'.$style;
		    echo ' <div id="message" class="updated fade"><p><strong>'. __('New theme selected.', 'cforms') .'</strong></p></div>'."\n";
} 
		


//
// Pick new CSS
//

?>

<div class="wrap" id="top">
<img src="<?php echo $cforms_root; ?>/images/cfii.gif" alt="" align="right"/><img src="<?php echo $cforms_root; ?>/images/p5-title.jpg" alt=""/>

	<p><?php _e('Please select a theme file that comes closest to what you\'re looking for and apply your own custom changes via the editor below.', 'cforms') ?></p>
	<p><?php _e('This is <strong>optional</strong> of course, if you\'re happy with the default look and feel, no need to do anything here.', 'cforms') ?></p>

	<form id="selectcss" method="post" action="" name="selectcss">

			 <fieldset class="cformsoptions">
				<p class="cflegend" style="margin:10px 0 20px;"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><?php _e('Select a form style', 'cforms') ?></p>

				<table>
				<tr valign="top">
				
					<td>
						<table>
							<tr valign="middle">
								<td width="300" align="right" style="font-size:10px;"><?php _e('Please choose a theme file <br />to style your forms' , 'cforms') ?></td>
								<td align="center">				
									<?php // include all css files
										$d   = $fullplugindir.'/styling';
						
										$exists = file_exists($d);
										if ( $exists == false )
											echo '<p><strong>' . __('Please make sure that the <code>/styling</code> folder exists in the cforms plugin directory!', 'cforms') . '</strong></p>';
						
										else {
											?>
											<select style="cursor:pointer;" name="style"><?php
							
												$allCSS = array();
												$dir = opendir($d);
												while ( $dir && ($f = readdir($dir)) ) {
												
													if( eregi("\.css$",$f) && !eregi("calendar\.css$",$f) ){
														array_push($allCSS, $f);
													}

												}

												sort($allCSS);
												foreach ( $allCSS as $f ) {
												
													if( $f==$style )
													    	echo '<option selected="selected">'.$f.'</option>'."\n";
													else
															echo '<option>'.$f.'</option>';																		
												}
												
												
											?></select>
									<?php } ?>																				
								</td>
								<td>
									<input type="submit" name="chg_css" class="allbuttons stylebutton" value="<?php _e('Select Style &raquo;', 'cforms'); ?>"/>
								</td>
							</tr>
							<tr style="height:200px;">
								<td colspan="3">
									<p class="ex"><?php _e('For comprehensive customization support you may choose to turn on <strong>label &amp; list element ID\'s</strong>. This way each input field &amp; label can be specifically addressed via CSS styles.', 'cforms') ?> </p>

									<input type="submit" name="label-ids" id="label-ids" class="allbuttons" value="<?php if ( get_option('cforms_labelID')=='' || get_option('cforms_labelID')=='0' ) _e('Activate Label IDs', 'cforms'); else  _e('Deactivate Label IDs', 'cforms'); ?>" />
									<?php if ( get_option('cforms_labelID')=='1' ) echo __('Currently turned on ', 'cforms') . '<img class="turnedon" src="' . $cforms_root.'/images/ok.gif" alt=""/>'; ?>
									<br />
									<input type="submit" name="li-ids" id="li-ids" class="allbuttons" value="<?php if ( get_option('cforms_liID')=='' || get_option('cforms_liID')=='0' ) _e('Activate List Element IDs', 'cforms'); else  _e('Deactivate List Element IDs', 'cforms'); ?>" />
									<?php if ( get_option('cforms_liID')=='1' ) echo __('Currently turned on ', 'cforms') . '<img class="turnedon" src="' . $cforms_root.'/images/ok.gif" alt=""/>'; ?>
									<br />
									<br />
									<input type="submit" name="no-css" id="no-css" class="allbuttons deleteall" style="height:30px" value="<?php if ( get_option('cforms_no_css')=='' || get_option('cforms_no_css')=='0' ) _e('Deactivate CSS styling altogether!', 'cforms'); else  _e('Reactivate CSS styling!', 'cforms'); ?>" />
									<?php if ( get_option('cforms_no_css')=='1' ) echo __('Theme is disabled', 'cforms') . '<img class="turnedon" src="' . $cforms_root.'/images/ok.gif" alt=""/>'; ?>

								</td>
							</tr>
							<tr>
								<td colspan="3">							
										<p><?php echo sprintf(__('You might also want to study the <a href="%s">PDF guide on cforms CSS & a web screencast</a> I put together to give you a head start.', 'cforms'),'http://www.deliciousdays.com/cforms-forum?forum=1&amp;topic=428&amp;page=1'); ?></p>
								</td>
							</tr>

						</table>
					</td>
					
					<td>					
						<?php if ( $exists ) {

								$existsjpg = file_exists($d.'/'.$style.'.jpg');
								if ( $existsjpg )
									echo __('PREVIEW:', 'cforms').'<br /><img height="228px" width="300px" src="' . $cforms_root.'/styling/'.$style.'.jpg' . '" alt="' . __('Theme Preview', 'cforms') . '" title="' . __('Theme Preview', 'cforms').': ' . $style .'"/>';
					
						}?>
					</td>
					
				</tr>
				</table>
				
			</fieldset>

	 </form>
<?php
//
// Edit current style
//
?>
	<form id="editcss" method="post" action="" name="editcss">
	
			 <fieldset class="cformsoptions">
				<p class="cflegend" style="margin-top:10px;"><a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><input type="submit" name="save_css" class="allbuttons updbutton" value="<?php _e('Update Changes &raquo;', 'cforms'); ?>"/><?php _e('Basic CSS editor. Current style file: ', 'cforms'); echo '<span style="color:#D54E21;">'.$style.'</span>' ?></p>

				<p><?php _e('Use this simple editor to further tailor your forms\' style to meet your requirements. Currently you\'re editing: ', 'cforms'); echo '<span style="color:#D54E21;">'.$style.'</span>' ?></p>

			    <textarea rows="20" cols="118" id="stylebox" name="csseditor"><?php 
					     
					if( is_file($stylefile) && filesize($stylefile) > 0) {

						$f = "";
						$f = fopen($stylefile, 'r');
						$file = fread($f, filesize($stylefile));
						echo $file;
						fclose($f);

					} else 
					    echo __('Sorry. The file you are looking for doesn\'t exist.', 'cforms');

				?></textarea>
								
		  </fieldset>
	</form>

</div> 
