<?php

 
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class Accomodation_List_Table extends WP_List_Table {
 
    var $data;

    function __construct() {
        global $status, $page;
     
            parent::__construct( array(
                'singular'  => __( 'accomodation', 'mylisttable' ),
                'plural'    => __( 'accomodations', 'mylisttable' ),
                'ajax'      => false 
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
          _e( 'No Record found!' );
    }
 
    function column_default( $item, $column_name ) {

          switch( $column_name ) { 
              case 'transaction_id':
              case 'package_id':
              case 'name':
              case 'email':
              case 'check_in':
              case 'check_out':
              case 'total_amount':
              case 'payment_type':
              case 'accomodation_status':
                return $item[$column_name];
          }
    }
 
    function get_sortable_columns() {

          $sortable_columns = array(
            'transaction_id'  => array('transaction_id',false),                        
            'name'  => array('name',false),
            'package_id'  => array('package_id',false),
            'check_out'  => array('package_id',false),
            'check_in'  => array('package_id',false),
            'total_amount'  => array('package_id',false),
            'payment_type'  => array('package_id',false),
            'accomodation_status'  => array('package_id',false),
          );
          return $sortable_columns;
    }
     
    function get_columns() {

        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'transaction_id' => __( 'Transaction ID', 'mylisttable' ),
            'package_id' => __( 'Package ID', 'mylisttable' ),
            'name' => __( 'Customer Name', 'mylisttable' ),
            'email' => __( 'E-mail', 'mylisttable' ),
            'check_in' => __( 'Check-in', 'mylisttable' ),
            'check_out' => __( 'Check-out', 'mylisttable' ),
            'total_amount' => __( 'Total Amount', 'mylisttable' ),
            'payment_type' => __( 'Payment Type', 'mylisttable' ),
            'accomodation_status' => __( 'Status', 'mylisttable' ),
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
     
    function column_transaction_id($item){
        $actions = array(
                  'view'      => sprintf('<a href="?page=%s&action=%s&uid=%s">View</a>',$_REQUEST['page'],'view',$item['ID']),
                  'delete'    => sprintf('<a href="?page=%s&action=%s&uid=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
              );
       
        return sprintf('%1$s %2$s', $item['transaction_id'], $this->row_actions($actions) );
    }

    function column_name($item) {
        return $item['firstname'] .' '. $item['lastname'];
    }

    function column_accomodation_status($item) {
        global $wpdb;

        $result = get_enum_field($wpdb->prefix .'accomodation', 'accomodation_status');
        
        $html = '<select>';
        
        foreach($result as $enum) {
            $selected = ($enum == $item['accomodation_status']) ? 'selected="selected"' : '';
            $html .= '<option value="'. $enum .'" '. $selected .'>'. $enum . '</option>';
        }

        $html .= '<select>';
        
        return $html;
    }
     

    /**
     * Add Some Action
     *
     * @access public
     * @param none
     * @return none
     */
     

    function get_bulk_actions() {
        $actions = array(
          'delete'    => 'Delete'
        );

        return $actions;
    }
     
    /**
     * Add checkbox to ID column
     *
     * @access public
     * @param none
     * @return none
     */
      
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="tid[]" value="%s" />', $item['ID']);    
    }
    

    /**
     * prepare item
     *
     * @access public
     * @param none
     * @return none
     */


    function prepare_items() {
        global $wpdb;

        $this->process_bulk_action();
        
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $query = 'SELECT * FROM '. $wpdb->prefix .'accomodation WHERE 1=1';
        if (isset($_POST['s'])) {
            $search_val = $_POST['s'];
            $query .= ' AND firstname like "%'. $search_val .'%" || ';
            $query .= ' lastname like "%'. $search_val .'%" || ';
            $query .= ' transaction_id like "%'. $search_val .'%" ';
        }
   

        $this->data = $wpdb->get_results($query,ARRAY_A);

        usort( $this->data, array( &$this, 'usort_reorder' ) );
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = count( $this->data );
       
        $this->found_data = array_slice( $this->data,( ( $current_page-1 )* $per_page ), $per_page );
       
        $this->set_pagination_args( array('total_items' => $total_items, 'per_page'    => $per_page ) );

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

        if( 'delete' === $this->current_action() ) {

            if ( isset($_POST['uid']) ) {
                foreach ( $_POST['uid'] as $id ) {
                    $sql = 'DELETE FROM '. $wpdb->prefix .'accomodation a JOIN '. $wpdb->prefix .'transaction_log b ON a.transaction_id == b.transaction_id  WHERE a.ID = %d';
                    $is_deleted = $wpdb->query($wpdb->prepare($sql,$id));
                }    
         
            } else {
                $id = $_GET['uid'];

                $sql = 'DELETE FROM '. $wpdb->prefix .'accomodation a JOIN '. $wpdb->prefix .'transaction_log b ON a.transaction_id == b.transaction_id  WHERE a.ID = %d';
                $is_deleted = $wpdb->query($wpdb->prepare($sql,$id));

            }

            if ( $is_deleted ) {
                echo '<div class="updated">
                        <p>Transaction successfully deleted.</p>
                     </div>';
                          
            } else {

                echo '<div class="error msg">
                        <p>There was an error while deleting Transaction. Please try again.</p>
                     </div>'; 
            }
                
        }
        
    }
 
} //class
 
 

 
 
 
