{include:header}

<div id="tpl-forum" class="module">

	<h1>Delete Post</h1>
	
	{if errors}
		<div class="error">
			{errors}
		</div>
	{/if}
	
	<form method="post" action="{page:uri}" class="default">
	
		<p><strong>Are you sure you want to delete this post?</strong></p>

		<blockquote>
			<p>{post:body}</p>
		</blockquote>
	
		<input type="submit" name="delete" value="Yes, Delete!" class="button" />	
		
	</form>

</div>

{include:footer}