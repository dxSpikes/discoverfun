<?php
/**
*manage hightlights
*@param none
*@return none
*/

function manage_transportation() {
    require(PLUGIN_PATH .'class/class.transportations.php');
    $myListTable =  new Transportation_List_Table();
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

function init_transportations(){
   
    global $wpdb;
    echo  '<div class="wrap">';
    echo  '<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>';
    echo  '<h2>'. __('Transportations') . '</h2>';

    if($_POST['save-transportation']) {
        $post =  $_POST;
        $post['action'] = $wpdb->escape($_GET['action']);
        $post['tid']    = $wpdb->escape($_GET['tid']);
        $result = insert_new_transportation($post);

       if( $result['count_err'] > 0 ):
            echo '<div class="error">';
                foreach($result['error'] as $err):
                    echo '<p>' . $err . '</p>'; 
                endforeach;
            echo '</div>';
        else:
            echo '<div class="updated"><p>' . $result['message'] . '</p></div>';

       endif;
    }



    echo  '<ul id="transportation-tabs" class="discovrfun-tab" style="">';
    echo  '<li><a href="admin.php?page=transportations" class="'. ( ($_GET['action'] == 'edit' || $_GET['action'] == 'delete') || !isset($_GET['action'])  ? 'selected' :'' ) .'">'. __('Manage') .'</a></li>'; 
    echo  '<li><a href="admin.php?page=transportations&action=new" class="'. (isset($_GET['action']) && $_GET['action'] == 'new' ? 'selected' : ''  ) .'">'. __('Add New') .'</a></li>';
    echo  '</ul>';
    echo  '<div id="tabs-wrapper">'; 
    echo  '<div class="tabs '. (isset($_GET['action']) && $_GET['action'] != 'delete' ?  '' :  'active') .'">';
    manage_transportation();
    echo  '</div><!--.tabs-->';
    echo  '<div class="tabs '. (isset($_GET['action']) && $_GET['action'] != 'delete' ? 'active' : '') .'">';
    manage_transportation_form();
    echo  '</div><!--.tabs-->';
    echo  '</div><!--#tabs-wrapper-->';
    echo  '</div>';

    echo $html;
}

function manage_transportation_form(){
    global $wpdb;
    if(isset($_GET['tid']) && $_GET['tid'] !='' ){
        $data = get_transportation( $wpdb->escape($_GET['tid']) );
    }
    if($_POST){
        $data = $_POST;
    }

    

    echo  '<form action="#" method="post">';
    echo  '<p>';
    echo  '<label for="transportation">' . __('Transportation') .' </label>';
    echo  '<input type="text" name="transportation" size="50" value="'. $data['transportation'] .'">';
    echo  '</p>';
    echo  '<p>';
    echo  '<label for="price">' . __('Price') .' </label>';
    echo  '<input type="text" name="price" size="10" value="'. $data['price'] .'">';
    echo  '</p>';
    echo  '<input type="submit" class="button-primary" name="save-transportation" value="Save">';
    echo  '<form>';
}