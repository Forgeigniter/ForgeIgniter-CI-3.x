{include:header}

{if errors}
	<div class="error clear">
		{errors}
	</div>
{/if}

<h1>Your Details</h1>

<p>Please fill in the form below with your details.</p>

<form method="post" action="{page:uri}" class="default">

	<h2>Email and password</h2>

	<label for="email">Email:</label>
	<input type="text" name="email" value="{form:email}" id="email" class="formelement" />
	<br class="clear" />

	<label for="password">Password:</label>
	<input type="password" name="password" value="" id="password" class="formelement" />
	<br class="clear" />

	<label for="confirmPassword">Confirm Password:</label>
	<input type="password" name="confirmPassword" value="" id="confirmPassword" class="formelement" />
	<br class="clear" /><br />

	<h2>About you</h2>

	<label for="firstName">First Name:</label>
	<input type="text" name="firstName" value="{form:firstName}" id="firstName" class="formelement" />
	<br class="clear" />

	<label for="lastName">Last Name:</label>
	<input type="text" name="lastName" value="{form:lastName}" id="lastName" class="formelement" />
	<br class="clear" />

	<label for="postcode">ZIP/Post code:</label>
	<input type="text" name="postcode" value="{form:postcode}" id="postcode" class="formelement" />
	<br class="clear" />

	<label for="country">Country:</label>
	{select:country}
	<br class="clear" /><br />

	<input type="submit" id="submit" value="Create Account" class="button nolabel" />

</form>

<hr />

<div class="box">
	<h3>Terms of Use</h3>

	<div class="scroll">

		<p>Please remember that we are not responsible for any content that is posted. We do not vouch for or warrant the accuracy, completeness or usefulness of any content, and are not responsible for the contents of any message, post, article, event or other data which may be posted by a user of this site.</p>

		<p>The posted content express the views of the author of the content, not necessarily the views of this site. Any user who feels that particular content is objectionable is encouraged to contact us immediately by email. We have the ability to remove objectionable content and we will make every effort to do so, within a reasonable time frame, if we determine that removal is necessary.</p>

		<p>You agree, through your use of this service, that you will not use this site to post any material which is knowingly false and/or defamatory, inaccurate, abusive, vulgar, hateful, harassing, obscene, profane, sexually oriented, threatening, invasive of a person's privacy, or otherwise violative of any law.</p>

		<p>All posted content becomes property of this site.</p>

		<p>You agree not to post any copyrighted material unless the copyright is owned by you or by this site.</p>

		<p>You must be over the age of 13 years old to use this site.</p>

	</div>

	<br />

</div>

{include:footer}
