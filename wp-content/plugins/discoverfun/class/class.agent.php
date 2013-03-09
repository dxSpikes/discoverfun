<?php
/*
Plugin Name: Test List Table Example
*/
 
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class Agent_List_Table extends WP_List_Table {
 
    var $data;

    function __construct(){
        global $status, $page;
     
            parent::__construct( array(
                'singular'  => __( 'agent', 'mylisttable' ),     //singular name of the listed records
                'plural'    => __( 'agents', 'mylisttable' ),   //plural name of the listed records
                'ajax'      => false        //does this table support ajax?
     
        ) );
     
        add_action( 'admin_head', array( &$this, 'admin_header' ) );            
 
    }
 
  function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if( 'my_list_test' != $page )
        return;
        echo '<style type="text/css">';
        echo '.wp-list-table .column-id { width: 5%; }';
        echo '.wp-list-table .column-title { width: 40%; }';
        echo '.wp-list-table .column-author { width: 35%; }';
        echo '.wp-list-table .column-isbn { width: 20%;}';
        echo '</style>';
  }
 
  function no_items() {
        _e( 'No title found, dude.' );
  }
 
  function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'username':
              return $item[$column_name];
            case 'name':
              return $item[$column_name];
            case 'email':
              return $item[$column_name];
        }
  }
 
    function get_sortable_columns() {
          $sortable_columns = array(
            'username'  => array('username',false),
            'name'  => array('name',false),
            'email'  => array('email',false),
          );
          return $sortable_columns;
    }
     
    function get_columns(){
            $columns = array(
                'cb'        => '<input type="checkbox" />',
                'username' => __( 'Username', 'mylisttable' ),
                'name' => __( 'Name', 'mylisttable' ),
                'email' => __( 'E-mail', 'mylisttable' ),
            );
             return $columns;
    }
     
    function usort_reorder( $a, $b ) {
      // If no sort, default to title
      $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'ID';
      // If no order, default to asc
      $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'DESC';
      // Determine sort order
      $result = strcmp( $a[$orderby], $b[$orderby] );
      // Send final sort direction to usort
      return ( $order === 'asc' ) ? $result : -$result;
    }
     
    function column_username($item){
      $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&uid=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&uid=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
            );
     
      return sprintf('%1$s %2$s', $item['username'], $this->row_actions($actions) );
    }
     
    function get_bulk_actions() {
      $actions = array(
        'delete'    => 'Delete'
      );
      return $actions;
    }
     
    function column_cb($item) {
            return sprintf(
                '<input type="checkbox" name="uid[]" value="%s" />', $item['ID']
            );    
        }
     
    function prepare_items() {
      global $wpdb;
      $this->process_bulk_action();
      $columns  = $this->get_columns();
      $hidden   = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array( $columns, $hidden, $sortable );

      $query = 'SELECT a.ID, a.user_login as username,a.user_email as email,a.display_name as name FROM '. $wpdb->prefix .'users a JOIN '. $wpdb->prefix .'usermeta b ON a.ID =  b.user_id WHERE b.meta_key = "df_capabilities" AND meta_value like "%ads_author%" ';
      
      /* -- Searching parameters -- */
      // Search for transaction id, default search
      if (!empty($_POST['s'])) {
              $query .= ' AND (a.user_login  LIKE "%' . $wpdb->escape($_POST['s']) . '%")';
      }

      /* -- Ordering parameters -- */
      //Parameters that are going to be used to order the result
      $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ID';
      $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';
      if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }







      $this->data = $wpdb->get_results($query,ARRAY_A);

      usort( $this->data, array( &$this, 'usort_reorder' ) );
      
      $per_page = 20;
      $current_page = $this->get_pagenum();
      $total_items = count( $this->data );
     
      // only ncessary because we have sample data
      $this->found_data = array_slice( $this->data,( ( $current_page-1 )* $per_page ), $per_page );
     
      $this->set_pagination_args( array(
        'total_items' => $total_items,                  //WE have to calculate the total number of items
        'per_page'    => $per_page                     //WE have to determine how many items to show on a page
      ) );
      $this->items = $this->found_data;
    }

    /**
     * This is for the processing of bulk actions / individual action
     *
     * @access public
     * @param none
     * @return none
     */
    function process_bulk_action() {
        global $wpdb;
        
        //Detect when a bulk action is being triggered...
        if( 'delete' === $this->current_action() ) {

            // Action from bulk
            if ( isset($_POST['uid']) ) {
                foreach ( $_POST['uid'] as $id ) {
                    
                    $is_deleted = wp_delete_user(  (int) $id );
                  
                    
                }
              
                
            // Individual action made    
            } else {
                $id = $_GET['uid'];
                $is_deleted = wp_delete_user(  (int) $id );

               
            }
            // if succesfully deleted, display success message
            if ( $is_deleted ) {
                echo '<div class="updated">
                        <p>Agent successfully deleted.</p>
                     </div>';
                    
            // else, prompts up error messge       
            } else {
                echo '<div class="error msg">
                        <p>There was an error while deleting Agent. Please try again.</p>
                     </div>';
                
            }
                
        }
        
    }
 
} //class
 
 

 
 
 
