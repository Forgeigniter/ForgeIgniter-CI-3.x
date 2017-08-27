{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Order ID #: <strong>{order:id}</strong> <small>(<a href="{site:url}shop/orders">Back to orders</a>)</small></h1>

		<table class="default">
			<tr>
				<th width="50%">Delivery Address</th>
				<th width="50%">Shipping Status</th>
			</tr>
			<tr>
				<td valign="top">		
					<p>
						{order:first-name} {order:last-name}<br />
						{order:address1}<br />
						{order:address2}<br />
						{order:address3}<br />
						{order:city}<br />
						{order:country}<br />
						{order:postcode}<br />
						<small>Phone:</small> {order:phone}<br />
						<small>Email:</small> {order:email}
					</p>
				</td>
				<td valign="top">

					{if order:status = U}
						<p><strong>Unprocessed</strong> - we have just received the order.</p>
					{/if}
					{if order:status = L}
						<p><strong>Allocated</strong> - we are just processing the order and getting it ready for shipping.</p>
					{/if}						
					{if order:status = A}
						<p><span style="color:red;"><strong>Awaiting goods</strong></span> - we are just waiting on stock.</p>
					{/if}					
					{if order:status = O}
						<p><span style="color:red;"><strong>Other complications</strong></span> - please contact us.</p>
					{/if}
					{if order:status = D}
						<p><span style="color:green;"><strong>Shipped</strong></span> - your order has been shipped.</p>
					{/if}
				
					{if order:notes}
						<p>{order:notes}</p>
					{else}
						<p><small>There are no shipping notes.</small></p>
					{/if}
				</td>					
			</tr>

		</table>
	
		<br />
	
		<h3>Products Ordered</h3>
		
		<table class="default">
			<tr>
				<th>Product</th>
				<th>Quantity</th>
				<th width="80">Price ({site:currency})</th>
			</tr>
			{if items}
				{items}		
				<tr>
					<td><a href="{item:link}">{item:title}</a> <small>{item:details}</small></td>
					<td>{item:quantity}</td>
					<td>{item:amount}</td>
				</tr>				
				{/items}
			{/if}
			<tr>
				<td colspan="3" ><hr /></td>
			</tr>
			{if order:discounts}
				<tr>
					<td colspan="2">Discounts applied:</td>
					<td>({order:discounts})</td>
				</tr>
			{/if}
			<tr>
				<td colspan="2">Sub total:</td>
				<td>{order:subtotal}</td>
			</tr>
			<tr>
				<td colspan="2">Shipping:</td>
				<td>{order:postage}</td>
			</tr>
			{if order:tax}
				<tr>
					<td colspan="2">Tax:</td>
					<td>{order:tax}</td>
				</tr>
			{/if}
			<tr>
				<td colspan="2"><strong>Total amount:</strong></td>
				<td><strong>{order:total}</strong></td>
			</tr>
			<tr>
				<td colspan="3" ><hr /></td>
			</tr>								
		</table>

		<br />

	</div>
	<div class="col col2">
	
		<ul class="menu">
			<li><a href="{site:url}shop">Back to Shop</a></li>
			<li><a href="{site:url}shop/account">My Account</a></li>				
			<li><a href="{site:url}shop/subscriptions">My Subscriptions</a></li>
			<li><a href="{site:url}shop/orders">My Orders</a></li>						
		</ul>
		
	</div>

</div>
	
{include:footer}