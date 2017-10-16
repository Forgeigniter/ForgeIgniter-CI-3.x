	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Forums :
		<small>Manage</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-list-alt"></i> Forums</a></li>
		<li class="active">Manage</li>
	  </ol>
	</section>

	<!-- Main content -->
    <section class="content container-fluid">

		<section class="content">
			<div class="row">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-newspaper-o"></i>
					<h3 class="box-title">Forums</h3>

					<div class="box-tools">
						<?php if (in_array('forums_edit', $this->permission->permissions)): ?>
							<a href="<?= site_url('/admin/forums/add_forum'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Forum</a>
						<?php endif; ?>
					</div>

					</div>
					<!-- /.box-header -->
					<div class="box-body">

					<?php if ($forums): ?>

					<?php echo $this->pagination->create_links(); ?>

					<table class="default clear">
						<tr>
							<th><?php echo order_link('/admin/forums/forums','forumName','Forum'); ?></th>
							<th><?php echo order_link('/admin/forums/forums','datecreated','Description'); ?></th>
							<th><?php echo order_link('/admin/forums/forums','active','Active'); ?></th>
							<th class="tiny">&nbsp;</th>
							<th class="tiny">&nbsp;</th>
							<th class="tiny">&nbsp;</th>
						</tr>
					<?php foreach ($forums as $forum): ?>
						<tr>
							<td><?php echo (in_array('forums_edit', $this->permission->permissions)) ? anchor('/admin/forums/edit_forum/'.$forum['forumID'], $forum['forumName']) : $forum['forumName']; ?></td>
							<td><?php echo $forum['description']; ?></td>
							<td>
								<?php
									if ($forum['active']) echo 'Yes';
									if (!$forum['active']) echo 'No';
								?>
							</td>
							<td><?php echo anchor('/forums/viewforum/'.$forum['forumID'], 'View'); ?></td>
							<td class="tiny">
								<?php if (in_array('forums_edit', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/forums/edit_forum/'.$forum['forumID'], 'Edit'); ?>
								<?php endif; ?>
							</td>
							<td class="tiny">
								<?php if (in_array('forums_delete', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/forums/delete_forum/'.$forum['forumID'], 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php echo $this->pagination->create_links(); ?>

					<?php else: ?>

					<p class="clear">There are no forums yet.</p>

					<?php endif; ?>

					</div>
				</div> <!-- End Box -->

			</div> <!-- End Row -->
		</section>
