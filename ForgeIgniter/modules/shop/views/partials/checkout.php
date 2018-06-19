<?php if ($this->site->config['shopGateway'] == 'paypal'): ?>

	<input type="hidden" name="cmd" value="_ext-enter" />
	<input type="hidden" name="redirect_cmd" value="_xclick" />
	<input type="hidden" name="business" value="<?php echo $this->site->config['shopEmail']; ?>" />
	<input type="hidden" name="item_name" value="<?php echo $this->site->config['siteName']; ?> Shopping Cart" />
	<input type="hidden" name="item_number" value="<?php echo $transaction['orderID']; ?>" />
	<input type="hidden" name="amount" value="<?php echo $amount; ?>" />
	<input type="hidden" name="custom" value="<?php echo $transaction['transactionID']; ?>" />
	<input type="hidden" name="no_shipping" value="1" />
	<input type="hidden" name="return" value="<?php echo site_url('/shop/success'); ?>" />
	<input type="hidden" name="cancel_return" value="<?php echo site_url('/shop/cancel'); ?>" />
	<input type="hidden" name="notify_url" value="<?php echo site_url('/shop/ipn'); ?>" />
	<input type="hidden" name="cn" value="Notes">
	<input type="hidden" name="currency_code" value="<?php echo $currency; ?>" />
	<input type="hidden" name="address_name" value="<?php echo trim($user['firstName'].$user['lastName']); ?>" />
	<input type="hidden" name="address_street" value="<?php echo $user['address1']; ?>" />
	<input type="hidden" name="address_city" value="<?php echo $user['city']; ?>" />
	<input type="hidden" name="address_state" value="<?php echo lookup_state($user['state']); ?>" />
	<input type="hidden" name="address_zip" value="<?php echo $user['postcode']; ?>" />
	<input type="hidden" name="address_country" value="<?php echo lookup_country($user['country']); ?>" />
	<input type="hidden" name="address_country_code" value="<?php echo $user['country']; ?>" />
	<input type="hidden" name="contact_phone" value="<?php echo $user['phone']; ?>" />
	<input type="hidden" name="payer_email" value="<?php echo $user['email']; ?>" />

<?php elseif ($this->site->config['shopGateway'] == 'paypalpro'): ?>

	<input type="hidden" name="IPADDRESS" value="<?php echo $this->input->ip_address(); ?>" />
	<input type="hidden" name="INVNUM" value="<?php echo $transaction['orderID']; ?>" />
	<input type="hidden" name="AMT" value="<?php echo $amount; ?>" />
	<input type="hidden" name="CURRENCYCODE" value="<?php echo $currency; ?>" />
	<input type="hidden" name="EMAIL" value="<?php echo $user['email']; ?>" />
	<input type="hidden" name="SHIPTONAME" value="<?php echo trim($user['firstName'].$user['lastName']); ?>" />
	<input type="hidden" name="SHIPTOSTREET" value="<?php echo $user['address1']; ?>" />
	<input type="hidden" name="SHIPTOCITY" value="<?php echo $user['city']; ?>" />
	<input type="hidden" name="SHIPTOSTATE" value="<?php echo lookup_state($user['state']); ?>" />
	<input type="hidden" name="SHIPTOZIP" value="<?php echo $user['postcode']; ?>" />
	<input type="hidden" name="SHIPTOCOUNTRY" value="<?php echo $user['country']; ?>" />
	<input type="hidden" name="SHIPTOPHONENUM" value="<?php echo $user['phone']; ?>" />

	<div class="formrow cctype-row">
		<label for="cctype">Cart Type:</label>
		<select name="CREDITCARDTYPE" id="cctype" class="formelement">
			<option value="Visa">Visa</option>
			<option value="MasterCard">MasterCard</option>
			<option value="Discover">Discover</option>
			<option value="Amex">Amex</option>
		</select>
		<br class="clear" />
	</div>

	<div class="formrow ccnumber-row">
		<label for="ccnumber">Card Number:</label>
		<input type="text" name="ACCT" id="ccnumber" class="formelement" autocomplete="off" />
		<br class="clear" />
	</div>

	<div class="formrow ccexpiry-row">
		<label for="ccexpirymonth">Expiry Date:</label>
		<?php echo @expiry_months_dropdown('expMonth', $this->input->post('expMonth'), 'id="ccexpirymonth" class="formelement small"'); ?>
		<?php echo @expiry_years_dropdown('expYear', $this->input->post('expYear'), 'id="ccexpiryyear" class="formelement small"'); ?>
		<br class="clear" />
	</div>

	<div class="formrow cvv2-row">
		<label for="cvv2">CVV2 Number:</label>
		<input type="text" name="CVV2" id="cvv2" maxlength="4" size="5" class="formelement small" />
		<br class="clear" />
	</div>

	<div class="formrow ccfirstname-row">
		<label for="ccfirstname">First Name:</label>
		<input type="text" name="FIRSTNAME" id="ccfirstname" class="formelement" value="<?php echo ($this->input->post('FIRSTNAME')) ? $this->input->post('FIRSTNAME') : @$user['firstName']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow cclastname-row">
		<label for="cclastname">Last Name:</label>
		<input type="text" name="LASTNAME" id="cclastname" class="formelement" value="<?php echo ($this->input->post('LASTNAME')) ? $this->input->post('LASTNAME') : @$user['lastName']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow ccstreet-row">
		<label for="cccity">Street:</label>
		<input type="text" name="STREET" id="ccstreet" class="formelement" value="<?php echo ($this->input->post('STREET')) ? $this->input->post('STREET') : @$user['billingAddress1']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow cccity-row">
		<label for="cccity">City:</label>
		<input type="text" name="CITY" id="cccity" class="formelement" value="<?php echo ($this->input->post('CITY')) ? $this->input->post('CITY') : @$user['billingCity']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow ccstate-row">
		<label for="ccstate">State:</label>
		<?php echo @display_states('STATE', (($this->input->post('STATE')) ? $this->input->post('STATE') : @$user['billingState']), 'id="ccstate" class="formelement"'); ?>
		<br class="clear" />
	</div>

	<div class="formrow cczip-row">
		<label for="cczip">Zip/Post Code:</label>
		<input type="text" name="ZIP" id="cczip" class="formelement" value="<?php echo ($this->input->post('ZIP')) ? $this->input->post('ZIP') : @$user['billingPostcode']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow cccountry-row">
		<label for="cccountry">Country:</label>
		<?php echo display_countries('COUNTRYCODE', (($this->input->post('COUNTRYCODE')) ? $this->input->post('COUNTRYCODE') : @$user['billingCountry']), 'id="cccountry" class="formelement"'); ?>
		<br class="clear" />
	</div>

<?php elseif ($this->site->config['shopGateway'] == 'rbsworldpay'): ?>

	<input type="hidden" name="cartId" value="{<?php echo $transaction['orderID']; ?>" />
	<input type="hidden" name="desc" value="<?php echo $this->site->config['siteName']; ?> Shopping Cart" />
	<input type="hidden" name="currency" value="<?php echo $currency; ?>" />
	<input type="hidden" name="custom" value="<?php echo $transaction['transactionID']; ?>" />
	<input type="hidden" name="amount" value="<?php echo $amount; ?>" />
	<input type="hidden" name="instId" value="" />

<?php elseif ($this->site->config['shopGateway'] == 'authorize'): ?>

	<input type="hidden" name="x_customer_ip" value="<?php echo $this->input->ip_address(); ?>" />
	<input type="hidden" name="x_invoice_num" value="<?php echo $transaction['orderID']; ?>" />
	<input type="hidden" name="x_description" value="<?php echo $this->site->config['siteName']; ?> Shopping Cart" />
	<input type="hidden" name="x_amount" value="<?php echo $amount; ?>" />
	<input type="hidden" name="x_email" value="<?php echo $user['email']; ?>" />
	<input type="hidden" name="x_ship_to_first_name" value="<?php echo $user['firstName']; ?>" />
	<input type="hidden" name="x_ship_to_last_name" value="<?php echo $user['lastName']; ?>" />
	<input type="hidden" name="x_ship_to_address" value="<?php echo $user['address1']; ?>" />
	<input type="hidden" name="x_ship_to_city" value="<?php echo $user['city']; ?>" />
	<input type="hidden" name="x_ship_to_state" value="<?php echo lookup_state($user['state']); ?>" />
	<input type="hidden" name="x_ship_to_zip" value="<?php echo $user['postcode']; ?>" />
	<input type="hidden" name="x_ship_to_country" value="<?php echo $user['country']; ?>" />

	<div class="formrow ccnumber-row">
		<label for="ccnumber">Card Number:</label>
		<input type="text" name="x_card_num" id="ccnumber" class="formelement" autocomplete="off" />
		<br class="clear" />
	</div>

	<div class="formrow ccexpiry-row">
		<label for="ccexpirymonth">Expiry Date:</label>
		<?php echo @expiry_months_dropdown('expMonth', $this->input->post('expMonth'), 'id="ccexpirymonth" class="formelement small"'); ?>
		<?php echo @expiry_years_dropdown('expYear', $this->input->post('expYear'), 'id="ccexpiryyear" class="formelement small"'); ?>
		<br class="clear" />
	</div>

	<div class="formrow cvv2-row">
		<label for="cvv2">CVV2 Number:</label>
		<input type="text" name="x_card_code" id="cvv2" maxlength="4" size="5" class="formelement small" />
		<br class="clear" />
	</div>

	<div class="formrow ccfirstname-row">
		<label for="ccfirstname">First Name:</label>
		<input type="text" name="x_first_name" id="ccfirstname" class="formelement" value="<?php echo ($this->input->post('x_first_name')) ? $this->input->post('x_first_name') : @$user['firstName']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow cclastname-row">
		<label for="cclastname">Last Name:</label>
		<input type="text" name="x_last_name" id="cclastname" class="formelement" value="<?php echo ($this->input->post('x_last_name')) ? $this->input->post('x_last_name') : @$user['lastName']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow ccstreet-row">
		<label for="cccity">Address:</label>
		<textarea name="x_address" id="address" class="formelement small"><?php echo ($this->input->post('x_address')) ? $this->input->post('x_address') : @$user['billingAddress1']."\n".@$user['billingCity']; ?></textarea>
		<br class="clear" />
	</div>

	<div class="formrow ccstate-row">
		<label for="ccstate">State:</label>
		<?php echo @display_states('x_state', (($this->input->post('x_state')) ? $this->input->post('x_state') : @$user['billingState']), 'id="ccstate" class="formelement"'); ?>
		<br class="clear" />
	</div>

	<div class="formrow cczip-row">
		<label for="cczip">Zip/Post Code:</label>
		<input type="text" name="x_zip" id="cczip" class="formelement" value="<?php echo ($this->input->post('x_zip')) ? $this->input->post('x_zip') : @$user['billingPostcode']; ?>" />
		<br class="clear" />
	</div>

	<div class="formrow cccountry-row">
		<label for="cccountry">Country:</label>
		<?php echo display_countries('x_country', (($this->input->post('x_country')) ? $this->input->post('x_country') : @$user['billingCountry']), 'id="cccountry" class="formelement"'); ?>
		<br class="clear" />
	</div>

<?php elseif ($this->site->config['shopGateway'] == 'sagepay'): ?>

	<input type="hidden" name="transactionID" value="<?php echo $transaction['transactionID']; ?>" />
	<input type="hidden" name="Description" value="<?php echo $this->site->config['siteName']; ?> Shopping Cart" />
	<input type="hidden" name="VendorTxCode" value="<?php echo $transaction['orderID']; ?>" />
	<input type="hidden" name="Amount" value="<?php echo $amount; ?>" />
	<input type="hidden" name="currency" value="GBP" />
	<input type="hidden" name="CustomerName" value="<?php echo $user['firstName']; ?> <?php echo $user['lastName']; ?>" />
	<input type="hidden" name="BillingFirstnames" value="<?php echo $user['firstName']; ?>" />
	<input type="hidden" name="BillingSurname" value="<?php echo $user['lastName']; ?>" />

	<?php if ($user['billingAddress1'] || $user['billingAddress2'] || $user['billingCity'] || $user['billingPostcode']): ?>

		<input type="hidden" name="BillingAddress" value="<?php echo $user['billingAddress1']; ?>, <?php echo $user['billingAddress2']; ?>, <?php echo $user['billingCity']; ?>" />
		<input type="hidden" name="BillingPostCode" value="<?php echo $user['billingPostcode']; ?>" />
		<input type="hidden" name="BillingAddress1" value="<?php echo $user['billingAddress1']; ?>" />
		<input type="hidden" name="BillingCity" value="<?php echo $user['billingCity']; ?>" />
		<input type="hidden" name="BillingState" value="<?php echo $user['billingState']; ?>" />
		<input type="hidden" name="BillingCountry" value="<?php echo $user['billingCountry']; ?>" />

	<?php else: ?>

		<input type="hidden" name="BillingAddress" value="<?php echo $user['address1']; ?>, <?php echo $user['address2']; ?>, <?php echo $user['city']; ?>" />
		<input type="hidden" name="BillingPostCode" value="<?php echo $user['postcode']; ?>" />
		<input type="hidden" name="BillingAddress1" value="<?php echo $user['address1']; ?>" />
		<input type="hidden" name="BillingCity" value="<?php echo $user['city']; ?>" />
		<input type="hidden" name="BillingState" value="<?php echo $user['state']; ?>" />
		<input type="hidden" name="BillingCountry" value="<?php echo $user['country']; ?>" />

	<?php endif; ?>

	<input type="hidden" name="DeliveryFirstnames" value="<?php echo $user['firstName']; ?>" />
	<input type="hidden" name="DeliverySurname" value="<?php echo $user['lastName']; ?>" />
	<input type="hidden" name="DeliveryPostCode" value="<?php echo $user['postcode']; ?>" />
	<input type="hidden" name="DeliveryAddress1" value="<?php echo $user['address1']; ?>" />
	<input type="hidden" name="DeliveryCity" value="<?php echo $user['city']; ?>" />
	<input type="hidden" name="DeliveryState" value="<?php echo $user['state']; ?>" />
	<input type="hidden" name="DeliveryCountry" value="<?php echo $user['country']; ?>" />

	<input type="hidden" name="CustomerEMail" value="<?php echo $this->session->userdata('email'); ?>" />
	<input type="hidden" name="NotificationURL" value="<?php echo site_url('/shop/response'); ?>" />
	<input type="hidden" name="TxType" value="PAYMENT" />

<?php endif; ?>
