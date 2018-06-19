{include:header}

<div id="tpl-shop" class="module">

	<div class="col col1">

		<h1>Login</h1>

		<p>You have shopped here before with the email <strong>{user:email}</strong>. Please enter your password below.</p>

		{if errors}
			<div class="error">
				{errors}
			</div>
		{/if}

		<form action="{page:uri}" method="post" class="default">

			<input type="hidden" name="email" value="{user:email}" />

			<label for="password">Password:</label>
			<input type="password" id="password" name="password" value="" class="formelement" />

			<input type="submit" id="login" name="login" value="Login" class="button" />
			<br class="clear" />
			
		</form>

		<br />

		<h3><a href="{site:url}shop/forgotten">Forgotten your password?</a></h3>

		<p>That's ok, we can <a href="{site:url}shop/forgotten">reset it for you</a>.</p>

		<br />

		<h3><a href="{site:url}shop/create_account/checkout">Want to create a new account?</a></h3>

		<p>Alternatively you can <a href="{site:url}shop/create_account/checkout">create a new account</a> if you want to.</p>

		<br />

	</div>
	<div class="col col2">

		<h3>Categories</h3>

		<ul class="menu">
			{shop:categories}
		</ul>

	</div>

</div>

{include:footer}
