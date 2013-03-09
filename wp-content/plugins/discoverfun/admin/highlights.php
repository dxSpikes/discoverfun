<?php
/**
*manage hightlights
*@param none
*@return none
*/


function manage_highlight() 
{
    require(PLUGIN_PATH .'class/class.highlight.php');
    $myListTable =  new Highlight_List_Table();
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

function init_highlights()
{
    global $wpdb; 
?>
    
    <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
        <h2><?php _e('Hightlight');?></h2>
            <?php
            
            if($_POST['save-highlight'])
            {
                $post =  $_POST;
                $post['action'] = $wpdb->escape($_GET['action']);
                $post['hid']    = $wpdb->escape($_GET['hid']);
                $result = insert_new_highlight($post);
               if( $result['err_count'] != 0 ):
            ?>
                    <div class="error">
                        <?php foreach($result['error'] as $err):?>
                            <p><?php echo $err; ?></p> 
                        <?php endforeach; ?>
                    </div>
            <?php else:?>
                <div class="updated"><p><?php echo $result['message']; ?></p></div>
            <?php  
                endif;
            }
            ?>


        <ul id="highlight-tabs" style="">
            <li><a href="admin.php?page=hightlights" class="<?php echo  ( ($_GET['action'] == 'edit' || $_GET['action'] == 'delete') || !isset($_GET['action'])  ? 'selected' :'' ); ?>"><?php _e('Manage') ?></a></li> 
            <li><a href="admin.php?page=hightlights&action=new" class="<?php echo  (isset($_GET['action']) && $_GET['action'] == 'new' ? 'selected' : ''  ); ?>"><?php _e('Add New'); ?></a></li>
        </ul>
        <div id="tabs-wrapper"> 
            <div class="tabs <?php echo (isset($_GET['action']) && $_GET['action'] != 'delete' ?  '' :  'active'); ?>"> <?php manage_highlight(); ?></div><!--.tabs-->
            <div class="tabs <?php echo (isset($_GET['action']) && $_GET['action'] != 'delete' ? 'active' : ''); ?>"><?php manage_highlight_form(); ?></div><!--.tabs-->
        </div><!--#tabs-wrapper-->
    </div>
<?php
}

function manage_highlight_form()
{
    global $wpdb;
    if(isset($_GET['hid']) && $_GET['hid'] !='' )
    {
        $data = get_highlight( $wpdb->escape($_GET['hid']) );
    }
?>
    

    <form action="#" method="post">
    <p>
    <label for="highlight"><?php _e('Highlight'); ?></label>
    <input type="text" name="highlight" size="50" value="<?php echo $data['highlight']; ?>">
    </p>
    <input type="submit" class="button-primary" name="save-highlight" value="Save">
    <form>
<?php
}