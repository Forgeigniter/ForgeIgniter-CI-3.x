	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Forums :
		<small>Add Forum</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/dashboard'); ?>"><i class="fa fa-list-alt"></i> Forums</a></li>
		<li class="active">Add Forum</li>
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

				<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

				<div class="box box-crey">
					<div class="box-header with-border">
						<i class="fa fa-list-alt"></i>
						<h3 class="box-title">Add Forum</h3>

						<div class="box-tools">
							<input type="submit" value="Add Forum" class="mb-xs mt-xs mr-xs btn btn-green" />
						</div>
					</div>

					<div class="box-body">
					  <div class="col col-md-4">
						<label for="forumName">Forum Name:</label>
						<?php echo @form_input('forumName', set_value('forumName', $data['forumName']), 'id="forumName" class="form-control input-style"'); ?>
						<br class="clear" />

						<label for="description">Description:</label>
						<?php echo @form_input('description', set_value('description', $data['description']), 'id="description" class="form-control input-style"'); ?>
						<br class="clear" /><br />

						<label for="category">Category: <small>[<a href="<?php echo site_url('/admin/forums/categories'); ?>" onclick="return confirm('You will lose any unsaved changes.\n\nContinue anyway?')">update</a>]</small></label>
						<?php
						if ($categories):
							foreach ($categories as $category):
								$options[$category['catID']] = $category['catName'];
							endforeach;

							echo @form_dropdown('catID',$options,set_value('catID', $data['catID']),'id="category" class="form-control input-style"');
						endif;
						?>
						<br class="clear" /><br />

						<label for="groupID">Group:</label>
						<?php
							$values = array(
								0 => 'Everyone',
								$this->session->userdata('groupID') => 'Administrators only'
							);
							if ($groups)
							{
								foreach($groups as $group)
								{
									$values[$group['groupID']] = $group['groupName'];
								}
							}
							echo @form_dropdown('groupID',$values,$data['groupID'], 'id="groupID" class="form-control input-style"');
						?>
						<div class="help-tip">
						    <p>Who has access to this forum?</p>
						</div>
						<br class="clear" />

						<label for="active">Active?</label>
						<?php
							$options = array();
							$options[1] = 'Yes';
							$options[0] = 'No';
							echo @form_dropdown('active',$options,set_value('active', $data['active']),'id="active" class="form-control input-style"');

						?>
						<br class="clear" /><br />
					  </div>
					</div> <!-- End Box Body -->
				</div> <!-- End Box-->

			</div> <!-- End Row -->
		</section>
	</section>
