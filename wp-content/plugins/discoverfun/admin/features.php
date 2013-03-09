<?php
/**
* Hightlights
*/


/**
*manage hightlights
*@param none
*@return none
*/


function manage_feature() {
    require(PLUGIN_PATH .'class/class.features.php');
    $myListTable =  new Features_List_Table();
    $myListTable->prepare_items(); 
?>
  <form method="post">
    <input type="hidden" name="page" value="hightlights">
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

function init_features(){
   
    global $wpdb;
    echo  '<div class="wrap">';
    echo  '<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>';
    echo  '<h2>'. __('Features') . '</h2>';

    if($_POST['save-feature']) {
        $post =  $_POST;
        $post['action'] = $wpdb->escape($_GET['action']);
        $post['fid']    = $wpdb->escape($_GET['fid']);
        $result = insert_new_feature($post);
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



    echo  '<ul id="feature-tabs" style="">';
    echo  '<li><a href="admin.php?page=features" class="'. ( ($_GET['action'] == 'edit' || $_GET['action'] == 'delete') || !isset($_GET['action'])  ? 'selected' :'' ) .'">'. __('Manage') .'</a></li>'; 
    echo  '<li><a href="admin.php?page=features&action=new" class="'. (isset($_GET['action']) && $_GET['action'] == 'new' ? 'selected' : ''  ) .'">'. __('Add New') .'</a></li>';
    echo  '</ul>';
    echo  '<div id="tabs-wrapper">'; 
    echo  '<div class="tabs '. (isset($_GET['action']) && $_GET['action'] != 'delete' ?  '' :  'active') .'">';
    manage_feature();
    echo  '</div><!--.tabs-->';
    echo  '<div class="tabs '. (isset($_GET['action']) && $_GET['action'] != 'delete' ? 'active' : '') .'">';
    manage_feature_form();
    echo  '</div><!--.tabs-->';
    echo  '</div><!--#tabs-wrapper-->';
    echo  '</div>';

    echo $html;
}

function manage_feature_form(){
    global $wpdb;
    if(isset($_GET['fid']) && $_GET['fid'] !='' ){
        $data = get_feature( $wpdb->escape($_GET['fid']) );
        
        //$query = 'SELECT * FROM '. $wpdb->prefix .'feature WHERE ID ='. $wpdb->escape($_GET['feature']);
        //$data = $wpdb->get_row($query,ARRAY_A);
    }

    

    echo  '<form action="#" method="post">';
    echo  '<p>';
    echo  '<label for="feature">' . __('Feature') .' </label>';
    echo  '<input type="text" name="feature" size="50" value="'. $data['feature'] .'">';
    echo  '</p>';
    echo  '<input type="submit" class="button-primary" name="save-feature" value="Save">';
    echo  '<form>';
}