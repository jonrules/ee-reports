<?php
/* @var $list_table EE_Reports_Registrations_List_Table */
?>

<h2>Event Espresso Registrations</h2>

<form method="get" class="ee-reports-search-form">
	<input type="hidden" name="page" class="ee-reports-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<input type="hidden" name="event" class="ee-reports-admin-event" value="<?php echo isset( $_REQUEST['event'] ) ? esc_attr( $_REQUEST['event'] ) : ''; ?>" />
	<?php echo $list_table->search_box( 'Search registrations', 'ee-reports-admin-search' ); ?>
</form>

<p>Registration information for events.</p>

<?php 
$export_url = add_query_arg( array(
	'action' => 'export_ee_reports_registrations',
	'per_page' => '999999',
	's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
	'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '',
	'order' => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : '',
	'event' => isset( $_REQUEST['event'] ) ? $_REQUEST['event']: ''
), admin_url( 'admin-ajax.php' ) );
?>
<p><a href="<?php echo esc_attr( $export_url ); ?>" 
	target="_blank" class="button">Export Results</a></p>
	
<form method="get" class="ee-reports-filter-form">
	<input type="hidden" name="page" class="ee-reports-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<input type="hidden" name="s" class="ee-reports-admin-s" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
	<select name="event" class="ee-reports-admin-event">
		<?php $selected_event = isset( $_REQUEST['event'] ) ? $_REQUEST['event']: ''; ?>
		<?php foreach ( $list_table->events as $event ): ?>
			<option value="<?php echo esc_attr( $event->id ); ?>" <?php selected( $selected_event, $event->id ); ?>><?php echo esc_html( $event->name ); ?></option>
		<?php endforeach; ?>
		<option value="all" <?php selected( $selected_event, 'all' ); ?>>All Events</option>
	</select>
	<button type="submit" class="button">Filter Events</button>
</form>

<form id="registrations-filter" method="post" class="ee-reports-admin-form">
	<input type="hidden" name="page" class="ee-reports-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<input type="hidden" name="event" class="ee-reports-admin-event" value="<?php echo isset( $_REQUEST['event'] ) ? esc_attr( $_REQUEST['event'] ) : ''; ?>" />
	<?php $list_table->display(); ?>
</form>