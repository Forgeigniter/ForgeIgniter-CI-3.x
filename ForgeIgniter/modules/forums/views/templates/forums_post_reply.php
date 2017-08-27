{include:header}

<div id="tpl-forum" class="module">

	<h1>
		Post Reply
		<br />
		<small>{breadcrumb}</small>
	</h1>
	
	{if errors}
		<div class="error">
			{errors}
		</div>
	{/if}
	
	<form method="post" action="{page:uri}" class="default">

		<label for="body">Post Body:</label>
		<textarea name="body" id="body" class="formelement big">{form:body}</textarea>
		<br class="clear" /><br />
	
		<input type="submit" value="Post Reply" class="button nolabel" />
		<br class="clear" />
		
	</form>

	<br />

	<h3>Last 10 Posts</h3>

	<table class="default">
		
	{posts}
		
		<tr>
			<th>{user:name}</th>
			<th><small>Posted:</small> {post:date}</th>
			<th class="medium">
				<div style="text-align: right;">
					{post:links}
				</div>				
			</th>
		</tr>
		<tr>
			<td class="medium" valign="top">
	
				{user:avatar}

				<br />

				<span class="group"><strong>{user:group}</strong></span><br />
				<span class="posts">Posts: <strong>{user:posts}</strong></span><br />
				<span class="kudos">Kudos: <strong>{user:kudos}</strong></span>		
					
			</td>
			<td colspan="2" valign="top">
				<a name="post{post:id}"></a>
				<div class="post">
					{post:body}
					{user:signature}
				</div>		
			</td>
		</tr>
		
	{/posts}
	
	</table>
	
</div>

{include:footer}