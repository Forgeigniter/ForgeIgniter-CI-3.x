{include:header}

<div id="tpl-forum" class="module">

	<h1>
		Post Topic
		<br />
		<small>{breadcrumb}</small>			
	</h1>
	
	{if errors}
		<div class="error">
			{errors}
		</div>
	{/if}
	
	<form method="post" action="{page:uri}" class="default">
		
		<label for="title">Title:</label>
		<input type="text" name="title" value="{form:title}" id="title" class="formelement" maxlength="50" />
		<br class="clear" />
		
		{if moderator}
			
			<label for="sticky">Post as:</label>
			<select name="sticky" class="formelement">
				<option value="0">Normal Topic</option>
				<option value="1">Sticky</option>
			</select>			
			<br class="clear" />
			
		{/if}
		
		<label for="body">Post Body:</label>
		<textarea name="body" id="body" class="formelement">{form:body}</textarea>
		<br class="clear" /><br />
		
		<input type="submit" value="Post Topic" class="button nolabel" />	
		<br class="clear" />
		
	</form>
	
</div>

{include:footer}