{include:header}

<div id="tpl-forum" class="module">

	{if categories}
	
	<table class="default forum">
	
		{categories}
		
			<tr class="header">
				<th colspan="4">
					{category:title}
				</th>
			</tr>
			<tr>
				<th>Forum Name</th>
				<th class="narrow">Topics</th>
				<th class="narrow">Replies</th>
				<th class="medium">Last Post</th>
			</tr>
			
		{category:forums}
	
			<tr>
				<td>
					<strong><a href="{forum:link}">{forum:title}</a></strong><br />
					<small>{forum:description}</small>
				</td>
				<td>{forum:topics}</td>
				<td>{forum:replies}</td>
				<td class="medium">{forum:latest-post}</td>
			</tr>

		{/category:forums}
		
		{/categories}
	
	</table>
	
	{else}
	
	<table class="default">
	
		<tr>
			<th>Forum Name</th>
			<th class="narrow">Topics</th>
			<th class="narrow">Replies</th>
			<th class="medium">Last Post</th>
		</tr>
	
		{forums}
	
			<tr>
				<td>
					<strong><a href="{forum:link}">{forum:title}</a></strong><br />
					<small>{forum:description}</small>
				</td>
				<td>{forum:topics}</td>
				<td>{forum:replies}</td>
				<td class="medium">{forum:latest-post}</td>
			</tr>

		{/forums}
		
	{/if}
	
	</table>

</div>

{include:footer}