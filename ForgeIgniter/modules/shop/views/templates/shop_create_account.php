{include:header}

<!-- jQuery needed for this -->
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
});
</script>

<div id="tpl-shop" class="module">
		
	<div class="col col1">

		<h1>Your Details</h1>

		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}
		{if message}
			<div class="message">
				{message}
			</div>
		{/if}

		<p>Please fill in the form below with your details.</p>

		<form method="post" action="{page:uri}" class="default">
		
			<h2>Login</h2>
		
			<label for="email">Email:</label>
			<input type="text" name="email" value="{form:email}" id="email" class="formelement" />
			<br class="clear" />

			<label for="password">Password:</label>
			<input type="password" name="password" value="" id="password" class="formelement" />
			<br class="clear" />
		
			<label for="confirmPassword">Confirm Password:</label>
			<input type="password" name="confirmPassword" value="" id="confirmPassword" class="formelement" />
			<br class="clear" /><br />

			<h2>Your Details</h2>		
		
			<label for="firstName">First Name:</label>
			<input type="text" name="firstName" value="{form:firstName}" id="firstName" class="formelement" />
			<br class="clear" />
		
			<label for="lastName">Last Name:</label>
			<input type="text" name="lastName" value="{form:lastName}" id="lastName" class="formelement" />
			<br class="clear" />

			<label for="phone">Phone:</label>
			<input type="text" name="phone" value="{form:phone}" id="phone" class="formelement" />
			<br class="clear" /><br />
		
			<h2>Delivery Address</h2>

			<label for="address1">Address 1:</label>
			<input type="text" name="address1" value="{form:address1}" id="address1" class="formelement" />
			<br class="clear" />
		
			<label for="address2">Address 2:</label>
			<input type="text" name="address2" value="{form:address2}" id="address2" class="formelement" />
			<br class="clear" />
		
			<label for="address3">Address 3:</label>
			<input type="text" name="address3" value="{form:address3}" id="address3" class="formelement" />
			<br class="clear" />
		
			<label for="city">City:</label>
			<input type="text" name="city" value="{form:city}" id="city" class="formelement" />
			<br class="clear" />

			<label for="state">State:</label>
			{select:state}
			<br class="clear" />				
		
			<label for="postcode">ZIP/Post code:</label>
			<input type="text" name="postcode" value="{form:postcode}" id="postcode" class="formelement" />
			<br class="clear" />
		
			<label for="country">Country:</label>
			{select:country}
			<br class="clear" /><br />

			<h2>Billing Address</h2>

			<p><input type="checkbox" name="sameAddress" value="1" class="checkbox" id="sameAddress" />
			My billing address is the same as my delivery address.</p>

			<div id="billing">

				<label for="billingAddress1">Address 1:</label>
				<input type="text" name="billingAddress1" value="{form:billingAddress1}" id="billingAddress1" class="formelement" />
				<br class="clear" />
			
				<label for="billingAddress2">Address 2:</label>
				<input type="text" name="billingAddress2" value="{form:billingAddress2}" id="billingAddress2" class="formelement" />
				<br class="clear" />
			
				<label for="billingAddress3">Address 3:</label>
				<input type="text" name="billingAddress3" value="{form:billingAddress3}" id="billingAddress3" class="formelement" />
				<br class="clear" />
			
				<label for="billingCity">City:</label>
				<input type="text" name="billingCity" value="{form:billingCity}" id="billingCity" class="formelement" />
				<br class="clear" />

				<label for="billingState">State:</label>
				{select:billingState}
				<br class="clear" />
			
				<label for="billingPostcode">ZIP/Post code:</label>
				<input type="text" name="billingPostcode" value="{form:billingPostcode}" id="billingPostcode" class="formelement" />
				<br class="clear" />
			
				<label for="billingCountry">Country:</label>
				{select:billingCountry}
				<br class="clear" /><br />
				
			</div>
			<input type="hidden" name="listID" value="29" />
			<input type="submit" value="Create Account" class="button nolabel" />
			
		</form>

	</div>
	<div class="col col2">
	
		<h3>Categories</h3>
	
		<ul class="menu">
			{shop:categories}
		</ul>
		
	</div>

</div>
	
{include:footer}