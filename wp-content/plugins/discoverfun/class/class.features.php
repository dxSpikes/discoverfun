<?php
/*
Plugin Name: Test List Table Example
*/
 
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class Features_List_Table extends WP_List_Table {
 
    var $data;

    function __construct(){
        global $status, $page;
     
            parent::__construct( array(
                'singular'  => __( 'title', 'mylisttable' ),     //singular name of the listed records
                'plural'    => __( 'titles', 'mylisttable' ),   //plural name of the listed records
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
            case 'title':
            case 'author':
            case 'isbn':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
  }
 
    function get_sortable_columns() {
          $sortable_columns = array(
            'title'  => array('feature',false),
          );
          return $sortable_columns;
    }
     
    function get_columns(){
            $columns = array(
                'cb'        => '<input type="checkbox" />',
                'title' => __( 'Title', 'mylisttable' ),
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
     
    function column_title($item){
      if(!current_user_can( 'manage_options' )) {   
        return $item['title'];
      }
      $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&fid=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&fid=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
            );
     
      return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
    }
     
    function get_bulk_actions() {
      if(!current_user_can( 'manage_options' )) {   
        return array();
      }
      $actions = array(
        'delete'    => 'Delete'
      );
      return $actions;
    }
     
    function column_cb($item) {
            return sprintf(
                '<input type="checkbox" name="feature[]" value="%s" />', $item['ID']
            );    
        }
     
    function prepare_items() {
      global $wpdb;
      $this->process_bulk_action();
      $columns  = $this->get_columns();
      $hidden   = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array( $columns, $hidden, $sortable );

      $query = 'SELECT ID, feature as title FROM '. $wpdb->prefix .'features WHERE 1=1';
        /* -- Searching parameters -- */
      // Search for transaction id, default search
      if (!empty($_POST['s'])) {
              $query .= ' AND (feature  LIKE "%' . $wpdb->escape($_POST['s']) . '%")';
      }
      
      $this->data = $wpdb->get_results($query,ARRAY_A);

      usort( $this->data, array( &$this, 'usort_reorder' ) );
      
      $per_page = 5;
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
        if( 'delete' === $this->current_action()  && current_user_can( 'manage_options' ) ) {

            // Action from bulk
            if ( isset($_POST['feature']) ) {
                foreach ( $_POST['feature'] as $id ) {
                    $sql        = 'DELETE FROM ' . $wpdb->prefix . 'features WHERE ID = %d';
                    $is_deleted = $wpdb->query( $wpdb->prepare( $sql, (int) $id ) );
                  
                    
                }
                $wpdb->flush();
                
            // Individual action made    
            } else {
                $sql        = 'DELETE FROM ' . $wpdb->prefix . 'features WHERE ID = %d';
                $is_deleted = $wpdb->query( $wpdb->prepare( $sql, (int) $_GET['hid'] ) );
                $wpdb->flush();
               
            }
            // if succesfully deleted, display success message
            if ( $is_deleted ) {
                echo '<div class="updated">
                        <p>Agent successfully deleted.</p>
                     </div>';
                    
            // else, prompts up error messge       
            } else {
                echo '<div class="error msg">
                        <p>There was an error while deleting feature. Please try again.</p>
                     </div>';
                
            }
                
        }
        
    }
 
} //class
 
 

 
 
 
