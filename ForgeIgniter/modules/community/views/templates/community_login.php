{include:header}

{if errors}
	<div class="error clear">
		{errors}
	</div>
{/if}

<div class="box">

	<h1>Login</h1>

	<form action="{page:uri}" method="post" class="default">

		<label for="email">Email:</label>
		<input type="text" id="email" name="email" class="formelement" />
		<br class="clear" />

		<label for="password">Password:</label>
		<input type="password" id="password" name="password" class="formelement"/>
		<br class="clear" />

		<input type="checkbox" name="remember" id="remember" value="1" class="nolabel" checked="checked" />
		<label for="remember">Remember me?</label>
		<br class="clear" />

		<a href="{site:url}users/forgotten" class="nolabel">Forgotten your password?</a>
		<br class="clear" /><br />

		<input type="submit" id="login" name="login" value="Login" class="nolabel button" />
		<br class="clear" />

	</form>

</div>

<hr />

<h3>Have you got an account with us?</h3>

<p>Sign up by clicking the button below to create an account with us.</p>

<p class="buttons"><a href="{site:url}users/create_account" class="button">Sign Up</a></p>

<br class="clear" />


{include:footer}
