{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Have you shopped here before?</h1>
		
		<p>We can find out if you are have shopped here before if you just enter your email below.</p>
		
		<br />
		
		<form action="{page:uri}" method="post" class="default">
						
			<label for="email">Email address:</label>
			<input type="text" id="email" name="email" value="" class="formelement" />
			<br class="clear" /><br />
		
			<input type="submit" id="login" name="login" value="Next Step &gt;" class="nolabel button" />
			
		</form>
		
		<br class="clear" /><br />

	</div>
	<div class="col col2">
	
		<h3>Categories</h3>
	
		<ul class="menu">
			{shop:categories}
		</ul>
		
	</div>

</div>
	
{include:footer}