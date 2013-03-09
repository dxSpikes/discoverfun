<?php

ob_start();
require ('highlights.php');
require ('features.php');
require ('agent.php');
require ('transaction.php');
require ('transportation.php');
require ('payment-settings.php');

/**
*register custom plugin called package
*@param none
*@return none
*/

function init_package() {
     $labels = array(
    'name'                => 'Packages',
    'singular_name'       => 'Package',
    'add_new'             => 'Add New',
    'add_new_item'        => 'Add New Package',
    'edit_item'           => 'Edit Package',
    'new_item'            => 'New Package',
    'all_items'           => 'All Packages',
    'view_item'           => 'View Package',
    'search_items'        => 'Search Packages',
    'not_found'           =>  'No Packages found',
    'not_found_in_trash'  => 'No Packages found in Trash', 
    'parent_item_colon'   => '',
    'menu_name'           => 'Packages'
  );

  $args = array(
    'labels'              => $labels,
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true, 
    'show_in_menu'        => true, 
    'query_var'           => true,
    'rewrite'             => array( 'slug' => 'package' ),
    'capability_type'     => 'post',
    'has_archive'         => true, 
    'hierarchical'        => false,
    'menu_position'       => null,
    'supports'            => array( 'title', 'editor', 'author', 'thumbnail')
  ); 

  register_post_type( 'package', $args );


}
//initialize init_package function
add_action( 'init', 'init_package' );


/**
*add metabox to package | Define the custom box
*@param none
*@return none
*/


add_action( 'add_meta_boxes', 'package_add_custom_box' );

add_action( 'save_post', 'package_save_postdata' );

function package_add_custom_box() {
    $screens = array( 'package' );
    foreach ($screens as $screen) {
        add_meta_box(
            'package-some-info',
            __( 'Other Information', 'discoverfun' ),
            'package_inner_custom_box',
            $screen
        );
         
         add_meta_box(
            'package-image',
            __( 'Upload Image', 'discoverfun' ),
            'package_image_custom_box',
            $screen
        );

       
    }
}

/* Prints the box content */
function package_inner_custom_box( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  $price = get_post_meta( $post->ID, $key = 'package_price', $single = true );
  $highlight = get_post_meta( $post->ID, $key = 'package_highlight', $single = true );
  $transpo = get_post_meta( $post->ID, $key = 'package_transportation', $single = true );

  $html = '';
  $html .= '<p>';
  $html .= '<label for="price">';
  $html .= __("Price", 'discoverfun' );
  $html .= '</label> ';
  $html .= '<input type="text" id="price" name="price" value="'.esc_attr($price).'" size="10" />';
  $html .= '</p>';
  $html .= '<p>';
  $html .= '<label for="highlights">';
  $html .= __("Highlights","discoverfun");
  $html .= '</label>';
  
  $package_highlights = (unserialize($highlight));

  $highlights = get_highlight();

  if($highlights){
    $html .= '<ul style="max-height: 200px;overflow-y: auto;overflow-x: hidden;">';
    foreach($highlights as $item){
      $selected =  is_array($package_highlights) ? (in_array($item['ID'],$package_highlights)) ? 'checked="checked"' : '' : '';

      $html .= '<li><input type="checkbox" name="highlight[]" value="'. $item['ID'] .'" '. $selected .' />'. $item['highlight'].'</li>';
    }
    $html .= '</ul>';
  }

  $html .= '</p>';
  $html .= '<p>';
  $html .= '<label for="highlights">';
  $html .= __("Transportation","discoverfun");
  $html .= '</label>';
  $package_tanspo = (unserialize($transpo));

  $transpos = get_transportation();

  if($transpos){
    $html .= '<ul style="max-height: 200px;overflow-y: auto;overflow-x: hidden;">';
    foreach($transpos as $item){
      $selected =  is_array($package_tanspo) ? (in_array($item['ID'],$package_tanspo)) ? 'checked="checked"' : '' : '';

      $html .= '<li><input type="checkbox" name="transportation[]" value="'. $item['ID'] .'" '. $selected .' />'. $item['transportation'].'</li>';
    }
    $html .= '</ul>';
  }
  $html .= '</p>';
 

  echo $html;
}
function package_uploaded_image_custom_box ($post) {

    $html = '';
    $html .= '<button type="button" id="delete-image" class="btn btn-danger delete"><i class="icon-trash icon-white"></i><span>Delete</span></button>';
    $html .= '<table class="image-tble">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<td><input type="checkbox" id="checkAll"/></td>';
    $html .= '<td>Thumbnails</th>';
    $html .= '<td>'. __('Caption') . '</td>';
    $html .= '<td>'. __('Description') .'</td>';
    //$html .= '<td>'. __('Size') .'</td>';
    //$html .= '<td>'. __('Type') .'</td>';
    $html .= '<td>'. __('Featured') .'</td>';
    $html .= '<td>'. __('Action') .'</td>';
    $html .= '</td>';
    $html .= '</thead>';
    foreach(get_post_images($post->ID) as $image):
    $img = unserialize($image['other_info']);
    $size = $img['size'];
    $image_type = $img['type'];
    $checked = ($image['featured'] == 1) ? 'checked="checked"' : '';

    $html .= '<tr>';
    $html .= '<td><input type="checkbox" name="img_id[]" value="'. $image['ID'] .'" /></td>';
    $html .= '<td><img src="' . UPLOAD_THUMB_DIR. $image['image'] . '" height="70" width="100"/></td>';
    $html .= '<td><input type="text" name="caption[]" size="50" value="'. stripslashes($image['caption']) .'"  disabled/></td>';
    $html .= '<td><textarea name="description[]" rows="3" cols="70"  disabled>'. stripslashes($image['description']) .'</textarea></td>';
    //$html .= '<td>'. bytes($size) .'</td>';
    //$html .= '<td>'. $image_type .'</td>';
    $html .= '<td><input type="checkbox" value="1" name="featured" '. $checked .'/></td>';
    $html .= '<td><a class="edit-thumb" style="cursor:pointer;cursor:hand;">EDIT</a></td>';
   
    $html .= '</tr>';
    endforeach;
    $html .= '</table>';
    
    echo $html;
}

function package_image_custom_box () {
   $html .= '<iframe src="'. plugins_url('includes/jQuery-File-Upload-master/index.html',__FILE__) .'" style="width:100%;height:300px;background:none;padding:0;margin:0;"/></iframe>';
   $html .= '<div id="files"></div>';

    echo $html;
}

/* When the post is saved, saves our custom data */
function package_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  
  // Check permissions
  if ( 'package' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  // OK, we're authenticated: we need to find and save the data

  //if saving in a custom table, get post_ID
  $post_ID = $_POST['post_ID'];
  //sanitize user input
  $price = sanitize_text_field( $_POST['price'] );

  // Do something with $mydata 
  // either using 
  add_post_meta($post_id, 'package_price', $price, true) or
  update_post_meta($post_id, 'package_price', $price);
  
  $highlight = serialize($_POST['highlight']);
  add_post_meta($post_id, 'package_highlight', $highlight, true) or
  update_post_meta($post_id, 'package_highlight', $highlight);

  $transportation = serialize($_POST['transportation']);
  add_post_meta($post_id, 'package_transportation', $transportation, true) or
  update_post_meta($post_id, 'package_transportation', $transportation);

  //insert images to db
  insert_post_images($post_id,$_POST['filenames']);
   //set expiration date to listing
  $from = date('m/d/Y H:i');
  $to = date('m/d/Y H:i', strtotime($from . ' + 1 day'));
  $args = array(
                'post_id' => $post_id,
                'from'  => $from,
                'to'  => $to,
                'location' => 'LISTING',
                'status' => 1,
          );

  set_ads_expyr_date($args);

}


add_filter( 'manage_edit-package_columns', 'set_custom_edit_package_columns' );
add_action( 'manage_package_posts_custom_column' , 'custom_package_column', 10, 2 );

function set_custom_edit_package_columns($columns) {
    unset($columns['author']);
    unset($columns['date']);
    $columns = $columns;
  if(current_user_can( 'manage_options' )) {       
    $columns = $columns + array('listing'=> __('Listing'), 'package' => __('Package'));
  }  
   return $columns;
}

function custom_package_column( $column, $post_id ) {
    switch ( $column ) {
      case ('listing' || 'package' ):
        $data = get_ads_xpyr_date($post_id,strtoupper($column));
        $chckd = ($data['status'] == 1 ) ? 'checked="checked"' :''; 
        echo '<ul class="action">';
        echo '<li><input type="checkbox" name="status" value="1" '. $chckd .' />'. __('Enable'). '</li>';
        echo '<li class="edit"><a href="#">'. __('edit') .'</a></li>';
        echo '<li class="cancel"><a href="#">'. __('cancel') .'</a></li>';
        echo '</ul>';
        if($data['from'] != '' && $data['to'] != ''){
          echo '<ul class="banner-xpyr-date">';
          echo '<li><small>'.__('From: ') . $data['from'] .'</small></li>';
          echo '<li><small>'.__('To: '). $data['to'] .'</small></li>';
          echo '</ul>'; 
        }
          echo '<ul class="banner-ul">';
          echo '<li><small>'.__('From') .'</small> <input type="text" size="10" class="from" value="'. $data['from'] .'" name="resort_from" /></li>';
          echo '<li><small>'.__('To'). '</small> <input type="text" size="10" class="to" value="'. $data['to'] .'" name="resort_to" /></li>';
          echo '</ul>';   
       
        echo '<input type="hidden" name="location" value="'. strtoupper($column) .'" />';  
        break;
    }
}

/**
*register custom post called ads
*@param none
*@return none
*/

function init_ads() {





  $labels = array(
    'name' => 'Ads',
    'singular_name' => 'Ads',
    'add_new' => 'Add New',
    'add_new_item' => 'Add New Ads',
    'edit_item' => 'Edit Ads',
    'new_item' => 'New Ads',
    'all_items' => 'All Ads',
    'view_item' => 'View Ads',
    'search_items' => 'Search Ads',
    'not_found' =>  'No Packages found',
    'not_found_in_trash' => 'No Ads found in Trash', 
    'parent_item_colon' => '',
    'menu_name' => 'Advertisement'
  );

 

  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => 'ads' ),
    'capability_type' => 'ads',
    'capabilities' => array(
        'publish_posts'       => 'publish_ads',
        'edit_posts'          => 'edit_ads',
        'edit_others_posts'   => 'edit_others_ads',
        'delete_posts'        => 'delete_ads',
        'delete_others_posts' => 'delete_others_ads',
        'read_private_posts'  => 'read_private_ads',
        'edit_post'           => 'edit_ad',
        'delete_post'         => 'delete_ad',
        'read_post'           => 'read_ad',
      ),
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => $supports
  ); 

  register_post_type( 'ads', $args );
 

}

//initialize init_package function
add_action( 'init', 'init_ads' );


add_filter( 'map_meta_cap', 'my_map_meta_cap', 10, 4 );

function my_map_meta_cap( $caps, $cap, $user_id, $args ) {

  /* If editing, deleting, or reading a ad, get the post and post type object. */
  if ( 'edit_ad' == $cap || 'delete_ad' == $cap || 'read_ad' == $cap ) {
    $post = get_post( $args[0] );
    $post_type = get_post_type_object( $post->post_type );

    /* Set an empty array for the caps. */
    $caps = array();
  }

  /* If editing a ad, assign the required capability. */
  if ( 'edit_ad' == $cap ) {
    if ( $user_id == $post->post_author )
      $caps[] = $post_type->cap->edit_posts;
    else
      $caps[] = $post_type->cap->edit_others_posts;
  }

  /* If deleting a ad, assign the required capability. */
  elseif ( 'delete_ad' == $cap ) {
    if ( $user_id == $post->post_author )
      $caps[] = $post_type->cap->delete_posts;
    else
      $caps[] = $post_type->cap->delete_others_posts;
  }

  /* If reading a private ad, assign the required capability. */
  elseif ( 'read_ad' == $cap ) {

    if ( 'private' != $post->post_status )
      $caps[] = 'read';
    elseif ( $user_id == $post->post_author )
      $caps[] = 'read';
    else
      $caps[] = $post_type->cap->read_private_posts;
  }

  /* Return the capabilities required by the user. */
  return $caps;
}


add_action('pre_get_posts', 'filter_posts_list');

function filter_posts_list($query)
{
    //$pagenow holds the name of the current page being viewed
     global $pagenow;
 
    //$current_user uses the get_currentuserinfo() method to get the currently logged in user's data
     global $current_user;
     get_currentuserinfo();
     
        //Shouldn't happen for the admin, but for any role with the edit_posts capability and only on the posts list page, that is edit.php
      if(!current_user_can('administrator') && current_user_can('edit_posts') && ('edit.php' == $pagenow))
     {
        //global $query's set() method for setting the author as the current user's id
        $query->set('author', $current_user->ID); 
        
      }

        $screen = (function_exists('get_current_screen')) ? get_current_screen() : null;
        add_filter('views_'.$screen->id, 'remove_post_counts');
}


function remove_post_counts($posts_count_disp)
{
    //$posts_count_disp contains the 3 links, we keep 'Mine' and remove the other two.
    unset($posts_count_disp['all']);
        unset($posts_count_disp['publish']);
     
        return $posts_count_disp;
}
/* Define the custom box */

add_action( 'add_meta_boxes', 'ads_add_custom_box' );

// backwards compatible (before WP 3.0)
// add_action( 'admin_init', 'myplugin_add_custom_box', 1 );

/* Do something with the data entered */
add_action( 'save_post', 'ads_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function ads_add_custom_box() {
  

    $screens = array( 'ads' );
    foreach ($screens as $screen) {
      global $post;
      $author_id=$post->post_author;
      if(is_exist_role('ads_author',$author_id)) {
        add_meta_box(
            'ads-some-info',
            __( 'Other Information', 'discoverfun' ),
            'ads_inner_custom_box',
            $screen
        );
        add_meta_box(
              'ads-map',       
               __('Map','discoverfun'),      
              'ads_map_custom_box');
      } else {
        remove_post_type_support('ads', 'editor');
      }


        
        add_meta_box(
              'ads-uploaded-photo',       
               __('Uploaded Photo','discoverfun'),      
              'package_uploaded_image_custom_box');
        
         add_meta_box(
            'ads-image-upload',
            __( 'Upload Images', 'discoverfun' ),
            'ads_image_custom_box',
            $screen
        );
        add_meta_box(
            'ads-address',
            __( 'Address', 'discoverfun' ),
            'ads_address_custom_box',
            $screen
        );
    }
}

function ads_address_custom_box($post){
  $location_value   = get_post_meta( $post->ID, $key = 'location', $single = true );
  $html .= '<p>';
  $html .= '<label for="location">';
  $html .= __("Address","discoverfun");
  $html .= '<input type="text" id="location" name="location" value="'.esc_attr($location_value).'" size="45" />';
  $html .= '</label>';
  $html .= '</p>';
  echo $html;
}

function ads_map_custom_box($post){
   $latitude   = get_post_meta( $post->ID, $key = 'latitude', $single = true );
   $longitude   = get_post_meta( $post->ID, $key = 'longitude', $single = true );

   $latitude = (empty($latitude))? 10.31344 :  $latitude ;
   $longitude = (empty($longitude))? 123.88381 : $longitude ;

  $html .= '  <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAgrj58PbXr2YriiRDqbnL1RSqrCjdkglBijPNIIYrqkVvD1R4QxRl47Yh2D_0C1l5KXQJGrbkSDvXFA"
      type="text/javascript"></script>
    <script type="text/javascript">

 function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        var center = new GLatLng('. $latitude .', '. $longitude .');
        map.setCenter(center, 15);
        geocoder = new GClientGeocoder();
        var marker = new GMarker(center, {draggable: true});  
        map.addOverlay(marker);
        document.getElementById("latitude").value = center.lat().toFixed(5);
        document.getElementById("longitude").value = center.lng().toFixed(5);

      GEvent.addListener(marker, "dragend", function() {
       var point = marker.getPoint();
          map.panTo(point);
       document.getElementById("latitude").value = point.lat().toFixed(5);
       document.getElementById("longitude").value = point.lng().toFixed(5);

        });


     GEvent.addListener(map, "moveend", function() {
          map.clearOverlays();
    var center = map.getCenter();
          var marker = new GMarker(center, {draggable: true});
          map.addOverlay(marker);
          document.getElementById("latitude").value = center.lat().toFixed(5);
       document.getElementById("longitude").value = center.lng().toFixed(5);


     GEvent.addListener(marker, "dragend", function() {
      var point =marker.getPoint();
         map.panTo(point);
      document.getElementById("latitude").value = point.lat().toFixed(5);
         document.getElementById("longitude").value = point.lng().toFixed(5);

        });
 
        });

      }
    }
    document.body.onload = load;

    </script>';
  $html .= '<p>';
  $html .= '<label for="map">';
  $html .= __("Latitude","discoverfun");
  $html .= '<input type="text" size="20" name="latitude" id="latitude"/>';
  $html .= '</label>';
  $html .= '</p>';
  $html .= '<p>';
  $html .= '<label for="map">';
  $html .= __("Longitude","discoverfun");
  $html .= '<input type="text" size="20" name="longitude" id="longitude"/>';
  $html .= '</label>';
  $html .= '</p>';
  $html .= '<div align="center" id="map" style="width: 100%; height: 400px"><br/></div>';
  //$html .= '<iframe src="'. PLUGIN_URL .'admin/map.html" style="width:100%;height:300px;"></iframe>';
  echo $html;
}

/* Prints the box content */
function ads_inner_custom_box( $post ) {

  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'ads_noncename' );

  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  $contact_value    = get_post_meta($post->ID, $key = 'contact', $single = true );
  $ads_features = get_post_meta( $post->ID, $key = 'ads_features', $single = true );
  $website = get_post_meta( $post->ID, $key = 'website', $single = true );

  $html = '';
  $html .= '<p>';
  $html .= '<label for="contact">';
  $html .= __("Contact", 'discoverfun' );
  $html .= '</label> ';
  $html .= '<input type="text" id="contact" name="contact" value="'.esc_attr($contact_value).'" size="30" />';
  $html .= '</p>';
  $html .= '<p>';
  $html .= '<label for="contact">';
  $html .= __("Website", 'discoverfun' );
  $html .= '</label> ';
  $html .= '<input type="text" id="website" name="website" value="'.esc_attr($website).'" size="60" />';
  $html .= '</p>';
  $html .= '<p>';
  $html .= '<label for="features">';
  $html .= __("Features","discoverfun");
  $html .= '</label>';
  
  $features = (unserialize($ads_features));

  $all_features = get_feature();

  if($all_features){
    $html .= '<ul style="max-height: 200px;overflow-y: auto;overflow-x: hidden;">';
    foreach($all_features as $item){
      $selected = is_array($features) ? (in_array($item['ID'],$features)) ? 'checked="checked"' : '' : '';

      $html .= '<li><input type="checkbox" name="feature[]" value="'. $item['ID'] .'" '. $selected .' />'. $item['feature'].'</li>';
    }
    $html .= '</ul>';
  }

  $html .= '</p>';

  echo $html;
}

function ads_image_custom_box () {
   $html .= '<iframe src="'. plugins_url('includes/jQuery-File-Upload-master/index.html',__FILE__) .'" style="width:100%;height:300px;background:none;padding:0;margin:0;"/></iframe>';
   $html .= '<div id="files"></div>';

    echo $html;
}

/* When the post is saved, saves our custom data */
function ads_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return;

  
  // OK, we're authenticated: we need to find and save the data

  //if saving in a custom table, get post_ID
  $post_ID = $_POST['post_ID'];
  //sanitize user input
  $location = sanitize_text_field( $_POST['location'] );
  $contact = sanitize_text_field( $_POST['contact'] );
  // Do something with $mydata 
  // either using 
  add_post_meta($post_ID, 'contact', $contact, true) or
  update_post_meta($post_ID, 'contact', $contact);

  add_post_meta($post_ID, 'location', $location, true) or
  update_post_meta($post_ID, 'location', $location);

  $feature = serialize($_POST['feature']);
  add_post_meta($post_id, 'ads_features', $feature, true) or
  update_post_meta($post_id, 'ads_features', $feature);

  $latitude = sanitize_text_field($_POST['latitude']);
  add_post_meta($post_id, 'latitude', $latitude, true) or
  update_post_meta($post_id, 'latitude', $latitude);

  $longitude = sanitize_text_field($_POST['longitude']);
  add_post_meta($post_id, 'longitude', $longitude, true) or
  update_post_meta($post_id, 'longitude', $longitude);

  $website = sanitize_text_field($_POST['website']);
  add_post_meta($post_id, 'website', $website, true) or
  update_post_meta($post_id, 'website', $website);

  insert_post_images($post_id,$_POST['filenames']);
  //set expiration date to listing
  $from = date('m/d/Y H:i');
  $to = date('m/d/Y H:i', strtotime($from . ' + 1 day'));
  $args = array(
                'post_id' => $post_id,
                'from'  => $from,
                'to'  => $to,
                'location' => 'LISTING',
                'status' => 1,
          );

  set_ads_expyr_date($args);
}


  



add_filter( 'manage_edit-ads_columns', 'set_custom_edit_ads_columns' );
add_action( 'manage_ads_posts_custom_column' , 'custom_ads_column', 10, 2 );

function set_custom_edit_ads_columns($columns) {
    unset($columns['author']);
    unset($columns['date']);
    $columns = $columns 
         + array('type' => __('Type'));
  if(current_user_can( 'manage_options' )) {       
    $columns = $columns + array('listing'=> __('Listing'), 'home' => __('Home'),'hotel' => __('Hotel'), 'restaurant' =>  __('Restaurant'),'resort' => __('Resort'));
  }  
   return $columns;
}

function custom_ads_column( $column, $post_id ) {
    switch ( $column ) {
      case 'type':
        if (  is_exist_role('ads_author',  get_post($post_id)->post_author ) ) {
            echo 'Premium';
        } else {
            echo 'Free';
        }
        break;
      case (('listing' || 'home' || 'hotel' || 'restaurant' || 'resort') && current_user_can( 'manage_options' ) ):
        $data = get_ads_xpyr_date($post_id,strtoupper($column));
        $chckd = ($data['status'] == 1 ) ? 'checked="checked"' :'';
       
        echo '<ul class="action">';
        echo '<li><input type="checkbox" name="status" value="1" '. $chckd .' />'. __('Enable'). '</li>';
        echo '<li class="edit"><a href="#">'. __('edit') .'</a></li>';
        echo '<li class="cancel"><a href="#">'. __('cancel') .'</a></li>';
        echo '</ul>';
        if($data['from'] != '' && $data['to'] != ''){
          echo '<ul class="banner-xpyr-date">';
          echo '<li><small>'.__('From: ') . $data['from'] .'</small></li>';
          echo '<li><small>'.__('To: '). $data['to'] .'</small></li>';
          echo '</ul>'; 
        }
          echo '<ul class="banner-ul">';
          echo '<li><small>'.__('From') .'</small> <input type="text" size="10" class="from" value="'. $data['from'] .'" name="resort_from" /></li>';
          echo '<li><small>'.__('To'). '</small> <input type="text" size="10" class="to" value="'. $data['to'] .'" name="resort_to" /></li>';
          echo '</ul>';  
       
        echo '<input type="hidden" name="location" value="'. strtoupper($column) .'" />';  
        break;
    }
}

//manage sortable column
add_filter( 'manage_edit-ads_sortable_columns', 'my_ads_sortable_columns' );

function my_ads_sortable_columns( $columns ) {

  $columns['type'] = 'type';

  return $columns;
}



//hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_ads_taxonomies', 0 );

//create two taxonomies, genres and writers for the post type "book"
function create_ads_taxonomies() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'                => _x( 'Establishment', 'taxonomy general name' ),
    'singular_name'       => _x( 'Establishment', 'taxonomy singular name' ),
    'search_items'        => __( 'Search Establishments' ),
    'all_items'           => __( 'All Establishments' ),
    'parent_item'         => __( 'Parent Establishments' ),
    'parent_item_colon'   => __( 'Parent Establishments:' ),
    'edit_item'           => __( 'Edit Establishment' ), 
    'update_item'         => __( 'Update Establishment' ),
    'add_new_item'        => __( 'Add New Establishment' ),
    'new_item_name'       => __( 'New Establishment Name' ),
    'menu_name'           => __( 'Establishment' )
  );  

  $args = array(
    'hierarchical'        => true,
    'labels'              => $labels,
    'show_ui'             => true,
    'show_admin_column'   => true,
    'query_var'           => true,
    'rewrite'             => array( 'slug' => 'establishment' ),
     'capabilities' => array(
      'manage_terms' => 'manage_establishment',
      'edit_terms' => 'edit_establishment',
      'delete_terms' => 'delete_establishment',
      'assign_terms' => 'assign_establishment'
    ),
  );

register_taxonomy( 'establishment', array( 'ads' ), $args );

}





/**
*register menu
*@param none
*@return none
*/



function init_menu(){
    add_menu_page('Discover Fun', 'Discover Fun','publish_ads','discover-fun','discover_fun');
    $hightlight = add_submenu_page('discover-fun','Accomodation Log','Accomodation Log','administrator','accomodation','init_accomodation_log');
    $hightlight = add_submenu_page('discover-fun','Package Hightlights','Package Hightlights','publish_ads','hightlights','init_highlights');
    $feature = add_submenu_page('discover-fun','Features','Features','publish_ads','features','init_features');
    $transportations = add_submenu_page('discover-fun','Transportation Manager','Transportation Manager','administrator','transportations','init_transportations');
    $ads = add_submenu_page('discover-fun','Payment Settings','Payment Settings','administrator','payment-setting','init_payment_manager');
    $ads_author = add_submenu_page('discover-fun','Ads Agent','Ads Agent','administrator','agent','init_ads_agent_manager');
}


add_action('admin_menu','init_menu');

/**
*discover fun function
*@param none
*@return none
*/




function init_admin_script(){
    global $typenow; 

    if(!wp_style_is('custom-style')) {
        wp_register_style('custom-style',PLUGIN_URL .'css/custom.css');
        wp_enqueue_style('custom-style');
    }  

    if(!wp_style_is('jquery-ui-css')) {
        wp_register_style('jquery-ui-css',PLUGIN_URL .'js/jquery-ui-1.10.0.custom/development-bundle/themes/base/jquery-ui.css');
        wp_enqueue_style('jquery-ui-css');
    }

    if(!wp_script_is('jquery-ui-js')) {
        wp_register_script('jquery-ui-js',PLUGIN_URL .'js/jquery-ui-1.10.0.custom/js/jquery-ui-1.10.0.custom.min.js',array('jquery'),FALSE,TRUE);
        wp_enqueue_script('jquery-ui-js');
    }

    if(!wp_script_is('timepicker-addon')) {
        wp_register_script('timepicker-addon',PLUGIN_URL .'js/timepicker-addon.js',array('jquery'),FALSE,TRUE);
        wp_enqueue_script('timepicker-addon');
    }


    if(!wp_script_is('custom-script')) {
        wp_register_script('custom-script', PLUGIN_URL . 'js/admin-custom-script.js',array('jquery','jquery-ui-js'),FALSE,TRUE);
        wp_enqueue_script('custom-script');
    } 
    
    
  
    if ($typenow=="package" || $typenow=="ads") {

      if(!wp_style_is('bootstrap')) {
        wp_register_style('bootstrap', PLUGIN_URL. 'admin/includes/jQuery-File-Upload-master/css/custom-bootstrap.css');
        wp_enqueue_style('bootstrap');
       }  
      
      

    }


    

   

}


add_action('admin_init','init_admin_script');



function discover_fun(){

}




