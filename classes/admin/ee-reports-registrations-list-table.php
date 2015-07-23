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
	
	function column_REG_date( $item ) {
		return $this->format_date( $item->REG_date );
	}
	
	function column_TXN_timestamp( $item ) {
		return $this->format_date( $item->TXN_timestamp );
	}
	
	function column_TKT_start_date( $item ) {
		return $this->format_date( $item->TKT_start_date );
	}
	
	function column_TKT_end_date( $item ) {
		return $this->format_date( $item->TKT_end_date );
	}
	
	function format_date( $value ) {
		if ( empty( $value ) || $value == '0000-00-00' ) {
			return '';
		}
		return date_i18n( 'M j, Y h:i a', strtotime( $value ) );
	}
	
	function get_columns() {
// Event	Transaction ID[TXN_ID]	Attendee ID[ATT_ID]	Registration ID[REG_ID]	Time registration occurred[REG_date]	
// Unique Code for this registration[REG_code]	Count of this registration in the group registration [REG_count]	
// Final Price of registration[REG_final_price]	Currency	Registration Status	Transaction Status	Transaction Amount Due	
// Amount Paid	Payment Date(s)	Payment Method(s)	Gateway Transaction ID(s)	Check-Ins	
// Ticket Name	Datetimes of Ticket	First Name[ATT_fname]	Last Name[ATT_lname]	Email Address[ATT_email]	
// Address Part 1[ATT_address]	Address Part 2[ATT_address2]	City[ATT_city]	State[STA_ID]	Country[CNT_ISO]	
// ZIP/Postal Code[ATT_zip]	Phone[ATT_phone]	Who are you?
		$columns = array(
			'EVT_title'  => __( 'Event', 'ee-reports' ),
			'TXN_ID'  => __( 'Transaction ID', 'ee-reports' ),
			'ATT_ID' => __( 'Attendee ID', 'ee-reports' ),
			'REG_ID'  => __( 'Registration ID', 'ee-reports' ),
			'EVT_ID' => __( 'Event ID', 'ee-reports' ),
			'REG_date' => __( 'Time registration occurred', 'ee-reports' ),
			'REG_code' => __( 'Unique Code for this registration', 'ee-reports' ),
			'REG_count' => __( 'Count of this registration in the group registration', 'ee-reports' ),
			'REG_final_price' => __( 'Final Price of registration', 'ee-reports' ),
			'REG_STS_code' => __( 'Registration Status', 'ee-reports' ),
			'TXN_STS_code' => __( 'Transaction Status', 'ee-reports' ),
			'TXN_total' => __( 'Transaction Amount Due', 'ee-reports' ),
			'TXN_paid' => __( 'Amount Paid', 'ee-reports' ),
			'TXN_timestamp' => __( 'Payment Date', 'ee-reports' ),
			'PMD_name' => __( 'Payment Method(s)', 'ee-reports' ),
			'PAY_txn_id_chq_nmbr' => __( 'Gateway Transaction ID(s)', 'ee-reports' ),
			'TKT_uses' => __( 'Check-Ins', 'ee-reports' ),
			'TKT_name' => __( 'Ticket Name', 'ee-reports' ),
			'TKT_start_date' => __( 'Ticket Start Date', 'ee-reports' ),
			'TKT_end_date' => __( 'Ticket End Date', 'ee-reports' ),
			'ATT_fname' => __( 'First Name', 'ee-reports' ),
			'ATT_lname' => __( 'Last Name', 'ee-reports' ),
			'ATT_email' => __( 'Email Address', 'ee-reports' ),
			'ATT_address' => __( 'Address Part 1', 'ee-reports' ),
			'ATT_address2' => __( 'Address Part 2', 'ee-reports' ),
			'ATT_city' => __( 'City', 'ee-reports' ),
			'STA_ID' => __( 'State', 'ee-reports' ),
			'CNT_ISO' => __( 'Country', 'ee-reports' ),
			'ATT_zip' => __( 'ZIP/Postal Code', 'ee-reports' ),
			'ATT_phone' => __( 'Phone', 'ee-reports' ),
			'ANS_who_are_you' => __( 'Who are you?', 'ee-reports' ),
			'PRO_code' => __( 'Promo Code', 'ee-reports' )
		);
		return $columns;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array();
		$columns = $this->get_columns();
		foreach ( $columns as $id => $name ) {
			$sortable_columns[ $id ] = array( $id, false );
		}
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
		

		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? preg_replace( '/[^A-Za-z_]/', '', $_REQUEST['orderby'] ) : 'REG_date';
		$order = ( ! empty( $_REQUEST['order'] ) ) ? preg_replace( '/[^A-Za-z_]/', '', $_REQUEST['order'] ) : 'DESC';
		
		$search_clause = '1';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$safe_search = addslashes( stripslashes( $_REQUEST['s'] ) );
			$exact_match_columns = array(
				'registrations.REG_ID',
				'registrations.EVT_ID',
				'registrations.TXN_ID',
				'registrations.ATT_ID',
				'promotions.PRO_code'
			);
			foreach ( $exact_match_columns as $name ) {
				$conditions[] = sprintf( '%s = \'%s\'', $name, $safe_search );
			}
			$partial_match_columns = array( 
				'events.post_title',
				'attendees.ATT_fname',
				'attendees.ATT_lname',
				'attendees.ATT_email',
				'attendees.ATT_address',
				'attendees.ATT_address2',
				'attendees.ATT_city',
				'attendees.STA_ID',
				'attendees.CNT_ISO',
				'attendees.ATT_zip',
				'attendees.ATT_phone'
			);
			foreach ( $partial_match_columns as $name ) {
				$conditions[] = sprintf( '%s LIKE \'%%%%%s%%%%\'', $name, $safe_search );
			}
			$search_clause = '(' . implode( ' OR ', $conditions ) . ')';
			
			/*
			 * Include search conditions in total items
			 */
			$total_items = (int) $wpdb->get_var( 
				"SELECT COUNT(*)
				FROM {$wpdb->prefix}esp_registration AS registrations
				LEFT JOIN {$wpdb->prefix}esp_transaction AS transactions ON(transactions.TXN_ID = registrations.TXN_ID)
				LEFT JOIN {$wpdb->prefix}esp_attendee_meta AS attendees ON(attendees.ATT_ID = registrations.ATT_ID)
				LEFT JOIN {$wpdb->prefix}esp_ticket AS tickets ON(tickets.TKT_ID = registrations.TKT_ID)
				LEFT JOIN {$wpdb->prefix}esp_status AS reg_statuses ON(reg_statuses.STS_ID = registrations.STS_ID)
				LEFT JOIN {$wpdb->prefix}esp_status AS txn_statuses ON(txn_statuses.STS_ID = transactions.STS_ID)
				LEFT JOIN {$wpdb->prefix}esp_payment AS payments ON(payments.TXN_ID = transactions.TXN_ID)
				LEFT JOIN {$wpdb->prefix}esp_payment_method AS payment_methods ON(payment_methods.PMD_ID = payments.PMD_ID)
				LEFT JOIN {$wpdb->prefix}esp_line_item AS line_items ON(line_items.TXN_ID = transactions.TXN_ID AND line_items.LIN_code LIKE 'promotion-%%')
				LEFT JOIN {$wpdb->prefix}esp_promotion AS promotions ON(CONCAT('promotion-', promotions.PRO_ID) = line_items.LIN_CODE)
				LEFT JOIN {$wpdb->prefix}posts AS events ON(events.ID = registrations.EVT_ID AND events.post_type = 'espresso_events')
				LEFT JOIN {$wpdb->prefix}esp_answer AS who_are_you ON(who_are_you.REG_ID = registrations.REG_ID AND who_are_you.QST_ID = 11)
				WHERE $search_clause
				GROUP BY registrations.REG_ID"
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
			reg_statuses.STS_code AS `REG_STS_code`,
			txn_statuses.STS_code AS `TXN_STS_code`,
			transactions.TXN_total AS `TXN_total`,
			transactions.TXN_paid AS `TXN_paid`,
			transactions.TXN_timestamp AS `TXN_timestamp`,
			GROUP_CONCAT(payment_methods.PMD_name SEPARATOR ', ') AS `PMD_name`,
			GROUP_CONCAT(payments.PAY_txn_id_chq_nmbr SEPARATOR ', ') AS `PAY_txn_id_chq_nmbr`,
			tickets.TKT_name AS `TKT_name`,
			tickets.TKT_uses AS `TKT_uses`,
			tickets.TKT_start_date AS `TKT_start_date`,
			tickets.TKT_end_date AS `TKT_end_date`,
			attendees.ATT_fname AS `ATT_fname`,
			attendees.ATT_lname AS `ATT_lname`,
			attendees.ATT_email AS `ATT_email`,
			attendees.ATT_address AS `ATT_address`,
			attendees.ATT_address2 AS `ATT_address2`,
			attendees.ATT_city AS `ATT_city`,
			attendees.STA_ID AS `STA_ID`,
			attendees.CNT_ISO AS `CNT_ISO`,
			attendees.ATT_zip AS `ATT_zip`,
			attendees.ATT_phone AS `ATT_phone`,
			who_are_you.ANS_value AS `ANS_who_are_you`,
			GROUP_CONCAT(promotions.PRO_code SEPARATOR ', ') AS `PRO_code`
			FROM {$wpdb->prefix}esp_registration AS registrations
			LEFT JOIN {$wpdb->prefix}esp_transaction AS transactions ON(transactions.TXN_ID = registrations.TXN_ID)
			LEFT JOIN {$wpdb->prefix}esp_attendee_meta AS attendees ON(attendees.ATT_ID = registrations.ATT_ID)
			LEFT JOIN {$wpdb->prefix}esp_ticket AS tickets ON(tickets.TKT_ID = registrations.TKT_ID)
			LEFT JOIN {$wpdb->prefix}esp_status AS reg_statuses ON(reg_statuses.STS_ID = registrations.STS_ID)
			LEFT JOIN {$wpdb->prefix}esp_status AS txn_statuses ON(txn_statuses.STS_ID = transactions.STS_ID)
			LEFT JOIN {$wpdb->prefix}esp_payment AS payments ON(payments.TXN_ID = transactions.TXN_ID)
			LEFT JOIN {$wpdb->prefix}esp_payment_method AS payment_methods ON(payment_methods.PMD_ID = payments.PMD_ID)
			LEFT JOIN {$wpdb->prefix}esp_line_item AS line_items ON(line_items.TXN_ID = transactions.TXN_ID AND line_items.LIN_code LIKE 'promotion-%%')
			LEFT JOIN {$wpdb->prefix}esp_promotion AS promotions ON(CONCAT('promotion-', promotions.PRO_ID) = line_items.LIN_CODE)
			LEFT JOIN {$wpdb->prefix}posts AS events ON(events.ID = registrations.EVT_ID AND events.post_type = 'espresso_events')
			LEFT JOIN {$wpdb->prefix}esp_answer AS who_are_you ON(who_are_you.REG_ID = registrations.REG_ID AND who_are_you.QST_ID = 11)
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
