<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
	Events :
	<small>Manage</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-calendar"></i> Event</a></li>
	<li class="active">Manage</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid">

	<section class="content">
		<div class="row extra-padding">

			<div class="box box-crey">
				<div class="box-header with-border">
					<i class="fa fa-calendar"></i>
					<h3 class="box-title">Events</h3>

					<div class="box-tools">
						<?php if (in_array('events_edit', $this->permission->permissions)): ?>
						<a href="<?= site_url('/admin/events/add_event'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Event</a>
						<?php endif; ?>
					</div>
				</div>
				<!-- /.box-header -->
				<div class="box-body table-responsive no-padding">

					<?php if ($events): ?>

					<?php echo $this->pagination->create_links(); ?>

					<table class="table table-hover">
						<tr>
							<th><?php echo order_link('/admin/events/viewall','eventtitle','Event'); ?></th>
							<th><?php echo order_link('/admin/events/viewall','location','Location'); ?></th>
							<th><?php echo order_link('/admin/events/viewall','eventDate','Event Start'); ?></th>
							<th><?php echo order_link('/admin/events/viewall','eventEnd','Event End'); ?></th>
							<th>Active</th>
							<th class="tiny">&nbsp;</th>
						</tr>
					<?php foreach ($events as $event): ?>
						<tr>
							<td><?php echo (in_array('events_edit', $this->permission->permissions)) ? anchor('/admin/events/edit_event/'.$event['eventID'], $event['eventTitle']) : $event['eventTitle']; ?></td>
							<td><?php echo $event['location']; ?></td>
							<td><?php echo dateFmt($event['eventDate'], '', FALSE); ?></td>
							<td><?php echo dateFmt($event['eventEnd'], '', FALSE); ?></td>
							<td>
								<?php
									if (strtotime($event['eventDate']) < time()) echo 'No';
									else echo 'Yes';
								?>
							</td>
							<td class="tiny">
								<?php if (in_array('events_edit', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/events/edit_event/'.$event['eventID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
								<?php endif; ?>

								<?php if (in_array('events_delete', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/events/delete_event/'.$event['eventID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php echo $this->pagination->create_links(); ?>

					<?php else: ?>

					<p class="clear">There are no events yet.</p>

					<?php endif; ?>

				</div><!-- End Box Body -->
			</div> <!-- End Box -->

		</div> <!-- End Row -->
	</section>
