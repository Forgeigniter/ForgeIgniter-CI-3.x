	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Images / Files :
		<small>Edit File</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/files'); ?>"><i class="fa fa-file-o"></i> Images / Files</a></li>
		<li class="active">Edit File</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

			<?php if ($errors = validation_errors()): ?>
			<div class="callout callout-danger">
				<h4>Warning!</h4>
				<?php echo $errors; ?>
			</div>
			<?php endif; ?>

			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data">

				<div class="box box-crey">
					<div class="box-header with-border">
					<i class="fa fa-file-o"></i>
					<h3 class="box-title">Edit File</h3>
						<div class="box-tools">

							<?php if ($this->site->config['plan'] = 0 || $this->site->config['plan'] = 6 || (($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6) && $quota < $this->site->plans['storage'])): ?>
							<input type="submit" class="mb-xs mt-xs mr-xs btn btn-green" style="float:none;" value="Save Changes"/>
							<?php endif; ?>

						</div>
					</div>

					<div class="box-body">

							<label for="fileRef">Reference:</label>
							<?php echo @form_input('fileRef', $data['fileRef'], 'class="formelement" id="fileRef"'); ?>
							<br class="clear" /><br />

							<label for="folderID">Folder: <small>[<a href="<?php echo site_url('/admin/files/folders'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
							<?php
								$options[0] = 'No Folder';
								if ($folders):
									foreach ($folders as $folderID):
										$options[$folderID['folderID']] = $folderID['folderName'];
									endforeach;
								endif;

								echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="folderID" class="formelement"');
							?>
							<br class="clear" /><br />

					</div> <!-- end box body -->

				</div> <!-- end box -->
			</form>
			</div> <!-- end row -->
		</section>
	</section>
