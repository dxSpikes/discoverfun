<?php
/**
 * Format the size of the image to Kb, Mb, Gb, Tb
 *
 * @access public
 * @param int $byte
 * @param string $force_unit
 * @param string $format
 * @param boolean $format
 * @return string
 **/
 function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
 {
     // Format string
     $format = ($format === NULL) ? '%01.2f %s' : (string) $format;
 
     // IEC prefixes (binary)
     if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
     {
         $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
         $mod   = 1024;
     }
     // SI prefixes (decimal)
     else
     {
         $units = array('B', 'kB', 'MB', 'GB', 'TB', 'PB');
         $mod   = 1000;
     }
 
     // Determine unit to use
     if (($power = array_search((string) $force_unit, $units)) === FALSE)
     {
         $power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
     }
 
     return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
 }

 
add_action('wp_ajax_post-upload-files', 'post_upload_files');

/**
 * Ajax function for submitting the fileupload form to upload the files
 *
 * @access public
 * @param none
 * @return none
 **/
 function post_upload_files() {
    include(PLUGIN_PATH . 'class/UploadHandler.php');
    $upload_handler = new UploadHandler();
    exit;
 }

 function init_ads_manager(){


 }



/**
 * Add new role with capabilities
 * @access public
 * @param none
 * @return none
 **/

add_action( 'admin_init', 'add_role_with_cap' );

function add_role_with_cap(){
   
    add_role('ads_author', 'Ads Author', array(
        'edit_ad' => true,
        'edit_ads' => true,
        'edit_others_ads' => false,
        'publish_ads' => true,
        'read_ad' => true,
        'read_private_ads' => false,
        'delete_ad' => true,
        'delete_ads' => true,
        'delete_others_ads' => false,
        'read' => true,
        'manage_establishment' => true,
        'edit_establishment' => true,
        'delete_establishment' => true,
        'assign_establishment' => true,


      ));
 
}


/**
 * remove capabilities of a role
 * @access public
 * @param none
 * @return none
 **/

//add_action( 'admin_init', 'clean_unwanted_caps' );

function clean_unwanted_caps(){
    $delete_caps = array('edit_ad','publish_ads', 'edit_ads', 'edit_others_ads', 'delete_ads','delete_others_ads', 'read_private_ads','delete_ad','read_ad');
    global $wp_roles;
    foreach ($delete_caps as $cap) {
        foreach (array_keys($wp_roles->roles) as $role) {
            $wp_roles->remove_cap($role, $cap);
        }
    }
}


/**
 * check if role is exists
 * @access public
 * @param role, user_id
 * @return boolean
 **/
function is_exist_role($role, $user){
    $user = new WP_User( $user );

    if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
         return in_array ($role, $user->roles);
        
    }
}


/**
 * Validate number
 * @access public
 * @param number
 * @return boolean
 **/

function validate_decimal($s_value) {
    $regex = '/^[+\-]?(?:\d+(?:\.\d*)?|\.\d+)$/';
    return preg_match($regex, $s_value); 
}


/**
 * Format a number with grouped thousands
 * @access public
 * @param number
 * @return formated number
 **/

function format_number($number) {
   return number_format($number, 2, '.', ',');
}

/**
 * validate email address
 * @access public
 * @param fields
 * @return response array
 **/

function isValidEmail($email){
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email);
}

/**
 * validate accomodation post field
 * @access public
 * @param fields
 * @return response array
 **/


function validate_accomodation_fields( $data ) {
    $response['error'] = array();
    $response['error_count'] = 0;
    if(trim($data['firstname']) == '') {
         $response['error_count']++;
         $response['error'][] = 'Firstname is required!';
    }
    if(trim($data['lastname']) == '') {
         $response['error_count']++;
         $response['error'][] = 'lastname is required!';
    } 
    if(trim($data['confirm_email']) != '' && trim($data['email']) != '' && $data['confirm_email'] != $data['email'] && isValidEmail($data['email']) && isValidEmail($data['confirm_email'])) {
         $response['error_count']++;
         $response['error'][] = 'Email is Mismatch';
    } else {
         if(trim($data['email']) == '' || !isValidEmail($data['email'])) {
         $response['error_count']++;
         $response['error'][] = 'Email is required and must be valid!';
        }
        if(trim($data['confirm_email']) == '' || !isValidEmail($data['confirm_email'])) {
             $response['error_count']++;
             $response['error'][] = 'Confirm email is required and must be valid!';
        }
    }

    if(trim($data['mobile']) == '') {
         $response['error_count']++;
         $response['error'][] = 'Mobile is required!';
    }
    if(trim($data['cus_address']) == '') {
         $response['error_count']++;
         $response['error'][] = 'Address is required!';
    }
    if(trim($data['state']) == '') {
         $response['error_count']++;
         $response['error'][] = 'State is required!';
    }
    if(trim($data['city']) == '') {
         $response['error_count']++;
         $response['error'][] = 'City is required!';
    }
    if(trim($data['zip_code']) == '') {
         $response['error_count']++;
         $response['error'][] = 'Zip Code is required!';
    }
    if(trim($data['check_in']) == '') {
         $response['error_count']++;
         $response['error'][] = 'Check-in is required!';
    }
    if(trim($data['check_out']) == '') {
         $response['error_count']++;
         $response['error'][] = 'Check-out is required!';
    }
    if(trim($data['no_of_persons']) == '' || (int)$data['no_of_persons'] == 0 ) {
         $response['error_count']++;
         $response['error'][] = 'State is required and must be greater than zero!';
    }
    if($data['with_transformation'] == 1) {
        //checkdate(12, 31, 2000)      
        if(trim($data['travel_date_from']) == '') {
             $response['error_count']++;
             $response['error'][] = 'Travel date from is required!';
        }
        if(trim($data['travel_date_to']) == '') {
             $response['error_count']++;
             $response['error'][] = 'Travel date to is required!';
        }
        if(trim($data['pick_up_point']) == '') {
             $response['error_count']++;
             $response['error'][] = 'Pick-up Point is required!';
        }
        if(trim($data['specific_address']) == '') {
             $response['error_count']++;
             $response['error'][] = 'Specific Address is required!';
        }

    }


    if($data['payment_method'] == 'credit_card') {
        if( $data['cc_type'] == -1) {
             $response['error_count']++;
             $response['error'][] = 'Credit Card Type is required!';
        }
        if( trim($data['cc_number']) == '') {
             $response['error_count']++;
             $response['error'][] = 'Credit Card Type is required!';
        }
        if( trim($data['card_holder_name']) == '') {
             $response['error_count']++;
             $response['error'][] = 'Card Holder Name is required!';
        }
        if( trim($data['cc_cvv_number']) == '') {
             $response['error_count']++;
             $response['error'][] = 'CVV Number is required!';
        }
         if( ($data['cc_month'].$data['cc_year']) < date('m').date('Y')) {
             $response['error_count']++;
             $response['error'][] = 'Invalid Expiration Date';
        }

    }
    return $response;
}

/**
 * display response messages
 * @access public
 * @param fields
 * @return response array
 **/


function display_message($response) {
    $msg = '';
    if( $response['error_count'] > 0 ) 
    {   
        $class = 'error';
        foreach( $response['error'] as $err ) {
            $msg .= '<p>'. $err .'</p>';
        }
        
    } else {
        $class = 'updated';
        $msg = $response['msg'];
    }
    echo '<div class="'. $class .' below-h2">';
    echo $msg;
    echo '</div>';
}

/*

//cron
if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
  wp_schedule_event( time(), 'hourly', 'my_task_hook' );
}

add_action( 'my_task_hook', 'my_task_function' );

function my_task_function() {
  wp_mail( 'your@email.com', 'Automatic email', 'Automatic scheduled email from WordPress.');
}*/


