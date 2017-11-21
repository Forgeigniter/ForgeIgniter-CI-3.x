	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Images / Files :
		<small>Edit Image</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/files'); ?>"><i class="fa fa-file-o"></i> Images / Files</a></li>
		<li class="active">Edit Image</li>
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
					<h3 class="box-title">Edit Image</h3>
						<div class="box-tools">

							<?php if ($this->site->config['plan'] = 0 || $this->site->config['plan'] = 6 || (($this->site->config['plan'] > 0 && $this->site->config['plan'] < 6) && $quota < $this->site->plans['storage'])): ?>
							<input type="submit" class="mb-xs mt-xs mr-xs btn btn-green" style="float:none;" value="Save Changes"/>
							<?php endif; ?>

						</div>
					</div>

					<div class="box-body">

						<div style="float: right;">
							<?php
								$image = $this->uploads->load_image($data['imageRef']);
								$thumb = $this->uploads->load_image($data['imageRef'], true);
								$imagePath = $image['src'];
								$imageThumbPath = $thumb['src'];
							?>
							<a href="<?= base_url($imagePath); ?>" title="<?php echo $image['imageName']; ?>" class="lightbox">
								<img src="<?= base_url($imageThumbPath); ?>" class="pic" />
							</a>
						</div>

							<label for="image">Image:</label>
							<div class="uploadfile">
								<?php echo form_upload('image', '', 'size="16" id="image"'); ?>
							</div>
							<br /><br />

							<label for="folderID">Folder: <small>[<a href="<?php echo site_url('/admin/images/folders'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
							<?php
								$options[0] = 'No Folder';
								if ($folders):
									foreach ($folders as $folderID):
										$options[$folderID['folderID']] = $folderID['folderName'];
									endforeach;
								endif;

								echo @form_dropdown('folderID',$options,set_value('folderID', $data['folderID']),'id="folderID" class="formelement"');
							?>
							<br /><br />

							<label for="imageName">Description:</label>
							<?php echo @form_input('imageName', $data['imageName'], 'class="formelement" id="imageName"'); ?>
							<br /><br />

							<label for="imageRef">Reference:</label>
							<?php echo @form_input('imageRef', $data['imageRef'], 'class="formelement" id="imageRef"'); ?>
							<br /><br />

							<label for="class">Display:</label>
							<?php
								$values = array(
									'default' => 'Default',
									'left' => 'Left Align',
									'center' => 'Center Align',
									'right' => 'Right Align',
									'bordered' => 'Border',
									'bordered left' => 'Border - Left Align',
									'bordered center' => 'Border - Center Align',
									'bordered right' => 'Border - Right Align',
									'full' => 'Full Width',
									'' => 'No Style'
								);
								echo @form_dropdown('class',$values,$data['class'], 'class="formelement"');
							?>
							<br /><br />

							<label for="maxsize">Max Size (px):</label>
							<?php echo @form_input('maxsize', set_value('maxsize', (($data['maxsize']) ? $data['maxsize'] : '')), 'class="formelement" id="maxsize"'); ?>
							<br /><br />

					</div> <!-- end box body -->

				</div> <!-- end box -->
			</form>
			</div> <!-- end row -->
		</section>
	</section>
