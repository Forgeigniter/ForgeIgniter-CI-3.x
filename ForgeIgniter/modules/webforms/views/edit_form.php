<script type="text/javascript">
function showGroup(){
	if ($('select#account').val() == 0){
		$('div.showGroup').hide();
	} else {
		$('div.showGroup').fadeIn();
	}
}
$(function(){
	$('select#account').change(function(){
		showGroup();
	});
	showGroup();
});
</script>

	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Web Forms :
		<small>Edit Form</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/webforms'); ?>"><i class="fa fa-edit"></i> Web Forms</a></li>
		<li class="active">Edit Form</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

			<form name="form" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

				<div class="row">
					<div class="pull-left">
						<a href="<?= site_url('/admin/webforms/viewall'); ?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Web Forms</a>
					</div>
					<div class="col-md-3 pull-right">
						<input
							type="submit"
							value="Save Changes"
							class="btn btn-green margin-bottom"
							style="right:4%;position: absolute;top: 0px;"
						/>
					</div>
				</div>

				<!-- Main row -->
				<div class="row">

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

					<div class="box box-crey">
						<div class="box-header with-border">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">Edit Web Form</h3>
						</div>

						<div class="box-body" style="padding:20px">
						  <div class="col-sm-3">

							<label for="formName">Form Name:</label>
							<?php echo @form_input('formName', set_value('formName', $data['formName']), 'id="formName" class="form-control input-style"'); ?>
							<br class="clear" />

							<label for="fieldSet">Type of Form:</label>
							<?php
								$values = array(
									1 => 'Enquiry Form',
									2 => 'Newsletter',
									0 => 'Custom'
								);
								echo @form_dropdown('fieldSet',$values,set_value('fieldSet', $data['fieldSet']), 'id="fieldSet" class="form-control input-style"');
							?>
							<span class="tip">Automatically populate your form with fields based on the type, or select 'Custom' to not populate with fields.</span>
							<br class="clear" />

							<label for="fileTypes">Allow Files?</label>
							<?php
								$values = array(
									'' => 'Don\'t allow files',
									'jpg|gif|png|jpeg' => 'Allow images',
									'doc|pdf|txt|rtf|xls' => 'Allow documents',
									'jpg|gif|png|jpeg|doc|pdf|txt|rtf|xls|swf' => 'Allow images and documents',
									'jpg|gif|png|jpeg|doc|pdf|txt|rtf|xls|swf|mp3|mp4' => 'Allow all files'
								);
								echo @form_dropdown('fileTypes',$values,set_value('fileTypes', $data['fileTypes']), 'id="fileTypes" class="form-control input-style"');
							?>
							<span class="tip">You can allow users to upload files such as images and documents if you wish. Form must have the correct enctype.</span>
							<br class="clear" />

							<br />

							<h4 class="underline">Outcomes <small>(optional)</small></h4>

							<label for="account">Create User Account?</label>
							<?php
								$values = array(
									0 => 'No',
									1 => 'Yes',
								);
								echo @form_dropdown('account',$values,set_value('account', $data['account']), 'id="account" class="form-control input-style"');
							?>
							<span class="tip">Optionally create user account.</span>
							<br class="clear" />

							<div class="showGroup">
								<label for="groupID">Move to Group:</label>
								<?php
									$values = array(
										0 => 'None'
									);
									if ($groups)
									{
										foreach($groups as $group)
										{
											$values[$group['groupID']] = $group['groupName'];
										}
									}
									echo @form_dropdown('groupID',$values,set_value('groupID', $data['groupID']), 'id="groupIDs" class="form-control input-style"');
								?>
								<span class="tip">You can only move the user to a group without admin permissions.</span>
								<br class="clear" />
							</div>

							<label for="outcomeEmails">Emails to CC:</label>
							<?php echo @form_input('outcomeEmails', set_value('outcomeEmails', $data['outcomeEmails']), 'id="outcomeEmails" class="form-control input-style"'); ?>
							<span class="tip">This will override the default email that the ticket is CCed to. Separate emails with a comma.</span>
							<br class="clear" />

							<label for="outcomeRedirect">Redirect:</label>
							<?php echo @form_input('outcomeRedirect', set_value('outcomeRedirect', $data['outcomeRedirect']), 'id="outcomeRedirect" class="form-control input-style"'); ?>
							<span class="tip">Here you can redirect the user to a URL on your website if you wish (e.g. form/success).</span>
							<br class="clear" />

							<label for="outcomeMessage">Message:</label>
							<?php echo @form_textarea('outcomeMessage', set_value('outcomeMessage', $data['outcomeMessage']), 'id="outcomeMessage" class="form-control input-style"'); ?>
							<br class="clear" />
							<span class="tip nolabel">Here you can display a custom message after the user submits the form.</span>
							<br class="clear" /><br />

						  </div>
						</div><!-- end box body -->

					</div> <!-- end box -->

				</div> <!-- end row -->

			</form>

			</div> <!-- end row -->
		</section>
