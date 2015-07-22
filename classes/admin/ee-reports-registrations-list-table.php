<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EE_Reports_Registrations_List_Table extends WP_List_Table {
	public function __construct() {
		global $status, $page;
	
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'registration',
			'plural'    => 'registrations',
			'ajax'      => false
		) );
	}
	
	function column_default( $item, $column_name ) {
		return $item->{$column_name};
	}
	
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->REG_ID
		);
	}
	
	function column_last_order_date( $item ) {
		if ( empty( $item->last_order_date ) || $item->last_order_date == '0000-00-00' ) {
			return '';
		}
		return date_i18n( get_option( 'date_format' ), strtotime( $item->last_order_date ) );
	}
	
	function column_next_order_date( $item ) {
		if ( empty( $item->next_order_date ) || $item->next_order_date == '0000-00-00' ) {
			return '';
		}
		return date_i18n( get_option( 'date_format' ), strtotime( $item->next_order_date ) );
	}
	
	function get_columns(){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'EVT_title'  => __( 'Event', 'ee-reports' ),
			'REG_ID'  => __( 'REG_ID', 'ee-reports' ),
			'EVT_ID' => __( 'EVT_ID', 'ee-reports' ),
			'ATT_ID' => __( 'ATT_ID', 'ee-reports' ),
			'REG_date' => __( 'REG_date', 'ee-reports' ),
			'REG_code' => __( 'REG_code', 'ee-reports' ),
			'REG_count' => __( 'REG_count', 'ee-reports' ),
			'REG_final_price' => __( 'REG_final_price', 'ee-reports' ),
			'STS_code' => __( 'STS_code', 'ee-reports' ),
			'REG_paid' => __( 'REG_paid', 'ee-reports' ),
			'ATT_fname' => __( 'ATT_fname', 'ee-reports' ),
			'ATT_lname' => __( 'ATT_lname', 'ee-reports' ),
			'ATT_email' => __( 'ATT_email', 'ee-reports' ),
			'ATT_address' => __( 'ATT_address', 'ee-reports' ),
			'ATT_address2' => __( 'ATT_address2', 'ee-reports' ),
			'ATT_city' => __( 'ATT_city', 'ee-reports' ),
			'STA_ID' => __( 'STA_ID', 'ee-reports' ),
			'CNT_ISO' => __( 'CNT_ISO', 'ee-reports' ),
			'ATT_zip' => __( 'ATT_zip', 'ee-reports' ),
			'ATT_phone' => __( 'ATT_phone', 'ee-reports' )
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'EVT_title' => array( 'EVT_title', false ),
			'REG_ID' => array( 'REG_ID', false ),
			'EVT_ID' => array( 'EVT_ID', false ),
			'ATT_ID' => array( 'ATT_ID', false ),
			'REG_date' => array( 'REG_date', false ),
			'REG_code' => array( 'REG_code', false ),
			'REG_count' => array( 'REG_count', false ),
			'REG_final_price' => array( 'REG_final_price', false ),
			'STS_code' => array( 'STS_code', false ),
			'REG_paid' => array( 'REG_paid', false ),
			'ATT_fname' => array( 'ATT_fname', false ),
			'ATT_lname' => array( 'ATT_lname', false ),
			'ATT_email' => array( 'ATT_email', false ),
			'ATT_address' => array( 'ATT_address', false ),
			'ATT_address2' => array( 'ATT_address2', false ),
			'ATT_city' => array( 'ATT_city', false ),
			'STA_ID' => array( 'STA_ID', false ),
			'CNT_ISO' => array( 'CNT_ISO', false ),
			'ATT_zip' => array( 'ATT_zip', false ),
			'ATT_phone' => array( 'ATT_phone', false )
		);
		return $sortable_columns;
	}
	
	function get_bulk_actions() {
		$actions = array(
// 			'delete' => 'Delete'
		);
		return $actions;
	}
	
	function process_bulk_action() {
		global $wpdb;
		if ( 'delete' === $this->current_action() ) {
			
		}
	}
	
	function prepare_items() {
		global $wpdb;
		
		$this->process_bulk_action();
	
		/*
		 * Pagination vars
		 */
		$per_page = isset( $_REQUEST['per_page'] ) ? $_REQUEST['per_page'] : 50;
		$total_items = 0;
	
		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
	
	
		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		*/
		$this->_column_headers = array( $columns, $hidden, $sortable );
		
		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();
		

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['orderby'] ) : 'REG_date';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? preg_replace( '/[^a-z_]/', '', $_REQUEST['order'] ) : 'DESC';
		
		$search_clause = '1';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$safe_search = addslashes( stripslashes( $_REQUEST['s'] ) );
			$conditions = array(
				sprintf( 'customers.id = \'%s\'', $safe_search )
			);
			$columns = array( 
				'users.user_login', 
				'users.user_email', 
				'meta_last_name.meta_value',
				'meta_first_name.meta_value'
			);
			foreach ( $columns as $name ) {
				$conditions[] = sprintf( '%s LIKE \'%%%%%s%%%%\'', $name, $safe_search );
			}
			$search_clause = '(' . implode( ' OR ', $conditions ) . ')';
			
			/*
			 * Include search conditions in total items
			 */
			$total_items = (int) $wpdb->get_var( 
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}esp_registration
				WHERE $search_clause"
			);
		} else {
			/*
			 * Total items
			 */
			$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}esp_registration" );
		}
		
		
		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT events.post_title AS `EVT_title`,
			registrations.REG_ID AS `REG_ID`,
			registrations.EVT_ID AS `EVT_ID`,
			registrations.TXN_ID AS `TXN_ID`,
			registrations.ATT_ID AS `ATT_ID`,
			registrations.REG_date AS `REG_date`,
			registrations.REG_code AS `REG_code`,
			registrations.REG_count AS `REG_count`,
			registrations.REG_final_price AS `REG_final_price`,
			statuses.STS_code AS `STS_code`,
			registrations.REG_paid AS `REG_paid`,
			attendees.ATT_fname AS `ATT_fname`,
			attendees.ATT_lname AS `ATT_lname`,
			attendees.ATT_email AS `ATT_email`,
			attendees.ATT_address AS `ATT_address`,
			attendees.ATT_address2 AS `ATT_address2`,
			attendees.ATT_city AS `ATT_city`,
			attendees.STA_ID AS `STA_ID`,
			attendees.CNT_ISO AS `CNT_ISO`,
			attendees.ATT_zip AS `ATT_zip`,
			attendees.ATT_phone AS `ATT_phone`
			FROM {$wpdb->prefix}esp_registration AS registrations
			LEFT JOIN {$wpdb->prefix}esp_transaction AS transactions ON(transactions.TXN_ID = registrations.TXN_ID)
			LEFT JOIN {$wpdb->prefix}esp_attendee_meta AS attendees ON(attendees.ATT_ID = registrations.ATT_ID)
			LEFT JOIN {$wpdb->prefix}esp_status AS statuses ON(statuses.STS_ID = registrations.STS_ID)
			LEFT JOIN {$wpdb->prefix}posts AS events ON(events.ID = registrations.EVT_ID AND events.post_type = 'espresso_events')
			WHERE $search_clause
			GROUP BY registrations.REG_ID
			ORDER BY `{$orderby}` {$order}
			LIMIT %d, %d",
			( $current_page - 1 ) * $per_page, $per_page
		) );
		
	
		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		*/
		$this->items = $items;
	
	
		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}
}
