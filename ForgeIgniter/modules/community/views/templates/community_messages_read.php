{include:header}

<h1>Read Message</h1>

<h3>Sent From</h3>

<div class="avatar">

	{user:avatar}

	<p><strong><a href="{user:link}">{user:name}</a></strong></p>

</div>

<h3>{message:title}</h3>

<p>{message:body}</p>

{if message:replies}

	{message:replies}

		<div id="reply{reply:id}" class="row">

			<div class="left">

				{reply:avatar}

				<p><strong><a href="{reply:link}">{reply:name}</a></strong></p>

			</div>

			<div class="right">

				<h4>{reply:title}</h4>

				<p><small>Replied on: <strong>{reply:date}</strong></small></p>

				<p>{reply:body}</p>

			</div>

		</div>

	{/message:replies}

{/if}

<br class="clear" />

<h3>Reply</h3>

<form method="post" action="{site:url}messages/send_reply/{message:id}" class="default">

	<label for="message">Message:</label>
	<textarea name="message" id="message" class="formelement small"></textarea>
	<br class="clear" /><br />

	<input type="submit" value="Send Message" id="submit" class="button nolabel" />

</form>

{include:footer}
