{include:header}

<h1>Recommend Product</h1>

{if errors}
	<div class="error">
		{errors}
	</div>
{/if}		

<form method="post" action="{site:url}shop/recommend/{product:id}" class="default">
	<label>Your Name:</label>
	<input type="text" name="fullName" value="{form:name}" class="formelement" />
	<br class="clear" />

	<label>Your Email:</label>
	<input type="text" name="email" value="{form:email}" class="formelement" />
	<br class="clear" />

	<label>Their Name:</label>
	<input type="text" name="toName" value="{form:to-name}" class="formelement" />
	<br class="clear" />

	<label>Their Email:</label>
	<input type="text" name="toEmail" value="{form:to-email}" class="formelement" />
	<br class="clear" />

	<label>Message: <small>(optional)</small></label>
	<textarea name="message" class="formelement small">{form:message}</textarea>
	<br class="clear" /><br />

	<input type="submit" value="Send Message" class="button nolabel" />
	<br class="clear" />
		
</form>

{include:footer}