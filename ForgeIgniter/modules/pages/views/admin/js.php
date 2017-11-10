	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Themes / Templates :
		<small>Manage Page JS</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/pages/templates'); ?>"><i class="fa fa-paint-brush"></i> Themes / Templates</a></li>
		<li class="active">Manage Page JS</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-paint-brush"></i>
					<h3 class="box-title">Page JS Files</h3>

						<div class="box-tools">

							<a href="<?= site_url('/admin/pages/templates'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">Templates</a>
							<a href="<?= site_url('/admin/pages/includes'); ?>" class="toggle-zip mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">Includes</a>
							<a href="<?= site_url('/admin/pages/includes/css'); ?>" class="toggle-zip mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">CSS</a>
							<a href="<?= site_url('/admin/pages/add_include/js'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Javascript</a>

						</div>

					</div><!-- End box header -->

					<div class="box-body table-responsive no-padding">

						<div class="hidden">
							<p class="hide"><a href="#">x</a></p>
							<div class="inner"></div>
						</div>

						<?php if ($includes): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="default clear">
							<tr>
								<th>Filename</th>
								<th class="tiny">&nbsp;</th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php
							$i = 0;
							foreach ($includes as $include):
							$class = ($i % 2) ? ' class="alt"' : ''; $i++;
						?>
							<tr<?php echo $class;?>>
								<td><?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], $include['includeRef']); ?></td>
								<td>
									<?php echo anchor('/admin/pages/edit_include/'.$include['includeID'], 'Edit'); ?>
								</td>
								<td>
									<?php echo anchor('/admin/pages/delete_include/'.$include['includeID'].'/js', 'Delete', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>

						<?php else: ?>

						<div class="col col-md-4" style="padding:10px;">
							<p class="clear">You haven't added any Javascript files yet.</p>
						</div>
						<?php endif; ?>

					</div> <!-- End box body -->
				</div> <!-- end box -->

			</div> <!-- end row -->
		</section>
