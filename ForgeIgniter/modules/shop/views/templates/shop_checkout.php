{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Checkout</h1>
		
		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}

		<p>Confirm your order and your dispatch address below is correct, then click on 'Proceed to Payment Page' to make payment. If you want to cancel your order click on the 'Cancel Order' button.</p>			
		
		<p>Your shopping cart contains:</p>
	
		<table class="default">
			<tr>
				<th>Product</th>
				<th>Quantity</th>
				<th width="80">Price ({site:currency})</th>
			</tr>
			{cart:items}				
			<tr>
				<td colspan="3" ><hr /></td>
			</tr>
			{if cart:discounts}
				<tr>
					<td colspan="2">Discounts applied:</td>
					<td>({cart:discounts})</td>
				</tr>
			{/if}											
			<tr>
				<td colspan="2">Sub total:</td>
				<td>{cart:subtotal}</td>
			</tr>
			<tr>
				<td colspan="2">Shipping:</td>
				<td>{cart:postage}</td>
			</tr>
			{if cart:tax}
				<tr>
					<td colspan="2">Tax:</td>
					<td>{cart:tax}</td>
				</tr>
			{/if}
			<tr>
				<td colspan="2"><strong>Total amount:</strong></td>
				<td><strong>{cart:total}</strong></td>
			</tr>
			<tr>
				<td colspan="3" ><hr /></td>
			</tr>
		</table>
		
		<div style="float: right;">
			<p><a href="{site:url}shop/cart" class="button">Update Order</a></p>
		</div>
		
		<br class="clear" />
		
		<table class="default">
			<tr>
				<td width="50%" valign="top">
					<h2>Delivery Address</h2>
				
					<p>
						<strong>Full name:</strong> {user:name}
						<br />
						
						{if user:address1}
							<strong>Address 1:</strong> {user:address1}
							<br />
						{/if}
					
						{if user:address2}
							<strong>Address 2:</strong> {user:address2}
							<br />
						{/if}
						
						{if user:address3}
							<strong>Address 3:</strong> {user:address3}
							<br />
						{/if}
						
						{if user:city}
							<strong>City:</strong> {user:city}
							<br />
						{/if}
						
						{if user:state}
							<strong>State:</strong> {user:state}
							<br />
						{/if}
						
						{if user:postcode}				
							<strong>Post/ZIP code:</strong> {user:postcode}
							<br />
						{/if}
						
						{if user:country}
							<strong>Country:</strong> {user:country}
						{/if}
					</p>
				</td>
				<td width="50%" valign="top">
					<h2>Billing Address</h2>
				
					<p>
						<strong>Full name:</strong> {user:name}
						<br />
						
						{if user:billing-address1}
							<strong>Address 1:</strong> {user:billing-address1}
							<br />
						{/if}
					
						{if user:billing-address2}
							<strong>Address 2:</strong> {user:billing-address2}
							<br />
						{/if}
						
						{if user:billing-address3}
							<strong>Address 3:</strong> {user:billing-address3}
							<br />
						{/if}
						
						{if user:billing-city}
							<strong>City:</strong> {user:billing-city}
							<br />
						{/if}
						
						{if user:billing-state}
							<strong>State:</strong> {user:billing-state}
							<br />
						{/if}
						
						{if user:billing-postcode}				
							<strong>Post/ZIP code:</strong> {user:billing-postcode}
							<br />
						{/if}
						
						{if user:billing-country}
							<strong>Country:</strong> {user:billing-country}
						{/if}
					</p>
				</td>
			</tr>
		</table>

		<div style="float: right;">
			<p><a href="{site:url}shop/account/checkout" class="button">Update Address</a></p>
		</div>
		<br class="clear" />

		<br />
		
		<form action="{shop:gateway}" method="post" class="default">

			{shop:checkout}

			<div style="float:right">
				<a href="{site:url}shop/cancel" class="button grey">Cancel Order</a>
				<input type="submit" value="Continue to Payment Page &gt;" style="font-weight: bold;" class="button" />
			</div>
			<br class="clear" />

		</form>
	
		<!-- cards --><p><a href="#" onclick="javascript:window.open('https://www.paypal.com/uk/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');"><img src="https://www.paypal.com/en_GB/GB/i/logo/PayPal_mark_37x23.gif" alt="Payments by Paypal"></a> <img src="{site:url}static/images/cards_visa.gif" alt="Visa Accepted" /> <img src="{site:url}static/images/cards_electron.gif" alt="Visa Electron Accepted" /> <img src="{site:url}static/images/cards_mastercard.gif" alt="Mastercard Accepted" /> <img src="{site:url}static/images/cards_visadelta.gif" alt="Visa Delta Accepted" /> <img src="{site:url}static/images/cards_switch.gif" alt="Switch Accepted" /> <img src="{site:url}static/images/cards_maestro.gif" alt="Maestro Accepted" /> <img src="{site:url}static/images/cards_solo.gif" alt="Solo Accepted" /></p>
				
		<p>Your order will be saved on file and you will receive an email confirmation containing your order details and reference number once the payment process is completed.</p>
		
		<p>For our Postage &amp; Packing rates, Returns Procedure and other useful information please see our Terms and Conditions</a>.</p>
		

	</div>
	<div class="col col2">
	
		<h3>Categories</h3>	
	
		<ul class="menu">
			{shop:categories}
		</ul>
		
	</div>

</div>
	
{include:footer}