{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Your Orders</h1>

		{if orders}
		
			{pagination}
			
			<table class="default">
				<tr>
					<th>Order ID</th>
					<th>Date</th>
					<th>Amount ({site:currency})</th>
					<th class="narrow">&nbsp;</th>
				</tr>
				{orders}
					<tr>
						<td>#{order:id}</td>
						<td>{order:date}</td>
						<td>{order:amount}</td>
						<td><a href="{order:link}">View Order</a></td>
					</tr>
				{/orders}
			</table>

			{pagination}

		{else}

			<p>You have no orders yet.</p>

		{/if}

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