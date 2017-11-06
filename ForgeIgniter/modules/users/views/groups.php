	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Users :
		<small>Groups</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/users'); ?>"><i class="fa fa-users"></i> Users</a></li>
		<li class="active">User Groups</li>
	  </ol>
	</section>

	<!-- Main content -->
    <section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-users"></i>
						<h3 class="box-title">User Groups</h3>

						<div class="box-tools">

							<?php if (in_array('users_groups', $this->permission->permissions)): ?>
								<a href="<?= site_url('/admin/users/add_group'); ?>" class="mb-xs mt-xs mr-xs btn btn-green" style="margin-left: 15px;">Add Group</a>
							<?php endif; ?>

							<?php if (in_array('users', $this->permission->permissions)): ?>
								<a href="<?= site_url('/admin/users'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="margin-left: 15px;">Users</a>
							<?php endif; ?>

						</div>
					</div> <!-- End Box Header -->

					<div class="box-body table-responsive no-padding">

						<?php if ($permission_groups): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="table table-hover">
							<tr>
								<th><?php echo order_link('/admin/users/groups','groupName','Group name'); ?></th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php foreach ($permission_groups as $group): ?>
							<tr>
								<td><?php echo (in_array('users_groups', $this->permission->permissions)) ? anchor('/admin/users/edit_group/'.$group['groupID'], $group['groupName']) : $group['groupName']; ?></td>
								<td class="tiny">
									<?php echo anchor('/admin/users/edit_group/'.$group['groupID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
									<?php echo anchor('/admin/users/delete_group/'.$group['groupID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<?php else: ?>

						<p class="clear">There are no permission groups set up yet.</p>

						<?php endif; ?>

					</div> <!-- End Box body -->
				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>
