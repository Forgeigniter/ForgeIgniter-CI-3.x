{include:header}

<div id="tpl-forum" class="module">

	<h1>Edit Topic</h1>
	
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

		<br />
		<input type="submit" value="Edit Topic" class="button nolabel" />
		<br class="clear" />
		
	</form>

</div>

{include:footer}