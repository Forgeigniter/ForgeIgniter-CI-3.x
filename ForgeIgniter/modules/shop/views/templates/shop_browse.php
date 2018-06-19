{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>{page:heading}</h1>

		{if category:description}
			{category:description}
		{/if}

		{if shop:products}

		{pagination}

		<table class="default">
			<tr>
				{shop:products}
					{product:rowpad}
					<td align="center" valign="top" width="{product:cell-width}%">
						<a href="{product:link}">
						<img src="{product:thumb-path}" alt="Product image" class="productpic" />
						<p><strong>{product:title}</strong><br />{product:price}</p></a>
					</td>
				{/shop:products}
				{rowpad}
			</tr>
		</table>

		{pagination}

		{else}

			<p>No products found.</p>

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
