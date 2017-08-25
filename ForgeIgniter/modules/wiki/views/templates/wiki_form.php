{include:header}

<div class="module">

	<div id="tpl-wiki">

		<h1>{page:heading} <small>(<a href="{wikipage:link}">back to page</a>)</small></h1>
		
		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}
		
		<form method="post" action="{page:uri}" class="default"  enctype="multipart/form-data">	
		
			<label for="pageName">Title:</label>
			<input type="text" name="pageName" value="{form:title}" id="pageName" class="formelement" maxlength="50" />
			<br class="clear" />
		
			<label for="category">Category:</label>
			{select:categories}
			<br class="clear" />
		
			<label for="body">Content:</label>
			<textarea name="body" id="body" class="formelement big">{form:body}</textarea>
			<br class="clear" />

			<label for="notes">Notes:<br /><small>(what changed?)</small></label>
			<input type="text" name="notes" value="{form:notes}" id="notes" class="formelement" maxlength="250" />
			<br class="clear" />			
		
			<input type="submit" value="Save Changes" class="button nolabel" />
			<br class="clear" />
			
		</form>
		
		<br />
		
		{if versions}
		
			<h2>Versions</h2>
		
			<ul>
				{versions}
					<li>{version}</li>
				{/versions}
			</ul>
		
		{/if}

	</div>

</div>

{include:footer}