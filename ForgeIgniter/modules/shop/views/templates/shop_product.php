{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

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
		
		<form method="post" action="{site:url}shop/cart/add" class="default">
		
			<div class="description">
		
				<input type="hidden" name="productID" value="{product:id}" />
				
				<h1>{product:title}</h1>

				{if product:subtitle}
				
					<h2>{product:subtitle}</h2>
					
				{/if}
				
				{product:body}

				<div id="reviews">
	
					<h3>Reviews</h3>

					{if product:reviews}
						
						{product:reviews}
						<div class="review {review:class}" id="review{review:id}">
	
							<div class="col1">
								<img src="{review:gravatar}" width="50" />
							</div>
			
							<div class="col2">
								<img src="{site:url}static/images/icons/rating/rating{review:rating}.png" alt="{review:rating} Rating" class="rating" />
								
								<p>By <strong>{review:author}</strong> <small>on {review:date}</small></p>
											
								<p>{review:body}</p>
							</div>
	
						</div>
						<div class="clear"></div>
						{/product:reviews}

					{else}

						<p><small>There are currently no reviews</small></p>

					{/if}						

				</div>

				<p>
					<a href="{site:url}shop/recommend/{product:id}" class="loader">Recommend this product</a><br />
					<a href="{site:url}shop/review/{product:id}" class="loader">Write a review</a>						
				</p>					
						
			</div>
						
			<div class="purchase">
			
				{if product:image-path}
				
					<p><a href="{product:image-path}" title="{product:title}" class="lightbox"><img src="{product:image-path}" alt="{product:title}" class="productpic" width="178" /></a></p>
		
				{else}
				
					<p><img src="{site:url}static/images/nopicture.jpg" alt="Product image" class="productpic" /></p>

				{/if}
				
				<p>{product:status}</p>
				
				<p><strong>{product:price}</strong></p>
		
				{product:variations}				
		
				<br class="clear" />
				
				{if product:stock}
					<input type="submit" value="Add to Cart" class="button" />
				{/if}
			</div>
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