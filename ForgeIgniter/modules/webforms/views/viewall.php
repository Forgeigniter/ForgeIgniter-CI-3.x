	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Web Forms :
		<small>Manage All</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/webforms'); ?>"><i class="fa fa-edit"></i> Web Forms</a></li>
		<li class="active">Manage All</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-edit"></i>
					<h3 class="box-title">Web Forms</h3>
						<div class="box-tools">
							<a href="<?= site_url('/admin/webforms/tickets'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue">Tickets</a>
							<a href="<?= site_url('/admin/webforms/add_form'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Form</a>
						</div>
					</div>

					<div class="box-body table-responsive no-padding">

						<?php if ($web_forms): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="table table-hover">
							<tr>
								<th><?php echo order_link('admin/webforms/viewall','formName','Form Name'); ?></th>
								<th><?php echo order_link('admin/webforms/viewall','dateCreated','Date Created'); ?></th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php
							$i=0;
							foreach ($web_forms as $form):
							$class = ($i % 2) ? ' class="alt"' : '';
							$i++;
						?>
							<tr<?php echo $class; ?>>
								<td>
									<?php echo anchor('/admin/webforms/edit_form/'.$form['formID'], $form['formName']); ?>
									<small>(<?php echo $form['formRef']; ?>)</small>
								</td>
								<td><?php echo dateFmt($form['dateCreated'], '', '', TRUE); ?></td>
								<td class="tiny">
									<?php echo anchor('/admin/webforms/edit_form/'.$form['formID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
									<?php if (in_array('webforms_delete', $this->permission->permissions)): ?>
										<?php echo anchor('/admin/webforms/delete_form/'.$form['formID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<?php else: ?>

						<p class="clear">You have not yet set up any web forms.</p>

						<?php endif; ?>

					</div><!-- End Box body -->

				</div> <!-- End box -->

			</div> <!-- End row -->
		</section>
