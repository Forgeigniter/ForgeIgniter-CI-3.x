{include:header}

<div id="tpl-blog" class="module">

	<div class="col col1">

		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}
		{if message}
			<div class="message">
				{message}
			</div>
		{/if}			

		<h1 class="post-title"><a href="{post:link}">{post:title}</a></h1>

		<p class="back">
			<small>
				<a href="{site:url}blog/">&lt; Back to blog</a>
				{if post:allow-comments} | <a href="#addcomment" id="addcommentlink">Add a comment</a>{/if}
			</small>
		</p>

		<p class="posted"><small>Posted: <strong>{post:date}</strong></small></p>

		<div class="clear"></div>

		<div class="post-body">
			{post:body}
		</div>

		<p class="posted"><small>
			Posted by: <strong>{post:author}</strong><br />

			{if post:categories}
				Categories: <strong>{post:categories}<a href="{category:link}">{category}</a>  {/post:categories}</strong><br />
			{/if}

			{if post:tags}
				Tags: <strong>{post:tags}<a href="{tag:link}">{tag}</a>  {/post:tags}</strong><br />
			{/if}			
		</small></p>

		{if post:comments}
			<div id="comments">

				<h4>Comments</h4>

				{post:comments}
				<div class="comment {comment:class}" id="comment{comment:id}">

					<div class="col1">
						<img src="{comment:gravatar}" width="50" />
					</div>
	
					<div class="col2">
						<p>By <strong>{comment:author}</strong> <small>on {comment:date}</small></p>

						<p>{comment:body}</p>
					</div>

				</div>
				{/post:comments}

			</div>
		{/if}

		<div class="clear"></div>

		{if post:allow-comments}
			<a name="addcomment"></a>
			<div id="addcomment">

				<h2>Add a comment</h2>		

				<form method="post" action="{page:uri}" class="default" id="commentsform">
	
					<label for="fullName">Your Name</label>
					<input type="text" name="fullName" value="{form:name}" id="fullName" class="formelement" />
					<br class="clear" />
		
					<label for="email">Your Email</label>
					<input type="text" name="email" value="{form:email}" id="email" class="formelement" />
					<br class="clear" />

					<label for="fullName">Your Website</label>
					<input type="text" name="website" value="{form:website}" id="website" class="formelement" />
					<br class="clear" />
		
					<label for="commentform">Comment</label>
					<textarea name="comment" id="commentform" class="formelement small">{form:comment}</textarea>
					<br class="clear" /><br />

					<input type="submit" value="Post Comment" class="button nolabel" />
		
				</form>

				<p class="back clear"><a href="#" id="totop">Back to top</a></p>
				
			</div>
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