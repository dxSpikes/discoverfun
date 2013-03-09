<?php

/**
*insert|update highlight
*@param none
*@return none
*/

function insert_new_highlight($data) 
{
    global $wpdb;
    
    $response['error'] = array();
    $response['message'] = '';
    $response['err_count'] = 0;

    if(trim($data['highlight']) == '')
    {
        $response['err_count']++;
        $response['error'][] = 'Highlight is required';
    }

    if($response['err_count'] == 0) 
    {
        if(isset($data['action']) && $data['action'] == 'edit' )
            $result = $wpdb->update($wpdb->prefix .'highlight', array('highlight' => $data['highlight']),array('ID' => $data['hid']),array('%s'),array('%d'));
        else 
            $result = $wpdb->insert($wpdb->prefix .'highlight', array('highlight' => $data['highlight']),array('%s'));

        if($result) 
        {
            $response['message'] = 'Highlight Successfully Save';
        } 
        else 
        {
            $response['err_count']++;
            $response['message'] = 'Unable to save highlight';
        }
    }


    return $response;
}

/**
*get highlight
*@param id, type
*@return object
*/

function get_highlight($id = FALSE, $type = 'ARRAY_A') 
{
    global $wpdb;
    if($id ===  FALSE) 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix .'highlight';
        $result = $wpdb->get_results($sql,$type);
    } 
    else 
    {

        $sql = 'SELECT * FROM '. $wpdb->prefix .'highlight WHERE ID = %d';
        $result = $wpdb->get_row($wpdb->prepare($sql,$id),$type);
    }
    return $result;
}


/**
*insert|update feature
*@param none
*@return none
*/


function insert_new_feature($data) 
{
    global $wpdb;
    
    $response['error'] = array();
    $response['message'] = '';
    $response['err_count'] = 0;

    if(trim($data['feature']) == '')
    {
        $response['err_count']++;
        $response['error'][] = 'Feature is required';
    }

    if($response['err_count'] == 0) 
    {
        if(isset($data['action']) && $data['action'] == 'edit' )
            $result = $wpdb->update($wpdb->prefix .'features', array('feature' => $data['feature']),array('ID' => $data['fid']),array('%s'),array('%d'));
        else 
            $result = $wpdb->insert($wpdb->prefix .'features', array('feature' => $data['feature']),array('%s'));

        if($result) 
        {
            $response['message'] = 'Feature Successfully Save';
        } 
        else 
        {
            $response['err_count']++;
            $response['error'][]  = 'No changes on the data';
        }
    }


    return $response;
}

/**
*get feature
*@param id, type
*@return object
*/


function get_feature($id = FALSE, $type = 'ARRAY_A') 
{
    global $wpdb;
    if($id ===  FALSE) 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix .'features';
        $result = $wpdb->get_results($sql,$type);
    } 
    else 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix .'features WHERE ID = %d';
        $result = $wpdb->get_row($wpdb->prepare($sql,$id),$type);
    }
    
    return $result;
}

/**
*insert image
*@param id, type
*@return object
*/

function insert_post_images($post,$image){
    global $wpdb;

    for($i = 0; $i < count($image); $i++) 
    {
        //serialize the other information of the image
        $info = serialize(array('size' => $image[$i]['size'],'type' => $image[$i]['img_type']));
        $result = $wpdb->insert($wpdb->prefix .'image', array('post_id' => $post,'image' => $image[$i]['name'], 'other_info' => $info) );
    }
}


/** 
*get post images 
*@param id,type
*@return object
*/

function get_post_images($post = FALSE,$type = 'ARRAY_A'){
    global $wpdb;
    
    if($post === FALSE) 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix .'image order by ID DESC';
    } 
    else 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix .'image WHERE post_id = '. $post  .' order by ID DESC';   
    }

    $result = $wpdb->get_results($sql, $type);

    return $result;
}


/**
 * delete post image
 *
 * @access public
 * @param none
 * @return none
 **/

 add_action('wp_ajax_delete-post-files', 'delete_post_img');

 function delete_post_img() 
 {
    global $wpdb;
    $imgs = split(',',$_POST['img_id']);

    foreach($imgs as $img) 
    {
        $data = $wpdb->get_row('SELECT FROM '.$wpdb->prefix .'image WHERE ID ='. $img );

        $sql = 'DELETE FROM '.$wpdb->prefix .'image WHERE ID = %d';
        $result = $wpdb->query($wpdb->prepare($sql,$img));
        
        if($result) 
        {
            unlink(UPLOAD_DIR. $data['image'] );
            unlink(UPLOAD_THUMB_DIR .  $data['image'] );
        }
    }

 }

/**
 * update post image
 *
 * @access public
 * @param none
 * @return none
 **/

 add_action('wp_ajax_update-post-file', 'update_post_img');

 function update_post_img() 
 {
    global $wpdb;
    $img_id         = $wpdb->escape($_POST['img_id']);
    $caption        = $wpdb->escape($_POST['caption']);
    $description    = $wpdb->escape($_POST['description']);
    $feat           = $wpdb->escape($_POST['featured']);

    $result = $wpdb->update($wpdb->prefix .'image',array('caption' => $caption,'description' => $description,'featured' => $feat),array('ID' => $img_id), array('%s','%s','%d'), array('%d'));
    $wpdb->flush();
    $flag = FALSE;
    if($result)
    {
        $flag = TRUE;
    }

    echo json_encode(array('status' => $flag ));
    die();
 }

/**
 * add new agent
 *
 * @access public
 * @param array
 * @return array
 **/
function insert_new_agent($data)
{
    global $wpdb;

    $response['error'] = array();
    $response['count_err'] = 0;
    $response['msg'] = '';
    if(trim($data['username']) == '' ) 
    {
        $response['error'][] = 'Username is required';
        $response['count_err']++;
    }

    if(trim($data['email']) == '' ) 
    {
        $response['error'][] = 'Email is required';
        $response['count_err']++;
    }
    if(trim($data['firstname']) == '' ) 
    {
        $response['error'][] = 'Firstname is required';
        $response['count_err']++;
    }
    if(trim($data['lastname']) == '' ) 
    {
        $response['error'][] = 'Lastname is required';
        $response['count_err']++;
    }
    if(trim($data['password']) == '' ) 
    {
        $response['error'][] = 'Password is required';
        $response['count_err']++;
    }
    if(trim($data['confirm']) == '' ) 
    {
        $response['error'][] = 'Confirm is required';
        $response['count_err']++;
    }

    if(trim($data['confirm']) != '' && trim($data['password']) != '' && $data['password'] != $data['confirm'] )
    {
        $response['error'][] = 'Password Mismatch is required';
        $response['count_err']++;
    }

    if($response['count_err'] == 0)
    {
       $result = wp_insert_user(array(
                        'user_login'    => $wpdb->escape($data['username']),
                        'user_pass'     => $wpdb->escape($data['password']),
                        'user_email'    => $wpdb->escape($data['email']),  
                        'first_name'    => $wpdb->escape($data['firstname']),   
                        'last_name'     => $wpdb->escape($data['last_name']),   
                        'role'          => 'ads_author' 
                       ));
       if($result)
       {
            $response['msg'] = 'Successfully added';
       } 
       else 
       {
             $response['count_err']++;
            $response['msg'] = 'Unable to add Agent';
       }

    } 
    else
    {
        $response['msg'] = 'Please check your data inputed';
    }
    return $response;


}

/**
 * get agent information
 *
 * @access public
 * @param int - agent id
 * @return none
 **/

 function get_agent($user) 
 {
    return  get_userdata($user);
 }

/**
 * get agent meta value
 *
 * @access public
 * @param userid, user key, single
 * @return metavalue
 **/
 function get_agent_meta($user,$key = 'last_name',$single = true)
 {
    return get_user_meta( $user, $key, $single );
 }




/**
 * set expiration of the ads
 *
 * @access public
 * @param none
 * @return none
 **/

 add_action('wp_ajax_set-ads-xpyr-date', 'set_ads_expiration_date');

 function set_ads_expiration_date()
 {
    
    $post = $_POST;
    $flag = FALSE;
    $response['error'] = array();
    $response['error_count'] = 0;

    if (trim($post['from']) == '') 
    {
        $response['error'][]= 'Date From is required!';
        $response['error_count']++;
    }
    if (trim($post['to']) == '') 
    {
        $response['error'][]= 'Date To is required!';
        $response['error_count']++;
    }
    if(trim($post['location']) == '') 
    {
        $response['error'][]= 'Cannot find location!';
        $response['error_count']++;
    }
    if($response['error_count'] == 0) 
    {

        
        $result = set_ads_expyr_date($post);

        if($result)
        {
            $flag = TRUE;
            $response['msg'] = 'Successfully Save!';
        } 
        else 
        {
            $response['error_count']++;
            $response['msg'] = 'No changes commited!';
        }
    } 
    else 
    {
         $response['msg'] = 'Unable to process you query!';
    }

    echo json_encode(array('status' => $flag,'response' => $response));
    die();
 }


function set_ads_expyr_date($post) 
{
    global $wpdb;

    $sql = 'SELECT * FROM '. $wpdb->prefix .'ads_meta WHERE post_id = %d AND location = %s';

    $data = $wpdb->query($wpdb->prepare($sql,$post['post_id'], $post['location']));

    if($data)
    {
        $result = $wpdb->update($wpdb->prefix. 'ads_meta',array('from' => $post['from'],'to'=> $post['to'],'status' => $post['status']),array('post_id' => $post['post_id'] ,'location' => $post['location']),array('%s','%s'),array('%d','%s'));
    } 
    else
    {
        $result = $wpdb->insert($wpdb->prefix. 'ads_meta',array('post_id' => $post['post_id'] ,'location' => $post['location'],'from' => $post['from'],'to'=> $post['to'],'status' => $post['status']),array('%d','%s','%s','%s'));
    }

    return $result;
}

/**
 * get ads expiration
 *
 * @access public
 * @param none
 * @return none
 **/

 function get_ads_xpyr_date($post,$location)
 {
    global $wpdb;

    $sql = 'SELECT * FROM '. $wpdb->prefix .'ads_meta WHERE post_id = %d AND location = %s';
    $data = $wpdb->get_row($wpdb->prepare($sql,$post, $location),ARRAY_A);

    return $data;
 }



/**
 * insert transportation
 *
 * @access public
 * @param Post
 * @return array
 **/

 function insert_new_transportation($data)
 {
    global $wpdb;
    $response['error'] = array();
    $response['count_err'] = 0;
    $response['message'] = '';

    if(trim($data['transportation']) == '' ) 
    {
        $response['error'][] = 'Transportation is required';
        $response['count_err']++;
    }

    if(trim($data['price']) == '' ) 
    {
        $response['error'][] = 'Price is required';
        $response['count_err']++;
    } 
    else if(!validate_decimal(trim($data['price']))) 
    {
        $response['error'][] = 'Price is Invalid';
        $response['count_err']++;
    }
  

    if($response['count_err'] == 0)
    {
        if($data['action'] == 'edit')
        {

            $result = $wpdb->update($wpdb->prefix.'transportation',
                                    array('transportation' => $wpdb->escape($data['transportation']),'price' => $wpdb->escape($data['price'])),
                                    array('ID' => $data['tid']),
                                    array('%s','%f'),
                                    array('%d')
                                    );
        } 
        else 
        {
            $result = $wpdb->insert($wpdb->prefix.'transportation',array('transportation' => $wpdb->escape($data['transportation']),'price' => $wpdb->escape($data['price'])),array('%s','%f'));    
        }    
        if($result)
        {
            $response['message'] = 'Successfully Saved!';
        } 
        else 
        {
             $response['count_err']++;
            $response['error'][]  = 'No changes on the data';
       }

    } 
    else
    {
        $response['count_err']++;
        $response['error'][] = 'Please check your data inputed';
    }

    return $response;


}


/*

function insert_accomodiation($data) {
    global $wpdb;

    $args = array(
                  'firstname'           => $wpdb->escape($data['firstname']),
                  'lastname'            => $wpdb->escape($data['lastname']),
                  'cus_address'         => $wpdb->escape($data['cus_address']),
                  'country'             => $wpdb->escape($data['country']),
                  'state'               => $wpdb->escape($data['state']),
                  'city'                => $wpdb->escape($data['city']),
                  'zipcode'             => $wpdb->escape($data['zipcode']),
                  'email'               => $wpdb->escape($data['email']),
                  'mobile'              => $wpdb->escape($data['mobile']),
                  'check_in'            => $wpdb->escape($data['check_in']),
                  'check_out'           => $wpdb->escape($data['check_out']),
                  'no_of_persons'       => $wpdb->escape($data['no_of_persons']),
                  'with_transport'      => $wpdb->escape($data['with_transport']),
                  'travel_from'         => $wpdb->escape($data['travel_from']),
                  'travel_to'           => $wpdb->escape($data['travel_to']),
                  'pick_up_point'       => $wpdb->escape($data['pick_up_point']),
                  'specific_address'    => $wpdb->escape($data['specific_address']),
                  'message'             => $wpdb->escape($data['message']),
                  'transport_services'  => $wpdb->escape($data['transport_services']),
                  'total_amount'        => $wpdb->escape($data['amount']),   
                  'payment_type'        => $wpdb->escape($data['payment_type']),
                  );

    $result = $wpdb->insert($wpdb->prefix. 'accomodation',$args,$format);
}
*/

/**
 * get transportation lists
 * @access public
 * @param transportation_id, return_type
 * @return array
 **/

function get_transportation( $trans_id = FALSE, $return_type = 'ARRAY_A' ) {
    global $wpdb;

    if($trans_id === FALSE) 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix . 'transportation';
        $result = $wpdb->get_results($sql, $return_type); 
    } 
    else 
    {
        $sql = 'SELECT * FROM '. $wpdb->prefix . 'transportation WHERE ID = %d';
        $result = $wpdb->get_row($wpdb->prepare($sql,$trans_id), $return_type); 
    }


   return $result;
}


/**
 * get country lists
 * @access public
 * @param country_code, return_type
 * @return array
 **/

function get_country($code = FALSE, $return_type = 'ARRAY_A') {
    global $wpdb;
    if( $code === FALSE ) 
    {
        $result = $wpdb->get_results('SELECT code,country FROM '. $wpdb->prefix .'country', $return_type);
    } 
    else 
    {   
        $sql = 'SELECT code,country FROM '. $wpdb->prefix .'country';
        $result = $wpdb->get_row($wpdb->prepare($sql), $return_type);

    }
    return $result;
}


/**
 * insert accomodation information
 * @access public
 * @param $_POST
 * @return response
 **/

function save_accomodation_information($data) {
    global $wpdb;

    $trans_services = array();
    foreach($data['trasport_quantity'] as $key=>$val) {
        $trans_info = get_transportation($key);
        //check if transportation is selected
        if(in_array($key,$data['transport_service'])) {
            $trans_services[] = array('trans_id' => $key,'qty' => $val, 'price' => $trans_info['price']); 
        }

    }
    
    $args = array('package_id'              => $data['itemNo'],
                  'transaction_id'          => $data['transID'],
                  'firstname'               => $data['firstname'],
                  'lastname'                => $data['lastname'],
                  'cus_address'             => $data['cus_address'],
                  'country'                 => $data['country'],
                  'state'                   => $data['state'],
                  'city'                    => $data['city'],
                  'zipcode'                 => $data['zip_code'],
                  'email'                   => $data['email'],
                  'mobile'                  => $data['mobile'],
                  'check_in'                => $wpdb->escape($data['check_in']),
                  'check_out'               => $wpdb->escape($data['check_out']),
                  'no_of_persons'           => $data['no_of_persons'],
                  'with_transport'          => $data['with_transfortation'],
                  'travel_from'             => $wpdb->escape($data['travel_date_from']),
                  'travel_to'               => $wpdb->escape($data['travel_date_to']),
                  'pick_up_point'           => $data['pick_up_point'],
                  'specific_address'        => $data['specific_address'],
                  'message'                 => $data['message'],
                  'transport_services'      => serialize($trans_services),
                  'package_price'           => $data['itemprice'],
                  'package_qty'             => $data['itemQTY'],
                  'total_amount'            => $data['totalamount'],
                  'accomodation_date'       => strtotime('now'),
                  'payment_type'            => $data['payment_method'],
                  'accomodation_status'     => $data['accomodation_status']);
    
    $format = array('%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%d','%s','%s','%s','%s','%s','%s','%f','%d','%f','%d','%s','%s');
    $wpdb->insert($wpdb->prefix . 'accomodation',$args, $format);
    echo '<pre>';
    print_r($wpdb->show_errors());
    print_r($args);
    print_r($result);
    echo '</pre>';
   

}


function get_enum_field($table, $column) {
    global $wpdb;
    $sql = "SELECT column_type FROM information_schema.columns WHERE table_name = '". $table ."' AND column_name = '". $column ."'";

    $result = $wpdb->get_var($sql);
    $result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $result);
    $arr = explode("','", $result);
    return $arr;

}