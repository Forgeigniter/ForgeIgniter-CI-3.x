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

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<h1 class="headingleft">Edit User <small>(<a href="<?php echo site_url('/admin/users'); ?>">Back to Users</a>)</small></h1>

	<div class="headingright">
		<input type="submit" value="Save Changes" class="button" />
	</div>
	
	<div class="clear"></div>
	
	<?php if ($errors = validation_errors()): ?>
		<div class="error">
			<?php echo $errors; ?>
		</div>
	<?php endif; ?>
	<?php if (isset($message)): ?>
		<div class="message clear">
			<?php echo $message; ?>
		</div>
	<?php endif; ?>

<ul class="innernav clear">
	<li class="selected"><a href="#tab1" class="showtab">Details</a></li>
	<?php if (@in_array('shop', $this->permission->sitePermissions) || @in_array('community', $this->permission->sitePermissions)): ?>	
		<li><a href="#tab2" class="showtab">Address</a></li>
		<?php if (@in_array('community', $this->permission->sitePermissions)): ?>
			<li><a href="#tab3" class="showtab">Community</a></li>
			<li><a href="#tab4" class="showtab">Company</a></li>
		<?php endif; ?>
	<?php endif; ?>
</ul>

<br class="clear" />

<div id="tab1" class="tab">

	<h2>User Details</h2>

	<label for="username">Username:</label>
	<?php echo @form_input('username', set_value('username', $data['username']), 'id="username" class="formelement"'); ?>
	<br class="clear" />

	<label for="password">Password:</label>
	<?php echo @form_password('password','', 'id="password" class="formelement"'); ?>
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
		echo @form_dropdown('groupID',$values,set_value('groupIDs', $data['groupID']), 'id="groupIDs" class="formelement"'); 
	?>
	<span class="tip">To edit permissions click on `User Groups` in the Users tab.</span>
	<br class="clear" />
<?php endif; ?>

	<label for="email">Email:</label>
	<?php echo @form_input('email',set_value('email', $data['email']), 'id="email" class="formelement"'); ?>
	<br class="clear" />	

	<label for="firstName">First Name:</label>
	<?php echo @form_input('firstName',set_value('firstName', $data['firstName']), 'id="firstName" class="formelement"'); ?>
	<br class="clear" />

	<label for="lastName">Last Name:</label>
	<?php echo @form_input('lastName',set_value('lastName', $data['lastName']), 'id="lastName" class="formelement"'); ?>
	<br class="clear" />

	<label for="displayName">Display Name:</label>
	<?php echo @form_input('displayName', set_value('displayName', $data['displayName']), 'id="displayName" class="formelement" maxlength="15"'); ?>
	<span class="tip">For use in the forums (optional).</span></span><br class="clear" />

	<label for="active">Active?</label>
	<?php 
		$values = array(
			1 => 'Yes',
			0 => 'No'			
		);
		echo @form_dropdown('active',$values,set_value('active', $data['active']), 'id="active" class="formelement"'); 
	?>
	<br class="clear" />

<br />

</div>

<div id="tab2" class="tab">

<?php if (@in_array('shop', $this->permission->sitePermissions) || @in_array('community', $this->permission->sitePermissions)): ?>	
	<h2>Delivery Address</h2>

	<label for="address1">Address 1:</label>
	<?php echo @form_input('address1',set_value('address1', $data['address1']), 'id="address1" class="formelement"'); ?>
	<br class="clear" />

	<label for="address2">Address 2:</label>
	<?php echo @form_input('address2',set_value('address2', $data['address2']), 'id="address2" class="formelement"'); ?>
	<br class="clear" />

	<label for="address3">Address 3:</label>
	<?php echo @form_input('address3',set_value('address3', $data['address3']), 'id="address3" class="formelement"'); ?>
	<br class="clear" />

	<label for="city">City:</label>
	<?php echo @form_input('city',set_value('city', $data['city']), 'id="city" class="formelement"'); ?>
	<br class="clear" />

	<label for="state">State:</label>
	<?php echo @display_states('state', $data['state'], 'id="state" class="formelement"'); ?>
	<br class="clear" />

	<label for="postcode">Post /ZIP Code:</label>
	<?php echo @form_input('postcode',set_value('postcode', $data['postcode']), 'id="postcode" class="formelement"'); ?>
	<br class="clear" />

	<label for="country">Country:</label>
	<?php echo @display_countries('country', $data['country'], 'id="country" class="formelement"'); ?>
	<br class="clear" />

	<label for="phone">Phone:</label>
	<?php echo @form_input('phone',set_value('phone', $data['phone']), 'id="phone" class="formelement"'); ?>
	<br class="clear" /><br />

	<h2>Billing Address</h2>

	<p><input type="checkbox" name="sameAddress" value="1" class="checkbox" id="sameAddress" />
	The billing address is the same as my delivery address.</p>

	<div id="billing">

		<label for="billingAddress1">Address 1:</label>
		<?php echo @form_input('billingAddress1',set_value('billingAddress1', $data['billingAddress1']), 'id="billingAddress1" class="formelement"'); ?>
		<br class="clear" />
	
		<label for="billingAddress2">Address 2:</label>
		<?php echo @form_input('billingAddress2',set_value('billingAddress2', $data['billingAddress2']), 'id="billingAddress2" class="formelement"'); ?>
		<br class="clear" />
	
		<label for="billingAddress3">Address 3:</label>
		<?php echo @form_input('billingAddress3',set_value('billingAddress3', $data['billingAddress3']), 'id="billingAddress3" class="formelement"'); ?>
		<br class="clear" />
	
		<label for="billingCity">City:</label>
		<?php echo @form_input('billingCity',set_value('billingCity', $data['billingCity']), 'id="billingCity" class="formelement"'); ?>
		<br class="clear" />

		<label for="billingState">State:</label>
		<?php echo display_states('billingState', $data['billingState'], 'id="billingState" class="formelement"'); ?>
		<br class="clear" />
	
		<label for="billingPostcode">Post /ZIP Code:</label>
		<?php echo @form_input('billingPostcode',set_value('billingPostcode', $data['billingPostcode']), 'id="billingPostcode" class="formelement"'); ?>
		<br class="clear" />
	
		<label for="billingCountry">Country:</label>
		<?php echo display_countries('billingCountry', $data['billingCountry'], 'id="billingCountry" class="formelement"'); ?>
		<br class="clear" />

	</div>
	<br />
		
<?php endif; ?>

</div>

<div id="tab3" class="tab">

<?php if (@in_array('community', $this->permission->permissions)): ?>

	<h2>Community</h2>

	<label for="signature">Signature:</label>
	<?php echo @form_textarea('signature',set_value('signature', $data['signature']), 'id="signature" class="formelement small"'); ?>
	<br class="clear" />

	<label for="bio">Bio:</label>
	<?php echo @form_textarea('bio',set_value('bio', $data['bio']), 'id="bio" class="formelement small"'); ?>
	<br class="clear" />

	<label for="companyName">Company Website:</label>
	<input type="text" name="companyWebsite" value="{form:companyWebsite}" id="companyWebsite" class="formelement" />
	<br class="clear" />

	<label for="notifications">Notifications:</label>
	<?php
		$values = array(
			0 => 'No',
			1 => 'Yes',
		);
		echo @form_dropdown('notifications', $values, set_value('notifications', $data['notifications']), 'id="notifications" class="formelement"'); 
	?>
	<br class="clear" />

	<label for="privacy">Privacy:</label>
	<?php
		$values = array(
			'V' => 'Everyone can see my profile',
			'H' => 'Hide my profile and feed'
		);
		echo @form_dropdown('privacy', $values, set_value('privacy', $data['privacy']), 'id="privacy" class="formelement"'); 
	?>
	<br class="clear" />

	<label for="kudos">Kudos:</label>
	<?php echo @form_input('kudos',set_value('kudos', $data['kudos']), 'id="kudos" class="formelement"'); ?>
	<br class="clear" /><br />	

<?php endif; ?>

</div>

<?php if (@in_array('community', $this->permission->sitePermissions)): ?>	

<div id="tab4" class="tab">

	<h2>Company</h2>

	<label for="companyName">Company Name:</label>
	<?php echo @form_input('companyName',set_value('companyName', $data['companyName']), 'id="companyName" class="formelement"'); ?>
	<br class="clear" />

	<label for="companyDescription">Company Description:</label>
	<?php echo @form_textarea('companyDescription',set_value('companyDescription', $data['companyDescription']), 'id="companyDescription" class="formelement small"'); ?>
	<br class="clear" />

	<label for="companyWebsite">Company Website:</label>
	<?php echo @form_input('companyWebsite',set_value('companyWebsite', $data['companyWebsite']), 'id="companyWebsite" class="formelement"'); ?>
	<br class="clear" />
	
</div>

<?php endif; ?>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>
	
</form>
