<?php
/**
* Hightlights
*/


/**
*manage hightlights
*@param none
*@return none
*/


function manage_agent() {
    require(PLUGIN_PATH .'class/class.agent.php');
    $myListTable =  new agent_List_Table();
    $myListTable->prepare_items(); 
?>
  <form method="POST">
    <input type="hidden" name="page" value="agent">
    <?php
        $myListTable->search_box( 'search', 'search_id' );
        $myListTable->display(); 
    ?>
    </form>
<?php 
}

/**
*manage hightlight form
*@param none
*@return none
*/

function init_ads_agent_manager(){
   
    global $wpdb;
    echo  '<div class="wrap">';
    echo  '<div id="icon-users" class="icon32"><br></div>';
    echo  '<h2>'. __('Agent') . '</h2>';
    echo '<p>Create a brand new user and add it to this site.</p>';

    if($_POST['save-agent']) {
        $post =  $_POST;
        $post['action'] = $wpdb->escape($_GET['action']);
        $post['fid']    = $wpdb->escape($_GET['fid']);
        $result = insert_new_agent($post);
       if( $result['err_count'] != 0 ):
            echo '<div class="error">';
                foreach($result['error'] as $err):
                    echo '<p>' . $err . '</p>'; 
                endforeach;
            echo '</div>';
        else:
            echo '<div class="updated"><p>' . $result['message'] . '</p></div>';

       endif;
    }



    echo  '<ul id="discoverfun-tabs" style="">';
    echo  '<li><a href="admin.php?page=agent" class="'. ( (in_array($_GET['action'],array('edit','delete')) || !empty($_POST['s'])) || !isset($_GET['action'])  ? 'selected' :'' ) .'">'. __('Manage') .'</a></li>'; 
    echo  '<li><a href="admin.php?page=agent&action=new" class="'. (isset($_GET['action']) && $_GET['action'] == 'new' ? 'selected' : ''  ) .'">'. __('Add New') .'</a></li>';
    echo  '</ul>';
    echo  '<div id="tabs-wrapper">'; 
    echo  '<div class="tabs '. (isset($_GET['action']) && $_GET['action'] != 'delete' ?  '' :  'active') .'">';
    manage_agent();
    echo  '</div><!--.tabs-->';
    echo  '<div class="tabs '. (isset($_GET['action']) && $_GET['action'] != 'delete' ? 'active' : '') .'">';
    manage_agent_form();
    echo  '</div><!--.tabs-->';
    echo  '</div><!--#tabs-wrapper-->';
    echo  '</div>';

    echo $html;
}

function manage_agent_form(){
    global $wpdb;
    if(isset($_GET['uid']) && $_GET['uid'] !='' ){
        //$data = get_agent( $wpdb->escape($_GET['fid']) );
        $uid = $_GET['uid'];
        $data = get_agent($wpdb->escape($uid) );
        $firstname = get_agent_meta($uid, 'first_name', true);
        $lastname = get_agent_meta($uid, 'last_name', true);
        $username = $data->user_login;
        $user_email = $data->user_email;

    } else {
        if($_POST) {
            $data    = $_POST;
            $password = $data['password'];
            $confirm = $data['confirm'];
        }
    }

    

    echo  '<form action="#" method="post">';
    echo  '<p>';
    echo  '<label for="username">' . __('Username') .' </label>';
    echo  '<input type="text" name="username" size="30" value="'. $username .'">';
    echo  '</p>';
    echo  '<p>';
    echo  '<label for="email">' . __('Email') .' </label>';
    echo  '<input type="text" name="email" size="30" value="'. $user_email .'">';
    echo  '</p>';
    echo  '<p>';
    echo  '<label for="firstname">' . __('Firstname') .' </label>';
    echo  '<input type="text" name="firstname" size="30" value="'. $firstname .'">';
    echo  '</p>';
    echo  '<p>';
    echo  '<label for="lastname">' . __('Lastname') .' </label>';
    echo  '<input type="text" name="lastname" size="30" value="'. $lastname .'">';
    echo  '</p>';
     echo  '<p>';
    echo  '<label for="password">' . __('Password') .' </label>';
    echo  '<input type="text" name="password" size="30" value="'. $password .'">';
    echo  '</p>';
    echo  '<p>';
    echo  '<label for="confirm">' . __('Confirm Password') .' </label>';
    echo  '<input type="text" name="confirm" size="30" value="'. $confirm .'">';
    echo  '</p>';
    echo  '<label for="confirm">' . __('Send Password?') .' </label>';
    echo  '<input type="checkbox" name="send-password" value="1">';
    echo  __(' Send this password to the new user by email.');
    echo  '</p>';
    echo  '<input type="submit" class="button-primary" name="save-agent" value="Add New User">';
    echo  '<form>';
}