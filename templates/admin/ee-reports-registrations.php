<?php
/* @var $list_table EE_Reports_Registrations_List_Table */
?>

<h2>Event Espresso Registrations</h2>

<p>Registration information for events.</p>

<form method="get" class="ee-reports-search-form">
	<input type="hidden" name="page" class="ee-reports--admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php echo $list_table->search_box( 'Search registrations', 'ee-reports-admin-search' ); ?>
</form>

<p><a href="<?php echo admin_url( '/admin-ajax.php?action=export_ee_reports_registrationss&per_page=999999' ); ?>" 
	target="_blank" class="button">Export Registrations</a></p>

<form id="registrations-filter" method="post" class="ee-reports-admin-form">
	<input type="hidden" name="page" class="ee-reports-admin-page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php $list_table->display(); ?>
</form>