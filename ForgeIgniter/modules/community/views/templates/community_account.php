{include:header}

<div id="tpl-community" class="module">
		
	<div class="col col1">
	
		<h1>Your Details</h1>
		
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
	
		<form method="post" action="{page:uri}" enctype="multipart/form-data" class="default">
		
			<h2 class="underline">Login</h2>
		
			<label for="email">Email:</label>
			<input type="text" name="email" value="{form:email}" id="email" class="formelement" />
			<br class="clear" />
		
			<label for="password">Password:</label>
			<input type="password" name="password" value="" id="password" class="formelement" />
			<br class="clear" /><br />
		
			<h2 class="underline">Your Name</h2>
		
			<label for="displayName">Display Name:</label>
			<input type="text" name="displayName" value="{form:displayName}" id="displayName" class="formelement" maxlength="15" /><br class="clear" />
			<span class="tip">Use this if you don't want your full name shown.</span></span><br class="clear" /><br />
		
			<label for="firstName">First Name:</label>
			<input type="text" name="firstName" value="{form:firstName}" id="firstName" class="formelement" />
			<br class="clear" />
		
			<label for="lastName">Last Name:</label>
			<input type="text" name="lastName" value="{form:lastName}" id="lastName" class="formelement" />
			<br class="clear" /><br />
		
			<h2 class="underline">About You</h2>	
		
			<a name="changeavatar"></a>
			<label for="image">Avatar:</label>
			<div class="uploadfile">
				<input type="file" name="image" size="16" id="image" />
			</div><br class="clear" />
			<span class="tip">Please use GIF or JPG under 200kb.</span><br class="clear" /><br />
		
			<a name="changebio"></a>
			<label for="bio">Bio:</label>
			<textarea name="bio" id="bio" class="formelement small">{form:bio}</textarea><br class="clear" />
			<span class="tip">A few paragraphs about yourself. This is shown on your profile.</span><br class="clear" /><br />

			<label for="website">Website:</label>
			<input type="text" name="website" value="{form:website}" id="website" class="formelement" />
			<br/>
			
			<label for="signature">Signature:</label>	
			<textarea name="signature" id="signature" class="formelement small">{form:signature}</textarea><br class="clear" />
			<span class="tip">This is placed at the end of your posts in the forum.</span>
			<br class="clear" /><br />
		
			<a name="changework"></a>
			<h2 class="underline">Your Work</h2>
			
			<label for="companyName">Company Name:</label>
			<input type="text" name="companyName" value="{form:companyName}" id="companyName" class="formelement" />
			<br class="clear" />
			
			<label for="companyName">Company Website:</label>
			<input type="text" name="companyWebsite" value="{form:companyWebsite}" id="companyWebsite" class="formelement" />
			<br class="clear" />

			<label for="companyDescription">About Your Company:</label>
			<textarea name="companyDescription" id="companyDescription" class="formelement small">{form:companyDescription}</textarea><br class="clear" />
			<span class="tip">Optionally describe your company. This is shown on your profile.</span><br class="clear" /><br />
		
			<h2 class="underline">Address</h2>
		
			<label for="address1">Address 1:</label>
			<input type="text" name="address1" value="{form:address1}" id="address1" class="formelement" />
			<br class="clear" />
		
			<label for="address2">Address 2:</label>
			<input type="text" name="address2" value="{form:address2}" id="address2" class="formelement" />
			<br class="clear" />
		
			<label for="address3">Address 3:</label>
			<input type="text" name="address3" value="{form:address3}" id="address3" class="formelement" />
			<br class="clear" />
		
			<label for="city">City / State:</label>
			<input type="text" name="city" value="{form:city}" id="city" class="formelement" />
			<br class="clear" />
		
			<label for="postcode">ZIP/Post code:</label>
			<input type="text" name="postcode" value="{form:postcode}" id="postcode" class="formelement" />
			<br class="clear" />
		
			<label for="country">Country:</label>
			{select:country}
			<br class="clear" />
		
			<label for="phone">Phone:</label>
			<input type="text" name="phone" value="{form:phone}" id="phone" class="formelement" />
			<br class="clear" /><br />
		
			<h2 class="underline">Privacy</h2>
			
			<label for="privacy">Profile &amp; Feed:</label>
			{select:privacy}
			<br class="clear" />
		
			<label for="notifications">Notifications:</label>
			{select:notifications}
			<br class="clear" />
			<span class="tip">Notifications are emails sent out regarding new messages.</span>
			<br class="clear" /><br />
				
			<input type="submit" value="Update Details" class="button nolabel" />
			<br class="clear" />
			
		</form>
		
	</div>
	<div class="col col2">
		
		{user:avatar}

		<p><a href="#changeavatar">Change Avatar</a></p>
		
		{if user:avatar}
			
			<p><a href="{site:url}users/delete_avatar" onclick="return confirm('Are you sure you want to delete this avatar?');">Delete Avatar</a></p>
		
		{/if}
	
	</div>

</div>
		
{include:footer}