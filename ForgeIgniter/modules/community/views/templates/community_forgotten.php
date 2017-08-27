{include:header}

<h1>Forgotten Password</h1>

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

	<p>Enter the email which you used to sign up and we will send out an email with instructions on how to reset your password.</p>
	
	<form method="post" action="{page:uri}" class="default">
	
		<label for="email">Email Address:</label>
		<input type="text" name="email" class="formelement" />
		<br class="clear" /><br />
		
		<input type="submit" value="Reset Password" class="button nolabel" />
		<br class="clear" />			
	
	</form>

{/if}	
		
{include:footer}