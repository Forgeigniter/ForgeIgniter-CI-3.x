<script type="text/javascript">
$(function(){
	$('div.hidden').hide();
	$('a.showform').click(function(event){
		event.preventDefault();
		$('div.hidden div.inner').load('/templates/add/');
		$('div.hidden').fadeIn();
	});
	$('p.hide a').click(function(event){
		event.preventDefault();
		$("#upload-zip").addClass( "hidden", 10, "easeInBack");
		$(this).parent().parent().fadeOut();
	});
	$('.toggle-zip').click(function(event){
		event.preventDefault();
		$("#upload-zip").removeClass( "hidden", 10, "easeInBack");
		$('div#upload-zip').toggle('400');
		$('div#upload-image:visible, div#loader:visible').toggle('400');
	});
	$('select#filter').change(function(){
		var status = ($(this).val());
		window.location.href = '<?php echo site_url('/admin/pages/templates'); ?>/'+status;
	});
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Pages :
		<small>Manage All Templates</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-paint-brush"></i> Pages</a></li>
		<li class="active">Manage All Templates</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-paint-brush"></i>
					<h3 class="box-title">Templates / Themes</h3>

						<div class="box-tools">

							<?php
								$options = array(
									'' => 'View All',
									'page' => 'Page Templates',
									'module' => 'Module Templates'
								);

								echo form_dropdown('filter', $options, $type, 'id="filter" class="form-control" style="right: 357px; width: 150px;"');
							?>

							<a href="<?= site_url('/admin/pages/includes'); ?>" class="mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">Includes</a>
							<a href="#" class="toggle-zip mb-xs mt-xs mr-xs btn btn-blue" style="float:none;">Import Theme</a>
							<a href="<?= site_url('/admin/pages/add_template'); ?>" class="mb-xs mt-xs mr-xs btn btn-green">Add Template</a>

						</div>

					</div><!-- End box header -->

					<div class="box-body table-responsive no-padding">

						<div class="hidden">
							<p class="hide"><a href="#">x</a></p>
							<div class="inner"></div>
						</div>

						<div class="clear"></div>

						<?php if ($errors = validation_errors()): ?>
						<div class="callout callout-danger">
							<h4>Warning!</h4>
							<?php echo $errors; ?>
						</div>
						<?php endif; ?>

						<?php if (isset($message)): ?>
						<div class="callout callout-info">
							<h4>Notice</h4>
							<?php echo $message; ?>
						</div>
						<?php endif; ?>

						<div id="upload-zip" class="hidden clear">
							<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

								<label for="image">ZIP File:</label>
								<div class="uploadfile">
									<?php echo @form_upload('zip', '', 'size="16" id="image"'); ?>
								</div>
								<br class="clear" /><br />

								<input type="submit" value="Import Theme" name="upload_zip" class="button nolabel" id="submit" />
								<a href="<?php echo site_url('/admin/images'); ?>" id="cancel" class="button toggle-zip grey">Cancel</a>

								<br class="clear" /><br />

							</form>
						</div>

						<?php if ($templates): ?>

						<?php echo $this->pagination->create_links(); ?>

						<table class="table table-hover">
							<tr>
								<th>Templates</th>
								<th>Date Modified</th>
								<th>Usage</th>
								<th class="tiny">&nbsp;</th>
							</tr>
						<?php
							$i = 0;
							foreach ($templates as $template):
							$class = ($i % 2) ? ' class="alt"' : ''; $i++;
						?>
							<tr<?php echo $class;?>>
								<td><?php echo anchor('/admin/pages/edit_template/'.$template['templateID'], ($template['modulePath'] != '') ? '<small>Module</small>: '.$template['modulePath'].' <em>('.ucfirst(preg_replace('/^(.+)_/i', '', $template['modulePath'])).')</em>' : $template['templateName']); ?></td>
								<td><?php echo dateFmt($template['dateCreated']); ?></td>
								<td><?php if ($this->pages->get_template_count($template['templateID']) > 0): ?>
										<?php echo $this->pages->get_template_count($template['templateID']); ?> <small>page(s)</small>
									<?php endif; ?></td>
								<td>
									<?php echo anchor('/admin/pages/edit_template/'.$template['templateID'], '<i class="fa fa-pencil"></i>', 'class="table-edit"'); ?>
									<?php echo anchor('/admin/pages/delete_template/'.$template['templateID'], '<i class="fa fa-trash-o"></i>', 'class="table-delete"', 'onclick="return confirm(\'Are you sure you want to delete this?\')"'); ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</table>

						<?php echo $this->pagination->create_links(); ?>


						<?php else: ?>

						<p>There are no templates here yet.</p>


						<?php endif; ?>

					</div> <!-- End box body -->
				</div> <!-- End Box -->

			</div> <!-- End row -->
		</section>
