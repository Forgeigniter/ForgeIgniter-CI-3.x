	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Wiki :
		<small>Most Recent Changes</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-file-text-o"></i> Wiki</a></li>
		<li class="active">Most Recent Changes</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header">
					<i class="fa fa-file-text-o"></i>
					<h3 class="box-title">Wiki - Recent Changes</h3>
				</div>

				<div class="box-body table-responsive no-padding">

					<?php if ($changes): ?>

					<table class="table table-hover">
						<tr>
							<th>Page</th>
							<th>Notes</th>
							<th>Date</th>
							<th>User</th>
							<th class="tiny">&nbsp;</th>
						</tr>
					<?php foreach ($changes as $change): ?>
						<tr>
							<td><?php echo (in_array('wiki_edit', $this->permission->permissions)) ? anchor('/admin/wiki/edit_page/'.$change['pageID'], $change['pageName']) : $change['pageName']; ?></td>
							<td><?php echo $change['notes']; ?></td>
							<td><?php echo dateFmt($change['dateCreated']); ?></td>
							<td><?php echo $this->wiki->lookup_user($change['userID'], TRUE); ?></td>
							<td><?php echo anchor('/wiki/'.$change['uri'], '<i class="fa fa-eye"></i>', 'class="table-view"'); ?></td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php else: ?>

					<p class="clear">There are no wiki pages yet.</p>

					<?php endif; ?>

				</div>
			</div> <!-- End Box -->
		</div> <!-- End Row -->
	</section>
