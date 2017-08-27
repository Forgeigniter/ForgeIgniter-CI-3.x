{include:header}

<div id="tpl-forum" class="module">

	<h1>Edit Post</h1>
	
	{if errors}
		<div class="error">
			{errors}
		</div>
	{/if}
	
	<form method="post" action="{page:uri}" class="default">
	
		<h2>{topic:title}</h2>

		<label for="body">Post Body:</label>
		<textarea name="body" id="body" class="formelement">{form:body}</textarea>
		<br class="clear" /><br />
	
		<input type="submit" value="Edit Post" class="button nolabel" />
		<br class="clear" />			
		
	</form>
	
</div>

{include:footer}