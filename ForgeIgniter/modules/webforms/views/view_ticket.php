<form action="<?php echo site_url($this->uri->uri_string()); ?>" method="post" class="default">

	<h1 class="headingleft">View Ticket <small>(<a href="<?php echo site_url('/admin/webforms/tickets'); ?>">Back to Tickets</a>)</small></h1>
	
	<div class="headingright">
		<input type="submit" value="Update Ticket" class="button nolabel" />	
	</div>

	<div class="clear"></div>

	<div class="message">
		<p>
			<strong>Subject:</strong> [#<?php echo $data['ticketID']; ?>]:</strong> <?php echo $data['subject']; ?><br />
			<strong>Date sent:</strong> <?php echo dateFmt($data['dateCreated']); ?><br />
			<?php if ($data['formName']): ?>
				<strong>Web Form:</strong> <?php echo $data['formName']; ?>
			<?php endif; ?>
		</p>
	</div>

	<div id="tpl-2col">

		<div class="col1">
		
			<h2 class="underline">Body</h2>
		
			<p><?php echo nl2br(auto_link($data['body'])); ?></p>
			
		</div>
		<div class="col2">
		
			<h2 class="underline">User Details</h2>
		
			<p><strong>Full name:</strong> <?php echo $data['fullName']; ?></p>
			<p><strong>Email:</strong> <a href="mailto:<?php echo $data['email']; ?>?subject=Re: [#<?php echo $data['ticketID']; ?>]: <?php echo $data['subject']; ?>"><?php echo $data['email']; ?></a></p>
			
		</div>
		<div class="clear"></div>
	</div>
	
	<br />
		
	<h2 class="underline">Process Ticket</h2>

	<label for="closed">Status:</label>
	<?php
		$options = array(
				0 => 'Open',
				1 => 'Closed');
		
		echo form_dropdown('closed',$options,set_value('closed', $data['closed']),'id="closed"');
	?>
	<br class="clear" />

	<label for="notes">Ticket notes:</label>
	<?php echo form_textarea('notes',set_value('notes', $data['notes']), 'id="notes" class="formelement small"'); ?>
	<br class="clear" />

</form>

<p class="clear" style="text-align: right;"><a href="#" class="button grey" id="totop">Back to top</a></p>