{include:header}

<div id="tpl-forum" class="module">
	<h1 class="headingleft">
		{forum:title}
		<br />
		<small>{breadcrumb}</small>
	</h1>

	<div class="headingright">
	{if logged-in}
		<a href="{site:url}forums/addtopic/{forum:id}" class="button">Post Topic</a>
	{/if}
	</div>
	
	<div class="search">
	
		<form method="post" action="{site:url}forums/search/{forum:id}">
	
			<label for="searchbox">Search:</label>
			<input type="text" name="query" id="searchbox" class="searchbox" maxlength="255" value="" />
			<input type="image" src="{site:url}static/images/btn_search.gif" id="searchbutton" />
	
		</form>
	
	</div>
	
	<br class="clear" />
	
	{if topics}
	
	{pagination}
	
	<table class="default">
		<tr>
			<th>Forum Name</th>
			<th class="narrow">Replies</th>
			<th class="narrow">Views</th>		
			<th>Last Post</th>
		</tr>
		
	{topics}
	
		<tr>
			<td>
				<span class="{topic:class}">
					<a href="{topic:link}">{topic:title}</a>
					<br />
					<small>By {topic:user}</small>
				</span>
			</td>
			<td>{topic:replies}</td>
			<td>{topic:views}</td>
			<td class="medium">
				{topic:latest-post}
			</td>		
		</tr>
		
	{/topics}
	
	</table>
	
	{pagination}
	
	{else}
	
	<p>There are no topics posted yet.</p>
	
	{/if}
	
</div>

{include:footer}