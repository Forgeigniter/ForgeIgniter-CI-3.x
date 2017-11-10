	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Themes / Templates :
		<small>Manage Page Includes</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/pages/templates'); ?>"><i class="fa fa-paint-brush"></i> Themes / Templates</a></li>
		<li class="active">Manage Page Includes</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-paint-brush"></i>
					<h3 class="box-title">Page Includes</h3>

						<div class="box-tools">

							<a href="<?= site_url('/admin/pages/templates'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">Templates</a>
							<a href="<?= site_url('/admin/pages/includes/css'); ?>" class="toggle-zip mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">CSS</a>
							<a href="<?= site_url('/admin/pages/includes/js'); ?>" class="toggle-zip mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">Javascript</a>
							<a href="<?= site_url('/admin/pages/add_include'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Include</a>

						</div>

					</div><!-- End box header -->

					<div class="box-body table-responsive no-padding">

						<div class="hidden">
							<p class="hide"><a href="#">x</a></p>
							<div class="inner"></div>
						</div>

						<div class="clear"></div>

						<?php if ($includes): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="table table-hover">
							<tr>
								<th>Reference</th>
								<th>Date Modified</th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php
							$i = 0;
							foreach ($includes as $include):
							$class = ($i % 2) ? ' class="alt"' : ''; $i++;
						?>
							<tr<?php echo $class;?>>
								<td><?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], $include['includeRef']); ?></td>
								<td><?php echo dateFmt($include['dateCreated']); ?></td>
								<td>
									<?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
									<?php echo anchor('/admin/pages/delete_include/'.$include['includeID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<?php else: ?>

						<p class="clear">You haven't made any Include files yet.</p>

						<?php endif; ?>

					</div> <!-- End box body -->
				</div> <!-- end box -->

			</div> <!-- end row -->
		</section>
