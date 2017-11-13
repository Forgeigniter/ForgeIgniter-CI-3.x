	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Web Forms :
		<small>View Ticket</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/webforms'); ?>"><i class="fa fa-edit"></i> Web Forms</a></li>
		<li class="active">View Ticket</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<section class="content">
			<div class="row extra-padding">

				<form action="<?php echo site_url($this->uri->uri_string()); ?>" method="post" class="default">

				<div class="row">
					<div class="pull-left">
						<a href="<?= site_url('/admin/webforms/tickets'); ?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Tickets</a>
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

					<div class="box box-crey">
						<div class="box-header with-border">
							<i class="fa fa-edit"></i>
							<h3 class="box-title">View Ticket</h3>
						</div>

						<div class="box-body">

							<div class="message">
								<p>
									<strong>Subject:</strong> [#<?php echo $data['ticketID']; ?>]:</strong> <?php echo $data['subject']; ?><br />
									<strong>Date sent:</strong> <?php echo dateFmt($data['dateCreated']); ?><br />
									<?php if ($data['formName']): ?>
										<strong>Web Form:</strong> <?php echo $data['formName']; ?>
									<?php endif; ?>
								</p>
							</div>

							<div id="tpl-2col">

								<div class="col1">

									<h2 class="underline">Body</h2>

									<p><?php echo nl2br(auto_link($data['body'])); ?></p>

								</div>
								<div class="col2">

									<h2 class="underline">User Details</h2>

									<p><strong>Full name:</strong> <?php echo $data['fullName']; ?></p>
									<p><strong>Email:</strong> <a href="mailto:<?php echo $data['email']; ?>?subject=Re: [#<?php echo $data['ticketID']; ?>]: <?php echo $data['subject']; ?>"><?php echo $data['email']; ?></a></p>

								</div>
								<div class="clear"></div>
							</div>

							<br />

							<h2 class="underline">Process Ticket</h2>

							<label for="closed">Status:</label>
							<?php
								$options = array(
										0 => 'Open',
										1 => 'Closed');

								echo form_dropdown('closed',$options,set_value('closed', $data['closed']),'id="closed"');
							?>
							<br class="clear" />

							<label for="notes">Ticket notes:</label>
							<?php echo form_textarea('notes',set_value('notes', $data['notes']), 'id="notes" class="formelement small"'); ?>
							<br class="clear" />

						</div> <!-- end box body -->

					</div> <!-- end box -->
				</div> <!-- end row -->

				</form>

			</div> <!-- end row -->
		</section>
