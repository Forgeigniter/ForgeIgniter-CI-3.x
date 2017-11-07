	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Users :
		<small>Import</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-users"></i> Users</a></li>
		<li class="active">Import</li>
	  </ol>
	</section> 

	<!-- Main content -->
    <section class="content container-fluid extra-padding">

		<section class="content">

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

			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" enctype="multipart/form-data" class="default">

			<div class="row">
				<div class="pull-left">
					<a href="<?= site_url('/admin/users'); ?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Users</a>
				</div>
				<div class="col-md-6 pull-right">
					<input type="submit" value="Upload File" name="upload file" id="submit" class="btn btn-green margin-bottom save" />
				</div>
			</div>

			<div class="row">
				<div class="box box-crey">

					<div class="box-header with-border">
						<i class="fa fa-users"></i>
						<h3 class="box-title">Import Users</h3>
					</div>

					<div class="box-body">

						<p>To import user in to the database please make sure you create a CSV file with the first column as Email, the second as First name and the third as Second name.</p>

							<label for="csv">CSV File:</label>
							<div class="uploadfile">
								<?php echo @form_upload('csv', '', 'size="16" id="csv"'); ?>
							</div>
							<br class="clear" />

							<input type="hidden" name="test" value="" />

					</div> <!-- End Box Body -->

				</div><!-- End Box -->
			</div><!-- End row -->

			</form>

		</section>
