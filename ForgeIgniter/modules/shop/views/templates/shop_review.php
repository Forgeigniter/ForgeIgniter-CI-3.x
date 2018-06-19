{include:header}

<h1>Review Product</h1>

{if errors}
	<div class="error">
		{errors}
	</div>
{/if}

<form method="post" action="{site:url}shop/review/{product:id}" class="default" id="reviewsform">

	<label for="fullName">Your Name</label>
	<input type="text" name="fullName" value="{form:name}" id="fullName" class="formelement" />
	<br class="clear" />

	<label for="email">Your Email</label>
	<input type="text" name="email" value="{form:email}" id="email" class="formelement" />
	<br class="clear" />

	<label for="rating">Rating</label>
	<select name="rating" class="formelement">
		<option value="1">1 / 5</option>
		<option value="2">2 / 5</option>
		<option value="3">3 / 5</option>
		<option value="4">4 / 5</option>
		<option value="5">5 / 5</option>
	</select>
	<br class="clear" />

	<label for="reviewform">Review</label>
	<textarea name="review" id="reviewform" class="formelement small">{form:review}</textarea>
	<br class="clear" /><br />

	<input type="submit" value="Post Review" class="button nolabel" />
	<br class="clear" />

</form>

{include:footer}
