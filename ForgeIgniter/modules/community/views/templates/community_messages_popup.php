<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Popup</title>

	<link rel="stylesheet" type="text/css" href="http://static.halogy.com/themes/default/css/default.css" />

</head>

<body>

	<div id="tpl-popup" class="content">
	
		<h1>Send a Message</h1>
	
		<form method="post" action="{site:url}messages/send_message/{form:recipient-id}" class="default">
		
			<label for="to">To:</label>
			<input type="text" name="to" value="{form:to}" id="to" class="formelement" disabled="disabled" />
			<br class="clear" />
		
			<label for="subject">Subject:</label>
			<input type="text" name="subject" value="{form:subject}" id="subject" class="formelement" />
			<br class="clear" />
		
			<label for="message">Message:</label>
			<textarea name="message" id="message" class="formelement small">{form:message}</textarea>
			<br class="clear" /><br />
		
			<input type="submit" value="Send Message" id="submit" class="button nolabel" />
			
		</form>
		
	</div>

</body>
</html>