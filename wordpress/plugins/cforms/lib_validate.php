<?php

// Validating non Ajax form submission
//
//

$cflimit = '';

//echo "***<br><pre>";print_r($_POST);echo "</pre>";  //debug

for($i = 1; $i <= $field_count; $i++) {

		if ( !$custom ) 
			$field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
		else
			$field_stat = explode('$#$', $customfields[((int)$i+(int)$off) - 1]);

		// filter non input fields
		while ( $field_stat[1] == 'fieldsetstart' || $field_stat[1] == 'fieldsetend' || $field_stat[1] == 'textonly' ) {
				$off++;

				if ( !$custom ) 
                    $field_stat = explode('$#$', get_option('cforms'.$no.'_count_field_' . ((int)$i+(int)$off) ));
                else
                    $field_stat = explode('$#$', $customfields[((int)$i+(int)$off) - 1]);
                    
				if( $field_stat[1] == '')
						break 2; // all fields searched, break both while & for
		}
			

		// custom error set?
		$c_err = explode('|err:', $field_stat[0], 2);
				
		$field_name = $c_err[0];
		$field_type = $field_stat[1];
		$field_required = $field_stat[2];
		$field_emailcheck = $field_stat[3];


		// ommit certain fields; validation only!
		if( in_array($field_type,array('cauthor','url','email')) ){
			if ( $user->ID ){
				$validations[$i+$off] = 1;   // auto approved
				continue;
			}
		}


		//input field names & label
		$custom_names = (get_option('cforms'.$no.'_customnames')=='1')?true:false;
		
		if ( $custom_names ){
			
			preg_match('/^([^#\|]*).*/',$field_name,$input_name);

			if ( strpos($input_name[1],'[id:')!==false ){
				$idPartA = strpos($input_name[1],'[id:');
				$idPartB = strpos($input_name[1],']',$idPartA);
				$current_field = $_REQUEST[ str_replace(' ','_', substr($input_name[1],$idPartA+4,($idPartB-$idPartA)-4) ) ];
				
				$field_name = substr_replace($input_name[1],'',$idPartA,($idPartB-$idPartA)+1);
			} else
				$current_field = $_REQUEST[ str_replace(' ','_',$input_name[1]) ];
			
			if ( is_array($current_field) )
				$current_field = $current_field;
			else
				$current_field = stripslashes($current_field);
			
		}
		else{
			if( in_array($field_type,array('comment','url','email','cauthor')) )  // WP comment field name exceptions
				$current_field = $_REQUEST[$field_type];
			else
				$current_field = $_REQUEST['cf'.$no.'_field_' . ((int)$i+(int)$off)];
				
			if ( is_array($current_field) )
				$current_field = $current_field;
			else
				$current_field = stripslashes($current_field);
		}

		
//		echo "***".('cf'.$no.'_field_' . ((int)$i+(int)$off)).'***'.$current_field."***<br>";
		
		if( $field_emailcheck ) {  // email field
		
				// special email field in WP Commente
				if ( $field_type=='email' )		
					$validations[$i+$off] = cforms_is_email( $_REQUEST['email']) || (!$field_required && $_REQUEST['email']=='');
				else
					$validations[$i+$off] = cforms_is_email( $current_field ) || (!$field_required && $current_field=='');

				if ( !$validations[$i+$off] && $err==0 ) $err=1;
			
		}
		else if( $field_required ) { // just required

				if( in_array($field_type,array('cauthor','url','comment','pwfield','textfield','datepicker','textarea','yourname','youremail','friendsname','friendsemail')) ){

							$validations[$i+$off] = ($current_field=='')?false:true;

				}else if( $field_type=="checkbox" ) {

							$validations[$i+$off] = ($current_field=='')?false:true;
	
				}else if( $field_type=="selectbox" || $field_type=="emailtobox" ) {

							$validations[$i+$off] = !($current_field == '-' );

				}else if( $field_type=="multiselectbox" ) {
				
							// how many multiple selects ?
                            $all_options = $current_field;
							if ( count($all_options) <= 1 && $all_options[0]=='' )                               
									$validations[$i+$off] = false;
                            else						    
									$validations[$i+$off] = true;

				}else if( $field_type=="upload" ) {  // prelim upload check

							$validations[$i+$off] = !($_FILES['cf_uploadfile'.$no][name][$filefield++]=='');
							if ( !$validations[$i+$off] && $err==0 )
									{ $err=3; $fileerr = get_option('cforms_upload_err2'); }
				
				}else if( in_array($field_type,array('cauthor','url','email','comment')) ) {  // prelim upload check

						$validations[$i+$off] = ($_REQUEST[$field_type]=='')?false:true;

						// regexp set for textfields?
						$obj = explode('|', $c_err[0], 3);
						
		  				if ( $obj[2] <> '') {
		  				
							$reg_exp = str_replace('/','\/',stripslashes($obj[2]) );
							if( !preg_match('/'.$reg_exp.'/',$_REQUEST[$field_type]) )
							    $validations[$i+$off] = false;
									    
						}						

				}

				if ( !$validations[$i+$off] && $err==0 ) $err=1;

		}
		else if( $field_type == 'verification' ){  // visitor verification code
		
        		$validations[$i+$off] = 1;
				if ( $_REQUEST['cforms_a'.$no] <> md5(rawurlencode(strtolower($_REQUEST['cforms_q'.$no]))) ) {
						$validations[$i+$off] = 0;
						$err = !($err)?2:$err;
				}
						
		}
		else if( $field_type == 'captcha' ){  // captcha verification
		
        		$validations[$i+$off] = 1;

				if ( $_SESSION['turing_string_'.$no] <> $_REQUEST['cforms_captcha'.$no] ) {
						$validations[$i+$off] = 0;
						$err = !($err)?2:$err;
				}
				
		}
		else
			$validations[$i+$off] = 1;


	
		// REGEXP now outside of 'is required'
		if( in_array($field_type,array('cauthor','url','comment','pwfield','textfield','datepicker','textarea','yourname','youremail','friendsname','friendsemail')) ){

				// regexp set for textfields?
				$obj = explode('|', $c_err[0], 3);
				
  				if ( $obj[2] <> '') { // check against other field!
  					if (  isset($_REQUEST[$obj[2]]) && $_REQUEST[$obj[2]]<>'' ){

						if( $current_field <> $_REQUEST[$obj[2]] )
						    $validations[$i+$off] = false;
  					}
  					else { // classic regexp
						$reg_exp = str_replace('/','\/',stripslashes($obj[2]) );
						if( $current_field<>'' && !preg_match('/'.$reg_exp.'/', $current_field) ){
						    $validations[$i+$off] = false;
						}
					}
				}	
				if ( !$validations[$i+$off] && $err==0 ) $err=1;					
		}
	
	
	
		$all_valid = $all_valid && $validations[$i+$off];

		if ( $c_err[1] <> '' && $validations[$i+$off] == false ){
			$c_errflag=4;
			
			if ( get_option('cforms_liID')=='1' ){
				$custom_error .= '<li><a href="#li-'.$no.'-'.($i+$off).'">'.stripslashes($c_err[1]).' &raquo;</li></a>';
			} else
				$custom_error .= '<li>' . stripslashes($c_err[1]) . '</li>';
			
		}

	}


//
// have to upload a file?
//

$uploadedfile='';
$filefield=0;   // for multiple file upload fields

if( isset($_FILES['cf_uploadfile'.$no]) && $all_valid){

	foreach( $_FILES['cf_uploadfile'.$no][name] as $value ) {
		
		if(!empty($value)){   // this will check if any blank field is entered

			  	$file = $_FILES['cf_uploadfile'.$no];
				
				$fileerr = '';
				// A successful upload will pass this test. It makes no sense to override this one.
				if ( $file['error'][$filefield] > 0 )
						$fileerr = get_option('cforms_upload_err1');
						
				// A successful upload will pass this test. It makes no sense to override this one.
				$fileext[$filefield] = substr($value,strrpos($value, '.')+1,strlen($value));
				$allextensions = explode(',' ,  preg_replace('/\s/', '', get_option('cforms'.$no.'_upload_ext')) );
				
				if ( get_option('cforms'.$no.'_upload_ext')<>'' && !in_array($fileext[$filefield], $allextensions) )
						$fileerr = get_option('cforms_upload_err5');

				// A non-empty file will pass this test.
				if ( !( $file['size'][$filefield] > 0 ) )
						$fileerr = get_option('cforms_upload_err2');

				// A non-empty file will pass this test.
				if ( $file['size'][$filefield] >= (int)get_option('cforms'.$no.'_upload_size') * 1024 )
						$fileerr = get_option('cforms_upload_err3');


				// A properly uploaded file will pass this test. There should be no reason to override this one.
				if (! @ is_uploaded_file( $file['tmp_name'][$filefield] ) )
						$fileerr = get_option('cforms_upload_err4');
		
				if ( $fileerr <> '' ){

						$err = 3;
						$all_valid = false;

				} else {

						// cool, got the file!

				  		$uploadedfile = file($file['tmp_name'][$filefield]);
			
			            $fp = fopen($file['tmp_name'][$filefield], "rb"); //Open it
			            $fdata = fread($fp, filesize($file['tmp_name'][$filefield])); //Read it
			            $filedata[$filefield] = chunk_split(base64_encode($fdata)); //Chunk it up and encode it as base64 so it can emailed
			            fclose($fp);
	
				} // file uploaded

        } // if !empty
		$filefield++;
        
    } // while all file

} // no file upload triggered

//
// what kind of error message?
//
switch($err){
	case 0: break;
	case 1:
			$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms'.$no.'_failure') );
			break;
	case 2:
			$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms_codeerr') );
			break;
	case 3:
			$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), $fileerr);
			break;
	case 4:
			$usermessage_text = preg_replace ( array("|\\\'|",'/\\\"/','|\r\n|'),array('&#039;','&quot;','<br />'), get_option('cforms'.$no.'_failure') );
			break;
			
}
if ( $err<>0 && $c_errflag ) 
	$usermessage_text .= '<ol>'.$custom_error.'</ol>';

// proxy functions
function cforms_is_email($string){
	return eregi("^[_a-z0-9+-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $string);
}	

?>
