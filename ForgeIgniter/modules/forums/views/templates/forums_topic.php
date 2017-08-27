{include:header}

<div id="tpl-forum" class="module">

	<h1 class="headingleft">
		{topic:title}
		<br />
		<small>{breadcrumb}</small>
	</h1>	

	<div class="headingright">
		{if topic:subscribed}
			<a href="{site:url}forums/subscribe/{topic:id}" class="button">Unsubscribe</a>
		{else}
			<a href="{site:url}forums/subscribe/{topic:id}" class="button">Subscribe</a>
		{/if}
		{if topic:locked}
			<a href="#" class="button" style="opacity: 0.5;">Locked</a>
		{else}
			<a href="{site:url}forums/addreply/{topic:id}" class="button">Post Reply</a>
		{/if}
	</div>

	<br class="clear" />
	
	{pagination}
	
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
	
	{pagination}
	
	<br class="clear" />
	
	<div class="headingright">
		{if topic:subscribed}
			<a href="{site:url}forums/subscribe/{topic:id}" class="button">Unsubscribe</a>
		{else}
			<a href="{site:url}forums/subscribe/{topic:id}" class="button">Subscribe</a>
		{/if}
		{if topic:locked}
			<a href="#" class="button" style="opacity: 0.5;">Locked</a>
		{else}
			<a href="{site:url}forums/addreply/{topic:id}" class="button">Post Reply</a>
		{/if}
	</div>
	
	<br class="clear" />

	{if moderator}
		<form method="post" action="{page:uri}" class="default">

			<label for="category">Move Topic</label>
			{select:forums}
			<br class="clear" />
		
			<input type="submit" name="moveTopic" value="Move Topic" class="button nolabel" />
			<input type="submit" name="lockTopic" value="Lock" class="button" />
			<input type="submit" name="unlockTopic" value="Unlock" class="button" />				
			<br class="clear" />
				
		</form>
	{/if}
			
</div>

{include:footer}