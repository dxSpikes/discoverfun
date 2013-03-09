<?php
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * This is for Listing the Paid Transaction in backend page.
 * It uses the native wordpress list table style by extending,
 * the WP_List_Table class
 *
 */
class Agent_List_Table extends WP_List_Table {
    
    var $data;
    
    /**
     * This is the class constructor
     *
     * @access public
     * @param none
     * @return none
     */
    function __construct(){

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'agent',     //singular name of the listed records
            'plural'    => 'agents',    //plural name of the listed records
            'ajax'      => false                   //does this table support ajax?
        ) );
        
    }
    
    /**
     * This is the default value of column in the table.
     * If no specified function for column, this will be shown
     *
     * @access public
     * @param array $item, item in the database
     * @param string $column_name, name/id of the current column
     * @return string
     */
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'code':
                return $item[$column_name];
                
            case 'contact_name':
                return stripslashes_deep( $item[$column_name] );
                
            case 'suburb':
                return stripslashes_deep( $item[$column_name] );

            case 'state':
                return stripslashes_deep( $item[$column_name] );

            case 'phone':
                return stripslashes_deep( $item[$column_name] );

        }
    }
    
    /**
     * This is the customize column for checkbox column.
     * This will override the value in function column_default.
     *
     * @access public
     * @param array $item, item in the database
     * @return string/html
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
        
    }
    
     /**
     * This is the customize column for Transaction Column.
     * This will override the value in function column_default.
     *
     * @access public
     * @param array $item, item in the database
     * @return string/html
     */
    function column_code( $item ) {
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf(
                '<a href="?page=%s&action=%s&id=%s" title="Edit">Edit</a>','add-new-agent','edit',$item['id']
            ),
            'view'    => sprintf(
                '<a href="?page=%s&action=%s&id=%s" title="View">View</a>','add-new-agent','view',$item['id']
            ),
            /*'delete'    => sprintf(
                '<a href="?page=%s&action=%s&id=%s" title="Delete">Delete</a>',$_REQUEST['page'],'delete',$item['id']
            ),*/
        );
        
        //Return the title contents
        return sprintf('<strong>%1$s</strong> %2$s',
            /*$1%s*/ $item['code'],
            /*$2%s*/ //$item['id'],
            /*$3%s*/ $this->row_actions( $actions )
        );
        
    }
    
     /**
     * This is for the header and footer value of our table
     *
     * @access public
     * @param none
     * @return array $columns
     */
    function get_columns() {
        $columns = array(
            'cb'                => '<input type="checkbox" />', //Render a checkbox instead of text
            'code'              => 'Agent Code',
            'contact_name'      => 'Agent Name',
            'suburb'            => 'Suburb',
            'state'             => 'State',
            'phone'             => 'Phone',

        );
        return $columns;
    }
    
     /**
     * This is for sorting of our columns
     *
     * @access public
     * @param none
     * @return array $sortable_columns
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'id'    => array( 'id', FALSE ),
            'code'  => array( 'code', FALSE )
        );
        return $sortable_columns;
    }
    
     /**
     * This is for the bulk action
     *
     * @access public
     * @param none
     * @return array $actions
     */
    /*function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }*/
    
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
            if ( isset($_GET['agent']) ) {
                foreach ( $_GET['agent'] as $id ) {
                    $sql        = 'DELETE FROM ' . $wpdb->prefix . 'agent WHERE wp_user = %d';
                    $is_deleted = $wpdb->query( $wpdb->prepare( $sql, (int) $id ) );
                    wp_delete_user($id);
                    
                }
                $wpdb->flush();
                
            // Individual action made    
            } else {
                $sql        = 'DELETE FROM ' . $wpdb->prefix . 'agent WHERE wp_user = %d';
                $is_deleted = $wpdb->query( $wpdb->prepare( $sql, (int) $_GET['id'] ) );
                $wpdb->flush();
                wp_delete_user($_GET['id']);
            }
            // if succesfully deleted, display success message
            if ( $is_deleted ) {
                echo '<div class="updated">
                        <p>Agent successfully deleted.</p>
                     </div>';
                    
            // else, prompts up error messge       
            } else {
                echo '<div class="error msg">
                        <p>There was an error while deleting visa validity. Please try again.</p>
                     </div>';
                
            }
                
        }
        
    }
    
     /**
     * This is for the content and pagination of table
     * @access public
     * @param none
     * @return none
     */
    function prepare_items() {
        global $wpdb;
        
        $per_page = 50;
        
        $columns    = $this->get_columns();
        $hidden     = array();
        $sortable   = $this->get_sortable_columns();
        
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        $this->process_bulk_action();
        
        /* -- Preparing your query -- */
	        $query = "SELECT a.user_login as code, b.wp_user as id,b.contact_name,b.suburb,b.state,b.phone FROM " . $wpdb->prefix . "users a JOIN " . $wpdb->prefix . "agent b 
                    ON  b.wp_user = a.ID WHERE 1=1 ";
                     
        /* -- Status parameters -- */             
            // Parameters for transaction status
            if (!empty($_GET['status'])) {
                switch ( $_GET['status'] ) {
                    case 'inactive':
                        $query .= ' AND status = "INACTIVE"';
                        break;
                    case 'active':
                         $query .= ' AND status = "ACTIVE"';
                        break;
                }
            }
            
        /* -- Searching parameters -- */
            // Search for transaction id, default search
            if (!empty($_GET['s'])) {
                if ($_GET['agent_filter'] == 'code') {
                    $query .= ' AND (a.user_login  LIKE "%' . $wpdb->escape($_GET['s']) . '%")';
                } else {
                    $query .= ' AND (b.contact_name  LIKE "%' . $wpdb->escape($_GET['s']) . '%")';
                }
            }
            
	    /* -- Ordering parameters -- */
	        //Parameters that are going to be used to order the result
	        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ID';
	        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';
	        if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
	 
	    /* -- Pagination parameters -- */
	        //Number of elements in your table?
	        $totalitems = $wpdb->query($query); //return the total number of affected rows

	        //Which page is this?
	        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
	        //Page Number
	        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
	        //How many pages do we have in total?
	        $totalpages = ceil($totalitems/$per_page);
	        //adjust the query to take pagination into account
	        if(!empty($paged) && !empty($per_page)){
	            $offset=($paged-1)*$per_page;
	            $query.=' LIMIT '.(int)$offset.','.(int)$per_page;
	        }

        $this->items = $wpdb->get_results( $query, ARRAY_A );
        $wpdb->flush();
        $this->set_pagination_args( array(
            'total_items' => $totalitems,  //WE have to calculate the total number of items
            'per_page'    => $per_page,    //WE have to determine how many items to show on a page
            'total_pages' => $totalpages   //WE have to calculate the total number of pages
        ) );
        
    }
    
    function subsub() {
        global $wpdb;
        
        $all = 0;
        $active = 0;
        $inactive = 0;
        $status = array( 'all', 'active', 'inactive' );
        
        $current_all = '';
        $current_active = '';
        $current_inactive = '';
        if ($_GET['status'] == 'inactive')
            $current_inactive = 'current';
        else if ($_GET['active'] == 'active')
            $current_active = 'current';
        else
            $current_all = 'current';
        
        /* -- Preparing your query -- */
	        $query = "SELECT * FROM " . $wpdb->prefix . "agent WHERE 1=1 ";
                     
        foreach ( $status as $value ) {
            $and = '';
            switch ( $value ) {
                case 'all':
                    $all = $wpdb->query($query);
                    break;
                case 'active':
                    $and = ' AND status = "ACTIVE"';
                    $active = $wpdb->query($query.$and);
                    break;
                case 'inactive':
                    $and = ' AND status = "INACTIVE"';
                    $inactive = $wpdb->query($query.$and);
                    break;
            }
        }
        
        $wpdb->flush();
        
        $html = '
        <ul class="subsubsub">
            <li class="all">
                <a class="'.$current_all.'" href="admin.php?page=all-agent">All <span class="count">('. 
                $all.')</span></a> |</li>
            <li class="publish">
                <a class="'.$current_active.'" href="admin.php?page=all-agent&status=active">Active <span class="count">('.
                $active.')</span></a> |</li>
            <li class="pending">
                <a class="'.$current_inactive.'" href="admin.php?page=all-agent&status=inactive">Inactive <span class="count">('.
                $inactive.')</span></a></li>
        </ul>
        ';
        
        echo $html;
    }

}
?>