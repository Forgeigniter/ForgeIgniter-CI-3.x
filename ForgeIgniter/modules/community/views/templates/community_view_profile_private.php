{include:header}

<div id="tpl-community" class="module">

	<div class="col col1">

		<h1>{page:heading}</h1>

		<p>This profile is private.</p>

	</div>
	<div class="col col2">

		<div class="avatar">
			{user:avatar}
		</div>

		<br />

		<ul class="menu">
			{profile:navigation}
		</ul>

		<br />
		
		<form method="post" action="{site:url}users/search" class="default">

			<label for="searchbox">Search user:</label><br class="clear" />
			<input type="text" name="query" id="searchbox" maxlength="255" value="" class="searchbox" />
			<input type="image" src="{site:url}static/images/btn_search.gif" id="searchbutton" />
			<br class="clear" />

		</form>

	</div>

</div>

{include:footer}
