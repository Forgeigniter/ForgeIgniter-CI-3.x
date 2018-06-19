{include:header}

<h1>Reset Password</h1>

{if errors}
	<div class="error">
		{errors}
	</div>
{/if}
{if message}
	<div class="message">
		{message}
	</div>

{else}

	<p>Enter your new password below.</p>

	<form method="post" action="{page:uri}" class="default">

		<label for="password">New Password:</label>
		<input type="password" name="password" class="formelement" />
		<br class="clear" />

		<label for="confirmPassword">Confirm Password:</label>
		<input type="password" name="confirmPassword" class="formelement" />
		<br class="clear" /><br />

		<input type="submit" value="Reset Password" class="button nolabel" />
		<br class="clear" />			

	</form>

{/if}

{include:footer}
