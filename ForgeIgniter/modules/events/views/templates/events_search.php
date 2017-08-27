{include:header}

<div id="tpl-events" class="module">

	<div class="col col1">
	
		<h4 class="month">Search results for "{query}"</h4>

		{if events:events}
			{events:events}

				<div class="event">
									
					<h2 class="title"><a href="{event:link}">{event:title}</a></h2>
					<h4 class="location">{event:location}</h4>
					<h3 class="date">Posted: <strong>{event:date}</strong></small></h3>
					<p>{event:body}</p>

				</div>
				
			{/events:events}
		{else}
			<p>No results found.</p>
		{/if}


		{pagination}

	</div>

	<div class="col col2">
	
		<div class="calendar">
			{events:calendar}			
		</div>

		<br />

		<form method="post" action="{site:url}events/search/" class="default">

			<label for="searchbox">Search Events:</label><br class="clear" />
			<input type="text" name="query" id="searchbox" maxlength="255" value="" class="searchbox" />
			<input type="image" src="{site:url}static/images/btn_search.gif" id="searchbutton" />
			<br class="clear" />

		</form>

		<br />	

		<h3>Archive</h3>

		<ul class="menu">
			{if events:archive}
				{events:archive}
					<li><a href="{archive:link}">{archive:title}</a> ({archive:count})</li>
				{/events:archive}
			{else}
				<li><small>Nothing to archive yet.</small></li>
			{/if}
		</ul>

		<br />

		<h3>Upcoming events</h3>

		<ul class="menu">
			{if events:latest}
				{events:latest}
					<li><a href="{latest:link}">{latest:title}</a></li>
				{/events:latest}
			{else}
				<li><small>No events yet.</small></li>
			{/if}
		</ul>
		
		<br />

		<h3>Subscribe to feed</h3>

		<ul class="menu">
			<li><a href="{site:url}events/feed/">Events RSS Feed</a></li>
		</ul>
		
	</div>

</div>

{include:footer}