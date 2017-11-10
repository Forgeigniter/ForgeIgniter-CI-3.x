<script type="text/javascript">
function hideAddress(){
	if (
		$('input#billingAddress1').val() == $('input#address1').val() &&
		$('input#billingAddress2').val() == $('input#address2').val() &&
		$('input#billingAddress3').val() == $('input#address3').val() &&
		$('input#billingCity').val() == $('input#city').val() &&
		$('select#billingState').val() == $('select#state').val() &&
		$('input#billingPostcode').val() == $('input#postcode').val() &&
		$('select#billingCountry').val() == $('select#country').val()
	){
		$('div#billing').hide();
		$('input#sameAddress').attr('checked', true);
	}
}
$(function(){
	$('a.showtab').click(function(event){
		event.preventDefault();
		var div = $(this).attr('href');
		$('div.tab').hide();
		$(div).show();
	});
	$('ul.innernav a').click(function(event){
		event.preventDefault();
		$(this).parent().siblings('li').removeClass('selected');
		$(this).parent().addClass('selected');
	});
	$('div.tab:not(#tab1)').hide();
	$('input#sameAddress').click(function(){
		$('div#billing').toggle(200);
		$('input#billingAddress1').val($('input#address1').val());
		$('input#billingAddress2').val($('input#address2').val());
		$('input#billingAddress3').val($('input#address3').val());
		$('input#billingCity').val($('input#city').val());
		$('select#billingState').val($('select#state').val());
		$('input#billingPostcode').val($('input#postcode').val());
		$('select#billingCountry').val($('select#country').val());
	});
	hideAddress();
});
</script>



	<!-- Content Header (Page header) -->
	<section class="content-header">
	  <h1>
		Users :
		<small>Add User</small>
	  </h1>
	  <ol class="breadcrumb">
		<li><a href="<?= site_url('admin/users'); ?>"><i class="fa fa-users"></i> Users</a></li>
		<li class="active">Add</li>
	  </ol>
	</section>

	<!-- Main content -->
	<section class="content container-fluid extra-padding">
		<section class="content">

			<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

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

			<div class="row">
				<div class="pull-left">
					<a href="<?=site_url('/admin/users');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Users</a>
				</div>
				<div class="col-md-6 pull-right">
					<input type="submit" value="Add User" name="save" id="save" class="btn btn-green margin-bottom save" />
				</div>
			</div>

			<div class="row">

				<div class="box box-crey nav-tabs-custom">

					<ul class="nav nav-tabs pull-right">
						<li class="pull-left header box-title"><i class="fa fa-edit"></i> Add New User </li>
						<?php if (@in_array('shop', $this->permission->sitePermissions) || @in_array('community', $this->permission->sitePermissions)): ?>
							<?php if (@in_array('community', $this->permission->sitePermissions)): ?>
						<li class=""><a href="#tab_company" data-toggle="tab" aria-expanded="false">Company</a></li>
						<li class=""><a href="#tab_community" data-toggle="tab" aria-expanded="false">Community</a></li>
							<?php endif; ?>
						<li class=""><a href="#tab_address" data-toggle="tab" aria-expanded="false">Address</a></li>
						<li class="active"><a href="#tab_details" data-toggle="tab" aria-expanded="true">Details</a></li>
						<?php endif; ?>
					</ul>

					<div class="box-body">
					  <div class="tab-content">

						<div class="tab-pane active" id="tab_details">
						  <div class="row">
							<div class="col col-md-4" style="padding-left:30px;">

								<h2>User Details</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="username">Username:</label>
								<?php echo @form_input('username', set_value('username', $data['username']), 'id="username" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="password">Password:</label>
								<?php echo @form_password('password','', 'id="password" class="form-control input-style"'); ?>
								<br class="clear" />

							<?php if (@in_array('users_groups', $this->permission->permissions)): ?>
								<label for="permissions">Group:</label>
								<?php
									$values = array(
										0 => 'None'
									);

									if ($this->session->userdata('groupID') == '-1')
									{
										$values[-1] = 'Superuser';
									}

									$values[$this->site->config['groupID']] = 'Administrator';
									if ($groups)
									{
										foreach($groups as $group)
										{
											$values[$group['groupID']] = $group['groupName'];
										}
									}
									echo @form_dropdown('groupID',$values,set_value('groupIDs', $data['groupID']), 'id="groupIDs" class="form-control input-style"');
								?>
								<span class="tip">To edit permissions click on `User Groups` in the Users tab.</span>
								<br class="clear" />
							<?php endif; ?>

								<label for="email">Email:</label>
								<?php echo @form_input('email',set_value('email', $data['email']), 'id="email" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="firstName">First Name:</label>
								<?php echo @form_input('firstName',set_value('firstName', $data['firstName']), 'id="firstName" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="lastName">Last Name:</label>
								<?php echo @form_input('lastName',set_value('lastName', $data['lastName']), 'id="lastName" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="displayName">Display Name:</label>
								<?php echo @form_input('displayName', set_value('displayName', $data['displayName']), 'id="displayName" class="form-control input-style" maxlength="15"'); ?>
								<span class="tip">For use in the forums (optional).</span></span><br class="clear" />

								<label for="active">Active?</label>
								<?php
									$values = array(
										1 => 'Yes',
										0 => 'No'
									);
									echo @form_dropdown('active',$values,set_value('active', $data['active']), 'id="active" class="form-control input-style"');
								?>
								<br class="clear" /><br />

								</div>

							</div>
						  </div>
						</div>

						<div class="tab-pane" id="tab_address">
						  <div class="row">
							<div class="col col-md-4" style="padding-left:30px;">

							<?php if (@in_array('shop', $this->permission->sitePermissions) || @in_array('community', $this->permission->sitePermissions)): ?>
								<h2>Delivery Address</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="address1">Address 1:</label>
								<?php echo @form_input('address1',set_value('address1', $data['address1']), 'id="address1" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="address2">Address 2:</label>
								<?php echo @form_input('address2',set_value('address2', $data['address2']), 'id="address2" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="address3">Address 3:</label>
								<?php echo @form_input('address3',set_value('address3', $data['address3']), 'id="address3" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="city">City:</label>
								<?php echo @form_input('city',set_value('city', $data['city']), 'id="city" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="state">State:</label>
								<?php echo @display_states('state', $data['state'], 'id="state" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="postcode">Post /ZIP Code:</label>
								<?php echo @form_input('postcode',set_value('postcode', $data['postcode']), 'id="postcode" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="country">Country:</label>
								<?php echo @display_countries('country', $data['country'], 'id="country" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="phone">Phone:</label>
								<?php echo @form_input('phone',set_value('phone', $data['phone']), 'id="phone" class="form-control input-style"'); ?>
								<br class="clear" /><br />



								</div>
							</div>

							<div class="col col-md-4" style="padding-left:30px;">

								<h2>Billing Address</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->
								<br />
								<p><input type="checkbox" name="sameAddress" value="1" id="sameAddress" />
								The billing address is the same as my delivery address.</p>

								<div id="billing">

									<label for="billingAddress1">Address 1:</label>
									<?php echo @form_input('billingAddress1',set_value('billingAddress1', $data['billingAddress1']), 'id="billingAddress1" class="form-control input-style"'); ?>
									<br class="clear" />

									<label for="billingAddress2">Address 2:</label>
									<?php echo @form_input('billingAddress2',set_value('billingAddress2', $data['billingAddress2']), 'id="billingAddress2" class="form-control input-style"'); ?>
									<br class="clear" />

									<label for="billingAddress3">Address 3:</label>
									<?php echo @form_input('billingAddress3',set_value('billingAddress3', $data['billingAddress3']), 'id="billingAddress3" class="form-control input-style"'); ?>
									<br class="clear" />

									<label for="billingCity">City:</label>
									<?php echo @form_input('billingCity',set_value('billingCity', $data['billingCity']), 'id="billingCity" class="form-control input-style"'); ?>
									<br class="clear" />

									<label for="billingState">State:</label>
									<?php echo display_states('billingState', $data['billingState'], 'id="billingState" class="form-control input-style"'); ?>
									<br class="clear" />

									<label for="billingPostcode">Post /ZIP Code:</label>
									<?php echo @form_input('billingPostcode',set_value('billingPostcode', $data['billingPostcode']), 'id="billingPostcode" class="form-control input-style"'); ?>
									<br class="clear" />

									<label for="billingCountry">Country:</label>
									<?php echo display_countries('billingCountry', $data['billingCountry'], 'id="billingCountry" class="form-control input-style"'); ?>
									<br class="clear" />

								</div>
								<br />

								</div>

							</div>

							<?php endif; ?>

						  </div>
						</div>

						<div class="tab-pane" id="tab_community">
						  <div class="row">
							<div class="col col-md-4" style="padding-left:30px;">

							<?php if (@in_array('community', $this->permission->permissions)): ?>

								<h2>Community</h2>

								<div style="padding-left:10px;"> <!-- Indent Content -->

								<label for="signature">Signature:</label>
								<?php echo @form_textarea('signature',set_value('signature', $data['signature']), 'id="signature" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="bio">Bio:</label>
								<?php echo @form_textarea('bio',set_value('bio', $data['bio']), 'id="bio" class="form-control input-style"'); ?>
								<br class="clear" />

								<label for="notifications">Notifications:</label>
								<?php
									$values = array(
										0 => 'No',
										1 => 'Yes',
									);
									echo @form_dropdown('notifications', $values, set_value('notifications', $data['notifications']), 'id="notifications" class="form-control input-style"');
								?>
								<br class="clear" />

								<label for="privacy">Privacy:</label>
								<?php
									$values = array(
										'V' => 'Everyone can see my profile',
										'H' => 'Hide my profile and feed'
									);
									echo @form_dropdown('privacy', $values, set_value('privacy', $data['privacy']), 'id="privacy" class="form-control input-style"');
								?>
								<br class="clear" />

								<label for="kudos">Kudos:</label>
								<?php echo @form_input('kudos',set_value('kudos', $data['kudos']), 'id="kudos" class="form-control input-style"'); ?>
								<br class="clear" /><br />

								</div>

							<?php endif; ?>

							</div>
						  </div>
						</div>

						<?php if (@in_array('community', $this->permission->sitePermissions)): ?>
						<div class="tab-pane" id="tab_company">
						  <div class="row">
							<div class="col col-md-4" style="padding-left:30px;">

							<h2>Company</h2>

							<div style="padding-left:10px;"> <!-- Indent Content -->

							<label for="companyName">Company Name:</label>
							<?php echo @form_input('companyName',set_value('companyName', $data['companyName']), 'id="companyName" class="form-control input-style"'); ?>
							<br class="clear" />

							<label for="companyDescription">Company Description:</label>
							<?php echo @form_textarea('companyDescription',set_value('companyDescription', $data['companyDescription']), 'id="companyDescription" class="form-control input-style"'); ?>
							<br class="clear" />

							<label for="companyWebsite">Company Website:</label>
							<?php echo @form_input('companyWebsite',set_value('companyWebsite', $data['companyWebsite']), 'id="companyWebsite" class="form-control input-style"'); ?>
							<br class="clear" /><br />

							</div>

							</div>
						  </div>
						</div>
						<?php endif; ?>

					  </div> <!-- End tab-content -->
					</div> <!-- End Box Body -->

				</div> <!-- End box / tabs -->

			</div>

			</form>

		</section>
