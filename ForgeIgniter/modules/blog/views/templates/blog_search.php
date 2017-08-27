{include:header}

<div id="tpl-blog" class="module">

	<div class="col col1">

		<p class="back"><small><a href="/blog/">&lt; Back to blog</a></small></p>

		<h1>Search results for "{query}"</h1>

		{if blog:posts}
			{blog:posts}

				<div class="post">
									
					<h2><a href="{post:link}">{post:title}</a></h2>
					<p><small>Posted: <strong>{post:date}</strong> (<a href="{post:link}#comments">{post:comments-count} comments</a>)</small></p>

					<div class="post-body">
						{post:body}
					</div>

				</div>

			{/blog:posts}
		{else}
			<p>No results found.</p>
		{/if}

	</div>

	<div class="col col2">
	
		<form method="post" action="{site:url}blog/search/" class="default">

			<label for="searchbox">Search Posts:</label><br class="clear" />
			<input type="text" name="query" id="searchbox" maxlength="255" value="" class="searchbox" />
			<input type="image" src="{site:url}static/images/btn_search.gif" id="searchbutton" />
			<br class="clear" />

		</form>
		
		<br />

		<h3>Categories</h3>

		<ul class="menu">
			{if blog:categories}
				{blog:categories}
					<li><a href="{category:link}">{category:title}</a> ({category:count})</li>
				{/blog:categories}
			{else}
				<li><small>No categories set yet.</small></li>
			{/if}
		</ul>
		
		<br />
		
		<h3>Recent posts</h3>

		<ul class="menu">
			{if blog:latest}
				{blog:latest}
					<li><a href="{latest:link}">{latest:title}</a></li>
				{/blog:latest}
			{else}
				<li><small>No posts yet.</small></li>
			{/if}
		</ul>
		
		<br />
		
		<h3>Archive</h3>
		
		<ul class="menu">
			{if blog:archive}
				{blog:archive}
					<li><a href="{archive:link}">{archive:title}</a> ({archive:count})</li>
				{/blog:archive}
			{else}
				<li><small>Nothing to archive yet.</small></li>
			{/if}
		</ul>
		
		<br />

		<h3>Subscribe to feed</h3>

		<ul class="menu">
			<li><a href="{site:url}blog/feed/">Blog RSS Feed</a></li>
		</ul>
		
	</div>

</div>

{include:footer}