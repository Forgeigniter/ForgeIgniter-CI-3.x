{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>The Shop</h1>

		<h3>Featured Products</h3>

		{if shop:featured}

		<table class="default">
			<tr>
				{shop:featured}
					{product:rowpad}
					<td align="center" valign="top" width="{product:cell-width}%">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					</td>
				{/shop:featured}
				{rowpad:featured}
			</tr>
		</table>

		{else}

			<p><small>There are currently no featured products.</small></p>

		{/if}


		<h3>Popular Products</h3>

		{if shop:popular}

		<table class="default">
			<tr>
				{shop:popular}
					{product:rowpad}
					<td align="center" valign="top" width="{product:cell-width}%">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					</td>
				{/shop:popular}
				{rowpad:popular}
			</tr>
		</table>

		{else}

			<p><small>There are currently no products here.</small></p>

		{/if}

		<h3>Latest Products</h3>

		{if shop:latest}

		<table class="default">
			<tr>
				{shop:latest}
					{product:rowpad}
					<td align="center" valign="top" width="{product:cell-width}%">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					</td>
				{/shop:latest}
				{rowpad:latest}
			</tr>
		</table>

		{else}

			<p><small>There are currently no products here.</small></p>

		{/if}

		<h3>Most Viewed</h3>

		{if shop:mostviewed}

		<table class="default">
			<tr>
				{shop:mostviewed}
					{product:rowpad}
					<td align="center" valign="top" width="{product:cell-width}%">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					</td>
				{/shop:mostviewed}
				{rowpad:mostviewed}
			</tr>
		</table>

		{else}

			<p><small>There are currently no products here.</small></p>

		{/if}

	</div>
	<div class="col col2">

		<h3>Categories</h3>

		<ul class="menu">
			{shop:categories}
		</ul>

	</div>

</div>

{include:footer}
