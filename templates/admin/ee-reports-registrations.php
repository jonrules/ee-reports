<?php
/* @var $list_table EE_Reports_Registrations_List_Table */
?>

<h2>Event Espresso Registrations</h2>

<form method="get" class="ee-reports-search-form">
	<input type="hidden" name="page" class="ee-reports--admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php echo $list_table->search_box( 'Search registrations', 'ee-reports-admin-search' ); ?>
</form>

<p>Registration information for events.</p>

<?php 
$export_url = add_query_arg( array(
	'action' => 'export_ee_reports_registrations',
	'per_page' => '999999',
	's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
	'orderby' => isset( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : '',
	'order' => isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : ''
), admin_url( 'admin-ajax.php' ) );
?>
<p><a href="<?php echo esc_attr( $export_url ); ?>" 
	target="_blank" class="button">Export Results</a></p>

<form id="registrations-filter" method="post" class="ee-reports-admin-form">
	<input type="hidden" name="page" class="ee-reports-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php $list_table->display(); ?>
</form>