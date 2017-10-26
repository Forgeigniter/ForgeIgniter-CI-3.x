	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Wiki :
		<small>Manage All</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/wiki'); ?>"><i class="fa fa-file-text-o"></i> Wiki</a></li>
		<li class="active">Manage</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<div class="row extra-padding">
			<div class="box box-crey">
				<div class="box-header">
					<i class="fa fa-file-text-o"></i>
					<h3 class="box-title">Wiki Pages</h3>
					<div class="box-tools">
					<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
					  <a href="<?= site_url('/admin/wiki/add_page'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Page</a>
					<?php endif; ?>
					</div>
				</div>

				<div class="box-body table-responsive no-padding">

					<?php if ($wiki): ?>

					<?php echo $this->pagination->create_links(); ?>

					<table class="table table-hover">
						<tr>
							<th><?php echo order_link('/admin/wiki/viewall','pageName','Page'); ?></th>
							<th><?php echo order_link('/admin/wiki/viewall','uri','URI'); ?></th>
							<th><?php echo order_link('/admin/wiki/viewall','datecreated','Date'); ?></th>
							<th class="tiny">&nbsp;</th>
						</tr>
					<?php foreach ($wiki as $page): ?>
						<tr>
							<td><?php echo (in_array('wiki_edit', $this->permission->permissions)) ? anchor('/admin/wiki/edit_page/'.$page['pageID'], $page['pageName']) : $page['pageName']; ?></td>
							<td><?php echo $page['uri']; ?></td>
							<td><?php echo dateFmt($page['dateCreated']); ?></td>

							<td class="tiny">
								<?php echo anchor('/wiki/'.$page['uri'], '<i class="fa fa-eye"></i>', 'class="table-view"'); ?>

								<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/wiki/edit_page/'.$page['pageID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
								<?php endif; ?>

								<?php if (in_array('wiki_edit', $this->permission->permissions)): ?>
									<?php echo anchor('/admin/wiki/delete_page/'.$page['pageID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>

					<?php echo $this->pagination->create_links(); ?>

					<?php else: ?>

					<p class="clear">There are no wiki pages yet.</p>

					<?php endif; ?>

				</div> <!-- End Box Body -->

			</div> <!-- End box -->
		</div> <!-- End Row -->
	</section>
